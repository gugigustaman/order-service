<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;

    /**
     * Apply authorization middleware to all the methods at initialization
     * @param Request $request instance of Http Request
     */
    public function __construct(Request $request) {
        $this->middleware('auth:api');

        // Storing cart as property
        if (auth()->check()) {
            $this->cart = Order::cart(auth()->user()->id);
        }
    }

    /**
     * Get Cart and its details
     * Route: [GET] /api/cart
     * @param  Request $request instance of Http Request
     * @return json of cart object and its details
     */
    public function detail(Request $request) {
        return response()->json(Order::cart(auth()->user()->id, true));
    }

    /**
     * Add item to cart
     * Route: [POST] /api/cart/add_item
     * @param Request $request instance of Http Request
     */
    public function addItem(Request $request) {
        if (!$request->product_id || !$request->qty) {
            return $this->sendInvalidRequest();
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->sendResourcesNotFound('Product not found');
        }

        try {
            $this->cart->addItem($product, $request->qty);   
        } catch (\Exception $e) {
            return $this->sendError();
        }

        return $this->sendResponse('Successfully added the product to cart');
    }

    /**
     * Remove item from cart
     * Route: [POST] /api/cart/remove_item
     * @param Request $request instance of Http Request
     */
    public function removeItem(Request $request) {
        if (!$request->product_id || !$request->qty) {
            return $this->sendInvalidRequest();
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->sendResourcesNotFound('Product not found');
        }

        try {
            $this->cart->removeItem($product, $request->qty);   
        } catch (CustomException $e) {
            return $this->sendResponse($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->sendError();
        }

        return $this->sendResponse('Successfully removed the product from cart');
    }

    /**
     * Pay unpaid order (cart) handler
     * Route: [POST] /api/cart/pay
     * @param  Request $request instance of Http Request
     * @return response           generated response
     */
    public function pay(Request $request) {
        if (!$request->payment_ref_num) {
            return $this->sendInvalidRequest();
        }

        if ($this->cart->status != 0) {
            return $this->sendResourcesNotFound('You have no unpaid order.');
        }

        if (!$this->cart->hasItems()) {
            return $this->sendInvalidRequest('You have no item in your cart.');
        }

        $this->cart->status = 2;
        $this->cart->save();
        
        dispatch(new \App\Jobs\PayOrderJob($this->cart, $request->payment_ref_num))
            ->onQueue('order');

        return $this->sendResponse('Your request is being processed.', 202);
    }
}
