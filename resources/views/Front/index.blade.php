@extends('Front.layouts.default')
@section('content')
<style>
	.section2 .slick-slide .icon-img span{display:block;}
	.section2 .slick-slide .icon-img span:before
	{
		position: absolute;
		content: '';
		display: block;
		background: url({{url('')}}/public/front/images/circle1.png);
		width: 125px;
		height: 125px;
		background-size: cover;
		top: -2px;
		right: 0;
		margin: auto;
		transform: rotate(73deg);
		left: 0px;
		background-position: center;
		opacity: 0;
		transition: all 0.2s;
	} 
	
	@media only screen and (min-width:320px) and (max-width:575px)
	{
		.section2 .slick-slide .icon-img span:before
		{
			width: 166px;
			height: 166px;
		}
	}
</style>

<!--- SECTION1 --->
<?php
	echo '<input id="dir" type="hidden" value="'.$dir.'">';
?>
<div class="section1-banner">
    <section class="slider your-carousel" id="reg" dir="<?php echo $dir; ?>"> 
		@php $img = $imgtext = ''; @endphp
		@if(count($banner_imgs) > 0) 
			@foreach($banner_imgs as $imges)
				@php $img = $imges->banner_image; $imgtext = $imges->image_title; @endphp
				<img src="{{url('public/images/banner/'.$img)}}" alt="{{ $imgtext }}">
			@endforeach
		@else
			<img src="{{url('public/images/noimage/'.$no_banner)}}" alt="banner image">       
		@endif  
	</section>      
</div>

<!--- SECTION2 --->
<div class="section2">
    <div class="container">
		<div class="row">
			@php  $cat_name = $cat_img = $cat_icon = ''; @endphp
			@if(count($category_list) > 0)
				<section class="icon-slider slider your-carousel">
					@php $i = 0; @endphp
					@foreach($category_list as $category)
						@php
							$cat_id = $category->cate_id;
							$cat_img = $category->cate_img;
							$cat_icon = $category->cate_icon;
						@endphp
						
						@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
							@php $cat_name = $category->cate_name; @endphp
						@else
							@php 
							$cate_name_tbl ='cate_name_'.Session::get('front_lang_code'); 
							$cat_name = $category->$cate_name_tbl; 
							if($cat_name==''){
								$cat_name = $category->cate_name;
							}
							@endphp
						@endif
						
						<div>
							<div class="icon-img">
								<span>
									@if($cat_img != '')
										<a href="{{url('all-categories').'/'.base64_encode($cat_id)}}" title="{{ucfirst($category->cate_name)}}"><img src="{{url('public/images/category').'/'.$cat_icon}}"></a>
									@else
										<a href="{{url('all-categories').'/'.base64_encode($cat_id)}}" title="{{ucfirst($category->cate_name)}}"> <img src="{{url('')}}/public/front/images/icon1.png"></a>
									@endif  
								</span>
							</div>
							<div class="icon-img-caption"><a href="{{url('all-categories').'/'.base64_encode($cat_id)}}" title="{{ucfirst($cat_name)}}">{{ $cat_name }} </a> </div>   
						</div>
					@endforeach
					<div>
						<a href="{{url('all-categories')}}"><div class="icon-img" title ="View All Categories"><span class="">  <img src="{{url('')}}/public/front/images/icon6.png"></span></div></a>
						<div class="icon-img-caption" title ="View All Categories"><a href="{{url('all-categories')}}">@lang(Session::get('front_lang_file').'.FRONT_VIEW_ALL')</a></div>
					</div>
				</section>
			@else
				<div class="col-md-12 section2-else">
					<!-- <i class="fa fa-cutlery"></i> -->
					<img src="{{url('')}}/public/front/images/catIcon.png">
					<h4>@lang(Session::get('front_lang_file').'.FRONT_NO_CATEGORIES')</h4>
				</div>
            @endif     
		</div>
	</div>
</div>

