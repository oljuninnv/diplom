<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Actions\ApplicationAction;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Enums\ApplicationStatusEnum;
use App\Models\Call;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        // Сохраняем параметры пагинации в сессии
        if ($request->has('per_page')) {
            session(['per_page' => $request->per_page]);
        }
        
        $perPage = $request->input('per_page', session('per_page', 10));

        $applications = Application::with(['user', 'department'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                $query->where('department_id', $request->department);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $departments = Department::pluck('name', 'id');
        $statuses = ApplicationStatusEnum::getAll();

        $hrManagers = User::whereHas('role', function ($query) {
            $query->where('name', UserRoleEnum::ADMIN);
        })->pluck('name', 'id');

        $tutors = User::whereHas('role', function ($query) {
            $query->where('name', UserRoleEnum::TUTOR_WORKER);
        })->pluck('name', 'id');

        return view('workers.applications', compact('applications', 'departments', 'statuses', 'hrManagers', 'tutors'));
    }

    public function approve(Application $application, Request $request)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'hr_manager_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
        ]);

        $action = new ApplicationAction();
        $result = $action->approve([
            'id' => $application->id,
            'tutor' => $request->tutor_id,
            'hr-manager' => $request->hr_manager_id,
            'task_id' => $request->task_id
        ]);

        return back()->with('success', $result);
    }

    public function decline(Application $application)
    {
        $action = new ApplicationAction();
        $result = $action->decline($application->id);

        return back()->with('success', $result);
    }

    public function underConsideration(Application $application)
    {
        $action = new ApplicationAction();
        $result = $action->underConsideration($application->id);

        return back()->with('success', $result);
    }

    public function assignCall(Application $application, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'meeting_link' => 'required|url',
            'hr-manager' => 'required|exists:users,id',
        ]);

        \Log::info($request->all());

        // Проверка на существующий созвон
        $existingCall = Call::where('candidate_id', $application->user_id)
            ->where(function($query) use ($request) {
                $query->where('date', '>', $request->date)
                    ->orWhere(function($q) use ($request) {
                        $q->where('date', $request->date)
                          ->where('time', '>=', $request->time);
                    });
            })
            ->first();

        if ($existingCall) {
            return back()->with('error', 'У этого пользователя уже есть активный созвон на ' . $existingCall->date . ' в ' . $existingCall->time);
        }

        $action = new ApplicationAction();
        $result = $action->assignCall($application->id, [
            'date' => $request->date,
            'time' => $request->time,
            'meeting_link' => $request->meeting_link,
            'hr-manager' => $request->input('hr-manager')
        ]);

        return back()->with('success', $result);
    }

    public function download(Application $application)
    {
        if (!$application->resume) {
            abort(404, 'Резюме не найдено');
        }

        $path = storage_path('app/public/' . $application->resume);
        
        if (!file_exists($path)) {
            abort(404, 'Файл резюме не найден');
        }

        return response()->download($path);
    }
}