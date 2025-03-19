<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\LevelOfExperienceEnum;
use App\Models\Worker;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Date;
use App\MoonShine\Resources\CityResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Select;
use MoonShine\Support\Attributes\Icon;

#[Icon('briefcase')]
/**
 * @extends ModelResource<Worker>
 */
class WorkerResource extends ModelResource
{
    protected string $title = 'Работники';
    protected string $model = Worker::class;

    protected array $with = ['user', 'department', 'post'];

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
            BelongsTo::make('Пользователь', 'user', resource: MoonShineUserResource::class)->sortable()->creatable(),
            BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class)->sortable()->creatable(),
            BelongsTo::make('Должность', 'post', resource: PostResource::class)->sortable()->creatable(),
            Date::make('Дата устройства на должность', 'hire_date')->sortable(),
            Select::make('Уровень', 'level_of_experience')->options(LevelOfExperienceEnum::getAll())->required()->searchable(),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                BelongsTo::make('Отдел', 'department', resource: DepartmentResource::class)->sortable()->creatable(),
                BelongsTo::make('Должность', 'post', resource: PostResource::class)->sortable()->creatable(),
                Date::make('Дата устройства на должность', 'hire_date')->sortable(),
                Select::make('Уровень', 'level_of_experience')->options(LevelOfExperienceEnum::getAll())->required()->searchable(),
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
            BelongsTo::make('Отделы', 'department', resource: DepartmentResource::class)->searchable(),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'user.name',
            'department.name',
            'post.name',
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'user_id' => ['int', 'required'],
            'department_id' => ['int', 'required'],
            'post_id' => ['int', 'required'],
            'hire_date' => ['string', 'required'],
            'level_of_experience' => ['string', 'required'],
        ];
    }
}