<!--- SECTION3 --->
<div class="section3">
    <div class="container"> 
		<div class="row">
			<div class="col-md-12 col-lg-12 section3-heading">
				<h2>@lang(Session::get('front_lang_file').'.FRONT_TOP_RESTAURANTS')</h2>
				<p>@lang(Session::get('front_lang_file').'.FORNT_EASY_FAV_FOOD')</p>
				<img src="{{url('')}}/public/front/images/line.png">
			</div>
			
			<!-- -----------first restaurant section start----------- -->
			@php $store_name = $st_address = $st_rating = $st_desc =  $st_category = $store_name_url = '' @endphp
			
			@if(count($near_restaurant) > 0)
				@php $second = $near_restaurant[0];  @endphp
				<div class="col-md-12 col-lg-12">
					<div class="row">
						<section class="product-slider slider your-carousel 1">
							{{--@if($second!='' )--}}
							@foreach($second as $firstdet)
								@php 
									$res_name = $firstdet->id;
									$st_address = $firstdet->st_address;
									$st_rating = $firstdet->review_rating;
									$store_name_url = $firstdet->store_slug;
									$st_logo = $firstdet->st_banner;
									$st_category = $firstdet->st_category;
									$arr = explode("/", $st_logo, 2);
									$store_img = $arr[0];
								@endphp
							
								@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
									@php  
										$store_name = $firstdet->st_store_name;
										$st_desc = $firstdet->st_desc;
										$length = strlen($st_desc);
									@endphp
								@else
									@php
										$store_name_dbl = 'st_store_name_'.session::get('front_lang_code');
										$st_desc_tbl = 'st_desc_'.Session::get('front_lang_code');
										$store_name = $firstdet->$store_name_dbl;
										$st_desc = $firstdet->$st_desc_tbl;
										$length = strlen($st_desc);
									@endphp
								@endif
								<div>
									<div class="pdt-slider-img">
										@if($st_logo != '')    
											<a href="{{url('restaurant').'/'.$store_name_url}}">
											<img src="{{url('public/images/restaurant/banner/').'/'.$store_img}}"></a>
										@else
											<a href="{{url('restaurant').'/'.$store_name_url}}">
											<img src="{{url('')}}/public/front/images/product-img1.png"></a>
										@endif
										<div class="pdt-wave">
											<img src="{{url('')}}/public/front/images/wave1.png">
										</div>
										<div class="pdt-star">
											<img src="{{url('')}}/public/front/images/star1.png">
											@if($st_rating != '')
												@if($st_rating == '5')
													<span>{{ number_format($st_rating,1) }}</span>
												@elseif(filter_var($st_rating, FILTER_VALIDATE_INT) && $st_rating != '5')
													<span>{{ number_format($st_rating,2) }}.0</span>
												@else
													<span>{{ number_format($st_rating,1) }}</span>
												@endif
											@else     
												<span><i title="@lang(session::get('front_lang_file').'.FRONT_NO_RATINGS')" class="fa fa-star-o" aria-hidden="true"></i></span>
											@endif
										</div>
									</div>
									
									<div class="pdt-slider-content">
										<div class="pdt-slider-cont">
											<a href="{{url('restaurant').'/'.$store_name_url}}"><h4>{{ $store_name }}</h4></a>
											<!-- <p>{{ $length }}</p> -->
											@if($length > 120)
											<p>{!!ucfirst(str_limit(strip_tags($st_desc),120))!!}</p>
											@else
											<p>{{ ucfirst(strip_tags($st_desc)) }}</p>
											@endif
											
											@php
												$min_price = get_min_price($res_name);
												$max_price = get_max_price($res_name);
											@endphp
											<h3>@if($min_price !='' || $max_price!='') <span>@lang(session::get('front_lang_file').'.FRONT_MIN')</span> {{$default_currency}} {{ ($min_price) ? $min_price : '0.00'}}- <span>@lang(session::get('front_lang_file').'.FRONT_MAX')</span> {{$default_currency}} {{ ($max_price) ? $max_price : '0.00' }}
											@endif</h3>
										</div>
										<div class="pdt-slider-loc">
											<img src="{{url('')}}/public/front/images/location-icon.png">
											<p>{{ $st_address }}</p>
										</div>
									</div>  
								</div> 
							@endforeach
							{{--@endif--}}
							<div>
								@php $cate_image =  $cate_name = ''; @endphp
								@if($st_category != '')
									@php 
										$cat_img = DB::table('gr_category')->where('cate_id','=',$st_category)->first();
										$cate_image = $cat_img->cate_img;
									@endphp
									
									@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
										@php $cate_name = $cat_img->cate_name; @endphp
									@else
										@php
											$cate_name_tbl = 'cate_name_'.Session::get('front_lang_code');
											$cate_name = $cat_img->$cate_name_tbl;
										@endphp
									@endif
								@endif
								
								<div class="pdt-slider-img show-all-img">
									@if($cate_image != '')
										<img src="{{url('public/images/category').'/'.$cate_image}}">
									@else
										<img src="{{url('')}}/public/front/images/show-all-img.png"> 
									@endif
									<div class="show-all-cont" title="View All Restaurants in {{ $cate_name }}">
										<a href="{{url('all-categories').'/'.base64_encode($st_category)}}"><img src="{{url('')}}/public/front/images/show-all-icon.png"></a>
										<a href="{{url('all-categories').'/'.base64_encode($st_category)}}"><p> @lang(Session::get('front_lang_file').'.FRONT_SHOW_ALL')</p></a>
									</div>
								</div>
							</div>              
						</section>
					</div>
				</div>
			@else
				<div class="col-md-12 section3-else">
					<!-- <i class="fa fa-cutlery"></i> -->
					<img src="{{url('')}}/public/front/images/restIcon.png">
					<h3>@lang(Session::get('front_lang_file').'.FRONT_NO_RESTAURANT_FOUNT')</h3>
				</div>
			@endif
			<!-- -----------------second restaurant section end -------------- -->
			

			
			<!-- -----------second restaurant section start----------- -->
			@php $store_name = $st_address = $st_rating = $st_desc =  $st_category = $store_name_url = '' @endphp
			@if(count($near_restaurant) > 1)
				@php  $second = $near_restaurant[1]; @endphp
				<div class="col-md-12 col-lg-12">
					<div class="row">
						
						<section class="product-slider slider your-carousel">
							
							@foreach($second as $firstdet)
								@php 
									$res_name = $firstdet->id;
									$st_address = $firstdet->st_address;
									$st_rating = $firstdet->review_rating;
									$store_name_url = $firstdet->store_slug;
									$st_logo = $firstdet->st_banner;
									$st_category = $firstdet->st_category;
									$arr = explode("/", $st_logo, 2);
									$store_img = $arr[0];
								@endphp
								
								@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
									@php  $store_name = $firstdet->st_store_name;
										$st_desc = $firstdet->st_desc;
										$length = strlen($st_desc);
									@endphp
								@else
									@php
										$store_name_dbl = 'st_store_name_'.session::get('front_lang_code');
										$st_desc_tbl = 'st_desc_'.Session::get('front_lang_code');
										$store_name = $firstdet->$store_name_dbl;
										$st_desc = $firstdet->$st_desc_tbl;
										$length = strlen($st_desc);
									@endphp
								@endif
								
								<div>
									<div class="pdt-slider-img">
										@if($st_logo != '')    
											<a href="{{url('restaurant').'/'.$store_name_url}}">
											<img src="{{url('public/images/restaurant/banner/').'/'.$store_img}}"></a>
										@else
											<a href="{{url('restaurant').'/'.$store_name_url}}">
											<img src="{{url('')}}/public/front/images/product-img1.png"></a>
										@endif
										<div class="pdt-wave">
											<img src="{{url('')}}/public/front/images/wave1.png">
										</div>
										<div class="pdt-star">
											<img src="{{url('')}}/public/front/images/star1.png">
											@if($st_rating != '')
												@if($st_rating == '5')
													<span>{{ number_format($st_rating,1) }}</span>
												@elseif(filter_var($st_rating, FILTER_VALIDATE_INT) && $st_rating != '5')
													<span>{{ $st_rating }}.0</span>
												@else
													<span>{{ number_format($st_rating,1) }}</span>
												@endif
											@else     
												<span><i class="fa fa-star-o" title="@lang(session::get('front_lang_file').'.FRONT_NO_RATINGS')" aria-hidden="true"></i></span>
											@endif
										</div>
									</div>
									<div class="pdt-slider-content">
										<div class="pdt-slider-cont">
											<a href="{{url('restaurant').'/'.$store_name_url}}"> <h4>{{ $store_name }}</h4></a>
											{{--<p>{{ $length }}</p>--}}
											@if($length > 120)
												<p>{!!ucfirst(str_limit(strip_tags($st_desc),120))!!}</p>                       
											@else
												<p>{{ ucfirst(strip_tags($st_desc)) }}</p>
											@endif
											
											@php
												$min_price = get_min_price($res_name);
												$max_price = get_max_price($res_name);
											@endphp
											<h3>
												@if($min_price !='' || $max_price!='')
													<span>@lang(session::get('front_lang_file').'.FRONT_MIN')</span> {{$default_currency}} {{ ($min_price) ? $min_price : '0.00'}}- <span>@lang(session::get('front_lang_file').'.FRONT_MAX')</span> {{$default_currency}} {{ ($max_price) ? $max_price : '0.00' }}
												@endif
											</h3>
										</div>
										<div class="pdt-slider-loc">
											<img src="{{url('')}}/public/front/images/location-icon.png">
											<p>{{ $st_address }}</p>
										</div>
									</div>  
								</div> 
								
							@endforeach
							
							<div>
								@php $cate_image = $cate_name = ''; @endphp
								@if($st_category != '')
									@php 
										$cat_img = DB::table('gr_category')->where('cate_id','=',$st_category)->first();
										$cate_image = $cat_img->cate_img;
									@endphp
								
									@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
										@php
											$cate_name = $cat_img->cate_name;
										@endphp
									@else
										@php
											$cate_name_tbl = 'cate_name_'.Session::get('front_lang_code');
											$cate_name = $cat_img->$cate_name_tbl;
										@endphp
									@endif
								@endif
								
								<div class="pdt-slider-img show-all-img">
									@if($cate_image != '')
										<img src="{{url('public/images/category').'/'.$cate_image}}">
									@else
										<img src="{{url('')}}/public/front/images/show-all-img.png"> 
									@endif
									<div class="show-all-cont" title="View All restaurantsin {{ $cate_name }}">
										<a href="{{url('all-categories').'/'.base64_encode($st_category)}}">  
										<img src="{{url('')}}/public/front/images/show-all-icon.png"></a>
										<a href="{{url('all-categories').'/'.base64_encode($st_category)}}"><p>@lang(Session::get('front_lang_file').'.FRONT_SHOW_ALL')</p></a>
									</div>
								</div>
							</div>              
						</section>
					</div>
				</div>
			@endif
			<!-- -----------------second restaurant section end -------------- -->
			<!-- -----------second restaurant section start----------- -->
			@php $store_name = $st_address = $st_rating = $st_desc =  $st_category = $store_name_url = '' @endphp
			
			@if(count($near_restaurant) > 2)
			
			@php  $second = $near_restaurant[2]; @endphp
			<div class="col-md-12 col-lg-12">
				<div class="row">
					<section class="product-slider slider your-carousel">
						@foreach($second as $firstdet)
							@php
								$res_name 		= $firstdet->id;
								$st_address 	= $firstdet->st_address;
								$st_rating 		= $firstdet->review_rating;
								$store_name_url = $firstdet->store_slug;
								$st_logo 		= $firstdet->st_banner;
								$st_category 	= $firstdet->st_category;
								$arr 		= explode("/", $st_logo, 2);
								$store_img 	= $arr[0];
							@endphp
							@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
								@php  $store_name 	= $firstdet->st_store_name;
									$st_desc 		= $firstdet->st_desc;
									$length 		= strlen($st_desc);
								@endphp
							@else
								@php
									$store_name_dbl = 'st_store_name_'.session::get('front_lang_code');
									$st_desc_tbl 	= 'st_desc_'.Session::get('front_lang_code');
									$store_name 	= $firstdet->$store_name_dbl;
									$st_desc 		= $firstdet->$st_desc_tbl;
									$length 		= strlen($st_desc);
								@endphp
							@endif
							
							<div>
								<div class="pdt-slider-img">
									@if($st_logo != '')
										<a href="{{url('restaurant').'/'.$store_name_url}}">
										<img src="{{url('public/images/restaurant/banner/').'/'.$store_img}}"></a>
									@else
										<a href="{{url('restaurant').'/'.$store_name_url}}">
										<img src="{{url('')}}/public/front/images/product-img1.png"></a>
									@endif
									<div class="pdt-wave">
										<img src="{{url('')}}/public/front/images/wave1.png">
									</div>
									<div class="pdt-star">
										<img src="{{url('')}}/public/front/images/star1.png">
										@if($st_rating != '')
											@if($st_rating == '5')
												<span>{{ number_format($st_rating,1) }}</span>
											@elseif(filter_var($st_rating, FILTER_VALIDATE_INT) && $st_rating != '5')
												<span>{{ $st_rating }}.0 </span>
											@else
												<span>{{ number_format($st_rating,1) }}</span>
											@endif
											
										@else
											<span><i class="fa fa-star-o" title="@lang(session::get('front_lang_file').'.FRONT_NO_RATINGS')" aria-hidden="true"></i></span>
										@endif
									</div>
								</div>
								<div class="pdt-slider-content">
									<div class="pdt-slider-cont">
										<a href="{{url('restaurant').'/'.$store_name_url}}"> <h4>{{ $store_name }}</h4></a>
										{{--<p>{{ $length }}</p>--}}
										@if($length > 120)
											<p>{!!ucfirst(str_limit(strip_tags($st_desc),120))!!}</p>
										@else
										<p>{{ ucfirst(strip_tags($st_desc)) }}</p>
										@endif
										
										@php
											$min_price = get_min_price($res_name);
											$max_price = get_max_price($res_name);
										@endphp
										<h3>
											@if($min_price !='' || $max_price!='')
												<span>@lang(session::get('front_lang_file').'.FRONT_MIN')</span> {{$default_currency}} {{ ($min_price) ? $min_price : '0.00'}}- <span>@lang(session::get('front_lang_file').'.FRONT_MAX')</span> {{$default_currency}} {{ ($max_price) ? $max_price : '0.00' }}
											@endif
										</h3>
									</div>
									<div class="pdt-slider-loc">
										<img src="{{url('')}}/public/front/images/location-icon.png">
										<p>{{ $st_address }}</p>
									</div>
								</div>
							</div>
							
						@endforeach
						
						<div>
							@php $cate_image = $cate_name = ''; @endphp
							@if($st_category != '')
								@php
									$cat_img 	= DB::table('gr_category')->where('cate_id','=',$st_category)->first();
									$cate_image = $cat_img->cate_img;
								@endphp
							
								@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
									@php $cate_name = $cat_img->cate_name; @endphp
								@else
									@php
										$cate_name_tbl 	= 'cate_name_'.Session::get('front_lang_code');
										$cate_name 		= $cat_img->$cate_name_tbl;
									@endphp
								@endif
							@endif
							
							<div class="pdt-slider-img show-all-img">
								@if($cate_image != '')
									<img src="{{url('public/images/category').'/'.$cate_image}}">
								@else
									<img src="{{url('')}}/public/front/images/show-all-img.png">
								@endif
								<div class="show-all-cont" title="View All restaurantsin {{ $cate_name }}">
									<a href="{{url('all-categories').'/'.base64_encode($st_category)}}">
									<img src="{{url('')}}/public/front/images/show-all-icon.png"></a>
									<a href="{{url('all-categories').'/'.base64_encode($st_category)}}"><p>@lang(Session::get('front_lang_file').'.FRONT_SHOW_ALL')</p></a>
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
			@endif
			<!-- -----------------second restaurant section end -------------- -->
			
			
			
			
		</div>
	</div>
