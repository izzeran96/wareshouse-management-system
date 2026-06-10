<?php

namespace App\Http\Livewire\Subscribe\Components;

use App\Models\SubscribePackage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PackageTable extends DataTableComponent
{
    protected $model = SubscribePackage::class;

    protected $listeners = ['deleteConfirmed'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setColumnSelectStatus(false);
        $this->setConfigurableAreas([
            'toolbar-left-start' => [
                'livewire.livewire-datatable.add-action-button',
                ['route' => route('subscribe-package.add')],
            ],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make(__('Title'), 'title')
                ->searchable()
                ->sortable(),
            Column::make(__('Price (RM)'), 'price')
                ->format(fn ($value) => number_format($value, 2))
                ->sortable(),
            Column::make(__('Duration (days)'), 'duration_days')
                ->sortable(),
            Column::make(__('Active'), 'is_active')
                ->format(fn ($value) => $value ? __('Yes') : __('No'))
                ->sortable(),
            Column::make(__('Created at'), 'created_at')
                ->format(fn ($value) => format_date($value))
                ->sortable(),
            Column::make(__('Actions'), 'id')
                ->view('livewire.subscribe.components.package-action-menu'),
        ];
    }

    public function actionDelete($id)
    {
        $this->emitTo(
            'components.delete-confirm-modal',
            'deleteConfirmation',
            'subscribe.components.package-table',
            $id
        );
    }

    public function deleteConfirmed($id)
    {
        if ($id) {
            SubscribePackage::where('id', $id)->delete();
        }
    }
}
