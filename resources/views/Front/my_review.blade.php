@extends('Front.layouts.default')
@section('content')
<style type="text/css">
/*TAB START*/
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {border-radius: 0px;font-weight: 500;}
/*TAB END*/
.testimonials-item{
	width:100%;
	margin: 0px 15px;
	padding:5px;
	
}
.user.row {
	padding:3px;
}
</style>

	<div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
	</div>
	
<div class="section9-inner">
	<div class="container userContainer userReview">
		<div class="row">   
			<!--<div class="col-lg-12">
						<h5 class="sidebar-head">             
								@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')              
						</h5>
			</div>-->
			<div class="userContainer-bg row">
			
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div"> 
				<div class="row">
					<div class="col-md-12">
						<ul class="nav nav-tabs" role="tablist">
						  <li class="nav-item">
						    <a class="nav-link active" href="#Restaurant" role="tab" data-toggle="tab">@lang(Session::get('front_lang_file').'.ADMIN_RESTS')</a>
						  </li>

						  <li class="nav-item">
						    <a class="nav-link" href="#Item" role="tab" data-toggle="tab">@lang(Session::get('front_lang_file').'.FRONT_ITEMS')</a>
						  </li>
						  <li class="nav-item">
						    <a class="nav-link" href="#Order" role="tab" data-toggle="tab">@lang(Session::get('front_lang_file').'.ADMIN_ORDS')</a>
						  </li>
						</ul>
					</div>
				</div>
				<div class="tab-content">
					{{-- restaurant review --}}
				  <div role="tab" class="tab-pane fade in active show" id="Restaurant">
				  	<div class="row panel-heading">
					@if(count($res_reviews) > 0)
						@foreach($res_reviews as $review) 
							
							<div class="col-lg-6 col-md-6 col-sm-12 col-12 review-rest-section">
								<div class="row">
									<div class="col-lg-3 col-sm-3 col-md-3 col-3">
										<div class="table-responsive form-group review-restaurant">
											@php $path = url('').'/public/images/noimage/'.$no_shop_logo; 
											$filename = public_path('images/restaurant/').$review->st_logo; 
											@endphp
											@if(file_exists($filename) && $review->st_logo != '')
											@php $path = url('').'/public/images/restaurant/'.$review->st_logo;
											@endphp
											@endif
											<img src="{{$path}}" alt="{{$review->shop_name}}" >											
										</div>
									</div>
										<div class="col-lg-9 col-md-9 col-sm-9 col-9 review-rest-cont">
											<div class="col-lg-12 col-sm-12 col-md-12 col-12 ">
												<div class="table-responsive form-group">
													<p class="title">{{ucfirst($review->shop_name)}}</p>
													<p class="desc">{!!stripslashes(ucfirst($review->review_comments))!!}</p>
													<div class="starGroup">
													@for($i=1;$i<=$review->review_rating;$i++)
													<i class="fa fa-star"></i>
													@endfor
													</div>
													<p class="date">{{ date('jS M Y h:i a',strtotime($review->created_date))}}</p>
												</div>
											</div>
											<div class="col-lg-12 col-sm-12 col-md-12 col-12">
												<div class="table-responsive form-group review-restaurant-btn">		                        	
													<a href="{{url('restaurant').'/'.$review->store_slug}}">
														{{ Form::button(__(Session::get('front_lang_file').'.FRONT_VISIT_RESTAURANT'))}}<br>
													</a>	                        		
														@if($review->review_status == '1')
															<span class="approveSpan approved"><i class="fa fa-thumbs-up"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_APPROVE')</span>
														@elseif($review->review_status == '0')
															<span style=""  class="approveSpan"><i class="fa fa-thumbs-down"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_DISAPPROVE')</span>
														@endif
													</a>
												</div>
											</div>
										</div>
									
								</div>
							</div>
						
							
							
							
							<div class="clearfix"></div>
						@endforeach
					@endif
					</div>
					@if(count($res_reviews) > 0)
						{{ $res_reviews->render() }}
					@endif
				  </div>
				  {{-- store review --}}
				  <div role="tab" class="tab-pane fade" id="Store">
				  	<div class="row panel-heading">
				  		
					@if(count($store_reviews) > 0)
						@foreach($store_reviews as $review) 
							
							<div class="col-lg-6">
								<div class="row">
									<div class="col-lg-3 col-sm-3 col-md-3 col-xs-3 ">
										<div class="table-responsive form-group review-restaurant">
											@php $path = url('').'/public/images/noimage/'.$no_shop_logo; 
											$filename = public_path('images/store/').$review->st_logo; 
											@endphp
											@if(file_exists($filename) && $review->st_logo != '')
											@php $path = url('').'/public/images/store/'.$review->st_logo;
											@endphp
											@endif
											<img src="{{$path}}" alt="{{$review->shop_name}}" width="50px" height="50px">
											<p class="title">{{ucfirst($review->shop_name)}}</p>
										</div>
									</div>
									<div class="col-lg-9 review-rest-cont">
										<div class="col-lg-12 col-sm-7 col-md-7 col-xs-7 ">
											<div class="table-responsive form-group">
												<p class="desc">{!!stripslashes(ucfirst($review->review_comments))!!}</p>
												<div class="starGroup">
												@for($i=1;$i<=$review->review_rating;$i++)
												<i class="fa fa-star"></i>
												@endfor
												</div>
												<p class="date">{{ date('jS M Y h:i a',strtotime($review->created_date))}}</p>
											</div>
										</div>
										<div class="col-lg-12 col-sm-2 col-md-2 col-xs-2 ">
											<div class="table-responsive form-group review-restaurant-btn">		                        	
												<a href="{{url('store').'/'.$review->store_slug}}">		          
												{{ Form::button(__(Session::get('front_lang_file').'.FRONT_VISIT_ST'))}}</a>
													@if($review->review_status == '1')
														<span class="approveSpan approved"><i class="fa fa-thumbs-up"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_APPROVE')</span>
													@elseif($review->review_status == '0')
														<span style="" class="approveSpan"><i class="fa fa-thumbs-down"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_DISAPPROVE')</span>
													@endif		                        	
											</div>
										</div>										
									</div>
								</div>
							</div>					
							
							
							
							<div class="clearfix"></div>
						@endforeach
					@endif
					</div>
					@if(count($store_reviews) > 0)
						{{ $store_reviews->render() }}
					@endif
				  </div>
				  {{-- product review --}}
				  <div role="tab" class="tab-pane fade" id="Product">

				  	<div class="row panel-heading">
				  		
					@if(count($pro_reviews) > 0) 
					
						@foreach($pro_reviews as $review) 
							
							<div class="col-lg-3 col-sm-3 col-md-3 col-xs-3 ">
		                        <div class="table-responsive form-group">
		                        	@php $path = url('').'/public/images/noimage/'.$no_product; 
									$filename = public_path('images/store/products/').$review->pro_image; 
									@endphp
									@if(file_exists($filename) && $review->pro_image != '')
									@php $path = url('').'/public/images/store/products/'.$review->pro_image;
									@endphp
									@endif
									<img src="{{$path}}" alt="{{$review->shop_name}}" width="50px" height="50px">
		                        	<p class="title">{{ucfirst($review->item_name).' '."in".' ' .ucfirst($review->shop_name)}}</p>
								</div>
							</div>
							<div class="col-lg-7 col-sm-7 col-md-7 col-xs-7 ">
		                        <div class="table-responsive form-group">
		                        	<p class="desc">{!!stripslashes(ucfirst($review->review_comments))!!}</p>
		                        	<div class="starGroup">
		                        	@for($i=1;$i<=$review->review_rating;$i++)
		                        	<i class="fa fa-star"></i>
		                        	@endfor
		                        	</div>
		                        	<p class="date">{{ date('jS M Y h:i a',strtotime($review->created_date))}}</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-2 col-md-2 col-xs-2 ">
		                        <div class="table-responsive form-group">
		                        	<a href="{{url('product-details').'/'.base64_encode($review->proitem_id)}}">
		                        		{{ Form::button(__(Session::get('front_lang_file').'.FRONT_VISIT_PRO'))}}
		                        	</a><br>
		                        		@if($review->review_status == '1')
		                        			<span class="approveSpan approved"><i class="fa fa-thumbs-up"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_APPROVE')</span>
		                        		@elseif($review->review_status == '0')
		                        			<span style="" class="approveSpan"><i class="fa fa-thumbs-down"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_DISAPPROVE')</span>
		                        		@endif
		                        	
								</div>
							</div>
							<div class="clearfix"></div>
						@endforeach
					@endif
					</div>
					@if(count($pro_reviews) > 0)
						{{ $pro_reviews->render() }}
					@endif
				  
				  </div>
				  {{-- item review --}}
				  <div role="tab" class="tab-pane fade" id="Item">
				  	<div class="row panel-heading">
				  		
					@if(count($item_reviews) > 0) 
					
						@foreach($item_reviews as $review) 
						
						<div class="col-lg-6 col-md-6 col-sm-12 col-12 review-rest-section">
							<div class="row">
								<div class="col-lg-3 col-sm-3 col-md-3 col-3 ">
									<div class="table-responsive form-group review-restaurant">
										@php $path = url('').'/public/images/noimage/'.$no_item; 
										$filename = public_path('images/restaurant/items/').$review->pro_image; 
										@endphp
										@if(file_exists($filename) && $review->pro_image != '')
										@php $path = url('').'/public/images/restaurant/items/'.$review->pro_image;
										@endphp
										@endif
										<img src="{{$path}}" alt="{{$review->shop_name}}">
									</div>
								</div>
								<div class="col-lg-9 col-md-9 col-sm-9 col-9 review-rest-cont">
									<div class="col-lg-12 col-sm-12 col-md-12 col-12 ">
										<div class="table-responsive form-group">
											<p class="title">{{ucfirst($review->item_name).' '."in".' ' .ucfirst($review->shop_name)}}</p>
											<p class="desc">{!!stripslashes(ucfirst($review->review_comments))!!}</p>
											<div class="starGroup">
											@for($i=1;$i<=$review->review_rating;$i++)
											<i class="fa fa-star"></i>
											@endfor
											</div>
											<p class="date">{{ date('jS M Y h:i a',strtotime($review->created_date))}}</p>
										</div>
									</div>
									<div class="col-lg-12 col-sm-12 col-md-12 col-12 ">
										<div class="table-responsive form-group review-restaurant-btn">
											<a href="{{url('').'/'.$review->store_slug.'/item-details/'.$review->pro_item_slug}}">
												{{ Form::button(__(Session::get('front_lang_file').'.FRONT_VISIT_IT'))}}
											</a>
												@if($review->review_status == '1')
													<span class="approveSpan  approved"><i class="fa fa-thumbs-up"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_APPROVE')</span>
												@elseif($review->review_status == '0')
													<span style="" class="approveSpan"><i class="fa fa-thumbs-down"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_DISAPPROVE')</span>
												@endif
											
										</div>
									</div>
								</div>
							</div>
						</div>
							
							
							
							
							<div class="clearfix"></div>
						@endforeach
					@endif
					</div>
					@if(count($item_reviews) > 0)
						{{ $item_reviews->render() }}
					@endif
				  </div>
				  {{-- order review --}}
				  <div role="tab" class="tab-pane fade" id="Order">
				  	<div class="row panel-heading">
				  		
					@if(count($or_reviews) > 0) 
						<div class="testimonials-item">
						@foreach($or_reviews as $review) 
							
								<div class="user row">
									<div class="col-md-3">
										<div class="user_image">
											@php $path = url('').'/public/images/noimage/default_user.png'; 
											@endphp
											<img src="{{$path}}" alt="" width="50px" height="50px">
											<p class="title">{{ucfirst($review->deliver_name)}}</p>
										</div>
									</div>
									<div class="testimonials-caption col-md-7 table-responsive">
										<div class="user_text">
											<p class="mbr-text mbr-fonts-style mbr-lighter display-7">
												<em>"{!!stripslashes(ucfirst($review->review_comments))!!}"</em>
											</p>
										</div>
										<div class="user_name mbr-bold mbr-fonts-style pt-3 display-7">
											@for($i=1;$i<=$review->review_rating;$i++)
												<i class="fa fa-star"></i>
											@endfor
										</div>
										<div class="user_desk mbr-light mbr-fonts-style pt-2 display-7">
											<p class="date" style="float:left">{{ date('jS M Y h:i a',strtotime($review->created_date))}}</p>
										</div>
									</div>
									<div class="col-md-2 table-responsive">
										@if($review->review_status == '1')
											<span class="approveSpan approved"><i class="fa fa-thumbs-up"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_APPROVE')</span>
										@elseif($review->review_status == '0')
											<span style=""  class="approveSpan"><i class="fa fa-thumbs-down"></i>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_DISAPPROVE')</span>
										@endif
									</div>
								</div>
							
		
							<div class="clearfix"></div>
						@endforeach
						</div>
					@endif
					</div>
					@if(count($or_reviews) > 0)
						{{ $or_reviews->render() }}
					@endif
				  </div>
				</div>
				{{--  --}}
			</div> 
			</div>
		</div>
	</div>
</div>


@stop