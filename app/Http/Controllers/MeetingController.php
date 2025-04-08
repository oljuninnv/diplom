<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreMeetingRequest;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Call::with(['candidate', 'tutor', 'hr_manager']);

        // Для тьютора показываем только технические созвоны без HR или где он назначен
        if ($user->role->name === UserRoleEnum::TUTOR_WORKER->value) {
            $query->where('tutor_id', $user->id);
        }

        if ($user->role->name === UserRoleEnum::ADMIN->value || $user->role->name === UserRoleEnum::SUPER_ADMIN->value) {
            $query->where('hr_manager_id', $user->id);
        }

        // Только будущие созвоны
        $query->where(function ($q) {
            $q->whereDate('date', '>', now()->format('Y-m-d'))
                ->orWhere(function ($q) {
                    $q->whereDate('date', now()->format('Y-m-d'))
                        ->whereTime('time', '>=', now()->format('H:i:s'));
                });
        });

        // Фильтрация
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('candidate', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('tutor', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('hr_manager', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $sortDirection = $request->sort === 'datetime_desc' ? 'desc' : 'asc';
        $query->orderBy('date', $sortDirection)->orderBy('time', $sortDirection);

        // Получаем данные для модальных окон
        $candidates = User::where('role_id', Role::getIdByRole(UserRoleEnum::USER))->get();
        $hrManagers = User::whereIn('role_id', [
            Role::getIdByRole(UserRoleEnum::ADMIN),
            Role::getIdByRole(UserRoleEnum::SUPER_ADMIN)
        ])->get();
        $tutors = User::where('role_id', Role::getIdByRole(UserRoleEnum::TUTOR_WORKER))->get();

        return view('workers.meeting', [
            'calls' => $query->paginate($request->perPage ?? 10)
                ->appends($request->except('page')),
            'candidates' => $candidates,
            'hrManagers' => $hrManagers,
            'tutors' => $tutors,
        ]);
    }

    public function store(StoreMeetingRequest $request)
    {
        $user = auth()->user();
        $isTutor = $user->role->name === UserRoleEnum::TUTOR_WORKER->value;

        $data = $request->validated();

        $callData = [
            'candidate_id' => $data['user_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'meeting_link' => $data['link'],
            'type' => $data['type'],
        ];

        if ($isTutor) {
            $callData['tutor_id'] = $user->id;
            $callData['type'] = 'technical';
            $callData['hr_manager_id'] = null;
        } elseif (
            $user->role->name === UserRoleEnum::ADMIN->value ||
            $user->role->name === UserRoleEnum::SUPER_ADMIN->value
        ) {
            $callData['hr_manager_id'] = $user->id;
            $callData['tutor_id'] = $data['tutor_id'];
        } else {
            $callData['hr_manager_id'] = $request->hr_manager_id;
            $callData['tutor_id'] = $data['tutor_id'];
        }

        Call::create($callData);

        return redirect()->route('meetings.index')
            ->with('success', 'Созвон успешно назначен');
    }

    public function edit(Call $meeting)
    {
        return response()->json([
            'id' => $meeting->id,
            'candidate_id' => $meeting->candidate_id,
            'date' => $meeting->date,
            'time' => $meeting->time,
            'meeting_link' => $meeting->meeting_link,
            'type' => $meeting->type,
            'tutor_id' => $meeting->tutor_id,
            'hr_manager_id' => $meeting->hr_manager_id,
        ]);
    }

    public function update(StoreMeetingRequest $request, Call $meeting)
    {
        $user = auth()->user();
        $isTutor = $user->role->name === UserRoleEnum::TUTOR_WORKER->value;

        $data = $request->validated();

        $callData = [
            'candidate_id' => $data['user_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'meeting_link' => $data['link'],
            'type' => $data['type'],
        ];

        if ($isTutor) {
            $callData['tutor_id'] = $user->id;
            $callData['type'] = 'technical';
        } elseif (
            $user->role->name === UserRoleEnum::ADMIN->value ||
            $user->role->name === UserRoleEnum::SUPER_ADMIN->value
        ) {
            $callData['hr_manager_id'] = $user->id;
            $callData['tutor_id'] = $data['tutor_id'];
        } else {
            $callData['hr_manager_id'] = $request->hr_manager_id;
            $callData['tutor_id'] = $data['tutor_id'];
        }

        $meeting->update($callData);

        return redirect()->route('meetings.index')
            ->with('success', 'Созвон успешно обновлен');
    }

    public function destroy(Call $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')
            ->with('success', 'Созвон успешно удален');
    }

    // Методы проверки прав доступа
    public function canUpdate(User $user, Call $call)
    {
        // Тьютор может редактировать только технические созвоны без HR или где он назначен
        if ($user->role->name === UserRoleEnum::TUTOR_WORKER->value) {
            return $call->type === 'technical' &&
                ($call->tutor_id === $user->id || is_null($call->tutor_id)) &&
                is_null($call->hr_manager_id);
        }

        // Admin/SuperAdmin могут редактировать любые созвоны
        if (in_array($user->role->name, [UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])) {
            return true;
        }

        // Остальные могут редактировать только свои созвоны (где они HR)
        return $call->hr_manager_id === $user->id;
    }

    public function canDelete(User $user, Call $call)
    {
        // Тьютор может удалять только технические созвоны без HR, где он назначен
        if ($user->role->name === UserRoleEnum::TUTOR_WORKER->value) {
            return $call->type === 'technical' &&
                $call->tutor_id === $user->id &&
                is_null($call->hr_manager_id);
        }

        if (in_array($user->role->name, [UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])) {
            return true;
        }

        return $call->hr_manager_id === $user->id;
    }

    public function getUserData(User $user)
    {
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'telegram_user' => $user->telegramUser ? [
                'username' => $user->telegramUser->username
            ] : null
        ];

        if ($user->worker) {
            $userData['worker'] = [
                'department' => $user->worker->department->name ?? null,
                'post' => $user->worker->post->name ?? null,
                'hire_date' => $user->worker->hire_date ? $user->worker->hire_date : null,
            ];
        }

        return response()->json($userData);
    }
}