</div>
<!--- SECTION4 --->
	@php 
		$ad_title 	= $ad_desc = $ad_link = '';
		$ad_img 	= 'background:url(public/front/images/section4-bg.jpg)';
		$ad_link	= '';
	@endphp
	@if(count($advertise_details)> 0)
		@foreach($advertise_details as $det)
			@php
				$ad_link 	= $det->ad_link;
				$ad_img 	= 'background:url(public/front/frontImages/'.$det->ad_image.')';
			@endphp
		
			@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
				@php 
					$ad_title 	= $det->ad_title;
					$ad_desc 	= $det->ad_desc;
				@endphp
			@else
				@php
					$add_tit_tbl= 'ad_title_'.Session::get('front_lang_code');
					$add_des_tit='ad_desc_'.Session::get('front_lang_code');
					$ad_title 	= $det->$add_tit_tbl;
					$ad_desc 	= $det->$add_des_tit;
				@endphp
			@endif
		@endforeach
	@endif

<a href="{{$ad_link}}" target="new">
	<div class="section4" style="{{$ad_img}} no-repeat; background-size:cover; padding:199px 0 50px;     background-position: center;">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-lg-12 section4-inner">
					{{--<h1><span>B</span>urger <span>A</span>ds</h1>--}}
					<h1>{{ $ad_title }}</h1>
					{{--<p>Placed indulgently between two surprisingly soft buns is a real beef patty, pickled tomatoes, onions and a fried <br> egg splattered with addictive animal sauce.</p>--}}
					<p>{{ $ad_desc }}</p>
				</div>
			</div>
		</div>
	</div>
