<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(Request $request) {
        $this->middleware('auth:api');
    }

    public function list(Request $request) {
        return Product::all();
    }
}
