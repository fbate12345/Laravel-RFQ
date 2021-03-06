@extends('layouts.dashboards')


@section('content')
	
	@if(session('flash'))
		<div class="alert alert-primary">
			{{ session('flash') }}
		</div>
	@endif
				
	<div class="card">
      	<div class="card-body" style="padding: 5%;">
      		<div class="row">
      			<a href="{{ route('category.create') }}" class="ps-btn">Create Category</a>	
      		</div>
            <br>
            <div class="row">
              	<div class="col-12">
					<div class="table-responsive">
						<table id="order-listing" class="table">
							<thead>
								<tr>
									<th>No</th>
									<th>Name</th>
									<th>Type</th>
									<th>Parent</th>
									<th width="50px">Photo</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($categories as $category)
								<tr>
									<td>{{ $category->id }}</td>
									<td>{{ $category->name }}</td>
									<td>{{ App\Category::getTypeofcategory($category->parent) }}</td>
									<td>{{ App\Category::getParentcategoryNamebyID($category->parent) }}</td>
									<?php 
                                        if(@$category->cate_photo) {
                                            $path = asset('uploads/') . "/" . $category->cate_photo;
                                        }else{
                                            $path = asset('images/') . "/" . "author-1.png";
                                        }
                                    ?>
                                    <td><img class="img-responsive" src="<?= $path ?>" style="border-radius: unset; height: unset;" /></td>

									<td>
										<a href="{{ route('category.edit', $category->id) }}" class="btn btn-primary btn-sm btn-flat">
											<i class="fa fa-edit"></i>
										</a>
										<a href="" onclick="event.preventDefault();
			                                 document.getElementById('delete-form-{{$category->id}}').submit();" class="btn btn-danger btn-sm btn-flat">
											<i class="fa fa-trash"></i>
										</a>

										<form id="delete-form-{{$category->id}}" action="{{ route('category.destroy', $category->id) }}" method="POST" style="display: none;">
							                  <input type="hidden" name="_method" value="delete">
							                  @csrf
							            </form>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop