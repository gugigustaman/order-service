<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(Request $request) {
        $this->middleware('auth:api');
    }

    /**
     * Get List of products
     * Route: [GET] /product
     * @param  Request $request Instance of Http Request
     * @return Response           List of products
     */
    public function list(Request $request) {
        return response()->json(Product::all());
    }
}
