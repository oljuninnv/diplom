<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\TaskLevelEnum;
use App\Models\Task;
use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\File;
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
            ID::make('id')->sortable(),
            Text::make('Название', 'title')->sortable(),
            File::make('Задание', 'task')
                ->disk(moonshineConfig()->getDisk())
                ->dir('moonshine_tasks')
                ->allowedExtensions(['pdf', 'doc', 'docx']),
            BelongsTo::make('Должность', 'post', resource: PostResource::class)
                ->sortable()
                ->searchable(),
            Select::make('Уровень', 'level')
                ->options(TaskLevelEnum::getAll())
                ->required()
                ->searchable()
                ->sortable(),
            Number::make('Время выполнения (в неделях)', 'deadline')
                ->sortable(),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                Text::make('Название', 'title')
                    ->required(),
                File::make('Задание', 'task')
                    ->disk(moonshineConfig()->getDisk())
                    ->dir('moonshine_tasks')
                    ->allowedExtensions(['pdf', 'doc', 'docx'])
                    ->required($this->getItem()?->exists === false),
                BelongsTo::make('Должность', 'post', resource: PostResource::class)
                    ->required()
                    ->searchable(),
                Select::make('Уровень', 'level')
                    ->options(TaskLevelEnum::getAll())
                    ->required()
                    ->searchable(),
                Number::make('Время выполнения (в неделях)', 'deadline')
                    ->min(1)
                    ->required(),
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
            Text::make('Название', 'title')
                ->placeholder('Поиск по названию'),

            Select::make('Должность', 'post_id')
                ->options(
                    Post::query()
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->nullable()
                ->searchable(),

            Select::make('Уровень', 'level')
                ->options(TaskLevelEnum::getAll())
                ->nullable(),

        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:100'],
            'task' => [
                $item->exists ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240'
            ],
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'level' => ['required', 'string', 'in:' . implode(',', TaskLevelEnum::getAll())],
            'deadline' => ['required', 'integer', 'min:1', 'max:52'],
        ];
    }

    public function search(): array
    {
        return ['title', 'post.name'];
    }
}