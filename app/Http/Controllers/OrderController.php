<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(Request $request) {
        $this->middleware('auth:api');
    }

    /**
     * Get List of orders
     * Route: [GET] /order
     * @param  Request $request Instance of Http Request
     * @return Response           List of orders
     */
    public function list(Request $request) {
        return response()->json(
        	Order::where('status', 1)
        	->orderBy('id', 'DESC')
        	->get()
        );
    }
}
