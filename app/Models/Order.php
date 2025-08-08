<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'created_at' => 'datetime',
        'customer_data' => 'array',
        'line_items' => 'array',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'total_price' => 'decimal:2',
    ];

    protected $fillable = [
        'id',
        'order_number',
        'created_at',
        'financial_status',
        'fulfillment_status',
        'total_price',
        'customer_data',
        'line_items',
        'shipping_address',
        'billing_address',
        'notes',
    ];

    /**
     * Accesor para obtener el nombre del cliente
     */
    public function getCustomerNameAttribute()
    {
        return $this->customer_data['first_name'] . ' ' . $this->customer_data['last_name'] ?? '';
    }

    /**
     * Accesor para obtener el email del cliente
     */
    public function getCustomerEmailAttribute()
    {
        return $this->customer_data['email'] ?? '';
    }

    /**
     * Scope para filtrar por estado financiero
     */
    public function scopeFinancialStatus($query, $status)
    {
        return $query->where('financial_status', $status);
    }

    /**
     * Scope para filtrar por estado de cumplimiento
     */
    public function scopeFulfillmentStatus($query, $status)
    {
        return $query->where('fulfillment_status', $status);
    }

    /**
     * Obtiene la cantidad total de items en el pedido
     */
    public function getTotalItemsAttribute()
    {
        return collect($this->line_items)->sum('quantity');
    }
}