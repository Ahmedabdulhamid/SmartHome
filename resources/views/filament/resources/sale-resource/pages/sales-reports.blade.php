<x-filament-panels::page>

    {{-- Filters --}}
    {{ $this->form }}

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <x-filament::card>
            <div class="text-sm text-gray-500">Total Revenue</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($this->getSalesQuery()->sum('total_amount'), 2) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500">Total Sales</div>
            <div class="text-2xl font-bold mt-2">
                {{ $this->getSalesQuery()->count() }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500">Average Sale</div>
            <div class="text-2xl font-bold mt-2">
                {{
                    number_format(
                        $this->getSalesQuery()->avg('total_amount') ?? 0,
                        2
                    )
                }}
            </div>
        </x-filament::card>
    </div>

    {{-- Chart --}}
    @php
        $dailySales = $this->getSalesQuery()
            ->selectRaw('DATE(sold_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $labels = $dailySales->pluck('date');
        $data = $dailySales->pluck('total');
    @endphp

    <div class="mt-6">
        <canvas id="dailySalesChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Daily Sales',
                    data: @json($data),
                    backgroundColor: 'rgba(34,197,94,0.2)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

</x-filament-panels::page>
