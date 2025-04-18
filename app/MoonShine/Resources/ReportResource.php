<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Report;
use App\Models\TaskStatus;
use App\Models\User;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Select;
use MoonShine\Support\Attributes\Icon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Enums\UserRoleEnum;
use App\Models\Role;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Text;

#[Icon('clipboard')]
/**
 * @extends ModelResource<Report>
 */
class ReportResource extends ModelResource
{
    protected string $model = Report::class;
    protected string $title = 'Отчёты';

    protected array $with = ['tutor', 'candidate', 'task'];

    protected bool $simplePaginate = true;
    protected bool $columnSelection = true;
    protected bool $createInModal = true;
    protected bool $detailInModal = true;
    protected bool $editInModal = true;
    protected bool $cursorPaginate = true;

    public function indexFields(): iterable
    {
        return [
            ID::make('id')->sortable(),
            BelongsTo::make('Создатель', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', [UserRoleEnum::TUTOR_WORKER,
                UserRoleEnum::ADMIN,
                UserRoleEnum::SUPER_ADMIN])->pluck('id')))
                ->sortable()
                ->searchable(),
            BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id')))
                ->sortable()
                ->searchable(),
            Select::make('Задание', 'task_id')
                ->options(
                    TaskStatus::with(['user', 'task'])
                        ->get()
                        ->mapWithKeys(function ($taskStatus) {
                            return [
                                $taskStatus->id => sprintf(
                                    '%s - %s',
                                    $taskStatus->user->name ?? 'Без кандидата',
                                    $taskStatus->task->title ?? 'Без задания'
                                )
                            ];
                        })
                        ->toArray()
                )
                ->required()
                ->searchable(),
            File::make('Отчёт', 'report')
                ->disk(moonshineConfig()->getDisk())
                ->dir('moonshine_reports')
                ->allowedExtensions(['pdf', 'doc', 'docx']),
            Text::make('Дата создания', 'created_at')
                ->sortable(),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                BelongsTo::make('Создатель', 'tutor', resource: UserResource::class)
                    ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::TUTOR_WORKER)->pluck('id')))
                    ->required()
                    ->searchable()
                    ->creatable(),
                BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                    ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id')))
                    ->required()
                    ->searchable()
                    ->creatable(),
                Select::make('Задание', 'task_id')
                    ->options(
                        TaskStatus::with(['user', 'task'])
                            ->get()
                            ->mapWithKeys(function ($taskStatus) {
                                return [
                                    $taskStatus->id => sprintf(
                                        '%s - %s',
                                        $taskStatus->user->name ?? 'Без кандидата',
                                        $taskStatus->task->title ?? 'Без задания'
                                    )
                                ];
                            })
                            ->toArray()
                    )
                    ->required()
                    ->searchable(),
                File::make('Отчёт', 'report')
                    ->disk(moonshineConfig()->getDisk())
                    ->dir('moonshine_reports')
                    ->allowedExtensions(['pdf', 'doc', 'docx'])
                    ->required($this->getItem()?->exists === false)
                    ->removable(),
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
            Select::make('Создатель', 'tutor_id')
            ->options(
                User::query()
                    ->whereIn('role_id', Role::whereIn('name', [
                        UserRoleEnum::TUTOR_WORKER,
                        UserRoleEnum::ADMIN,
                        UserRoleEnum::SUPER_ADMIN
                    ])->pluck('id'))
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->searchable()
            ->nullable(),

        Select::make('Кандидат', 'user_id')
            ->options(
                User::query()
                    ->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id'))
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->searchable()
            ->nullable(),

            DateRange::make('Дата создания', 'created_at')
                ->nullable(),
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'tutor_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $isTutor = Role::whereIn('name', [
                        UserRoleEnum::TUTOR_WORKER,
                        UserRoleEnum::ADMIN,
                        UserRoleEnum::SUPER_ADMIN
                    ])
                        ->whereHas('users', fn($q) => $q->where('id', $value))
                        ->exists();

                    if (!$isTutor) {
                        $fail('Выбранный пользователь не имеет право доступа к отчётам');
                    }
                }
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $isCandidate = Role::where('name', UserRoleEnum::USER)
                        ->whereHas('users', fn($q) => $q->where('id', $value))
                        ->exists();

                    if (!$isCandidate) {
                        $fail('Выбранный пользователь не является кандидатом');
                    }
                },
                'different:tutor_id'
            ],
            'report' => [
                $item->exists ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240' // 10MB
            ],
        ];
    }

    public function search(): array
    {
        return ['tutor.name', 'candidate.name'];
    }
}