<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Sale;
use Carbon\Carbon;

class SalesReports extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.resources.sale-resource.pages.sales-reports';
    public ?string $status = null;
    public ?int $currency_id = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date'   => now()->toDateString(),
            'status'    => null,
            'currency_id' => null,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Filters')
                ->schema([
                    Forms\Components\DatePicker::make('from_date')
                        ->label('From')
                        ->reactive(),

                    Forms\Components\DatePicker::make('to_date')
                        ->label('To')
                        ->reactive(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'paid' => 'Paid',
                            'cancelled' => 'Cancelled',
                            'pending' => 'Pending',
                            'partially_paid' => "partially_paid",
                            'refunded' => 'refunded',

                        ])
                        ->reactive()
                        ->placeholder('All'),

                    Forms\Components\Select::make('currency_id')
                        ->label('Currency')
                        ->options(\App\Models\Currency::pluck('name', 'id'))
                        ->reactive()
                        ->placeholder('All'),
                ])
                ->columns(2),
        ];
    }
public static function getNavigationLabel(): string
{
    return __('filament::admin.sales_reports');
}

public static function getNavigationGroup(): ?string
{
    return __('filament::admin.reports');
}
    public function getSalesQuery()
    {
        $from = $this->from_date ? Carbon::parse($this->from_date)->startOfDay() : now()->startOfMonth();
        $to = $this->to_date ? Carbon::parse($this->to_date)->endOfDay() : now();

        $query = Sale::query()
            ->whereBetween('sold_at', [$from, $to]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->currency_id) {
            $query->where('currency_id', $this->currency_id);
        }

        return $query;
    }

}
