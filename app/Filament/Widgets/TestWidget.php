<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TestWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Total number of users')
                ->icon('heroicon-o-users')
                ->chart([1, 2, 23, 4, 35, 6,37, 8, 9, 10])
                ->color('success')
                ->chartColor('blue-500'),
            Stat::make('Posts', Post::count())
                ->description('Total number of posts')
                ->icon('heroicon-o-rectangle-stack')
                ->chart([10, 9, 8, 7, 6, 5, 4, 3, 2, 1])
                ->chartColor('green-500'),
        ];
    }
}
