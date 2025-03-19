<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Department;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Text;

#[Icon('user-group')]
/**
 * @extends ModelResource<Department>
 */
class DepartmentsResource extends ModelResource
{
    protected string $model = Department::class;

    protected string $title = 'Отдел';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
        ];
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make('Название', 'name')
                    ->required(),
            ]),
        ];
    }

    /**
     * @return array{name: array|string}
     */
    protected function rules($item): array
    {
        return [
            'name' => ['required', 'min:2'],
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
