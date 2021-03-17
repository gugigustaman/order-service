<?php

namespace App\Models;

use App\Exceptions\CustomException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id'
    ];

    protected $appends = ['total_price'];

    /**
     * Relationship definition of each order has many order details
     * @return HasMany has many relationship to OrderDetail
     */
    public function details() {
    	return $this->hasMany(OrderDetail::class);
    }

    /**
     * Accessor definition of total_price
     * @return float sum of each details total prices
     */
    public function getTotalPriceAttribute() {
        return $this->details()->sum('total_price');
    }

    /**
     * Check if this order has items in it
     * @return boolean true if yes, otherwise false
     */
    public function hasItems() {
        return $this->details()->count() > 0;
    }

    /**
     * Check if cart already has item with id of product_id
     * @param  int  $product_id ID of product
     * @return boolean             true if exists, otherwise false
     */
    public function getDetailByProduct(Product $product) {
    	return $this->details()->where('product_id', $product->id)->first();
    }

    /**
     * Add Item to cart. if product is not in the cart, create new detail with 0 qty
     * Then, increase the qty with $qty
     * @param  Product $product instance of Product that want to be added to cart
     * @param  integer  $qty     The qty of product that want to be added to cart
     */
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

    
    /**
     * Remove Item from cart. if product is not in the cart, throw exception 1501
     * Otherwise, decrease the qty or even delete the detail 
     * @param  Product $product instance of Product that want to be removed from cart
     * @param  integer  $qty     The qty of product that want to be removed from cart
     * @return mixed           void or exception (no such product in cart)
     */
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
     * Pay unpaid order (cart)
     * @param  String $payment_ref_num reference number of payment
     * @return mixed                  void or exception (not unpaid order or no items)
     */
    public function pay($payment_ref_num) {
        if ($this->status != 0) {
            throw new CustomException(1502, 404);
        }

        if (!$this->hasItems()) {
            throw new CustomException(1503, 400);
        }

        $this->payment_ref_num = $payment_ref_num;
        $this->ref_num = self::newRefNum();
        $this->paid_at = Carbon::now();
        $this->status = 1;

        $this->save();

        foreach ($this->details as $detail) {
            $detail->product->deductStock($detail->qty);
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

        $cart = $cart->firstOrCreate([
            'user_id' => $user_id
        ]);

        if ($with_detail) { 
            $cart = $cart->fresh(['details.product']);
        } else {
            $cart = $cart->fresh();
        }

    	return $cart;
    }

    public static function newRefNum() {
        return strtoupper('INV' . date('ymd') . Str::random(5));
    }
}