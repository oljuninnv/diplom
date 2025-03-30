<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Enums\TaskStatusEnum;
use App\Models\User;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use App\Enums\UserRoleEnum;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\DateRange;

#[Icon('table-cells')]
/**
 * @extends ModelResource<TaskStatus>
 */
class TaskStatusResource extends ModelResource
{
    protected string $model = TaskStatus::class;
    protected string $title = 'Статус выполнения заданий';

    protected array $with = ['user', 'tutor', 'hr_manager', 'task'];

    protected bool $createInModal = true;
    protected bool $detailInModal = true;
    protected bool $editInModal = true;
    protected bool $cursorPaginate = true;

    public function indexFields(): iterable
    {
        return [
            ID::make('id')->sortable(),
            BelongsTo::make('Кандидат', 'user', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value)))
                ->sortable()
                ->searchable(),
            BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value)))
                ->sortable()
                ->searchable(),
            BelongsTo::make('HR-менеджер', 'hr_manager', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::ADMIN->value)))
                ->sortable()
                ->searchable(),
            BelongsTo::make('Задание', 'task', resource: TaskResource::class)
                ->sortable()
                ->searchable(),
            Text::make('Ссылка на задание', 'github_repo')
                ->nullable()
                ->sortable(),
            Select::make('Статус', 'status')
                ->options(TaskStatusEnum::getAll())
                ->sortable(),
            Date::make('Дата окончания', 'end_date')
                ->sortable(),
            Number::make('Количество запросов с помощью', 'number_of_requests')
                ->sortable(),
            Text::make('Дата создания', 'created_at')
                ->sortable(),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                BelongsTo::make('Кандидат', 'user', resource: UserResource::class)
                    ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value)))
                    ->required()
                    ->searchable(),
                BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                    ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value)))
                    ->required()
                    ->searchable(),
                BelongsTo::make('HR-менеджер', 'hr_manager', resource: UserResource::class)
                    ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::ADMIN->value)))
                    ->required()
                    ->searchable(),
                Select::make('Задание', 'task')
                    ->required()
                    ->options(Task::query()->get()->pluck('title', 'id')->toArray())
                    ->searchable()
                    ->reactive(function (FieldsContract $fields, ?string $value) {
                        $endDateField = $fields->findByColumn('end_date');
                        if ($value) {
                            $task = Task::find($value);
                            if ($task && $task->deadline) {
                                $endDateField->setValue(
                                    now()->addWeeks($task->deadline)->format('Y-m-d')
                                );
                            }
                        } else {
                            $endDateField->setValue(null);
                        }

                        return $fields;
                    }),

                Date::make('Дата окончания', 'end_date')
                    ->reactive()
                    ->required()
                    ->default(now()->addWeeks(1)->format('Y-m-d')),
                Text::make('Ссылка на задание', 'github_repo')
                    ->nullable(),
                Select::make('Статус', 'status')
                    ->options(TaskStatusEnum::getAll())
                    ->default(TaskStatusEnum::IN_PROGRESS)
                    ->required(),
                Number::make('Количество запросов с помощью', 'number_of_requests')
                    ->default(0)
                    ->min(0)
                    ->required(),
            ])
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields()
        ];
    }

    public function filters(): iterable
    {
        return [
            BelongsTo::make('Кандидат', 'user', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value)))
                ->searchable()
                ->nullable(),
            BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value)))
                ->searchable()
                ->nullable(),
            BelongsTo::make('HR-менеджер', 'hr_manager', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::ADMIN->value)))
                ->searchable()
                ->nullable(),
            BelongsTo::make('Задание', 'task', resource: TaskResource::class)
                ->searchable()
                ->nullable(),
            Select::make('Статус', 'status')
                ->options(TaskStatusEnum::getAll())
                ->nullable(),
            DateRange::make('Дата окончания', 'end_date')
                ->nullable(),
            DateRange::make('Дата создания', 'created_at')
                ->nullable(),
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $isUser = User::where('id', $value)
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value))
                        ->exists();
                    if (!$isUser) {
                        $fail('Выбранный пользователь не является кандидатом');
                    }
                }
            ],
            'tutor_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $isTutor = User::where('id', $value)
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value))
                        ->exists();
                    if (!$isTutor) {
                        $fail('Выбранный пользователь не является тьютором');
                    }
                }
            ],
            'hr_manager_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $isHr = User::where('id', $value)
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::ADMIN->value))
                        ->exists();
                    if (!$isHr) {
                        $fail('Выбранный пользователь не является HR-менеджером');
                    }
                }
            ],
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'github_repo' => [
                'nullable',
                'string',
                'url',
                'max:255'
            ],
            'status' => ['required', 'string', 'in:' . implode(',', TaskStatusEnum::getAll())],
            'end_date' => ['required', 'date', 'after_or_equal:today'],
            'number_of_requests' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function search(): array
    {
        return ['user.name', 'tutor.name', 'hr_manager.name', 'task.title'];
    }
}