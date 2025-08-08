<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Response;

class OrdersTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'customer';
    public $sortDirection = 'desc';
    protected $queryString = ['search', 'sortField', 'sortDirection'];

    protected $listeners = ['render'];

    public function render()
    {
        $orders = Order::where('customer', 'like', "%{$this->search}%")
                ->orWhere('financial_status', 'like', "%{$this->search}%")
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(100);
        return view('livewire.orders-table', compact('orders'));
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
        $fileName = 'orders.csv';

        $orders = Order::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Created At',
                'Customer Name',
                'Total Price',
                'Line Items'
            ], ';');

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->created_at,
                    $order->customer_data['name'] ?? '',
                    $order->total_price,
                    json_encode($order->line_items),
                ], ';');
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
