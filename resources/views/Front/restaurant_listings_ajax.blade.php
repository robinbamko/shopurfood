 @if(count($all_categories) > 0)
			@foreach($all_categories as $cate)
		@php 	$restaurant_lists	 = get_category_shops($cate->cate_id,'1');  @endphp
 @if(count($restaurant_lists) > 0 )
 @php $count = count($restaurant_lists); @endphp
 @if($count == 1)
 <div class="section2">
		<div class="container">
			<div class="row">
				@foreach($restaurant_lists as $restaurant)
				@php 
				$image = explode('/**/',$restaurant->st_banner,-1); 
 				$path = url('')."/public/images/noimage/".$no_list_banner;
			 	if(count($image) > 0)
			 	{
			 		$filename = public_path('images/restaurant/banner/').$image[0];
				 	if(file_exists($filename) && $image[0]!= '')
				 	{
				 		$path = url('')."/public/images/restaurant/banner/".$image[0];
					}
				}
				@endphp
				<div class="col-md-12 section2-content">
					<h4>{{$cate->category_name}}</h4>
					<div class="section2-img"> 
					<a href="{{url('restaurant').'/'.$restaurant->store_slug}}" title="{{ucfirst($restaurant->st_name)}}">	<img src="{{$path}}"> </a>

					@if($restaurant->store_closed == "Closed")
					<div class="closed-div">
						<p>@lang(Session::get('front_lang_file').'.FRONT_CLOSED')</p>
					</div>
					@endif
					</div>
					
					<h5>{{ucfirst($restaurant->st_name)}}</h5>
					<p>{!!ucfirst(str_limit(strip_tags($restaurant->st_desc),120))!!}</p>
				</div>
					
				@endforeach
			</div>
		</div>
	  </div>
@elseif($count == 2)
	  <div class="section3">
		<div class="container">
			<div class="row">
				<div class="col-md-12 section3-head">
					<h4>{{$cate->category_name}}</h4>
				</div>
				<div class="col-md-12 section3-inner">
					<div class="row">
						@foreach($restaurant_lists as $restaurant)
						@php 
						$image = explode('/**/',$restaurant->st_banner,-1); 
		 				$path = url('')."/public/images/noimage/".$no_list_banner;
					 	if(count($image) > 0)
					 	{
					 		$filename = public_path('images/restaurant/banner/').$image[0];
						 	if(file_exists($filename) && $image[0]!= '')
						 	{
						 		$path =url('')."/public/images/restaurant/banner/".$image[0];
							}
						}
						@endphp
						<div class="col-md-6">
							<div class="section3-img">
							<a href="{{url('restaurant').'/'.$restaurant->store_slug}}" title="{{ucfirst($restaurant->st_name)}}">	<img src="{{$path}}"> </a>
							</div>
							
								<!--<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>-->
								
								<h5>{{ucfirst($restaurant->st_name)}}</h5>
								<p>{!!ucfirst(str_limit(strip_tags($restaurant->st_desc),60))!!}</p>
								
											
						</div>
						@if($restaurant->store_closed == "Closed")
						<div class="closed-div">
							<p>@lang(Session::get('front_lang_file').'.FRONT_CLOSED')</p>
						</div>
						@endif
						@endforeach
					</div>
				</div>
			</div>
		</div>
	  </div>
@elseif($count >= 3 )	     
	  <div class="section4">
		<div class="container">
			<div class="row">
				<div class="col-md-12 section4-heading">
					<div class="row">
						<div class="col-md-10">
							<h4>{{$cate->category_name}}</h4>
						</div>
						<div class="col-md-2">
							<h5><a href="{{url('all-categories').'/'.base64_encode($cate->cate_id)}}">@lang(Session::get('front_lang_file').'.FRONT_SEE_ALL')<span>&gt;</span></a></h5>
							
						</div>
					</div>
				</div>
				<div class="section4-slider col-md-12">
					   <section class="regular3 slider">
					   	@foreach($restaurant_lists as $restaurant)
					   	@php 
						$image = explode('/**/',$restaurant->st_banner,-1); 
		 				$path = url('')."/public/images/noimage/".$no_list_banner;
					 	if(count($image) > 0)
					 	{
					 		$filename = public_path('images/restaurant/banner/').$image[0];
						 	if(file_exists($filename) && $image[0]!= '')
						 	{
						 		$path = url('')."/public/images/restaurant/banner/".$image[0];
							}
						}
						@endphp
						
						   <div>
								<div class="section4-img">
								<a href="{{url('restaurant').'/'.$restaurant->store_slug}}" title="{{ucfirst($restaurant->st_name)}}">	<img src="{{$path}}"> </a>
								</div>
								<div class="section4-content">
									<h5>{{ucfirst($restaurant->st_name)}}</h5>
									<p>{!!ucfirst(str_limit(strip_tags($restaurant->st_desc),40))!!}</p>
								</div>
							
							@if($restaurant->store_closed == "Closed")
							<div class="closed-div">
								<p>@lang(Session::get('front_lang_file').'.FRONT_CLOSED')</p>								
							</div>
							@endif
							</div>
						@endforeach
						
					  </section>
				</div>
			</div>
		</div>
	  </div>
@endif
@endif
@endforeach
@endif