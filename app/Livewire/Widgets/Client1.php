<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\Reservation;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Support\Facades\Auth;

class Client1 extends Component
{
    public $statuses = ['pending', 'done', 'ongoing', 'approve', 'completed', 'in_progress'];

    // Define colors for each status
    public $colors = [
        'pending' => '#f6ad55',
        'done' => '#90cdf4',
        'ongoing' => '#66DA26',
        'approve' => '#fc8181',
        'completed' => '#3ee66b',
        'in_progress' => '#d41b2c',
    ];

    public $firstRun = true;
    public $showDataLabels = true;

    public function render()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->whereIn('status', $this->statuses)
            ->get();

        if ($reservations->isEmpty()) {
            return view('livewire.widgets.client1', [
                'pieChartModel' => null, // No chart model if there are no reservations
            ]);
        }

        $pieChartModel = $reservations->groupBy('status')
            ->reduce(function ($pieChartModel, $data) {
                $status = $data->first()->status; 
                $count = $data->count(); 

                return $pieChartModel->addSlice($status, $count, $this->colors[$status]);
            }, LivewireCharts::pieChartModel()
                ->setTitle('Reservations by Status') 
                ->setAnimated($this->firstRun)
                ->setType('donut')
                ->withOnSliceClickEvent('onSliceClick')
                ->legendPositionBottom()
                ->legendHorizontallyAlignedCenter()
                ->setDataLabelsEnabled($this->showDataLabels)
                ->setColors(array_values($this->colors))
            );

        $this->firstRun = false;

        return view('livewire.widgets.client1', [
            'pieChartModel' => $pieChartModel,
        ]);
    }
}