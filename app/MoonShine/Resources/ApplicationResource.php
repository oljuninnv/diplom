<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use MoonShine\UI\Fields\Hidden;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use App\Models\Task;
use App\Models\Post;
use App\Models\Department;
use App\Models\Application;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\Modal;
use App\Enums\UserRoleEnum;
use App\Models\User;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Date;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Fields\DateRange;
use App\Enums\ApplicationStatusEnum;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;
use App\Actions\ApplicationAction;
use MoonShine\Laravel\MoonShineRequest;

#[Icon('chat-bubble-bottom-center-text')]
/**
 * @extends ModelResource<Application>
 */
class ApplicationResource extends ModelResource
{
    protected string $model = Application::class;
    protected string $title = 'Заявки';

    protected string $column = 'id';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

    protected array $with = ['user', 'department', 'vacancy'];

    public function indexFields(): iterable
    {
        return [
            ID::make('id'),
            BelongsTo::make('Пользователь', 'user', resource: UserResource::class),
            File::make('Резюме', 'resume')
                ->disk(moonshineConfig()->getDisk())
                ->dir('moonshine_applications')
                ->allowedExtensions(['pdf', 'docx', 'doc']),
            Text::make('Статус', 'status'),
            BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class),
            BelongsTo::make('Вакансия', 'vacancy', resource: VacancyResource::class),
            Date::make('Дата создания', 'created_at')
                ->sortable(),
        ];
    }

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()->add(
            ActionButton::make('Одобрить')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)
                ->inModal(
                    'Назначить тестовое задание',
                    fn(Application $application) => FormBuilder::make()
                        ->name('testTaskModal')
                        ->fields([
                            Hidden::make('id')->setValue($application->id),
                            Select::make('Тьютор', 'tutor')
                                ->options(
                                    User::query()
                                        ->whereHas('role', function ($query) {
                                            $query->where('name', UserRoleEnum::TUTOR_WORKER);
                                        })
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->sortable()
                                ->searchable(),
                            Select::make('HR-мэнеджер', 'hr-manager')
                                ->options(
                                    User::query()
                                        ->whereHas('role', function ($query) {
                                            $query->where('name', UserRoleEnum::ADMIN);
                                        })
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->sortable()
                                ->searchable(),
                            Select::make('Задание', 'task_id')
                                ->nullable()
                                ->required()
                                ->options(Task::query()->get()->pluck('title', 'id')->toArray())
                                ->searchable(),
                        ])
                        ->asyncMethod('approve')
                        ->submit('Назначить')
                ),
            ActionButton::make('Отклонить')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)->method('decline'),
            ActionButton::make('Назначить созвон')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)
                ->inModal(
                    'Назначить созвон',
                    fn(Application $application) => FormBuilder::make()
                        ->name('assignCallModal')
                        ->fields([
                            ID::make('id')->setValue($application->id),
                            Date::make('Дата', 'date')->sortable()->required(),
                            Text::make('Время', 'time')->placeholder('HH:mm')->sortable()->required(),
                            Select::make('Тьютор', 'tutor')
                                ->options(
                                    User::query()
                                        ->whereHas('role', function ($query) {
                                            $query->where('name', UserRoleEnum::TUTOR_WORKER);
                                        })
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->sortable()
                                ->searchable(),
                            Select::make('HR-мэнеджер', 'hr-manager')
                                ->options(
                                    User::query()
                                        ->whereHas('role', function ($query) {
                                            $query->where('name', UserRoleEnum::ADMIN);
                                        })
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->sortable()
                                ->searchable(),
                        ])
                        ->asyncMethod('assignCall')
                        ->submit('Назначить')
                ),
            // ActionButton::make('Test')->inModal(
            //     'Одобрить заявку',
            //     fn() => '',
            //     builder: fn(Modal $modal) => $modal->setComponents([
            //         $this->form()
            //     ]),
            // ),
        );

    }

    // private function form(): FormBuilder
    // {
    //     return FormBuilder::make()
    //         ->name('asign')
    //         ->fields([
    //             Hidden::make('id', 'id'),
    //             Date::make('Дата', 'date')->sortable()->required(),
    //             Text::make('Время', 'time')->placeholder('HH:mm')->sortable()->required(),
    //             Select::make('Тьютор', 'tutor')
    //                 ->options(
    //                     User::query()
    //                         ->whereHas('role', function ($query) {
    //                             $query->where('name', UserRoleEnum::TUTOR_WORKER);
    //                         })
    //                         ->pluck('name', 'id')
    //                         ->toArray()
    //                 )
    //                 ->required()
    //                 ->sortable()
    //                 ->searchable(),
    //             Select::make('HR-мэнеджер', 'hr-manager')
    //                 ->options(
    //                     User::query()
    //                         ->whereHas('role', function ($query) {
    //                             $query->where('name', UserRoleEnum::ADMIN);
    //                         })
    //                         ->pluck('name', 'id')
    //                         ->toArray()
    //                 )
    //                 ->required()
    //                 ->sortable()
    //                 ->searchable(),
    //             Select::make('Отделы', 'department_id')
    //                 ->nullable()
    //                 ->options(Department::query()->get()->pluck('name', 'id')->toArray())
    //                 ->reactive(function (FieldsContract $fields, ?string $value) {
    //                     $fields->findByColumn('post_id')
    //                             ?->options(
    //                             Post::where('department_id', $value)
    //                                 ->get()
    //                                 ->pluck('name', 'id')
    //                                 ->toArray()
    //                         );

    //                     return $fields;
    //                 })
    //                 ->searchable()
    //                 ->required(),

    //             Select::make('Должность', 'post_id')
    //                 ->nullable()
    //                 ->options(Post::query()->get()->pluck('name', 'id')->toArray())
    //                 ->reactive(function (FieldsContract $fields, ?string $value) {
    //                     $fields->findByColumn('task_id')
    //                             ?->options(
    //                             Task::where('post_id', $value)
    //                                 ->get()
    //                                 ->pluck('title', 'id')
    //                                 ->toArray()
    //                         );

    //                     return $fields;
    //                 })
    //                 ->required()
    //                 ->searchable(),

    //             Select::make('Задание', 'task_id')
    //                 ->nullable()
    //                 ->options(Task::query()->get()->pluck('title', 'id')->toArray())
    //                 ->reactive()
    //                 ->required()
    //                 ->searchable(),
    //         ])->asyncMethod('assignCall')
    //         ->submit('Назначить');
    // }

    // protected function pageComponents(): array
    // {
    //     return [
    //         Div::make([
    //             $this->form()
    //         ])->style('display: none'),
    //     ];
    // }

    public function approve(MoonShineRequest $request)
    {
        $reportAction = new ApplicationAction();
        $reportAction->approve($request->all());

    }

    public function decline(MoonShineRequest $request)
    {
        $id = (int) $request->get('resourceItem');
        $reportAction = new ApplicationAction();
        $reportAction->decline($id);

    }

    public function assignCall(MoonShineRequest $request)
    {
        \Log::info($request->all());
    }
    public function formFields(): iterable
    {
        return [
            Box::make([
                BelongsTo::make('Пользователь', 'user', resource: UserResource::class)
                    ->required()
                    ->searchable(),
                File::make('Резюме', 'resume')
                    ->disk(moonshineConfig()->getDisk())
                    ->dir('moonshine_applications')
                    ->allowedExtensions(['pdf', 'docx', 'doc'])
                    ->required($this->getItem()?->exists === false),
                Select::make('Статус', 'status')
                    ->options(ApplicationStatusEnum::getAll())
                    ->required()
                    ->searchable(),
                BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Вакансия', 'vacancy', resource: VacancyResource::class)
                    ->searchable(),
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
            BelongsTo::make('Пользователь', 'user', resource: UserResource::class)
                ->nullable()
                ->searchable(),
            Select::make('Статус', 'status')
                ->options(ApplicationStatusEnum::getAll())
                ->default('ожидание')
                ->nullable(),
            BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class)
                ->nullable()
                ->searchable(),

            BelongsTo::make('Вакансия', 'vacancy', resource: VacancyResource::class)
                ->nullable()
                ->searchable(),

            DateRange::make('Дата создания', 'created_at')
                ->nullable(),
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'resume' => [
                $item->exists ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240' // 10MB
            ],
            'status' => ['required', 'string', 'in:' . implode(',', ApplicationStatusEnum::getAll())],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'vacancy_id' => ['nullable', 'integer', 'exists:vacancies,id'],
        ];
    }

    public function search(): array
    {
        return ['user.name', 'department.name', 'vacancy.posst.name'];
    }
}