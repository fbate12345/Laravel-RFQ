<?php

namespace App\Http\Controllers\Frontend;

use Mail;
use App\User;
use App\Image;
use App\Product;
use App\Category;
use App\LocalizationSetting;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Filters\ProductFilters;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Frontend\EmailsController;

class ProductController extends Controller
{
    public function __construct(){

        $this->middleware('auth')->except(['index', 'show', 'getcategory', 'getlocalizationsettings', 'getproductsbyfilter', 'getrole']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductFilters $filters)
    {
        $categories = Product::latest()->filter($filters)->where('status', 2)->paginate(15);
        $categorys = Category::all();
        $count = count($categories);

        return view('frontend.product.index', compact('categories', 'categorys', 'count'));
    }

    public function getproductsbyfilter($word, $by, $min_price, $max_price, $category)
    {
        ($word == 'null') ? $word = '' : $word = $word;
        ($by == 'null') ? $by = '' : $by = $by;
        ($min_price == 'null') ? $min_price = 0 : $min_price = $min_price;
        
        if($category == 'null') {
            $products = DB::table('products')
                            ->select('products.*', 'images.url', 'users.name as username', 'users.id as user_id')
                            ->Join('images', 'products.id', '=', 'images.product_id')
                            ->Join('users', 'users.id', '=', 'products.user_id')
                            ->where('products.status', 2)
                            ->where('products.name', 'like', '%'.$word.'%')
                            ->where('products.username', 'like', '%'.$by.'%')
                            ->where('products.price_from', '>=', $min_price)
                            ->where('products.price_to', '<=', $max_price)
                            ->orderBy('products.sign_date', 'desc')
                            ->groupBy('products.id')
                            ->paginate(15);
        }else{
            $products = DB::table('products')
                            ->select('products.*', 'images.url', 'users.name as username', 'users.id as user_id')
                            ->Join('images', 'products.id', '=', 'images.product_id')
                            ->Join('users', 'users.id', '=', 'products.user_id')
                            ->Join('categories', 'categories.id', '=', 'products.category_id')
                            ->where('categories.slug', $category)
                            ->where('products.status', 2)
                            ->where('products.name', 'like', '%'.$word.'%')
                            ->where('products.username', 'like', '%'.$by.'%')
                            ->where('products.price_from', '>=', $min_price)
                            ->where('products.price_to', '<=', $max_price)
                            ->orderBy('products.sign_date', 'desc')
                            ->groupBy('products.id')
                            ->paginate(15);
        }

        return response()->json($products);
    }

    /**
     * Return Ajax response category data.
     *
     * @return \Illuminate\Http\Response
     */
    public function getcategory()
    {
        $data = [];
        $data['categorys'] = Category::all();
        $data['url'] = Route('product.index') . "?category=";

        return response()->json($data);
    }

    /**
     * Return Ajax response user role infor.
     *
     * @return \Illuminate\Http\Response
     */
    public function getrole()
    {
        $userid = auth()->id();
        if(@$userid) {
            if (auth()->user()->hasRole('buyer')) {
                $role = "buyer";
            }else{
                $role = "";
            }
        }else{
            $role = "guest";
        }
        

        return response()->json($role);
    }

    /**
     * Return Ajax response localization settings data.
     *
     * @return \Illuminate\Http\Response
     */
    public function getlocalizationsettings()
    {
        $localization_setting = LocalizationSetting::first();

        return response()->json($localization_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myproduct() {
        $products = Product::where('user_id', auth()->id())->get();
        return view('frontend.product.my', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('frontend.product.create', compact('categories'));
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
            'category_id' => 'required',
            'MOQ'        => 'required',
            'price_from'       => 'required',
            'price_to'       => 'required',
            'description' => 'required',
            'images'      => 'required',
        ]);

        $userid = auth()->id();
        $user_record = User::where('id', $userid)->first();
        $username = $user_record->name;

        $product = Product::create([
            'name'        => request('name'),
            'MOQ'        => request('MOQ'),
            'description' => request('description'),
            'user_id'     => auth()->id(),
            'username'     => $username,
            'price_from'       => request('price_from'),
            'price_to'       => request('price_to'),
            'category_id' => request('category_id'),
            'slug'        => createSlug(request('name')),
            'sign_date'     => date('y-m-d h:i:s'),
        ]);

        Image::upload_product_images($product->id);
        
        $controller = new EmailsController;

        $array = [];

        $userid = auth()->id();
        $user = User::where('id', $userid)->first();
        $array['username'] = $user->name;
        $array['receiver_address'] = $user->email;
        $array['data'] = array('name' => $array['username'], "body" => "Thanks for your product has been recieved. It will be reviewed and approved.");
        $array['subject'] = "Successfully added product.";
        $array['sender_address'] = "jovanovic.nemanja.1029@gmail.com";

        $controller->save($array);

        return redirect()->route('product.my');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('frontend.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('frontend.product.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate(request(), [
            'name'        => 'required',
            'category_id' => 'required',
            'MOQ'        => 'required',
            'price_from'       => 'required',
            'price_to'       => 'required',
            'description' => 'required',
        ]);

        $product->name = request('name');
        $product->MOQ = request('MOQ');
        $product->description = request('description');
        $product->user_id = auth()->id();
        $product->price_from = request('price_from');
        $product->price_to = request('price_to');
        $product->category_id = request('category_id');
        $product->save();

        Image::upload_product_images($product->id, request('existings'));

        return redirect()->route('product.my');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if($product->user_id == auth()->id()) {
            $product->delete();
        }

        return back();
    }
}
