<?php

namespace App\Http\Livewire\Subscribe\Pages;

use App\Models\SubscribePackage;
use Livewire\Component;

class EditPackagePage extends Component
{
    public string $title = '';
    public string|null $description = null;
    public $price = 0;
    public $duration_days = 30;
    public $is_active = true;

    public $package;
    public $packageId;

    protected $rules = [
        'title' => 'required|max:120',
        'description' => 'nullable|max:1000',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function mount($id)
    {
        $this->packageId = $id;
        $this->loadPackage();
    }

    public function loadPackage()
    {
        $this->package = SubscribePackage::find($this->packageId);

        if (! $this->package) {
            return redirect()->to(route('subscribe-package.index'));
        }

        $this->title = $this->package->title;
        $this->description = $this->package->description;
        $this->price = $this->package->price;
        $this->duration_days = $this->package->duration_days;
        $this->is_active = $this->package->is_active;
    }

    public function submit()
    {
        $this->validate();

        $this->package->update([
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'is_active' => $this->is_active,
        ]);

        return redirect()->to(route('subscribe-package.index'));
    }

    public function render()
    {
        return view('livewire.subscribe.pages.edit-package-page');
    }
}
