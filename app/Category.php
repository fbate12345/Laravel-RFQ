<?php

namespace App;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{    
    public $fillable = ['name', 'meta_title', 'meta_keywords', 'meta_description', 'cate_photo', 'slug', 'sign_date', 'parent'];

    public function products(){
        return $this->hasMany('App\Product');
    }

    public function scopeSearchId($query, $param) {
    	$category = $query->where('slug', $param)
    					  ->orWhere('name', 'like', '%'.$param.'%')
    					  ->first();

    	return $category ? $category->id : false;
    }

    /** 
    * get parent category name by parent ID
    * @param parent ID
    * @author Nemanja
    * @since 2020-12-16
    * @return parent category name as string type
    */
    public static function getParentcategoryNamebyID($parentID)
    {
        if (@$parentID) {
            $parentRecord = Category::where('id', $parentID)->first();
            $name = $parentRecord->name;
        }else
            $name = "Root";

        return $name;
    }

    /**
    * @param cate_id
    * This is a feature to upload a profile logo
    */
    public static function upload_photo($cate_id, $existings = null) {
        if(!request()->hasFile('cate_photo')) {
            return false;
        }

        Storage::disk('public_local')->put('uploads/', request()->file('cate_photo'));

        self::save_cate_img($cate_id, request()->file('cate_photo'));
    }

    /**
    * photo of category upload function
    * @param categoryID and photo file
    * @return boolean true or false
    * @since 2020-12-16
    * @author Nemanja
    */
    public static function save_cate_img($cate_id, $image) {
        $category = Category::where('id', $cate_id)->first();

        if($category) {
            Storage::disk('public_local')->delete('uploads/', $category->cate_photo);
            $category->cate_photo = $image->hashName();
            $category->update();
        }
    }

    /** 
    * get category type by ID
    * @param parent ID
    * @author Nemanja
    * @since 2020-12-16
    * @return category type(ex: main or sub) name as string type
    */
    public static function getTypeofcategory($parent)
    {
        if($parent == NULL) {
            $name = "Main-Category";
        }else{
            $name = "Sub-Category";
        }

        return $name;
    }
}
