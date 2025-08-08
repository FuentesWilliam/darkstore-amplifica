<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\Response;

class ProductTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'title';
    public $sortDirection = 'desc';
    protected $queryString = ['search', 'sortField', 'sortDirection'];

    protected $listeners = ['render'];

    public function render()
    {
        $products = Product::where('title', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%")
                ->orWhere('price', 'like', "%{$this->search}%")
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(100);
        
        return view('livewire.product-table', compact('products'));

    }

    public function order($sort)
    {
        if ($this->sortField === $sort) {
            $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sortField = $sort;
            $this->sortDirection = 'asc';
        }
    }

    public function getSortIcon($column)
    {
        if ($this->sortField === $column) {
            return $this->sortDirection === 'asc'
                ? '<i class="fa fa-sort-alpha-asc ml-2 float-right" aria-hidden="true"></i>'
                : '<i class="fa fa-sort-alpha-desc ml-2 float-right" aria-hidden="true"></i>';
        }

        return '<i class="fa fa-sort ml-2 text-gray-400 float-right" aria-hidden="true"></i>';
    }

    public function export()
    {
        $fileName = 'products.csv';

        $products = Product::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');

            // Encabezado del CSV
            fputcsv($handle, ['ID', 'Title', 'SKU', 'Price', 'Image URL'], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->title,
                    $product->sku,
                    $product->price,
                    $product->image_url
                ], ';');
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
