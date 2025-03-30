<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\DepartmentResource;
use App\MoonShine\Resources\PostResource;
use App\MoonShine\Resources\WorkerResource;
use App\MoonShine\Resources\TelegramUserResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\MessageResource;
use App\MoonShine\Resources\VacancyResource;
use App\MoonShine\Resources\ReportResource;
use App\MoonShine\Resources\TaskResource;
use App\MoonShine\Resources\ApplicationResource;
use App\MoonShine\Resources\TaskStatusResource;
use App\MoonShine\Resources\CallResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
        $config->authEnable();

        $config->authPipelines([
            \App\MoonShine\AuthPipelines\CheckAdmin::class,
        ]);

        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                DepartmentResource::class,
                PostResource::class,
                WorkerResource::class,
                TelegramUserResource::class,
                UserResource::class,
                MessageResource::class,
                VacancyResource::class,
                ReportResource::class,
                TaskResource::class,
                ApplicationResource::class,
                TaskStatusResource::class,
                CallResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
