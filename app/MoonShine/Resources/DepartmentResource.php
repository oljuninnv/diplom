<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Department;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Attributes\Icon;

#[Icon('user-group')]
/**
 * @extends ModelResource<Department>
 */
class DepartmentResource extends ModelResource
{
    protected string $model = Department::class;

    protected string $title = 'Отделы';

    protected string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

    public function indexFields(): iterable
    {
        return [
			ID::make('id')->sortable(),
			Text::make('Название', 'name')->sortable(),
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
