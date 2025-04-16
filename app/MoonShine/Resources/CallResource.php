<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\CallEnum;
use App\Models\Call;
use MoonShine\UI\Fields\DateRange;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use App\Enums\UserRoleEnum;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Select;
use App\Models\User;

/**
 * @extends ModelResource<Call>
 */
class CallResource extends ModelResource
{
    protected string $model = Call::class;
    protected bool $simplePaginate = true;
    protected string $title = 'Созвоны';

    protected bool $columnSelection = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;


    protected array $with = ['candidate', 'tutor', 'hr_manager'];

    public function indexFields(): iterable
    {
        return [
            ID::make('id'),
            Select::make('Тип', 'type')
                ->required()
                ->searchable()
                ->options(CallEnum::getAll())
                ->sortable(),
            Text::make('Ссылка на видео-конференцию', 'meeting_link')->required(),
            Date::make('Дата', 'date')->sortable()->required(),
            Text::make('Время', 'time')->placeholder('HH:mm')->sortable()->required(),
            BelongsTo::make('Кандидат', 'candidate', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value)))
                ->required()
                ->sortable()
                ->searchable(),
            BelongsTo::make('Тьютор', 'tutor', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value)))
                ->required()
                ->sortable()
                ->searchable(),
            BelongsTo::make('HR-менеджер', 'hr_manager', resource: UserResource::class)
                ->valuesQuery(fn($q) => $q->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::ADMIN->value)))
                ->sortable()
                ->searchable()
                ->required(),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields()
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
            Select::make('Тип', 'type')
                ->options(CallEnum::getAll())
                ->nullable(),

            Select::make('Кандидат', 'candidate_id')
                ->options(
                    User::query()
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value))
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            Select::make('Тьютор', 'tutor_id')
                ->options(
                    User::query()
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::TUTOR_WORKER->value))
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            Select::make('HR-менеджер', 'hr_manager_id')
                ->options(
                    User::query()
                        ->whereHas('role', fn($q) => $q->whereIn('name', [UserRoleEnum::ADMIN->value,UserRoleEnum::SUPER_ADMIN->value]))
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->nullable(),

            DateRange::make('Дата', 'date')
                ->nullable(),
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'type' => [
                'required',
                'string',
                'in:' . implode(',', CallEnum::getAll())
            ],
            'meeting_link' => [
                'required',
                'string',
                'url',
                'max:255'
            ],
            'date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'time' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'candidate_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $isCandidate = User::where('id', $value)
                        ->whereHas('role', fn($q) => $q->where('name', UserRoleEnum::USER->value))
                        ->exists();
                    if (!$isCandidate) {
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
        ];
    }

    public function search(): array
    {
        return ['candidate.name', 'tutor.name', 'hr_manager.name'];
    }


}
