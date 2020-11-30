<?php

namespace App\Http\Controllers\Frontend;

use Mail;
use App\User;
use App\Unit;
use App\Image;
use App\Product;
use App\Category;
use App\LocalizationSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
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

        $this->middleware('auth')->except(['index', 'show', 'getcategory', 'getlocalizationsettings', 'getproductsbyfilter', 'getrole', 'deleteproductsbychoosing']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductFilters $filters, Request $request)
    {
        $categories = Product::latest()->filter($filters)->where('status', 2)->paginate(15);
        $categorys = Category::all();
        $category = Category::where('slug', $request['category'])->first();
        $units = Unit::all();
        $count = count($categories);
        $arrs = 'all';
        foreach ($categorys as $cate) {
            if (@$cate['meta_keywords']) {
                $arrs = $arrs . ", " . $cate->meta_keywords;
            }
        }

        return view('frontend.product.index', compact('categories', 'categorys', 'category', 'count', 'units', 'arrs'));
    }

    /**
     * All products by every filter conditions.
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductsbyfilter($word, $by, $min_price, $max_price, $category, $sort)
    {
        ($word == 'null') ? $word = '' : $word = $word;
        ($by == 'null') ? $by = '' : $by = $by;
        ($min_price == 'null') ? $min_price = 0 : $min_price = $min_price;
        ($sort == 'null') ? $sortV = -1 : $sortV = $sort;
        if($sortV == -1 || $sortV == 1) {
            $column = "products.sign_date";
            $type = "DESC";
        }elseif($sortV == 2) {  //old to latest
            $column = "products.sign_date";
            $type = "ASC";
        }elseif($sortV == 3) {  //low to high
            $column = "products.price_from";
            $type = "ASC";
        }elseif($sortV == 4) {  //high to low
            $column = "products.price_from";
            $type = "DESC";
        }    

        if($category == 'null') {
            $products = DB::table('products')
                            ->select('products.*', 'images.url', 'users.name as username', 'users.id as user_id', 'users.company_name')
                            ->Join('images', 'products.id', '=', 'images.product_id')
                            ->Join('users', 'users.id', '=', 'products.user_id')
                            ->where('products.status', 2)
                            ->whereNull('products.deleted_at')
                            ->where('products.name', 'like', '%'.$word.'%')
                            ->where('products.username', 'like', '%'.$by.'%')
                            ->where('products.price_from', '>=', $min_price)
                            ->where('products.price_to', '<=', $max_price)
                            ->orderBy($column, $type)
                            ->groupBy('products.id')
                            ->paginate(15);
        }else{
            //////////////////////////////////// sub-category part ////////////////////////////////////

            // $CT = Category::where('slug', $category)->first();
            // $arr = [];

            // if(@$CT->parent) {
            //     $arr[] = $CT->id;
            // }else{
            //     $childs = Category::where('parent', $CT->id)->get();
                
            //     if(@$childs) {
            //         $arr[] = $CT->id;
            //         foreach($childs as $key => $child) {
            //             $arr[] = $child->id;
            //         }
            //     }
            // }

            // $cate = $arr;

            //////////////////////////////////// sub-category part ////////////////////////////////////

            $products = DB::table('products')
                            ->select('products.*', 'images.url', 'users.name as username', 'users.id as user_id', 'users.company_name')
                            ->Join('images', 'products.id', '=', 'images.product_id')
                            ->Join('users', 'users.id', '=', 'products.user_id')
                            ->Join('categories', 'categories.id', '=', 'products.category_id')
                            ->where('categories.slug', $category)
                            // ->whereIn('categories.id', $cate)
                            ->where('products.status', 2)
                            ->whereNull('products.deleted_at')
                            ->where('products.name', 'like', '%'.$word.'%')
                            ->where('products.username', 'like', '%'.$by.'%')
                            ->where('products.price_from', '>=', $min_price)
                            ->where('products.price_to', '<=', $max_price)
                            ->orderBy($column, $type)
                            ->groupBy('products.id')
                            ->paginate(15);
        }

        return response()->json($products);
    }

    /**
     * Remove selected products by ajax calling.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteproductsbychoosing(Request $request) {
        $ids = $request->input('ids');
        if(@$ids) {
            $diff = explode(',', $ids);
            foreach($diff as $key => $id) {
                $product = Product::where('id', $id)->delete();
            }

            return response()->json(['msg' => 'Successfully deleted!', 'status' => '200']);
        }else{
            return response()->json(['msg' => 'Please choose any items! There are not any chosen items now.', 'status' => '400']);
        }
    }

    /**
     * Return Ajax response category data.
     *
     * @return \Illuminate\Http\Response
     */
    public function getcategory()
    {
        $data = [];

        //////////////////////////////////// sub-category part ////////////////////////////////////

        // $root_categorys = Category::whereNull('parent')->get();  //Get Root Categories
        // if(@$root_categorys) {
        //     foreach($root_categorys as $key => $rC) {
        //         $childs = Category::where('parent', $rC->id)->get();    //Get Child Categories by parent id
        //         $root_categorys[$key]['childs'] = $childs;  //Set sub-array in Main array
        //     }
        // }

        // $data['categorys'] = $root_categorys;

        //////////////////////////////////// sub-category part ////////////////////////////////////

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
            'unit_id' => 'required',
            'MOQ'        => 'required',
            'description' => 'required',
            'meta_title'        => 'required',
            'meta_description'        => 'required',
            'meta_keywords'        => 'required',
            'price_from'       => 'required',
            'price_to'       => 'required',
            'images'      => 'required',
        ]);

        $userid = auth()->id();
        $user_record = User::where('id', $userid)->first();
        $username = $user_record->name;
        
        $product = Product::create([
            'name'        => request('name'),
            'meta_title'        => request('meta_title'),
            'meta_description'        => request('meta_description'),
            'meta_keywords'        => request('meta_keywords'),
            'MOQ'        => request('MOQ'),
            'description' => request('description'),
            'user_id'     => auth()->id(),
            'username'     => $username,
            'price_from'       => request('price_from'),
            'price_to'       => request('price_to'),
            'category_id' => request('category_id'),
            'unit' => request('unit_id'),
            'slug'        => createSlug(request('name')),
            'status' => "2",    //testing
            'sign_date'     => date('y-m-d h:i:s'),
        ]);

        $file = Input::file('images');
        $fl = $file[0];
        
        $filename = $fl->getClientOriginalName();
        
        $path = hash( 'sha256', time());
        // print_r(File::get($fl)); exit();
        if(Storage::disk('public_local')->put($path.'/'.$filename,  File::get($fl))) {

            $input['url'] = $filename;
            $input['product_id'] = $product->id;
            $file = Image::create($input);

            $controller = new EmailsController;
            $array = [];
            $userid = auth()->id();
            $user = User::where('id', $userid)->first();
            $array['username'] = $user->name;
            $array['receiver_address'] = $user->email;
            $array['data'] = array('name' => $array['username'], "body" => "Thanks for your product has been recieved. It will be reviewed and approved.");
            $array['subject'] = "Successfully added product.";
            $array['sender_address'] = "solaris.dubai@gmail.com";
            $controller->save($array);

            return response()->json([
                'success' => true,
                'id' => $file->id
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request)
    {
        $this->validate(request(), [
            'name'        => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'MOQ'        => 'required',
            'price_from'       => 'required',
            'price_to'       => 'required',
            'description' => 'required',
            'meta_title'        => 'required',
            'meta_description'        => 'required',
            'meta_keywords'        => 'required',
            'images'      => 'required',
        ]);

        $userid = auth()->id();
        $user_record = User::where('id', $userid)->first();
        $company_name = $user_record->company_name;
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
            'meta_title'        => request('meta_title'),
            'meta_description'        => request('meta_description'),
            'meta_keywords'        => request('meta_keywords'),
            'unit' => request('unit_id'),
            'slug'        => createSlug(request('name')),
            'status' => "2",    //testing
            'sign_date'     => date('y-m-d h:i:s'),
        ]);

        $product_link = route('product.show', $product->slug);
        $category = Category::where('id', $request->category_id)->first();
        $unit = Unit::where('id', $request->unit_id)->first();
        $categoryname = $category->name;
        $unitname = $unit->name;

        $files = Input::file('images');
        if(@$files) {
            for($i=0; $i < count($files); $i++) {
                $filename = $files[$i]->getClientOriginalName();
                $path = 'uploads';
                if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($files[$i]))) {

                    $input['url'] = $filename;
                    $input['product_id'] = $product->id;
                    $file = Image::create($input);
                }
            }
        }
        
        $controller = new EmailsController;
        $array = [];
        $user = User::where('id', $userid)->first();
        $array['username'] = $user->name;
        $array['receiver_address'] = $user->email;
        $array['data'] = array('name' => $array['username'], "body" => "Thanks for your product has been recieved. It will be reviewed and approved.", "company_name" => $company_name, "product_link" => $product_link, "product" => $product, 'category' => $categoryname, 'unitname' => $unitname);
        $array['subject'] = "Successfully added product.";
        $array['sender_address'] = "solaris.dubai@gmail.com";
        $controller->addProductseller($array);

        return response()->json([
            'success' => true,
            'redirect_urls' => route('product.my')
        ], 200);
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
        $units = Unit::all();
        return view('frontend.product.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updateupload(Request $request)
    {
        $this->validate(request(), [
            'name'        => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'MOQ'        => 'required',
            'price_from'       => 'required',
            'price_to'       => 'required',
            'description' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required'
        ]);

        $userid = auth()->id();
        $user_record = User::where('id', $userid)->first();
        $username = $user_record->name;
        if(@$request->product_id) {
            $product = Product::where('id', $request->product_id)->first();

            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->unit = $request->unit_id;
            $product->MOQ = $request->MOQ;
            $product->description = $request->description;
            $product->price_from = $request->price_from;
            $product->price_to = $request->price_to;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_keywords = $request->meta_keywords;
            $product->slug = createSlug(request('name'));
            $product->save();
        }else{
            
        }
        
        $files = Input::file('images');
        if(@$files) {
            for($i=0; $i < count($files); $i++) {
                $filename = $files[$i]->getClientOriginalName();
                $path = 'uploads';
                if(Storage::disk('uploads')->put($path.'/'.$filename,  File::get($files[$i]))) {

                    $input['url'] = $filename;
                    $input['product_id'] = $product->id;
                    $file = Image::create($input);
                }
            }
        }

        return response()->json([
            'success' => true,
            'redirect_urls' => route('product.my')
        ], 200);
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
