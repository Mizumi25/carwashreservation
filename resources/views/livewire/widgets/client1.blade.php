<div class='inset-0 h-full w-full flex flex-col justify-center items-center'>
    @if($pieChartModel)
        <br /> 
        <livewire:livewire-pie-chart
            key="{{ $pieChartModel->reactiveKey() }}"
            :pie-chart-model="$pieChartModel" />
    @else
        <div class="flex flex-col items-center">
            <i class="fas fa-times text-slate-300 text-4xl"></i> 
            <span class="mt-2 text-slate-400">No Reservations</span> 
        </div>
    @endif
</div>