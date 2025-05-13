<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use MoonShine\Support\Enums\ToastType;
use App\Actions\TaskStatusAction;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\ListOf;
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
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Url;

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
    protected bool $simplePaginate = true;
    protected bool $cursorPaginate = true;

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()->add(
            ActionButton::make('Отправить на доработку')
                ->showInDropdown()
                ->canSee(fn($model) => $model->status === TaskStatusEnum::UNDER_REVIEW->value)
                ->inModal(
                    'Отправить на доработку',
                    fn(TaskStatus $taskStatus) => FormBuilder::make()
                        ->name('revisionModal')
                        ->fields([
                            Hidden::make('id')->setValue($taskStatus->id),
                            Textarea::make('Комментарий', 'comment'),
                            File::make('Файл', 'file')->required()
                        ])
                        ->asyncMethod('revision')
                        ->submit('Отправить')
                ),
            ActionButton::make('Одобрить задание')
                ->showInDropdown()
                ->canSee(fn($model) => $model->status === TaskStatusEnum::UNDER_REVIEW->value)
                ->inModal(
                    'Одобрить задание',
                    fn(TaskStatus $taskStatus) => FormBuilder::make()
                        ->name('revisionModal')
                        ->fields([
                            Hidden::make('id')->setValue($taskStatus->id),
                            File::make('Отчёт', 'file')->required(),
                        ])
                        ->asyncMethod('approved')
                        ->submit('Отправить')
                ),
            ActionButton::make('Задание провалено')
                ->showInDropdown()
                ->canSee(fn($model) => $model->status === TaskStatusEnum::UNDER_REVIEW->value)
                ->inModal(
                    'Задание провалено',
                    fn(TaskStatus $taskStatus) => FormBuilder::make()
                        ->name('revisionModal')
                        ->fields([
                            Hidden::make('id')->setValue($taskStatus->id),
                            File::make('Отчёт', 'file')->required(),
                        ])
                        ->asyncMethod('failed')
                        ->submit('Отправить')
                ),
            ActionButton::make('Назначить финальный созвон')
                ->showInDropdown()
                ->canSee(fn($model) => $model->status === TaskStatusEnum::APPROVED->value)
                ->inModal(
                    'Назначить финальный созвон',
                    fn(TaskStatus $taskStatus) => FormBuilder::make()
                        ->name('assignCallModal')
                        ->fields([
                            Hidden::make('id')->setValue($taskStatus->id),
                            Date::make('Дата', 'date')->sortable()->required(),
                            Text::make('Время', 'time')->placeholder('HH:mm')->sortable()->required(),
                            URL::make('Ссылка на звонок', 'meeting_link')->required(),
                        ])
                        ->asyncMethod('final_call')
                        ->submit('Назначить')
                ),
            ActionButton::make('Назначить технический созвон')
                ->showInDropdown()
                ->canSee(fn($model) => $model->status === TaskStatusEnum::IN_PROGRESS->value || $model->status === TaskStatusEnum::UNDER_REVIEW->value)
                ->inModal(
                    'Назначить технический созвон',
                    fn(TaskStatus $taskStatus) => FormBuilder::make()
                        ->name('assignCallModal')
                        ->fields([
                            Hidden::make('id')->setValue($taskStatus->id),
                            Date::make('Дата', 'date')->sortable()->required(),
                            Text::make('Время', 'time')->placeholder('HH:mm')->sortable()->required(),
                            URL::make('Ссылка на звонок', 'meeting_link')->required(),
                        ])
                        ->asyncMethod('technical_call')
                        ->submit('Назначить')
                ),
        );
    }

    public function revision(MoonShineRequest $request): MoonShineJsonResponse
    {
        $data = $request->all();

        // Сохранение файла и получение пути
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('moonshine_reports', moonshineConfig()->getDisk());
            $data['file_path'] = $filePath;
        }

        $reportAction = new TaskStatusAction();
        $reportAction->revision($data);
        return MoonShineJsonResponse::make()
            ->events([AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName())])
            ->toast('Задание отправлено на доработку', ToastType::SUCCESS);
    }

    public function approved(MoonShineRequest $request): MoonShineJsonResponse
    {
        $data = $request->all();

        // Сохранение файла и получение пути
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('moonshine_reports', moonshineConfig()->getDisk());
            $data['file_path'] = $filePath;
        }

        // Логика для обработки других данных
        $reportAction = new TaskStatusAction();
        $reportAction->approved($data);

        return MoonShineJsonResponse::make()
            ->events([AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName())])
            ->toast('Задание было одобрено', ToastType::SUCCESS);
    }

    public function failed(MoonShineRequest $request): MoonShineJsonResponse
    {
        $data = $request->all();

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('moonshine_reports', moonshineConfig()->getDisk());
            $data['file_path'] = $filePath;
        }

        $reportAction = new TaskStatusAction();
        $reportAction->failed($data);
        return MoonShineJsonResponse::make()
            ->events([AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName())])
            ->toast('Задание было провалено', ToastType::SUCCESS);
    }

    public function final_call(MoonShineRequest $request): MoonShineJsonResponse
    {
        $reportAction = new TaskStatusAction();
        $reportAction->final_call($request->all());
        return MoonShineJsonResponse::make()
            ->events([AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName())])
            ->toast('Финальный созвон был назначен', ToastType::SUCCESS);
    }

    public function technical_call(MoonShineRequest $request): MoonShineJsonResponse
    {
        $reportAction = new TaskStatusAction();
        $reportAction->technical_call($request->all());
        return MoonShineJsonResponse::make()
            ->events([AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName())])
            ->toast('Технический созвон был назначен', ToastType::SUCCESS);
    }

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
                Select::make('Задание', 'task_id')
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
            Select::make('Тьютор', 'tutor_id')
                ->options(
                    User::query()
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value))
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            Select::make('HR-менеджер', 'hr_manager_id')
                ->options(
                    User::query()
                        ->whereHas('role', fn($q) => $q->whereIn('name', [UserRoleEnum::ADMIN->value,UserRoleEnum::SUPER_ADMIN->value]))
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            Select::make('Задание', 'task_id')
                ->options(
                    Task::query()
                        ->get()
                        ->pluck('title', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            Select::make('Статус', 'status')
                ->options(TaskStatusEnum::getAll())
                ->nullable(),

            DateRange::make('Дедлайн', 'end_date')
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
        ];
    }

    public function search(): array
    {
        return ['user.name', 'tutor.name', 'hr_manager.name', 'task.title'];
    }
}