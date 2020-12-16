@extends('layouts.dashboards')

@section('content')

<div class="card">
  	<div class="card-body" style="padding: 5%;">
        <div class="row">
          	<div class="col-12">
          		<form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="box">
						<div class="box-body">
							<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
								<label>Name</label>
								<input required type="text" name="name" class="form-control" placeholder="Name" />

								@if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group">
								<label>Parent</label>
								<select class="form-control parent" id="parent" name="parent">
									<option value="-1">Root</option>
									@if($parents)
										@foreach($parents as $parent)
											<option value="{{ $parent['id'] }}">{{ $parent['name'] }}</option>
										@endforeach
									@endif
								</select>
							</div>

							<div class="form-group">
						        <label for="cate_photo" class="col-sm-3 col-form-label">{{ __('Photo') }}</label>
						        <div class="col-sm-9">
						          	<span>
				                    	<input type="file" name="cate_photo" id="file" onchange="loadPreview(this, 'preview_img');" class="inputfile">
					                    <label for="file" @click="onClick" inputId="1" style="" id='preview_img'>
					                    	<i class="fa fa-plus-circle"></i>
					                    </label>
				                  	</span>
						        </div>
						  	</div>

							<div class="form-group {{ $errors->has('meta_title') ? 'has-error' : '' }}">
								<label>Meta Title</label>
								<input required type="text" name="meta_title" class="form-control" placeholder="Meta Title" />

								@if ($errors->has('meta_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_title') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group {{ $errors->has('meta_description') ? 'has-error' : '' }}">
								<label>Meta Description</label>
								<input required type="text" name="meta_description" class="form-control" placeholder="Meta Description" />

								@if ($errors->has('meta_description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_description') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group {{ $errors->has('meta_keywords') ? 'has-error' : '' }}">
								<label>Meta Keywords</label>
								<input required="" type="text" name="meta_keywords" class="form-control" placeholder="Meta Keywords" />

								@if ($errors->has('meta_keywords'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_keywords') }}</strong>
                                    </span>
                                @endif
							</div>
						</div>

						<div class="box-footer">
							<button class="ps-btn btn-flat pull-right">Save Category</button>
						</div>
					</div>
				</form>
          	</div>
      	</div>
  	</div>
</div>
@stop

<style type="text/css">
    .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .inputfile + label {
        font-size: 1.25em;
        font-weight: 700;
        color: white;
        background-color: #E9ECEF;
        padding: 50px;
        display: inline-block;
        cursor: pointer;
        background-size: contain;
        width: 100%;
        background-repeat: no-repeat;
    }

    .inputfile:focus + label,
    .inputfile + label:hover {
        /*background-color: #38C172ed;*/
    }

    .hidden {
        display: none !important;
    }
</style>

@section('script')
    <script>
        function loadPreview(input, id) {
            id = "#" + id;
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var path = "background-image: " + "url('" + e.target.result + "')";
                    $(id).attr('style', path);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection