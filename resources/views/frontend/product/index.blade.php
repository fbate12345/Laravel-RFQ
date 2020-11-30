@extends('layouts.app')

@if($category)
  @section('main_title', $category->name . " | " . $general_setting->site_name)
  @section('title', $category->meta_title)
  @section('description', $category->meta_description)
  @section('keywords', $category->meta_keywords)
@else
  @section('main_title', "All Categories | " . $general_setting->site_name)
  @section('title', "all")
  @section('description', "all")
  @section('keywords', $arrs)
@endif

@section('content')

<div class="ps-page--shop" id="shop-sidebar">
  <div class="container">
      <div class="ps-layout--shop">
          <div class="ps-layout__left">
            <product-category-left></product-category-left>
          </div>
          <div class="ps-layout__right">
            <product-right></product-right>
          </div>
      </div>
  </div>
</div>
@stop
