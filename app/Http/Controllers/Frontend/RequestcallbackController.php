<?php

namespace App\Http\Controllers\Frontend;

use App\User;
use App\Product;
use App\Requestcallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RequestcallbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userid = auth()->id();
        if(auth()->user()->hasRole('buyer')) {
            $requestcallback = Requestcallback::where('customer_id', $userid)->get();
        }
        if (auth()->user()->hasRole('seller')) {
            $requestcallback = DB::table('requestcallback')
                            ->Join('products', 'products.id', '=', 'requestcallback.product_id')
                            ->Join('users', 'users.id', '=', 'products.user_id')
                            ->select('requestcallback.*')
                            ->get();
        }
        
        $products = Product::all();

        return view('frontend.requestcallback.index', compact('products', 'requestcallback'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name'        => 'required',
            'email_add'        => 'required',
            'mobile'       => 'required'
        ]);

        $userid = auth()->id();
        $user = User::where('id', $userid)->first();
        $username = $user->name;
        $useremail = $user->email;
        $product = Product::where('id', $request->product_id)->first();
        $product_name = $product->name;

        $rfq = Requestcallback::create([
            'name' => request('name'),
            'email_add' => request('email_add'),
            'mobile' => request('mobile'),
            'customer_id' => auth()->id(),
            'product_id' => request('product_id'),
            'prod_name' => $product_name,
            'sign_date' => date('y-m-d h:i:s'),
        ]);

        return back()->with('flash', 'Request Call Back has been successfully submitted.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ss = Requestcallback::where('id', $id)->delete();

        return redirect()->route('requestcallback.index')->with('flash', 'Request Call Back has successfully deleted.');
    }
}