</a>

<!-- SECTION5 -->
<div class="section5">
    <div class="container">
		<div class="row">
			<div class="col-md-12 col-lg-12 section3-heading">
				<h2>@lang(Session::get('front_lang_file').'.FRONT_FEATURE_RESTAURANT')</h2>
				<p>@lang(Session::get('front_lang_file').'.FRONT_EXPLORE_RESTAURANT')</p>
				<img src="{{url('')}}/public/front/images/line.png">
			</div>
			
			<?php //{{url('restaurant').'/'.str_slug($store_name,'-').'/'.base64_encode($res_name)}} ?>
			
			@if(count($featuredstores)>0)
			<div class="col-md-12 col-lg-12 section5-inner">
				<div class="row">
					<div class="col-md-5 col-lg-5 col-xl-4">
						<div class="row">
							@for($i=1;$i< count($featuredstores);$i++)
							<?php
								if($featuredstores[$i]->st_logo!="")
									$logopath=url('/public/images/restaurant/'.$featuredstores[$i]->st_logo);
								else
									$logopath=url('/public/images/noimage/shop_logo1541146541.jpg');
								
								$store_nameUrl=$featuredstores[$i]->st_store_name;
								if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en'){
									$store_name=$featuredstores[$i]->st_store_name;
									$cate_name = $featuredstores[$i]->cate_name;
									}else{
									$store_name_tbl = 'st_store_name_'.Session::get('front_lang_code');
									$cate_name_tbl = 'cate_name_'.Session::get('front_lang_code');
									$store_name=$featuredstores[$i]->$store_name_tbl;
									
									$cate_name = $featuredstores[$i]->$cate_name_tbl;
									
								}
								
								
								//               $store_name=$featuredstores[$i]->st_store_name;
								$res_name=$featuredstores[$i]->id;
								
								$reflink=url('restaurant').'/'.str_slug($store_nameUrl,'-').'/'.base64_encode($res_name);
							?>
							
							<div class="col-md-12 col-lg-12 featured-rest">
								<a href="{{$reflink}}">
									<div class="featured-img">
										
										<img src="{{$logopath}}">
										
									</div>
									<div class="featured-content">
										{{--<h3>{{$featuredstores[$i]->st_store_name}}</h3>--}}
										
										<h3>{{$store_name}}</h3>
										{{--<p>{{$featuredstores[$i]->cate_name }}</p>--}}
										<p>{{ $cate_name }}</p>
										@if($featuredstores[$i]->active == '1')
										<div class="premium">@lang(Session::get('front_lang_file').'.FRONT_PREMIUM')</div>
										@endif
									</div>
									<div class="featured-pdt-star">
										<img src="{{url('')}}/public/front/images/star1.png">
										@if($featuredstores[$i]->review_rating!="")
										@if($featuredstores[$i]->review_rating!=5)
										<span>{{ number_format($featuredstores[$i]->review_rating, 1) }}</span>
										@else
										<span>{{ number_format($featuredstores[$i]->review_rating,1) }}</span>
										@endif
										@else
										<span><i class="fa fa-star-o" title="@lang(session::get('front_lang_file').'.FRONT_NO_RATINGS')" aria-hidden="true"></i></span>
										@endif
									</div>
								</a>
							</div>
							@endfor
							
						</div>
					</div>
					<div class="col-xl-1 section5-spacing">
					</div>
					<?php
						
						$image=DB::table('gr_general_setting')->select('gs_feature_res_image')->first();

						if($image->gs_feature_res_image != ''){
							$filename = public_path('/front/frontimages/'.$image->gs_feature_res_image);
							if(file_exists($filename)){

								$filepath=url('/public/front/frontimages/'.$image->gs_feature_res_image);
							}
							else{
								$filepath=url('/public/front/images/featured-restaurant.jpg');
							}
							}else{
							$filepath=url('/public/front/images/featured-restaurant.jpg');
						}
						$filepath='';
						$store_nameUrl=$featuredstores[0]->st_store_name;
						
						if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en'){
							$store_name=$featuredstores[0]->st_store_name;
							}else{
							$store_name_tbl = 'st_store_name_'.Session::get('front_lang_code');
							$store_name=$featuredstores[0]->$store_name_tbl;
						}
						
						$res_name=$featuredstores[0]->id;
						$reflink=url('restaurant').'/'.str_slug($store_nameUrl,'-').'/'.base64_encode($res_name);
						
						/*$min_max_value = DB::table('gr_product')->select(DB::Raw('MIN(pro_original_price) as min_price'),DB::Raw('Max(pro_original_price) as max_price'))->where('pro_store_id','=',$res_name)->first();*/
						/* $min_max_value = DB::table('gr_product')->select(DB::Raw('if(SUM(pro_discount_price) > 0,MIN(pro_discount_price),MIN(pro_original_price)) AS min_price'),DB::Raw('if(SUM(pro_discount_price) > 0,MAX(pro_discount_price),MAX(pro_original_price)) AS max_price'))->where('pro_store_id','=',$res_name)->first();*/
						$min_price = get_min_price($res_name);
						$max_price = get_max_price($res_name);
					?>
					
					<div class="col-md-7 col-lg-7 col-xl-7 featured-img-sec">
						<a href="{{$reflink}}">
							<img src="{{$filepath}}">
							
							<div class="featured-sec-cont">
								<h3>{{ $store_name }}</h3>
								<p>@lang(Session::get('front_lang_file').'.FRONT_MIN_ORDER')- {{$default_currency}} {{$min_price}} @lang(Session::get('front_lang_file').'.FRONT_MAX_ORDER')- {{$default_currency}} {{$max_price}}</p>
							</div>
							@if($featuredstores[0]->active == '1')
							<div class="featured-img-premium">
								<span>@lang(Session::get('front_lang_file').'.FRONT_PREMIUM')</span>
							</div>
							@endif
							<div class="featured-img-star">
								<img  src="{{url('')}}/public/front/images/star1.png">
								
								@if($featuredstores[0]->review_rating!="")
								@if($featuredstores[0]->review_rating!=5)
								<span>{{ number_format($featuredstores[0]->review_rating, 1) }}</span>
								@else
								<span>{{ number_format($featuredstores[0]->review_rating,1) }}</span>
								@endif
								@else
								<span><i class="fa fa-star-o" title="@lang(session::get('front_lang_file').'.FRONT_NO_RATINGS')" aria-hidden="true"></i></span>
								@endif
							</div>
							<div class="featured-food-icon">
								<img src="{{url('')}}/public/front/images/food-icon.png">
							</div>
						</a>
					</div>
					
					
				</div>
			</div>
			
			@else
			<div class="col-md-12 section3-else">
				<!-- <i class="fa fa-cutlery"></i> -->
				<img src="{{url('')}}/public/front/images/restIcon.png">
				<h3>@lang(Session::get('front_lang_file').'.FRONT_NO_RESTAURANTS_NEARBY')</h3>
			</div>
			@endif
			
		</div>
	</div>
