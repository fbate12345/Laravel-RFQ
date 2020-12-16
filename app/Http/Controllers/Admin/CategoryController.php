<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Adminlogs;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware(['auth', 'manager']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Category::whereNull('parent')->get();

        return view('admin.category.create', compact('parents'));
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
            'name' => 'required',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required'
        ]);

        if (@$request->parent) {
            if ($request->parent == -1) {
                $parent = NULL;
            }else
                $parent = $request->parent;
        }

        $category = Category::create([
            'name' => $request->name,
            'meta_title' => $request->meta_title,
            'slug' => createCategorySlug(request('name')),
            'parent' => $parent,
            'cate_photo' => @$request->cate_photo,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'sign_date' => date('y-m-d h:i:s'),
        ]);

        Category::upload_photo($category->id);

        $data = [];
        $data['title'] = 'Added';
        $data['description'] = 'Category Name: '.$request->name;
        $add_logs = Adminlogs::Addlog($data);

        return redirect()->route('category.index')->with('flash', 'Category has been successfully created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $categories = Category::all();

        return view('admin.category.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->validate(request(), [
            'name' => 'required',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required'
        ]);

        if (@$request->parent) {
            if ($request->parent == -1) {
                $parent = NULL;
            }else
                $parent = $request->parent;
        }

        $category->name = $request->name;
        $category->parent = $parent;
        $category->meta_title = $request->meta_title;
        $category->meta_keywords = $request->meta_keywords;
        $category->meta_description = $request->meta_description;
        if (@$request->cate_photo) {
            $category->cate_photo = @$request->cate_photo;
        }

        $category->save();

        if (@$request->cate_photo) {
            Category::upload_photo($category->id);
        }

        $data = [];
        $data['title'] = 'Updated';
        $data['description'] = 'Category Name: '.$request->name;
        $add_logs = Adminlogs::Addlog($data);

        return redirect()->route('category.index')->with('flash', 'Category has successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        $data = [];
        $data['title'] = 'Deleted';
        $data['description'] = 'Category Name: '.$category['name'];
        $add_logs = Adminlogs::Addlog($data);

        return redirect()->route('category.index')->with('flash', 'Category has successfully deleted');
    }
}
