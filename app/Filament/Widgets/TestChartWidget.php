<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TestChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?string $subheading = 'This is a chart widget.';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {

        $data = Trend::model(User::class)
        ->between(
            start: now()->subYear(),
            end: now(),
        )
            ->perMonth()
            ->count();

        return [
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(0, 0, 255, 0.1)',
                    'borderColor' => 'rgba(0, 0, 255, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Posts',
                    'data' => [10, 9, 8, 17, 36, 25, 34, 33, 22, 41],
                    'backgroundColor' => 'rgba(0, 255, 0, 0.1)',
                    'borderColor' => 'rgba(0, 255, 0, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
