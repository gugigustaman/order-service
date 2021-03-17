<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Apply authorization middleware to all the methods at initialization
     * @param Request $request instance of Http Request
     */
    public function __construct(Request $request) {
        $this->middleware('auth:api');
    }

    public function list(Request $request) {
        return Order::all();
    }
}
