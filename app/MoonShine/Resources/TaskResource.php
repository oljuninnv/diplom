<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\TaskLevelEnum;
use App\Models\Task;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\Support\Attributes\Icon;

#[Icon('clipboard-document-check')]
/**
 * @extends ModelResource<Task>
 */
class TaskResource extends ModelResource
{
    protected string $model = Task::class;
    protected string $title = 'Тестовые задания';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

	protected array $with = ['post'];

    public function indexFields(): iterable
    {
        return [
			ID::make('id'),
			Text::make('Название', 'title'),
			Text::make('Задание', 'task'),
			BelongsTo::make('Должность', 'post', resource: PostResource::class),
            Select::make('Уровень', 'level')->options(TaskLevelEnum::getAll())->required()->searchable(),
			Number::make('Время выполнения (в неделях)', 'deadline'),
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
        // TODO change it to your own rules
        return [
			'id' => ['int', 'nullable'],
			'title' => ['string', 'nullable'],
			'task' => ['string', 'nullable'],
			'post_id' => ['int', 'nullable'],
			'level' => ['string', 'nullable'],
			'deadline' => ['int', 'nullable'],
        ];
    }
}
