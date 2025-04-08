<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMeetingRequest;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Call::with(['candidate', 'tutor', 'hr_manager']);

        if ($user->isTutorWorker()) {
            $query->where('tutor_id', $user->id);
        } elseif ($user->isAdmin()) {
            $query->where('hr_manager_id', $user->id);
        }

        $query->where(function($q) {
            $q->whereDate('date', '>', now()->format('Y-m-d'))
              ->orWhere(function($q) {
                  $q->whereDate('date', now()->format('Y-m-d'))
                    ->whereTime('time', '>=', now()->format('H:i:s'));
              });
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('candidate', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('tutor', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('hr_manager', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $request->whenFilled('type', fn($value) => $query->where('type', $value));
        $request->whenFilled('date', fn($value) => $query->where('date', $value));

        $sortDirection = $request->sort === 'datetime_desc' ? 'desc' : 'asc';
        $query->orderBy('date', $sortDirection)->orderBy('time', $sortDirection);

        return view('workers.meeting', [
            'calls' => $query->paginate($request->perPage ?? 10)->appends($request->except('page')),
            'candidates' => User::candidates()->get(),
            'hrManagers' => User::hrManagers()->get(),
            'tutors' => User::tutors()->get(),
        ]);
    }

    public function store(StoreMeetingRequest $request)
    {
        if (Call::where('date', $request->date)->where('time', $request->time)->exists()) {
            return redirect()->back()->withInput()->withErrors(['time' => 'На это время уже назначен созвон']);
        }

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
            $callData['tutor_id'] = $data['tutor_id'];
        } else {
            $callData['hr_manager_id'] = $request->hr_manager_id;
            $callData['tutor_id'] = $data['tutor_id'];
        }

        Call::create($callData);
        return redirect()->route('meetings.index')->with('success', 'Созвон успешно назначен');
    }

    public function edit(Call $meeting)
    {
        return response()->json($meeting->only([
            'id', 'candidate_id', 'date', 'time', 'meeting_link', 'type', 'tutor_id', 'hr_manager_id'
        ]));
    }

    public function update(StoreMeetingRequest $request, Call $meeting)
    {
        if (Call::where('date', $request->date)
            ->where('time', $request->time)
            ->where('id', '!=', $meeting->id)
            ->exists()) {
            return redirect()->back()->withInput()->withErrors(['time' => 'На это время уже назначен созвон']);
        }

        $user = auth()->user();
        $data = $request->validated();

        $meeting->update([
            'candidate_id' => $data['user_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'meeting_link' => $data['link'],
            'type' => $data['type'],
            'tutor_id' => $user->isTutorWorker() ? $user->id : $data['tutor_id'],
            'hr_manager_id' => $user->isAdmin() ? $user->id : $request->hr_manager_id,
        ]);

        return redirect()->route('meetings.index')->with('success', 'Созвон успешно обновлен');
    }

    public function destroy(Call $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Созвон успешно удален');
    }

    public function canUpdate(User $user, Call $call)
    {
        return match($user->role->name) {
            UserRoleEnum::TUTOR_WORKER->value => $call->type === 'technical' && 
                ($call->tutor_id === $user->id || is_null($call->tutor_id)) && 
                is_null($call->hr_manager_id),
            UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value => true,
            default => $call->hr_manager_id === $user->id,
        };
    }

    public function canDelete(User $user, Call $call)
    {
        return match($user->role->name) {
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