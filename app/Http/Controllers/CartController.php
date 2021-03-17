<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Apply authorization middleware to all the methods at initialization
     * @param Request $request instance of Http Request
     */
    public function __construct(Request $request) {
        $this->middleware('auth:api');
    }

    /**
     * Get Cart and its details
     * Route: [GET] /api/cart
     * @param  Request $request instance of Http Request
     * @return json of cart object and its details
     */
    public function detail(Request $request) {
        return Order::cart(auth()->user()->id, true);
    }

    /**
     * Add item to cart
     * Route: [POST] /api/cart/add_item
     * @param Request $request instance of Http Request
     */
    public function addItem(Request $request) {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response([
                'message' => 'Product not found'
            ], 404);
        }

        $cart = Order::cart(auth()->user()->id);

        try {
            $cart->addItem($product, $request->qty);   
        } catch (\Exception $e) {
            return response([
                'message' => 'There\'s something wrong. Please try again later.'
            ], 500);
        }

        return response([
            'message' => 'Successfully added the product to cart'
        ]);
    }

    /**
     * Remove item from cart
     * Route: [POST] /api/cart/remove_item
     * @param Request $request instance of Http Request
     */
    public function removeItem(Request $request) {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response([
                'message' => 'Product not found'
            ], 404);
        }

        $cart = Order::cart(auth()->user()->id);

        try {
            $cart->removeItem($product, $request->qty);   
        } catch (CustomException $e) {
            return response([
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response([
                'message' => 'There\'s something wrong. Please try again later.'
            ], 500);
        }

        return response([
            'message' => 'Successfully removed the product from cart'
        ]);
    }
}
