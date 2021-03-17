<?php

namespace App\Models;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
     * Relationship definition of each order has many order details
     * @return HasMany has many relationship to OrderDetail
     */
    public function details() {
    	return $this->hasMany(OrderDetail::class);
    }

    /**
     * Check if cart already has item with id of product_id
     * @param  int  $product_id ID of product
     * @return boolean             true if exists, otherwise false
     */
    public function getDetailByProduct(Product $product) {
    	return $this->details()->where('product_id', $product->id)->first();
    }

    public function addItem(Product $product, $qty) {
    	$detail = $this->getDetailByProduct($product);

    	if (!$detail) {
    		$detail = new OrderDetail();
    		$detail->order_id = $this->id;
    		$detail->product_id = $product->id;
    		$detail->qty = 0;
            $detail->total_price = 0;
    		$detail->save();
    	}

    	$detail->qty += $qty;

    	$detail->save();    	
    }

    
    public function removeItem(Product $product, $qty) {
        $detail = $this->getDetailByProduct($product);

        if (!$detail) {
            throw new CustomException(1501, 400);
        }

        if ($qty >= $detail->qty) {
            $detail->delete();
        } else {
            $detail->qty -= $qty;
            $detail->save();
        }
    }

    /**
     * Get current cart (order with status 0) or create a new one
     * @param  int $user_id ID of user
     * @return Order          instance of Order with status 0
     */
    public static function cart($user_id, $with_detail = false) {
    	$cart = self::where('user_id', $user_id)
    	    ->where('status', 0)
    	    ->orderBy('id', 'desc');

        if ($with_detail) {
            $cart->with('details');
        }

        $cart = $cart->firstOrCreate([
            'user_id' => $user_id
        ]);

    	return $cart;
    }
}