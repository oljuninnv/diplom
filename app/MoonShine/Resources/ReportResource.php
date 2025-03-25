<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Report;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Attributes\Icon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Enums\UserRoleEnum;
use App\Models\Role;

#[Icon('clipboard')]
/**
 * @extends ModelResource<Report>
 */
class ReportResource extends ModelResource
{
    protected string $model = Report::class;

	protected array $with = ['user'];

    protected string $title = 'Отчёт';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;


    public function indexFields(): iterable
    {
        return [
			ID::make('id'),
			BelongsTo::make('Тьютор', 'user', resource: UserResource::class)
            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])->whereIn('role_id',Role::where('name',UserRoleEnum::TUTOR_WORKER)->pluck('id'))),
			BelongsTo::make('Кандидат', 'user', resource: UserResource::class)
            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])->whereIn('role_id',Role::where('name',UserRoleEnum::USER)->pluck('id'))),
			Text::make('Отчёт', 'report'),
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
        ];
    }

    public function rules(mixed $item): array
    {
        return [
			'id' => ['int', 'nullable'],
			'tutor_id' => ['int', 'nullable'],
			'user_id' => ['int', 'nullable'],
			'report' => ['string', 'nullable'],
        ];
    }
}
