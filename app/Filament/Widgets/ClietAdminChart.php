<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ClietAdminChart extends ChartWidget
{
    protected static ?string $heading = 'Clients';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                'label' => 'Amount of Users',
                'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
