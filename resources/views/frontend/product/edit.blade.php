@extends('layouts.product')

@section('section')

      @if ($errors->any())
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card rounded-0">
        <div class="card-body" style="padding: 5%;">

          <h3>{{ __('Edit Product') }}</h3>
          <br>
          <?php 
            $path = asset('uploads/') . "/";
          ?>
          <file-upload-multiple productinfo="{{ $product }}" categoriesjson="{{ $categories }}" actions_urls="{{ route('product.updateupload') }}" urls="{{ $path }}" images="{{ $product->images }}"></file-upload-multiple>
        </div>
      </div> <!-- [End] .card -->
@endsection