</div>
<!-- SECTION6 -->
<div class="section6">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-lg-12">
				<div class="row">
					<div class="col-md-7 col-lg-7">
						<div class="section6-inner1">
							{{-- <div class="section6-img">
								@if(count($logo_settings_details) > 0)
								@php
								foreach($logo_settings_details as $logo_set_val){ }
								$filename = public_path('images/logo/').$logo_set_val->admin_logo;
								@endphp
								@if(file_exists($filename))
								<a href="#"><img src="{{url('public/images/logo/'.$logo_set_val->favicon)}}" alt="Logo" class=""></a>
								@else
								<a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="" width="50px"></a>
								@endif
								@else
								<a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="" width="50px"></a>
								@endif
								<!-- <img src="{{url('')}}/public/front/images/location-icon1.png"> -->
							</div> --}}
							@php $pre_foot_text = $pre_foot_des = $playstore_lnk = $itunes_lnk = $images = ''; @endphp
							@if(count($pre_footer))
							@foreach($pre_footer as $det)
							@php
							
							$playstore_lnk = $det->playstore_link;
							$itunes_lnk = $det->itunes_link;
							$images = $det->app_sec_image;
							@endphp
							@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')
							@php
							$pre_foot_text = $det->prefooter_text;
							$pre_foot_des = $det->prefooter_desc;
							@endphp
							@else
							@php
							$pre_ft_texttbl = 'prefooter_text_'.Session::get('front_lang_code');
							$pre_des_tbl = 'prefooter_desc_'.Session::get('front_lang_code');
							$pre_foot_text = $det->$pre_ft_texttbl;
							$pre_foot_des = $det->$pre_des_tbl;
							@endphp
							@endif
							@endforeach
							@endif
							<div class="section6-content">
								@if($pre_foot_des != '')
								<p>{{ ucfirst(str_limit($pre_foot_des,300)) }}</p>
								@endif
								
							</div>
						</div>
						<div class="section6-inner2">
							@if($pre_foot_text != '')
							<h4>{{ $pre_foot_text }}</h4>
							@endif
							<ul>
								
								@if($playstore_lnk != '')
								<li><a href="{{$playstore_lnk}}"><img src="{{url('')}}/public/front/images/google-play.png"></a></li>
								@else
								<li><a href="#"><img src="{{url('')}}/public/front/images/google-play.png"></a></li>
								@endif
								
								@if($itunes_lnk != '')
								<li><a href="{{ $itunes_lnk }}"><img src="{{url('')}}/public/front/images/app-store.png"></a></li>
								@else
								<li><a href="#"><img src="{{url('')}}/public/front/images/app-store.png"></a></li>
								@endif
								
							</ul>
						</div>
					</div>
					<div class="col-md-5 col-lg-5 section6-right">
						@if($images != '')
						<img src="{{url('public/front/frontImages/'.$images)}}">
						@else
						<img src="{{url('')}}/public/front/images/section6-img.png">
						@endif
					</div>           
				</div>
			</div>
		</div>
	</div>
