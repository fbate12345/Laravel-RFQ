@extends('layouts.dashboards')

@section('content')

<div class="card">
  	<div class="card-body" style="padding: 5%;">
        <div class="row">
          	<div class="col-12">
          		<form action="{{ route('category.update', $category->id) }}" method="POST">
					@csrf

					<input type="hidden" name="_method" value="put">

					<div class="box">
						<div class="box-body">
							<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
								<label>Name</label>
								<input required type="text" name="name" class="form-control" placeholder="Name" value="{{ $category->name }}" />

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
									@if($categories)
										@foreach($categories as $cate)
											<option <?php if($cate->id==$category->parent){echo 'selected';} ?> value="{{ $cate->id }}">{{ $cate->name }}</option>
										@endforeach
									@endif
								</select>
							</div>

							<div class="form-group {{ $errors->has('meta_title') ? 'has-error' : '' }}">
								<label>Meta Title</label>
								<input required type="text" name="meta_title" class="form-control" placeholder="Meta Title" value="{{ $category->meta_title }}" />

								@if ($errors->has('meta_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_title') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group {{ $errors->has('meta_description') ? 'has-error' : '' }}">
								<label>Meta Description</label>
								<input required type="text" name="meta_description" class="form-control" placeholder="Meta Description" value="{{ $category->meta_description }}" />

								@if ($errors->has('meta_description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_description') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group {{ $errors->has('meta_keywords') ? 'has-error' : '' }}">
								<label>Meta Keywords</label>
								<input required="" type="text" name="meta_keywords" class="form-control" placeholder="Meta Keywords" value="{{ $category->meta_keywords }}" />

								@if ($errors->has('meta_keywords'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meta_keywords') }}</strong>
                                    </span>
                                @endif
							</div>
						</div>

						<div class="box-footer">
							<button class="ps-btn btn-flat pull-right">Update Category</button>
						</div>
					</div>
				</form>
          	</div>
      	</div>
  	</div>
</div>
@stop