<?php

namespace App\Http\Livewire\GoodsCategory\Components;

use App\Models\Goods;
use App\Models\GoodsCategory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class GoodsCategoryTable extends DataTableComponent
{
    // protected $model = GoodsCategory::class;

    public function builder(): Builder
    {
        return GoodsCategory::select('*');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setColumnSelectStatus(false);
        $this->setConfigurableAreas([
            'toolbar-left-start' => [
                'livewire.livewire-datatable.add-action-button',
                [
                    'route' => route('goods-category.add')
                ],
            ],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make(__('Name'), 'name')
                ->sortable(),
            Column::make(__('Actions'), 'id')
                ->view('livewire.components.datatable-row-actions'),
        ];
    }

    public function actionDelete($id) {
        GoodsCategory::where('id', $id)->delete();
    }
}
