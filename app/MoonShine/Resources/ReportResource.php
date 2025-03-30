<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Report;
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

    protected array $with = ['tutor', 'candidate'];

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
            BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::TUTOR_WORKER)->pluck('id')))
                ->sortable()
                ->searchable(),
            BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id')))
                ->sortable()
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
                BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                    ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::TUTOR_WORKER)->pluck('id')))
                    ->required()
                    ->searchable()
                    ->creatable(),
                BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                    ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id')))
                    ->required()
                    ->searchable()
                    ->creatable(),
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
            BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::TUTOR_WORKER)->pluck('id')))
                ->searchable()
                ->nullable(),

            BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                ->valuesQuery(fn(Builder $q) => $q->whereIn('role_id', Role::where('name', UserRoleEnum::USER)->pluck('id')))
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
                    $isTutor = Role::where('name', UserRoleEnum::TUTOR_WORKER)
                        ->whereHas('users', fn($q) => $q->where('id', $value))
                        ->exists();

                    if (!$isTutor) {
                        $fail('Выбранный пользователь не является тьютором');
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