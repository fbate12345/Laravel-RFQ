<?php

namespace App;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{    
    public $fillable = ['name', 'meta_title', 'meta_keywords', 'meta_description', 'slug', 'sign_date', 'parent'];

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
}
