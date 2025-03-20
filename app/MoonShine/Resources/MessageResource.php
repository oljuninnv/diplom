<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Message;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Attributes\Icon;

#[Icon('pencil-square')]
/**
 * @extends ModelResource<Message>
 */
class MessageResource extends ModelResource
{
    protected string $model = Message::class;

	protected array $with = ['sender_user_id', 'receiver_id_user'];

    public function indexFields(): iterable
    {
        return [
			ID::make('id'),
			BelongsTo::make('sender_id', 'sender_user_id', resource: UserResource::class),
			BelongsTo::make('receiver_id', 'receiver_id_user', resource: UserResource::class),
			Text::make('message', 'message'),
			Text::make('document', 'document'),
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
			'sender_id' => ['int', 'nullable'],
			'receiver_id' => ['int', 'nullable'],
			'message' => ['string', 'nullable'],
			'document' => ['string', 'nullable'],
        ];
    }
}
