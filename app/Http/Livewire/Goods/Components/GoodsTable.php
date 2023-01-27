<?php

namespace App\Http\Livewire\Goods\Components;

use App\Models\Goods;
use App\Services\PrintService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Termwind\Components\Raw;

class GoodsTable extends DataTableComponent
{
    protected $model = Goods::class;

    public function configure(): void
    {
        $configurationAreas = [
            'toolbar-right-start' => [
                'livewire.livewire-datatable.export-pdf-action-button',
            ],
        ];
        $this->setPrimaryKey('id');
        $this->setColumnSelectStatus(false);

        if (Auth::user()->hasPermissionTo('goods.create')) {
            $configurationAreas['toolbar-left-start'] = [
                'livewire.livewire-datatable.add-action-button',
                [
                    'route' => route('goods.add')
                ],
            ];
        }
        $this->setConfigurableAreas($configurationAreas);
    }

    public function builder(): Builder
    {
        return Goods::query()
            ->select(
                DB::raw('(stock * price) as total_price')
            );
    }

    public function showBulkActionsDropdown(): bool
    {
        return false;
    }

    public function columns(): array
    {
        return [
            Column::make(__('Code'), 'code')
                ->sortable(),
            Column::make(__('Name'), 'name')
                ->sortable(),
            Column::make(__('Stock'), 'stock')
                ->format(fn($value, $row, $column) => number_format($value))
                ->sortable(),
            Column::make(__('Stock Limit'), 'minimum_stock')
                ->format(fn($value, $row, $column) => number_format($value))
                ->sortable(),
            Column::make(__('Unit'), 'unit.symbol')
                ->sortable(),
            Column::make(__('Price'), 'price')
                ->sortable()
                ->format(fn($value, $row, $column) => number_format($value)),
            Column::make(__('Total Price'), 'id')
                ->format(
                    fn($value, $row, $column) => number_format($row->total_price)
                )
                ->sortable(
                    fn($builder, $direction) => $builder->orderBy('total_price', $direction)
                ),
            Column::make(__('Actions'), 'id')
                ->view('livewire.goods.components.goods-action-menu'),
        ];
    }

    public function bulkActions(): array {
        return [
            'exportPDF' => __('Export PDF'),
        ];
    }

    public function exportPDF() {
        $goods = $this->getRows()->getCollection();
        $pdfContent = PrintService::printGoodsList($goods)->output();
        $filename = __('goods-list') . '-' . date("Ymd") . '.pdf';

        return response()->streamDownload(
            fn () => print($pdfContent),
            $filename
        );
    }
}
