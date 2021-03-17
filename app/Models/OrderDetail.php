<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        // Calculate total_price before saving model
        static::saving(function ($model) {
            $model->total_price = $model->qty * $model->product->price;
        });
    }

    /**
     * Relationship definition of this order detail belongs to product
     * @return BelongsTo belongs to relationship to Product
     */
    public function product() {
    	return $this->belongsTo(Product::class);
    }
}