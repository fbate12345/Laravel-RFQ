<?php

namespace App\Http\Controllers\Frontend;

use App\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest()->where('status', 2)->paginate(12);

        return view('frontend.home', compact('products'));
    }
}
