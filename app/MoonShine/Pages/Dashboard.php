<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\UI\Components\Layout\Grid;
use App\Models\Department;
use App\Models\Application;
use App\Enums\ApplicationStatusEnum;
#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            Grid::make([
                DonutChartMetric::make('Количество сотрудников')
                    ->values(
                        Department::query()
                            ->withCount('workers')
                            ->get()
                            ->mapWithKeys(function ($department) {
                                return [$department->name => $department->workers_count];
                            })
                            ->toArray()
                    ),
                LineChartMetric::make('Количество поданых заявок за год')
                    ->line([
                        'Заявки' => Application::query()
                            ->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
                            ->whereYear('created_at', now()->year)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->pluck('count', 'month')
                            ->toArray()
                    ], '#42aaff')
                    ->line([
                        'Принятые заявки' => Application::query()->where('status', ApplicationStatusEnum::APPROVED->value)
                            ->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
                            ->whereYear('created_at', now()->year)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->pluck('count', 'month')
                            ->toArray()
                    ], '#008000')
                    ->line([
                        'Отклонённые заявки' => Application::query()->where('status', ApplicationStatusEnum::REJECTED->value)
                            ->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
                            ->whereYear('created_at', now()->year)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->pluck('count', 'month')
                            ->toArray()
                    ], '#EC4176')
                    ->columnSpan(12),
            ])
        ];

    }
}