</div>
<div class="section-newletter">
 	<div class="container">
		<div class="row">		
			<div class="col-lg-12 col-sm-12">  
				<div class="newsletter-outer"> 
					<div class="sectionnews-heading">
						<h2><span>@lang(Session::get('front_lang_file').'.JOIN_NEWSLETTER')</span></h2>
						<img src="{{url('')}}/public/front/images/line.png">
					</div>			
					
					<div class="newsletter-inner">
						<input class="newsletter-email" id="sub_email" type="email"  name="email" placeholder=" @lang(Session::get('front_lang_file').'.ENTER_EMAIL_NEWSLETTER')" >
						<button class="button subscribe" id="subscribe_submit" title="Subscribe"> @lang(Session::get('front_lang_file').'.FRONT_SUBSCRIBE') </button>
						<div class="mail-loader" style="display:none"> <img src="<?php echo url('')?>/public/images/loader.gif"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="preFooter">
	<div class="container">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-6 pre-foot preFootercol1">
					<div class="span"><h1>{!! trans(Session::get('front_lang_file').'.FRONT_BE_A_MERCHANT')!!}</h1>
                     <a href="{{url('merchant_signup')}}" class="btn">@lang(Session::get('front_lang_file').'.FRONT_CL_HERE')</a>
					</div>
					
				</div>
				<div class="col-lg-6 pre-foot preFootercol2">
					<div class="span"><h1>{!! trans(Session::get('front_lang_file').'.FRONT_BE_A_DEL_BOY')!!}</h1>
						<a href="{{url('delivery-person-signup')}}" class="btn">@lang(Session::get('front_lang_file').'.FRONT_CL_HERE')</a>
					</div>
					
				</div>
			</div>
		</div>
	</div>	
