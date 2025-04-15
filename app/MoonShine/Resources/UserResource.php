<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\User;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\StackFields;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Date;
use MoonShine\Support\Attributes\Icon;
use App\Models\Role;
use MoonShine\UI\Components\Collapse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\UI\Fields\Email;
use Illuminate\Validation\Rule;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use App\Actions\GetId;
use MoonShine\Laravel\MoonShineRequest;

#[Icon('users')]
/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

	protected array $with = ['role', 'telegramUser'];

    protected string $title = 'Пользователи';

    protected string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;
    protected bool $simplePaginate = true;

    protected bool $cursorPaginate = true;

    public function indexButtons(): ListOf
    {

        return parent::indexButtons()->add(
            ActionButton::make('GetId')
                ->method('GetId')
        );

    }

    public function GetId(MoonShineRequest $request)
    {
        // \Log::info($request->get('resourceItem'));
        $id = (int)$request->get('resourceItem');
        $reportAction = new GetId();
        $reportAction->execute($id);
    }

    public function indexFields(): iterable
    {
        return [
			ID::make('id'),
			Text::make('Имя', 'name'),
			StackFields::make('Files')->fields([
                Image::make('avatar')
                    ->disk('public')
                    ->dir('moonshine_users'),
            ]),
			Text::make('Почта', 'email'),
			Text::make('телефон', 'phone'),
			BelongsTo::make('Роль', 'role', resource: MoonShineUserRoleResource::class),
			BelongsTo::make('Telegram-аккаунт', 'telegramUser', resource: TelegramUserResource::class),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        BelongsTo::make(
                            __('moonshine::ui.resource.role'),
                            'role',
                            formatted: static fn (Role $model) => $model->name,
                            resource: MoonShineUserRoleResource::class,
                        )
                            ->creatable()
                            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])),
                        
                        Flex::make([
                            Text::make(__('moonshine::ui.resource.name'), 'name')
                                ->required(),

                            Email::make(__('moonshine::ui.resource.email'), 'email')
                                ->required(),
                        ]),

                        Image::make(__('moonshine::ui.resource.avatar'), 'avatar')
                            ->disk(moonshineConfig()->getDisk())
                            ->dir('moonshine_users')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                        Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                            ->format("d.m.Y")
                            ->default(now()->toDateTimeString()),
                    ])->icon('user-circle'),

                    Tab::make(__('moonshine::ui.resource.password'), [
                        Collapse::make(__('moonshine::ui.resource.change_password'), [
                            Password::make(__('moonshine::ui.resource.password'), 'password')
                                ->customAttributes(['autocomplete' => 'new-password'])
                                ->eye(),

                            PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                                ->customAttributes(['autocomplete' => 'confirm-password'])
                                ->eye(),
                        ])->icon('lock-closed'),
                    ])->icon('lock-closed'),
                ]),
            ]),
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
            BelongsTo::make(
                'Роль',
                'Role',
                formatted: static fn (Role $model) => $model->name,
                resource: MoonShineUserRoleResource::class,
            )->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])),

            Email::make('E-mail', 'email'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }

    protected function rules($item): array
    {
        return [
            'name' => 'required',
            'role_id' => 'required',
            'email' => [
                'sometimes',
                'bail',
                'required',
                'email',
                Rule::unique('users')->ignoreModel($item),
            ],
            'password' => $item->exists
                ? 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat'
                : 'required|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
}
