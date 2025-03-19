<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Post;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\Models\Department;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Support\Attributes\Icon;

#[Icon('computer-desktop')]
/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $column = 'name';

	protected array $with = ['department'];

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

    protected bool $columnSelection = true;

    public function indexFields(): iterable
    {
        return [
			ID::make('id')->sortable(),
            BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class)->sortable()->creatable(),
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

    protected function filters(): iterable
    {
        return [
            BelongsTo::make(
                'Отдел',
                'department',
                formatted: static fn(Department $model) => $model->name,
                resource: DepartmentResource::class,
            )->valuesQuery(static fn(Builder $q) => $q->select(['id', 'name'])),

            Text::make('Название', 'name'),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required',
            'department_id' => 'required',
        ];
    }   
}
