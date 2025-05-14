<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use App\Mail\CallNotificationMail;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Call::with(['candidate', 'tutor', 'hr_manager']);

        if ($user->isTutorWorker()) {
            $query->where('tutor_id', $user->id);
        } elseif ($user->isAdmin()) {
            $query->where('hr_manager_id', $user->id);
        }

        $query->where(function ($q) {
            $q->whereDate('date', '>', now()->format('Y-m-d'))
                ->orWhere(function ($q) {
                    $q->whereDate('date', now()->format('Y-m-d'))
                        ->whereTime('time', '>=', now()->format('H:i:s'));
                });
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('candidate', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('tutor', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('hr_manager', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $request->whenFilled('type', fn($value) => $query->where('type', $value));
        $request->whenFilled('date', fn($value) => $query->where('date', $value));

        $sortDirection = $request->sort === 'datetime_desc' ? 'desc' : 'asc';
        $query->orderBy('date', $sortDirection)->orderBy('time', $sortDirection);

        $queryParams = $request->except('page');

        return view('workers.meeting', [
            'calls' => $query->paginate($request->perPage ?? 10)->appends($request->except('page')),
            'candidates' => User::candidates()->get(),
            'hrManagers' => User::hrManagers()->get(),
            'tutors' => User::tutors()->get(),
            'currentParams' => $queryParams,
        ]);
    }

    public function store(StoreMeetingRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $callData = [
            'candidate_id' => $data['user_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'meeting_link' => $data['link'],
            'type' => $data['type'],
        ];

        if ($user->isTutorWorker()) {
            $callData['tutor_id'] = $user->id;
            $callData['type'] = 'technical';
            $callData['hr_manager_id'] = null;
        } elseif ($user->isAdmin()) {
            $callData['hr_manager_id'] = $user->id;
            $callData['tutor_id'] = $data['tutor_id'] ?? null;
        } else {
            $callData['hr_manager_id'] = $request->hr_manager_id;
            $callData['tutor_id'] = $data['tutor_id'];
        }

        $call = Call::create($callData);

        // Получаем всех участников
        $candidate = User::with('telegramUser')->find($call->candidate_id);
        $tutor = $call->tutor_id ? User::with('telegramUser')->find($call->tutor_id) : null;
        $hrManager = $call->hr_manager_id ? User::with('telegramUser')->find($call->hr_manager_id) : null;

        // Отправка уведомлений
        $this->sendNotifications($candidate, $tutor, $hrManager, $call, 'scheduled');

        return redirect()->route('meetings.index', $request->only(['perPage']))
            ->with('success', 'Созвон успешно назначен. Уведомления отправлены.');
    }

    public function edit(Call $meeting)
    {
        return response()->json($meeting->only([
            'id',
            'candidate_id',
            'date',
            'time',
            'meeting_link',
            'type',
            'tutor_id',
            'hr_manager_id'
        ]));
    }

    public function update(UpdateMeetingRequest $request, Call $meeting)
    {
        $user = auth()->user();
        $data = $request->validated();

        $updateData = [
            'candidate_id' => $data['user_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'meeting_link' => $data['link'],
            'type' => $data['type'],
            'tutor_id' => $user->isTutorWorker() ? $user->id : ($data['tutor_id'] ?? null),
            'hr_manager_id' => $user->isAdmin() ? $user->id : $request->hr_manager_id,
        ];

        $meeting->update($updateData);

        // Получаем всех участников
        $candidate = User::with('telegramUser')->find($meeting->candidate_id);
        $tutor = $meeting->tutor_id ? User::with('telegramUser')->find($meeting->tutor_id) : null;
        $hrManager = $meeting->hr_manager_id ? User::with('telegramUser')->find($meeting->hr_manager_id) : null;

        // Отправка уведомлений
        $this->sendNotifications($candidate, $tutor, $hrManager, $meeting, 'updated');

        return redirect()->route('meetings.index', $request->only(['perPage']))
            ->with('success', 'Созвон успешно обновлен. Уведомления отправлены.');
    }

    public function destroy(Call $meeting)
    {
        // Получаем всех участников перед удалением
        $candidate = User::with('telegramUser')->find($meeting->candidate_id);
        $tutor = $meeting->tutor_id ? User::with('telegramUser')->find($meeting->tutor_id) : null;
        $hrManager = $meeting->hr_manager_id ? User::with('telegramUser')->find($meeting->hr_manager_id) : null;

        // Отправка уведомлений об отмене
        $this->sendNotifications($candidate, $tutor, $hrManager, $meeting, 'cancelled');

        $meeting->delete();

        return redirect()->route('meetings.index', request()->except(['_token', 'page']))
            ->with('success', 'Созвон успешно отменен. Уведомления отправлены.');
    }

    protected function sendNotifications(?User $candidate, ?User $tutor, ?User $hrManager, Call $call, string $action)
    {
        try {
            $callType = $this->getCallTypeName($call->type);

            // Отправка кандидату
            if ($candidate) {
                $this->sendEmailNotification($candidate, $tutor, $hrManager, $call, $action, $callType);
                $this->sendTelegramNotification($candidate, $call, $callType, $action);
            }

            // Отправка тьютору
            if ($tutor) {
                $this->sendTelegramNotification($tutor, $call, $callType, $action);
            }

            // Отправка HR-менеджеру
            if ($hrManager) {
                $this->sendTelegramNotification($hrManager, $call, $callType, $action);
            }

        } catch (\Exception $e) {
            Log::error('Ошибка отправки уведомлений: ' . $e->getMessage());
        }
    }

    protected function sendEmailNotification(User $user, ?User $tutor, ?User $hrManager, Call $call, string $action, string $callType)
    {
        try {
            $emailData = [
                'user' => $user,
                'tutor' => $tutor ?? new User(['name' => 'Не указан']),
                'hrManager' => $hrManager ?? new User(['name' => 'Не указан']),
                'call' => $call,
                'action' => $action,
                'call_type' => $callType,
                'credentials' => [
                    'email' => $user->email,
                    'password' => 'password',
                    'login_url' => config('app.url')
                ]
            ];

            Mail::to($user->email)->send(new CallNotificationMail($emailData));

        } catch (\Exception $e) {
            Log::error("Ошибка отправки email пользователю {$user->id}: " . $e->getMessage());
        }
    }

    protected function sendTelegramNotification(User $user, Call $call, string $callType, string $action)
    {
        if (!$user->telegramUser) {
            return;
        }

        try {
            $actionTexts = [
                'scheduled' => 'назначен',
                'updated' => 'изменен',
                'cancelled' => 'отменен'
            ];

            $text = "📅 <b>Созвон {$actionTexts[$action]}</b>\n\n";
            $text .= "🔹 <b>Тип:</b> {$callType}\n";
            $text .= "📅 <b>Дата:</b> {$call->date}\n";
            $text .= "🕒 <b>Время:</b> {$call->time}\n";

            if ($action !== 'cancelled') {
                $text .= "🔗 <b>Ссылка:</b> {$call->meeting_link}\n\n";
            } else {
                $text .= "\nДля уточнения деталей свяжитесь с организатором.";
            }

            $this->telegram->sendMessage([
                'chat_id' => $user->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

        } catch (\Exception $e) {
            Log::error("Ошибка отправки Telegram уведомления пользователю {$user->id}: " . $e->getMessage());
        }
    }

    protected function getCallTypeName(string $type): string
    {
        return match ($type) {
            'primary' => 'Первичный созвон',
            'technical' => 'Технический созвон',
            'final' => 'Финальный созвон',
            default => 'Созвон'
        };
    }

    public function canUpdate(User $user, Call $call)
    {
        return match ($user->role->name) {
            UserRoleEnum::TUTOR_WORKER->value => $call->type === 'technical' &&
            ($call->tutor_id === $user->id || is_null($call->tutor_id)) &&
            is_null($call->hr_manager_id),
            UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value => true,
            default => $call->hr_manager_id === $user->id,
        };
    }

    public function canDelete(User $user, Call $call)
    {
        return match ($user->role->name) {
            UserRoleEnum::TUTOR_WORKER->value => $call->type === 'technical' &&
            $call->tutor_id === $user->id &&
            is_null($call->hr_manager_id),
            UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value => true,
            default => $call->hr_manager_id === $user->id,
        };
    }

    public function getUserData(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'telegram_user' => $user->telegramUser ? ['username' => $user->telegramUser->username] : null,
            'worker' => $user->worker ? [
                'department' => $user->worker->department->name ?? null,
                'post' => $user->worker->post->name ?? null,
                'hire_date' => $user->worker->hire_date,
            ] : null
        ]);
    }
}