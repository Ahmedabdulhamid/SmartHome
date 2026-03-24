<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class SearchProduct extends Component
{
    public $item = '';

    public function render()
    {
        $products = [];

        if ($this->item !== '') {
            $search = '%' . $this->item . '%';

            $products = Product::query()
                ->where(function ($q) use ($search) {
                    $q->where('name->ar', 'LIKE', $search)
                      ->orWhere('name->en', 'LIKE', $search);
                })
                ->orWhereHas('variants', function ($q) use ($search) {
                    $q->where('name->ar', 'LIKE', $search)
                      ->orWhere('name->en', 'LIKE', $search);
                })
                ->orWhereHas('brand', function ($q) use ($search) {
                    $q->where('name->ar', 'LIKE', $search)
                      ->orWhere('name->en', 'LIKE', $search);
                })
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name->ar', 'LIKE', $search)
                      ->orWhere('name->en', 'LIKE', $search);
                })
                ->take(10)
                ->get();
        }

        return view('livewire.search-product', compact('products'));
    }
}
