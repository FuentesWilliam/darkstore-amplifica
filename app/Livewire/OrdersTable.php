<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrdersTable extends Component
{
    public function render()
    {
        $orders = Order::all();
        return view('livewire.orders-table', compact('orders'));
    }
}
