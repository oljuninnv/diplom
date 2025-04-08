<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\User;
use App\Http\Resources\CallResource;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMeetingRequest;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Call::with(['candidate', 'tutor', 'hr_manager'])
            ->futureMeetings()
            ->filterByUserRole($user)
            ->search($request->search)
            ->filterByType($request->type)
            ->filterByDate($request->date)
            ->orderByDateTime($request->sort);

        return view('workers.meeting', [
            'calls' => $query->paginate($request->perPage ?? 10)->appends($request->except('page')),
            'candidates' => User::candidates()->get(),
            'hrManagers' => User::hrManagers()->get(),
            'tutors' => User::tutors()->get(),
        ]);
    }

    public function store(StoreMeetingRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $call = Call::create($this->prepareCallData($request));
            return redirect()->route('meetings.index')->with('success', 'Созвон успешно назначен');
        });
    }

    public function edit(Call $meeting)
    {
        return new CallResource($meeting);
    }

    public function update(StoreMeetingRequest $request, Call $meeting)
    {
        return DB::transaction(function () use ($request, $meeting) {
            $meeting->update($this->prepareCallData($request, $meeting));
            return redirect()->route('meetings.index')->with('success', 'Созвон успешно обновлен');
        });
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

    protected function prepareCallData(StoreMeetingRequest $request, ?Call $meeting = null): array
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
            $callData['tutor_id'] = $data['tutor_id'];
        } else {
            $callData['hr_manager_id'] = $request->hr_manager_id;
            $callData['tutor_id'] = $data['tutor_id'];
        }

        return $callData;
    }
}