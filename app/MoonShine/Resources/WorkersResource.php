<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Enums\Color;
use App\Models\Worker;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Attributes\Icon;
use Illuminate\Http\Request;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Select;

#[Icon('briefcase')]
/**
 * @extends ModelResource<Worker>
 */
class WorkersResource extends ModelResource
{
    protected string $model = Worker::class;

    protected string $title = 'Worker';

    protected array $with = ['user', 'post', 'department'];

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make(
                'Пользователь',
                'user',
                formatted: static fn(User $model) => $model->name,
                resource: MoonShineUserResource::class,
            ),
            BelongsTo::make(
                'Должность',
                'post',
                formatted: static fn(Post $model) => $model->name,
                resource: PostResource::class,
            )->badge(Color::INFO),
            BelongsTo::make(
                'Department',
                'department',
                formatted: static fn(Department $model) => $model->name,
                resource: DepartmentResource::class,
            ),
            Date::make('Дата приёма на должность', 'hire_date')
                ->format("d.m.Y")
                ->sortable(),
            Text::make('Уровень', 'level_of_experience'),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
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
                    'Department',
                    'department',
                    formatted: static fn(Department $model) => $model->name,
                    resource: DepartmentResource::class,
                )
                    ->creatable(),
                BelongsTo::make(
                    'Должность',
                    'post',
                    formatted: static fn(Post $model) => $model->name,
                    resource: PostResource::class,
                )
                    // ->associatedWith('department_id')
                    ->creatable()
            ])
        ];
    }

    /**
     * @param Worker $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }

    public function filters(): array
    {
        return [
            BelongsTo::make(
                'Должность',
                'Post',
                formatted: static fn(Post $model) => "$model->name - отдел: {$model->department->name}",
                resource: PostResource::class,
            )
                ->searchable(),
        ];
    }
}
