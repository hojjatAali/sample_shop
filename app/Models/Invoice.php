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

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
