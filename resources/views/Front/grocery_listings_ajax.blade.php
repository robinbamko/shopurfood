 @if(count($all_categories) > 0) 
			@foreach($all_categories as $cate)
		@php 	$store_lists	 = get_category_shops($cate->cate_id,'2');   @endphp
 @if(count($store_lists) > 0 )
 @php $count = count($store_lists); @endphp
 @if($count == 1)
 <div class="section2">
		<div class="container">
			<div class="row">
				@foreach($store_lists as $store)
				@php 
				$image = explode('/**/',$store->st_banner,-1); 
 				$path = url('')."/public/images/noimage/".$no_list_banner;
			 	if(count($image) > 0)
			 	{
			 		$filename = public_path('images/store/banner/').$image[0];
				 	if(file_exists($filename) && $image[0]!= '')
				 	{
				 		$path = url('')."/public/images/store/banner/".$image[0];
					}
				}
				@endphp
				<div class="col-md-12 section2-content store-section2-content">
					<h4>{{$cate->category_name}}</h4>
					<div class="section2-img"> 
						<a href="{{url('store').'/'.str_slug($store->st_name,'-').'/'.base64_encode($store->id)}}" title="{{ucfirst($store->st_name)}}"><img src="{{$path}}"> </a>
					</div>
					
					<h5>{{ucfirst($store->st_name)}}</h5>
					<p>{!!ucfirst(str_limit(strip_tags($store->st_desc),120))!!}</p>
				</div>
				@endforeach
			</div>
		</div>
	  </div>
@elseif($count == 2)
	  <div class="section3">
		<div class="container">
			<div class="row">
				<div class="col-md-12 section3-head store-section3-head">
					<h4>{{$cate->category_name}}</h4>
				</div>
				<div class="col-md-12 section3-inner">
					<div class="row">
						@foreach($store_lists as $store)
						@php 
						$image = explode('/**/',$store->st_banner,-1); 
		 				$path = url('')."/public/images/noimage/".$no_list_banner;
					 	if(count($image) > 0)
					 	{
					 		$filename = public_path('images/store/banner/').$image[0];
						 	if(file_exists($filename) && $image[0]!= '')
						 	{
						 		$path =url('')."/public/images/store/banner/".$image[0];
							}
						}
						@endphp
						<div class="col-md-6">
							<div class="section3-img">
							<a href="{{url('store').'/'.str_slug($store->st_name,'-').'/'.base64_encode($store->id)}}" title="{{ucfirst($store->st_name)}}">	<img src="{{$path}}"> </a>
							</div>
							
								<h5>{{ucfirst($store->st_name)}}</h5>
								<p>{!!ucfirst(str_limit(strip_tags($store->st_desc),60))!!}</p>
											
						</div>
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
				<div class="col-md-12 section4-heading store-section4-heading">
					<div class="row">
						<div class="col-md-10">
							<h4>{{$cate->category_name}}</h4>
						</div>
						<div class="col-md-2">
							<h5><a href="{{url('all-grocery-categories').'/'.base64_encode($cate->cate_id)}}">@lang(Session::get('front_lang_file').'.FRONT_SEE_ALL')<span>&gt;</span></a></h5>
							
						</div>
					</div>
				</div>
				<div class="section4-slider col-md-12">
					   <section class="regular3 slider">
					   	@foreach($store_lists as $store)
					   	@php 
						$image = explode('/**/',$store->st_banner,-1); 
		 				$path = url('')."/public/images/noimage/".$no_list_banner;
					 	if(count($image) > 0)
					 	{
					 		$filename = public_path('images/store/banner/').$image[0];
						 	if(file_exists($filename) && $image[0]!= '')
						 	{
						 		$path = url('')."/public/images/store/banner/".$image[0];
							}
						}
						@endphp
						   <div>
								<div class="section4-img">								
								<a href="{{url('store').'/'.str_slug($store->st_name,'-').'/'.base64_encode($store->id)}}" title="{{ucfirst($store->st_name)}}">	<img src="{{$path}}"> </a>
								</div>
								<div class="section4-content">
									<h5>{{ucfirst($store->st_name)}}</h5>
									<p>{!!ucfirst(str_limit(strip_tags($store->st_desc),40))!!}</p>									
								</div>
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