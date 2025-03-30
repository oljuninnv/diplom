<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Application;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\File;
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
use MoonShine\Support\Enums\JsEvent;
use Moonshine\Support\AlpineJs;

#[Icon('chat-bubble-bottom-center-text')]
/**
 * @extends ModelResource<Application>
 */
class ApplicationResource extends ModelResource
{
    protected string $model = Application::class;
    protected string $title = 'Заявки';

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
                ActionButton::make('Одобрить')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)->method('approve'),
                ActionButton::make('Отклонить')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)->method('decline')->async(),
                ActionButton::make('Назначить созвон')->showInDropdown()->canSee(fn($model) => $model->status === ApplicationStatusEnum::PENDING->value)->method('assignCall')->async()
            );
    }

    public function approve(MoonShineRequest $request)
    {
        $id = (int)$request->get('resourceItem');
        $reportAction = new ApplicationAction();
        $reportAction->approve($id);
    }

    public function decline(MoonShineRequest $request)
    {
        $id = (int)$request->get('resourceItem');
        $reportAction = new ApplicationAction();
        $reportAction->decline($id);

    }

    // public function assignCall(MoonShineRequest $request)
    // {
    //     $id = (int)$request->get('resourceItem');
    //     $reportAction = new ApplicationAction();
    //     $reportAction->approve($id);
    // }
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