<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Orchid\Attachment\Attachable;
use Orchid\Platform\Concerns\Sortable;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory,AsSource,Sortable,Attachable,Searchable;

    protected $fillable=[
      "product_name",
        "barcode",
        "expire_date",
        "produce_date",
        "max_in_card",
        "purchase_price",
        "selling_price",
        "customer_price",
        "quantity_in_box",
        "is_active",
        "package_type",

    ];

    public function getDisplayNameAttribute()
    {
        return $this->barcode .'_ '.$this->product_name;
    }
}