</div>
@section('script')



<script src="{{url('')}}/public/front/js/slick.js"></script>
<script>
	$(document).ready(function() {
		var dir = $("#dir").val();
		if(dir == 'rtl'){
			$("#reg").slick({
				dots: true,
				infinite: true,
				autoplay:true,
				rtl: true,
				slidesToShow: 1,
				slidesToScroll: 1
			});
			}else {
			$("#reg").slick({
				dots: true,
				infinite: true,
				autoplay:true,
				rtl: false,
				slidesToShow: 1,
				slidesToScroll: 1 
			});
		}
		// $(".regular").slick({
		// dots: true,
		// infinite: true,
		// autoplay:true,
		// slidesToShow: 1,
		// slidesToScroll: 1     
		// });
		
		$(".icon-slider").slick({
			dots: true,
			infinite: true,
			slidesToShow: 6,
			slidesToScroll: 6,
			responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			},
			{
				breakpoint: 576,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			},
			
			]
			
		});
		
		$(".product-slider").slick({
			dots: true,
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 3,
			responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			},
			{
				breakpoint: 576,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true
				}
			}
			]
		});
		
		
	});
</script>

<script>
	var str = $('.section4 h1').text().split(" ");
	$('.section4 h1').empty();
	str.forEach(function(a) {
		$('.section4 h1').append('&nbsp;<span>' + a.slice(0, 1) + '</span>' + a.slice(1))
	})
	
</script>



@endsection
@stop

