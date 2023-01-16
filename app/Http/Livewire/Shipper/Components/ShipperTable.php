<?php

namespace App\Http\Livewire\Shipper\Components;

use App\Models\Goods;
use App\Models\Shipper;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class ShipperTable extends DataTableComponent
{
    protected $model = Shipper::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setColumnSelectStatus(false);
        $this->setConfigurableAreas([
            'toolbar-left-start' => [
                'livewire.livewire-datatable.add-action-button', 
                [
                    'route' => route('shipper.add')
                ],
            ],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Name')
                ->sortable(),
            Column::make('Contact Phone', 'cp_phone')
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->sortable(),
            Column::make('Actions', 'id')
                ->view('livewire.shipper.components.shipper-action-menu'),
        ];
    }
}