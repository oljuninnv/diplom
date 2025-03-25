<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\MenuManager\MenuItem;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use App\MoonShine\Resources\MoonShineUserResource;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When};
use App\MoonShine\Resources\DepartmentResource;
use App\MoonShine\Resources\PostResource;
use App\MoonShine\Resources\WorkerResource;
use App\MoonShine\Resources\TelegramUserResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\MessageResource;
use App\MoonShine\Resources\VacancyResource;
use App\MoonShine\Resources\ReportResource;
use App\MoonShine\Resources\TaskResource;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuGroup::make('Пользователи',[
                MenuItem::make('Пользователи', UserResource::class),
                MenuItem::make('Telegram-аккаунты', TelegramUserResource::class),
            ],'user-group'),
            MenuGroup::make('Организация',[
                MenuItem::make('Отделы', DepartmentResource::class),
                MenuItem::make('Должности', PostResource::class),
                MenuItem::make('Работники', WorkerResource::class),
            ],'building-office'),
            MenuGroup::make('Тестовые задания и заявки',[
                MenuItem::make('Тестовые задания', TaskResource::class),
                MenuItem::make('Заявки', MoonShineUserResource::class),
                MenuItem::make('Статус выполнения', MoonShineUserResource::class),
            ],'document-magnifying-glass'),
            MenuGroup::make('Отчёты и вакансии',[
                MenuItem::make('Отчёты', ReportResource::class),
                MenuItem::make('Вакансии', VacancyResource::class),
            ],'document'),
            MenuItem::make('Вернуться на сайт', static fn () => route('home'),'home'),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
