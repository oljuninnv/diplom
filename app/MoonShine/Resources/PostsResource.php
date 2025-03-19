<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Department;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\Post;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\ListOf;
use MoonShine\Support\Enums\Color;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;

#[Icon('computer-desktop')]
/**
 * @extends ModelResource<Post>
 */
class PostsResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Должность';

    protected array $with = ['department'];

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

    protected bool $columnSelection = true;

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
            BelongsTo::make(
                'Отделы',
                'department',
                formatted: static fn(Department $model) => $model->name,
                resource: DepartmentsResource::class,
            )->badge(Color::PURPLE),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                BelongsTo::make(
                    'Отделы',
                    'department',
                    formatted: static fn(Department $model) => $model->name,
                    resource: DepartmentsResource::class
                )
                    ->creatable()
                    ->valuesQuery(
                        static fn(Builder $q) => $q->select(['id', 'name'])
                    ),
                Text::make('Название', 'name')->required(),
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @param Post $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required',
            'department_id' => 'required',
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make(
                __('Отдел'),
                'department',
                formatted: static fn(Department $model) => $model->name,
                resource: DepartmentsResource::class,
            )->valuesQuery(static fn(Builder $q) => $q->select(['id', 'name'])),

            Text::make('Название', 'name'),
        ];
    }
}
