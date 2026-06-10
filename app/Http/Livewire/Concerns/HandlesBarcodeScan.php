<?php

namespace App\Http\Livewire\Concerns;

use App\Models\Goods;

/**
 * Shared behaviour for transaction pages (receiving / dispatching /
 * stock opname) that build a `$goodsItems` list and accept scanned barcodes.
 *
 * The host component must expose a public array `$goodsItems`, where each item
 * is shaped ['goodsId' => ?, 'quantity' => ?].
 */
trait HandlesBarcodeScan
{
    /**
     * Called when a barcode is scanned. Finds the matching goods and either
     * increments the existing line or appends a new one.
     */
    public function handleScan($code)
    {
        $code = is_string($code) ? trim($code) : '';

        if ($code === '') {
            return;
        }

        $goods = Goods::findByScan($code);

        if (! $goods) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('No goods found for code') . ': ' . $code,
            ]);
            return;
        }

        // Already in the list? Increment its quantity.
        foreach ($this->goodsItems as $index => $item) {
            if (($item['goodsId'] ?? null) === $goods->id) {
                $this->goodsItems[$index]['quantity'] = (int) ($item['quantity'] ?? 0) + 1;
                $this->afterScan($goods);
                return;
            }
        }

        // Reuse the first empty line if one exists, otherwise append.
        foreach ($this->goodsItems as $index => $item) {
            if (empty($item['goodsId'])) {
                $this->goodsItems[$index]['goodsId'] = $goods->id;
                $this->goodsItems[$index]['quantity'] = (int) ($item['quantity'] ?? 0) ?: 1;
                $this->afterScan($goods);
                return;
            }
        }

        $this->goodsItems[] = [
            'goodsId' => $goods->id,
            'quantity' => 1,
        ];

        $this->afterScan($goods);
    }

    protected function afterScan(Goods $goods): void
    {
        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => __('Added') . ': ' . $goods->code_name,
        ]);
    }
}
