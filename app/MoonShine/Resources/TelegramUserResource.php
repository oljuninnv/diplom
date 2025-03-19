<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\TelegramUser;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\Laravel\Enums\Action;

#[Icon('user')]
/**
 * @extends ModelResource<TelegramUser>
 */
class TelegramUserResource extends ModelResource
{
    protected string $model = TelegramUser::class;

    protected string $column = 'username';

    protected string $title = 'Telegramm-аккаунты';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::MASS_DELETE)
            ->except(Action::DELETE)
            ->except(Action::UPDATE)
            ->except(Action::CREATE);
    }

    public function indexFields(): iterable
    {
        return [
			ID::make('id'),
			Number::make('telegram_id', 'telegram_id'),
			Text::make('Имя', 'first_name'),
			Text::make('Фамилия', 'last_name'),
			Text::make('Username', 'username'),
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields()
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'username',
        ];
    }
}
