<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Vacancy;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\Support\Attributes\Icon;

#[Icon('paper-airplane')]
/**
 * @extends ModelResource<Vacancy>
 */
class VacancyResource extends ModelResource
{
    protected string $model = Vacancy::class;

	protected array $with = ['post'];

    protected string $title = 'Вакансии';

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
			BelongsTo::make('Должность', 'post', resource: PostResource::class),
			Text::make('Описание', 'description'),
			Number::make('Зарплата', 'salary'),
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
			'post_id' => ['int', 'required'],
			'description' => ['string', 'required'],
			'salary' => ['int', 'required'],
        ];
    }
}
