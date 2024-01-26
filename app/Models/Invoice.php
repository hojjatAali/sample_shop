<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Orchid\Attachment\Attachable;
use Orchid\Platform\Concerns\Sortable;
use Orchid\Screen\AsSource;

class Invoice extends Model
{
    use HasFactory, Attachable, Sortable, AsSource,Searchable;

    protected $fillable=[
        "user_id",
        "total_price",
        "seller_name",
        "discount",
        "price_after_discount",
        "shipping_cost",
        "customer_name",
        "driver_name",
        "description",
        "address",
        "recipient",
        "status",
        "delivered_at",
        "canceled_at",
        "payment_date",
        "pay_deadline",
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price_of_product');
    }
}
