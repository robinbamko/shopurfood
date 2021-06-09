@extends('Front.layouts.default')
@section('content')
<style>
	.tab-content h5{
    font-size: 13px !important;
	line-height:1.8 !important;
	font-weight: normal;
	}
	input.hidden {
		display: none;
	}
</style>

<div id="mySuxesMsg"></div> 	

@if(empty($shop_details) === false)	
<div class="main-sec">
	{{ Form::hidden('st_id',$shop_details->id,['id' => 'store_id'])}}
	@if($shop_details->store_closed == "Avail")
	
	<div class="banner-slider">	
		@php $banner_image = explode('/**/',$shop_details->st_banner,-1); @endphp
		<div class="container">
			<div class="row">
				
				<div class="slider-caption">	
					<div class="slider-logo">
						
						<!-- <img src="'.$url.'/../../../public/front/images/slider-logo.png"> -->
						<!-- <h2>{{ $shop_details->st_logo}}</h2> -->
						<img src="{{ url('public/images/restaurant').'/'.$shop_details->st_logo}}">
					</div>
					<div class="slider-content">
						<h4><?php echo $shop_details->st_name; ?><a class="btn">@lang(Session::get('front_lang_file').'.FRONT_OPEN')</a></h4>
						<p>{{ $shop_details->cate_name }}</p>
						<ul>
							@php $min = '' @endphp
							@if($shop_details->st_minimum_order != '')
							<li><img src="{{url('')}}/public/front/images/right-icon.png"> @lang(Session::get('front_lang_file').'.FRONT_MIN') {{$shop_details->st_currency}} {{ $shop_details->st_minimum_order }} </li>
							@else
							<li><img src="{{url('')}}/public/front/images/right-icon.png"> @lang(Session::get('front_lang_file').'.FRONT_MIN') {{$shop_details->st_currency}} 0.00 </li>
							@endif
							<li><img src="{{url('')}}/public/front/images/bike-icon.png"> {{ $shop_details->st_delivery_time }} {{ $shop_details->st_delivery_duration }} </li>
							
							<li class="star-des">
								<img src="{{url('')}}/public/front/images/star1.png">
								@if($shop_details->avg_val != '')
									@if($shop_details->avg_val == '5')
										<span>{{ intval($shop_details->avg_val) }}</span>
									@elseif(filter_var($shop_details->avg_val, FILTER_VALIDATE_INT) && $shop_details->avg_val != '5')
										<span>{{ intval($shop_details->avg_val) }}.0</span>
									@else
										<span>{{ intval($shop_details->avg_val) }}</span>
									@endif
								@else     
									<span><i class="fa fa-star-o" title="@lang(Session::get('front_lang_file').'.FRONT_NO_RATINGS')" aria-hidden="true"></i></span>
								@endif
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
			echo '<input id="dir" type="hidden" value="'.$dir.'">';
		?>			
		
		<section class=" slider your-carousel" id="bannerRest" dir="<?php echo $dir; ?>" >
			@php $path = url('').'/public/images/noimage/'.$no_reStoreDetailbanner; @endphp
			@if(count($banner_image) > 0) 
			@for($i=0;$i<count($banner_image); $i++)
			@if(isset($banner_image[$i]))
			@php $filename = public_path('images/restaurant/banner/').$banner_image[$i]; 
			@endphp
			@if(file_exists($filename))
			@php $path = url('').'/public/images/restaurant/banner/'.$banner_image[$i]; @endphp
			@endif
			@endif
			
			<div style="background: url('{{$path}}'); background-size: cover; background-position: center; width:100%;
			height:100%;"></div>
			
			
			@endfor
			@else
			<div style="background: url('{{$path}}'); background-size: cover; background-position: center;width:100%;
			height:100%;"></div>
			
			
		</section>
		<!-- <div class="container">
			<div class="row">
			<div class="slider-caption">
			<div class="slider-logo">
			<img src="'.$url.'/../../../public/front/images/slider-logo.png">
			</div>
			<div class="slider-content">
			<h4>JOOMA MEET RESTAURANT<a href="" class="btn">OPEN</a></h4>
			<p>Burgers, American, Sandwiches, Fast Food, BBQ</p>
			<ul>
			@php $min = ''; @endphp
			@if($shop_details->st_minimum_order != '')
			<li><img src="'.$url.'/../../../public/front/images/right-icon.png"> Min $ 10,00 </li>
			@endif
			<li><img src="'.$url.'/../../../public/front/images/bike-icon.png"> 30 min </li>
			<li class="star-des"><img src="'.$url.'/../../../public/front/images/star.png"> <span>4.5</span></li>
			</ul>
			</div>
			</div>
			</div>
		</div> -->
		@endif
		
	</div>
	@else
	<div class="closed-bg" style="background:url({{url('/public/front/images/wood.jpg')}}); background-size: cover; height:300px; background-position:center;">		
	</div>
	@endif
	
	<div class="section8">
		<div class="container">
			<div class="row">
				<div class="col-lg-3  section8-heading">
					<!--<p>{{ucfirst($shop_details->st_name)}}</p> 
					<a href="#" data-toggle="modal" data-target="#about-modal">@lang(Session::get('front_lang_file').'.ABOUT')</a>-->
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{url('')}}">@lang(Session::get('front_lang_file').'.HOME')</a></li>
							<li class="breadcrumb-item" style=" display: block;max-width: 100px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><a href="{{url('all-categories').'/'.base64_encode($shop_details->cate_id)}}" title="{{ $shop_details->cate_name }}" >{{ $shop_details->cate_name }}</a></li>
							<li class="breadcrumb-item active" aria-current="page" style=" display: block;max-width: 100px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" title="{{ $shop_details->st_name }}">{{ $shop_details->st_name }}</li>
						</ol>
					</nav>						
				</div>
				
				
				
				<div class="col-lg-3 col-md-6 col-sm-6 section8-search" style="">
					<input type="text" Placeholder="@lang(Session::get('front_lang_file').'.FRONT_SEARCH_ITEM')" id="itemSearch">
					<i class="fa fa-times" style="" id="clearSearch"><a href="javascript:clearItemSearch();">Clear</a></i>					
				</div>
				
				<div class="col-lg-6 col-md-6 col-sm-6 section8-filter">

					
					<span class="button-checkbox"><button type="button" class="btn" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="fa fa-tag" style="color: #00800a;"></i> @lang(Session::get('front_lang_file').'.FRONT_TOP_OFFER')</button><input type="checkbox" name="top_discount_check" value="2" class="hidden"  /></span>
					
					<span class="button-checkbox"><button type="button" class="btn" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="fa fa-leaf"></i>@lang(Session::get('front_lang_file').'.FRONT_PRO_VEG')</button><input type="checkbox" name="food_type_filter[]" value="1" class="hidden"  /></span>
					
					<span class="button-checkbox"><button type="button" class="btn" data-color="danger" style="padding: 3px 12px 3px 0px;"><img src="{{url('')}}/public/front/images/non-veg-icon.png">  &nbsp; @lang(Session::get('front_lang_file').'.FRONT_PRO_NONVEG')</button><input type="checkbox" name="food_type_filter[]" value="2" class="hidden"  /></span>
					<button data-toggle="modal" data-target="#about-modal"><i class="fa fa-info-circle"></i>@lang(Session::get('front_lang_file').'.ABOUT')</button>
					<!--<div class="rest-about-popup"><a href="#" data-toggle="modal" data-target="#about-modal">@lang(Session::get('front_lang_file').'.ABOUT')</a></div>-->
				</div>
				
				
				
				
			</div>
		</div>
	</div> 
	<div class="container">
		<div class="row">
			<div id="wrapper" class="wrapper">
				<!-- Sidebar -->
				<div id="sidebar-wrapper">
					<div class="sidebar-brand sidebar-head">@lang(Session::get('front_lang_file').'.FRONT_ORDER_CATEGORY')<i class="fa fa-angle-down"></i></div>
					<ul class="sidebar-nav nav nav-tabs" id="sidebar-nav">
						@php $mainCatName=''; $maincatId=''; $mc_product_count =''; @endphp
						@if(count($get_category_details) > 0) {{-- Main category --}}
						@php $mainCatCount = 0 @endphp
						@foreach($get_category_details as $main_cate)
						@if($mainCatCount==0)
						@php 
						$maincatId = $main_cate->pro_mc_id;
						$mainCatName = ucfirst($main_cate->mc_name);
						@endphp
						@endif
						@php
						$sql = DB::table('gr_product')->select('pro_id')
								->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
								->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
								->where('pro_store_id','=',$shop_details->id)
								->where('gr_proitem_maincategory.pro_mc_status','=','1')
								->where('gr_proitem_maincategory.pro_mc_type','=',1)
								->where('gr_product.pro_status','=','1')
								->where('gr_proitem_subcategory.pro_sc_status','=','1')
								->whereRaw(('gr_proitem_subcategory.pro_main_id=gr_product.pro_category_id'));
						if(INVENTORY_STATUS == '0')
						{
						$sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
						}
						$mc_product = $sql->get();  
						$mc_product_count = $mc_product->count();  
						@endphp
						<li>
							<a href="javascript:;" id="mainCatSelId_{{$main_cate->pro_mc_id}}" onClick = "set_main_cate({{$main_cate->pro_mc_id}},'{{$main_cate->mc_name}}');" class="maincatA @if($mainCatCount==0) active @endif">{{ucfirst($main_cate->mc_name)}}</a>
							@php $get_sub_cate_details = get_sub_categories($main_cate->pro_mc_id,$shop_details->id,1); @endphp
							@if(count($get_sub_cate_details) > 0) {{-- Sub category --}}
							<i class="fa fa-angle-double-right"></i>
							<ul class="sub-menu">
								@foreach($get_sub_cate_details as $sub)
								@if($sub->pro_main_id == $main_cate->pro_mc_id)
								<li><a href="javascript:;" id="subCatSelId_{{$sub->pro_main_id}}_{{$sub->pro_sc_id}}" onClick = "set_sub_cate({{$sub->pro_main_id}},{{$sub->pro_sc_id}},'{{$main_cate->mc_name}}','{{ucfirst($sub->sc_name)}}');" class="subcatA">{{ucfirst($sub->sc_name)}}</a></li>
								@endif
								@endforeach
							</ul>
							@endif
						</li>
						@php $mainCatCount++; @endphp
						@endforeach
						@else
						<li>
							<a href="#seafood">@lang(Session::get('front_lang_file').'.FRONT_NO_CATE_FOUND')</a>
						</li>
						@endif
						{{ Form::hidden('cate_id','',['id' =>'category_id'])}}
						{{ Form::hidden('sub_cate_id','',['id' =>'sub_category_id'])}}
					</ul>
				</div>
				<!-- /#sidebar-wrapper -->
				<!-- Page Content -->
				<div class="tab-content" id="dynamictabstrp">
					<div class="tab-pane active" id="seafood">
						<div class="container-fluid">
							<div class="row">
								<input type="hidded"  style="display: none"name="main_catid" id="main_catid" value="{{ $maincatId }}">
								<input type="hidded" style="display: none" name="main_catname" id="main_catid" value="{{ $mainCatName }}">
								<div class="col-md-12 item-head">
									<?php //echo  Session::get('front_lang_file').'.FRONT_ALL_ITEMS';?>
									{{--   --}}
									
									<!-- <h5 id="cate_name">{{ $mainCatName }}<span id="cate_item">{{ $mc_product_count }} Items</span></h5> -->
									@if($mc_product_count > 0)
									<h5><span id="cate_name">@lang(session::get('front_lang_file').'.FRONT_ALL'){{-- $mainCatName --}}</span><span id="cate_item">{{ $mc_product_count }}</span><span>@lang(Session::get('front_lang_file').'.FRONT_ITEMS');</span></h5>
									@endif
									<!-- {{ Form::button('View All',['class'=>'btn view-all','onClick' => 'show_all()','style' => 'float:right;'])}} -->
									
									{{ Form::select('sort',['all' => __(Session::get('front_lang_file').'.FRONT_SORT_BY'),'new' => __(Session::get('front_lang_file').'.FRONT_BY_NEW'),'low_high' => __(Session::get('front_lang_file').'.FRONT_BY_LTOH'),'high_low' => __(Session::get('front_lang_file').'.FRONT_BY_HTOL')],'',['id' =>'sort_by','onChange' => 'load_more(1);'])}}
									
									{{ Form::button(__(Session::get('front_lang_file').'.FRONT_VIEW_ALL'),['class'=>'btn view-all','onClick' => 'show_all()','style' => 'float:right;'])}}
									
								</div>
								
								<div class="col-md-12 item-list">									
									<div class="row" id="load_data"></div>
								</div>
								<div>
								<span class="ajax-loading"/> @lang(session::get('front_lang_file').'.LOAD')</span>
							</div>
						</div>
					</div>
				</div>
				
				
				
				<!-- About Modal Popup -->
				<div id="about-modal" class="modal fade about-modal-rest" role="dialog">
					
					
					<div class="modal-dialog">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<!-- Modal content-->
						<div class="modal-content">
							<!--<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button></div>	-->
							<div class="modal-body">
								<div class="about-modal-img">
									@php $path = url('').'/public/images/noimage/'.$no_shop_logo; 
									$filename = public_path('images/restaurant/').$shop_details->st_logo; 
									@endphp
									@if(file_exists($filename) && $shop_details->st_logo != '')
									@php $path = url('').'/public/images/restaurant/'.$shop_details->st_logo;
									@endphp
									@endif
									<img src="{{$path}}" alt="{{$shop_details->st_name}}">				
								</div>
								<div class="about-tab">
									<ul class="tabs">
										<li class="tab-link current" data-tab="tab-1">
											@lang(Session::get('front_lang_file').'.FRONT_INFO')
										</li>
										<li class="tab-link" data-tab="tab-2">
											@lang(Session::get('front_lang_file').'.FRONT_WKING_HRS')
										</li>
										<li class="tab-link" data-tab="tab-3">
											@lang(Session::get('front_lang_file').'.FRONT_REVIEW')
										</li>					
									</ul>
									<div id="tab-1" class="tab-content current">
										<p class="resDesc">
											{!!ucfirst($shop_details->st_desc)!!}
											{{--{!!ucfirst(str_limit(strip_tags($shop_details->st_desc),250))!!}--}}
										</p><br>
										<table>
											@php $duration = $ref_sts = $cancel_status = '';@endphp
											@if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en')
											@php $duration = $shop_details->st_delivery_duration;
											$ref_sts = $shop_details->refund_status;
											$cancel_status = $shop_details->cancel_status;
											@endphp
											@else
											@if($shop_details->st_delivery_duration == 'hours')
											@php $duration = __(Session::get('front_lang_file').'.FRONT_HOURS')@endphp
											@elseif($shop_details->st_delivery_duration == 'minutes')
											@php $duration = __(Session::get('front_lang_file').'.FRONT_MINUTES')@endphp
											@endif
											
											@if($shop_details->refund_status == 'Yes')
											@php $ref_sts = __(Session::get('front_lang_file').'.FRONT_YES')@endphp
											@elseif($shop_details->refund_status == 'No')
											@php $ref_sts = __(Session::get('front_lang_file').'.FRONT_NO')@endphp
											@endif
											
											@if($shop_details->cancel_status == 'Yes')
											@php $cancel_status = __(Session::get('front_lang_file').'.FRONT_YES')@endphp
											@elseif($shop_details->cancel_status == 'No')
											@php $cancel_status = __(Session::get('front_lang_file').'.FRONT_NO')@endphp
											@endif
											@endif
											
											<tr>
												<td width="23%">
												@lang(Session::get('front_lang_file').'.FRONT_MIN_ORDER') : </td>
												<td>
													@if($shop_details->st_minimum_order != '') {{ $shop_details->st_currency}} &nbsp; {{ $shop_details->st_minimum_order}} @else {{"-"}} @endif
												</td>
											</tr>
											<tr>
												<td>
												@lang(Session::get('front_lang_file').'.FRONT_PRE_ORDER') : </td>
												<td>
													{{ ($shop_details->st_pre_order == '1') ? __(Session::get('front_lang_file').'.FRONT_YES') : __(Session::get('front_lang_file').'.FRONT_NO')  }}
												</td>
											</tr>
											<tr>
												<td>
												@lang(Session::get('front_lang_file').'.FRONT_DELI_TIME') : </td>
												
												
												
												
												<td>
													@if($shop_details->st_delivery_time != '') {{ $shop_details->st_delivery_time}}&nbsp; {{ $duration }} @else {{ "-"}} @endif
												</td>
											</tr>
											<tr>
												<td>
												@lang(Session::get('front_lang_file').'.FRONT_RATINGS') : </td>
												<td>
													@for($i=1;$i<=$shop_details->st_rating;$i++)
														<i class="fa fa-star checked"></i>
														@endfor
													</td>
												</tr>
												<tr>
													
													<td>
													@lang(Session::get('front_lang_file').'.FRONT_CANCEL_POLICY') : </td>
													<td>
														
														{!! ($shop_details->mer_cancel_policy != '') ? ucfirst($shop_details->mer_cancel_policy)  : "-" !!}
													</td>
												</tr>
												<tr>
													<td>
													@lang(Session::get('front_lang_file').'.FRONT_REFUND') : </td>
													<td>
														{{ ($ref_sts != '') ? ucfirst($ref_sts)  : "-" }}
													</td>
												</tr>
												<tr>
													<td>@lang(Session::get('front_lang_file').'.FRONT_CANCEL_ALLOWED') : </td>
													<td>
														{{ ($cancel_status != '') ? ucfirst($cancel_status)  : "-" }}
													</td>
												</tr>
												
												<tr>
													<td>
													@lang(Session::get('front_lang_file').'.ADMIN_LOCATION') : </td>
													<td>
														{{ ($shop_details->st_address != '') ? ucfirst($shop_details->st_address)  : "-" }}
													</td>
												</tr>
											</table>
										</div>
										<div id="tab-2" class="tab-content">
											<table>
												@if(count($get_wk_hrs) > 0)
												@for($i = 1;$i<=7;$i++)
												
												<tr>
													<td>
													@lang(Session::get('front_lang_file').'.ADMIN_DAY'.$i) </td>
													<td>{{$get_wk_hrs[$i-1]->wk_start_time}}&nbsp;{{"-"}}&nbsp;{{$get_wk_hrs[$i-1]->wk_end_time}}</td>
												</tr>
												
												
												@endfor
												@else
												@lang(Session::get('front_lang_file').'.FRONT_NO_DETAILS_FOUND')
												@endif
											</table>
										</div>
										<div id="tab-3" class="tab-content">
											@if(count($all_reviews) > 0)
											@foreach($all_reviews as $review)
											<div class="row">
												<div class="col-md-2">
													@php $path = url('').'/public/images/noimage/user.png';
													@endphp
													@if($review->cus_image != '')
													@php  $filename = public_path('images/customer/').$review->cus_image; 
													@endphp
													@if(file_exists($filename))
													@php $path = url('').'/public/images/customer/'.$review->cus_image;
													@endphp
													@endif
													@endif
													<img src="{{$path}}" width="50px" height= "50px">
												</div>
												<div class="col-md-10">
													<p>{{ucfirst($review->review_comments)}}</p>
													@if($review->review_rating != '')
													@for($i=1;$i<=$review->review_rating;$i++)
														<i class="fa fa-star checked"></i>
														@endfor
														@endif
														<h6>{{ucfirst($review->cus_fname)}}</h6>
														
													</div>
												</div>
												@endforeach
												@else
												@lang(Session::get('front_lang_file').'.FRONT_NO_DETAILS_FOUND')
												@endif
												{{ $all_reviews->render()}}
											</div>
										</div>
									</div>			 
								</div>
								
							</div>
						</div>
						
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="food_veg_nonveg" value="">
	@endif
	
	
	
	@section('script')
	<!--<link rel="stylesheet" href="{{url('')}}/public/front/css/auto-complete.css">
		<script src="{{url('')}}/public/front/js/auto-complete.js"></script>
		
	<script src="{{url('')}}/public/front/js/bootstrap-typeahead.js"></script>-->
	<script src='{{url('public/front/js')}}/jquery-ui.min.js'></script>
	<script>
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});
		
		$('#sidebar-nav a').click(function (e) {
			e.preventDefault()
			$(this).tab('show');
		})	
		
		if (jQuery(window).width() < 767)
		{
			$('.sidebar-brand').click(function(){
				$('#sidebar-nav').slideToggle('fast');
			})
		}
		
		
		if($(window).width() <= 767){
			$('.sub-menu').hide();
			$("#sidebar-nav li:has(ul)").click(function(){
				$("ul",this).slideToggle('');
			});
		}	
		
	</script>
	
	<!-- ABOUT MODAL POPUP -->
	
	<script>
		$(document).ready(function(){
			
			$('#about-modal ul.tabs li').click(function(){
				var tab_id = $(this).attr('data-tab');
				
				$('#about-modal ul.tabs li').removeClass('current');
				$('#about-modal .tab-content').removeClass('current');
				
				$(this).addClass('current');
				$("#"+tab_id).addClass('current');
			});
			/*AUTO COMPLETE*/
			//autocomplete_fun();
			/*$("#itemSearch").autocomplete({
				source: function(request, response) {
				$.getJSON("{{url('')}}/getItemName", { ca_id: ca_id }, 
				response);
				},
				minLength: 2,
				select: function(event, ui){
				//action
				
				}
			});*/
			/*$( "#itemSearch" ).autocomplete({
				source: function( request, response ) {
				$.ajax( {
				url: "search.php",
				dataType: "jsonp",
				data: {
				term: request.term
				},
				success: function( data ) {
				response( data );
				}
				} );
				},
				minLength: 2,
				select: function( event, ui ) {
				log( "Selected: " + ui.item.value + " aka " + ui.item.id );
				}
			} );*/
			//$('#itemSearch').keyup(function(){
			// $('#itemSearch').bind('keyup change', function () {
			
			$(document).ready(function(){
				
					$('.button-checkbox').each(function () {
						// Settings
						var $widget = $(this),
							$button = $widget.find('button'),
							$checkbox = $widget.find('input:checkbox'),
							color = $button.data('color'),
							settings = {
								on: {
									icon: ''//glyphicon glyphicon-check
								},
								off: {
									icon: ''//glyphicon glyphicon-unchecked
								}
							};

						// Event Handlers
						$button.on('click', function () {
							$checkbox.prop('checked', !$checkbox.is(':checked'));
							$checkbox.triggerHandler('change');
							updateDisplay();
						});
						$checkbox.on('change', function () {
							updateDisplay();
							vegNonVeg();
						});

						// Actions
						function updateDisplay() {
							var isChecked = $checkbox.is(':checked');

							// Set the button's state
							$button.data('state', (isChecked) ? "on" : "off");

							// Set the button's icon
							$button.find('.state-icon')
								.removeClass()
								.addClass('state-icon ' + settings[$button.data('state')].icon);

							// Update the button's color
							if (isChecked) {
								$button
									.removeClass('btn-default')
									.addClass('btn-' + color + ' active');
							}
							else {
								$button
									.removeClass('btn-' + color + ' active')
									.addClass('btn-default');
							}
							//
						}

						// Initialization
						function init() {

							updateDisplay();

							// Inject the icon if applicable
							if ($button.find('.state-icon').length == 0) {
								$button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
							}
						}
						init();
					});
					
					//TOP OFFER BUTTON
				});
			/* food type filter starts */
			function vegNonVeg()
			{
				var searchIDs = $("input[name='food_type_filter[]']:checked").map(function(){
				  return $(this).val();
				}).get(); 
				if(searchIDs!='') { var pro_veg = searchIDs; } else { var pro_veg = ''; } 
				
				if($("input[name='top_discount_check']").is(":checked")){ var top_disc = 1; } else{ var top_disc = 0; }

				var ca_id = document.getElementById('category_id').value;
				var sub_ca_id = document.getElementById('sub_category_id').value;
				var st_id = document.getElementById('store_id').value;
				var sortby = document.getElementById('sort_by').value;
				var text = document.getElementById('itemSearch').value;
				// var veg = document.getElementById('vegSearch').value;
				
				$('#food_veg_nonveg').val('2');
				$.ajax({
					url: '?page=1',
					data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'pro_veg': pro_veg,'text':text,'top_disc':top_disc},
					type: "get",
					datatype: "html",
					beforeSend: function()
					{
						
						$('.ajax-loading').show();
					}
				})
				.done(function(data){
					var details = data.split("~`");
					if(details[0].length == 0)
					{
						console.log(details[0].length);
						$('.ajax-loading').show();
						//notify user if nothing to load
						$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
						return;
					}
					$('.ajax-loading').hide();
					$("#load_data").html(details[0]); //add data into #results element
					$('[data-toggle="tooltip"]').tooltip(); 
					page_count = details[1];
					$('#cate_item').html(details[2]);
				})
				.fail(function(jqXHR, ajaxOptions, thrownError){
					//alert('No response from server');
				});
			}
			/*filter veg start */
			$('#vegSearch').on('click',function(){
				
				//load_more(1);
				var ca_id = document.getElementById('category_id').value;
				var sub_ca_id = document.getElementById('sub_category_id').value;
				var st_id = document.getElementById('store_id').value;
				var sortby = document.getElementById('sort_by').value;
				var text = document.getElementById('itemSearch').value;
				// var veg = document.getElementById('vegSearch').value;
				var pro_veg = '1';
				$('#food_veg_nonveg').val('1');
				$.ajax({
					url: '?page=1',
					data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'pro_veg': pro_veg,'text':text},
					type: "get",
					datatype: "html",
					beforeSend: function()
					{
						
						$('.ajax-loading').show();
					}
				})
				.done(function(data){
					var details = data.split("~`");
					if(details[0].length == 0)
					{
						// console.log(details[0].length);
						$('.ajax-loading').show();
						//notify user if nothing to load
						$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
						return;
					}
					$('.ajax-loading').hide();
					$("#load_data").html(details[0]); //add data into #results element
					$('[data-toggle="tooltip"]').tooltip(); 
					page_count = details[1];
					$('#cate_item').html(details[2]);
					
				})
				.fail(function(jqXHR, ajaxOptions, thrownError){
					//alert('No response from server');
				});
				$('#vegSearch').css({'background-color': '#e6450c','color':'white'});
				$('#nonvegSearch').css({'background-color': '#f8f9fad4','color':'black'});
			});    
			
			/*filter veg end */
			
			
			/*filter non veg start */
			$('#nonvegSearch').on('click',function(){
				
				//load_more(1);
				var ca_id = document.getElementById('category_id').value;
				var sub_ca_id = document.getElementById('sub_category_id').value;
				var st_id = document.getElementById('store_id').value;
				var sortby = document.getElementById('sort_by').value;
				var text = document.getElementById('itemSearch').value;
				// var veg = document.getElementById('vegSearch').value;
				var pro_veg = '2';
				$('#food_veg_nonveg').val('2');
				$.ajax({
					url: '?page=1',
					data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'pro_veg': pro_veg,'text':text},
					type: "get",
					datatype: "html",
					beforeSend: function()
					{
						
						$('.ajax-loading').show();
					}
				})
				.done(function(data){
					var details = data.split("~`");
					if(details[0].length == 0)
					{
						console.log(details[0].length);
						$('.ajax-loading').show();
						//notify user if nothing to load
						$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
						return;
					}
					$('.ajax-loading').hide();
					$("#load_data").html(details[0]); //add data into #results element
					$('[data-toggle="tooltip"]').tooltip(); 
					page_count = details[1];
					$('#cate_item').html(details[2]);
					
				})
				.fail(function(jqXHR, ajaxOptions, thrownError){
					//alert('No response from server');
				});
				$('#nonvegSearch').css({'background-color': '#e6450c','color':'white'});
				$('#vegSearch').css({'background-color': '#f8f9fad4','color':'black'});
			});
			
			
			/*filter non veg end */
			
			
			/*search item start */
			$('#itemSearch').bind('keyup change', function () {
				//alert('s');
				//load_more(1);
				var ca_id = document.getElementById('category_id').value;
				var sub_ca_id = document.getElementById('sub_category_id').value;
				var st_id = document.getElementById('store_id').value;
				var sortby = document.getElementById('sort_by').value;
				var text = document.getElementById('itemSearch').value;
				//var pro_veg = $('#food_veg_nonveg').val();
				var searchIDs = $("input[name='food_type_filter[]']:checked").map(function(){
					  return $(this).val();
					}).get(); 
				if(searchIDs!='') { var pro_veg = searchIDs; } else { var pro_veg = ''; } 
				if($("input[name='top_discount_check']").is(":checked")){ var top_disc = 1; } else{ var top_disc = 0; }
				
				$.ajax({
					url: '?page=1',
					data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'text':text,'pro_veg':pro_veg,'top_disc':top_disc},
					type: "get",
					datatype: "html",
					beforeSend: function()
					{
						$('.ajax-loading').show();
					}
				})
				.done(function(data){
					var details = data.split("~`");
					if(details[0].length == 0)
					{
						console.log(details[0].length);
						$('.ajax-loading').show();
						//notify user if nothing to load
						$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
						return;
					}
					$('.ajax-loading').hide();
					$("#load_data").html(details[0]); //add data into #results element
					$('[data-toggle="tooltip"]').tooltip(); 
					page_count = details[1];
					$('#cate_item').html(details[2]);
				})
				.fail(function(jqXHR, ajaxOptions, thrownError){
					// alert('No response from server');
				});
				//get_pro_count(ca_id,sub_ca_id);
			});
			
			/*search item end */
		});
		
		
		/*function autocomplete_fun(){
			alert('s');
			var demo1 = new autoComplete({
			selector: '#itemSearch',
			minChars: 1,
			source: function(term, suggest){
			term = term.toLowerCase();
			var ca_id = document.getElementById('category_id').value;
			var sub_ca_id = document.getElementById('sub_category_id').value;
			var st_id = document.getElementById('store_id').value;
			$.getJSON('{{url('')}}/getItemName?ca_id='+ca_id+'&st_id='+st_id+'&sub_ca_id='+sub_ca_id, { query: term }, function(data){ 
			suggest(data); 
			});
			},
			renderItem: function (item, search){
			search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&amp;');
			var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
			return '<div class="autocomplete-suggestion" data-langname="'+item[0]+'" data-lang="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
			},
			onSelect: function(e, term, item){
			console.log('Item "'+item.getAttribute('data-langname')+' ('+item.getAttribute('data-lang')+')" selected by '+(e.type == 'keydown' ? 'pressing enter' : 'mouse click')+'.');
			document.getElementById('food_type_'+menu_id).value = item.getAttribute('data-langname');
			document.getElementById('food_type_ph_'+menu_id).value=item.getAttribute('data-lang');
			}
			});
		}*/
		function displayResult(item) {
			//item.value
			$('#clearSearch').show();
			load_more(1);
		}
		function clearItemSearch()
		{
			$('#itemSearch').val('');
			$('#clearSearch').hide();
			load_more(1);
		}
	</script>
	
	<!-- ADD/MINUS PLUGIN -->
	<script>
		
		$('.quantity').bind('keyup blur',function(){ 
			var node = $(this);
			node.val(node.val().replace(/[^0-9]/g,'') ); 
			///alert(node.val().replace(/[^0-9]/g,''));
		});
		
		function isNumberKey(evt)
		{
		    var charCode = (evt.which) ? evt.which : event.keyCode;
		    //alert(charCode);  
		    if (charCode > 31 && (charCode < 48 || charCode > 57 ) )
		    {
				/*  if(charCode!=46)*/
				return false; 
			}
		    
			return true;
			
		}
	 	function plus(i,max){
			var countEl = document.getElementById("count"+i).value;
			if(countEl < max )
			{
				countEl++;
			}
			$('#count'+i).val(countEl);
		}
		function minus(i){
			var countEl = document.getElementById("count"+i).value;
			if (countEl > 1) { 
				countEl--;
				$('#count'+i).val(countEl);
			}  
		}
		function checkQuantity(gotVal,i){
			if(gotVal==''){
				$('#count'+i).val('1');
			}
		}
		/*function chk_qty(i)
			{ 
			var out = true;	
			var max = document.getElementById("max_qty"+i).value;
			var countEl = document.getElementById("count"+i).value;
			var pro_id = document.getElementById("product_id"+i).value;
			var st_id = document.getElementById("store_id"+i).value;
			if(countEl == 0 || countEl == '')
			{	
			$('#count'+i).css({'border' : '1px solid red'});
			return false;
			}
			else if(parseInt(countEl) > parseInt(max))
			{
			$('#count'+i).css({'border' : '1px solid red'});
			$('#err'+i).html("@lang(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')");
			$('#err'+i).css({'color' : 'red'});
			return false;
			}
			else
			{	
			
			$.ajax({
			'type' : 'get',
			'async' : false,
			'data' : {'pro_id' : pro_id,'st_id' : st_id},
			'url'	: '<?php echo url('cart_update_chk_qty'); ?>',
			success:function(response)
			{
			var total_count = parseInt(countEl)+parseInt(response);
			
			if(total_count > max)
			{
			$('#count'+i).css({'border' : '1px solid red'});
			$('#err'+i).html("@lang(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')&nbsp;@lang(Session::get('front_lang_file').'.FRONT_HAVE_CART')&nbsp;" + response);
			$('#err'+i).css({'color' : 'red'});
			out = false;
			
			}
			else
			{
			out =  true;
			
			}
			}
			});
			
			return out;
			}
		}*/
	</script>
	
	<script>
		$(document).ready(function(){
			/*var mc_id = $('#main_catid').val();
			var mc_name = $('#main_catname').val();
			
			$('#category_id').val(mc_id);
			$('#sub_category_id').val('');
			$('#cate_name').html(mc_name);*/
			// alert(mc_id);
			$('.maincatA').removeClass('active');
			$('#itemSearch').val('');
			//$('#mainCatSelId_'+mc_id).addClass('active');
			//load_more(1);   //load items basec on main category
			$('html, body').animate({ 'scrollTop' : 175 });
			//autocomplete_fun();
			//get_pro_count(mc_id,0);
			
		});
		var page = 1; //track user scroll as page number, right now page number is 1
		load_more(page); //initial content load
		var page_count = 1;
		$(window).scroll(function() { //detect page scroll
			if($(window).scrollTop() + $(window).height() >= $(document).height()) { //if user scrolled from top to bottom of the page 
				//alert(page);
				if(page <= page_count)
				{
					page++; //page number increment
					load_more(page); //load content   
				}     
			}
		}); 
		
		function load_more(page1){
			page = page1;
			
			var ca_id = document.getElementById('category_id').value;
			var sub_ca_id = document.getElementById('sub_category_id').value;
			var st_id = document.getElementById('store_id').value;
			var sortby = document.getElementById('sort_by').value;
			var text = document.getElementById('itemSearch').value;
			
			var searchIDs = $("input[name='food_type_filter[]']:checked").map(function()
			{
			  return $(this).val();
			}).get(); 
			if(searchIDs!='')
			{ 
				var pro_veg = searchIDs; 
			}
			else 
			{ 
				var pro_veg = ''; 
			} 
			if($("input[name='top_discount_check']").is(":checked"))
			{ 
				var top_disc = 1; 
			}
			else
			{ 
				var top_disc = 0; 
			}
			//alert(ca_id);
			$.ajax({
				url: '?page=' + page,
				data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'text':text,'pro_veg':pro_veg,'top_disc':top_disc},
				type: "get",
				datatype: "html",
				beforeSend: function()
				{
					$('.ajax-loading').show();
				}
				}).done(function(data){
				$('.ajax-loading').hide(); //hide loading animation once data is received
				var details = data.split("~`");
				if(details[0].length == 0)
				{
					console.log(details[0].length);
					$('.ajax-loading').show();
					//notify user if nothing to load
					$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
					return;
				}
				if(page == 1)
				{            	
					$("#load_data").html(details[0]); //add data into #results element
				}    
				else
				{
					$("#load_data").append(details[0]);	//append data into #results element
				}
				$('[data-toggle="tooltip"]').tooltip(); 
				page_count = details[1];
				
				}).fail(function(jqXHR, ajaxOptions, thrownError){
				//alert('No response from server');
			});
		}	
		
		/** clear quantity **/
			function clear_data(i)
			{
				$('#count'+i).val('1');
				$('#count'+i).css({'border' : '1px solid #eee'});
				$('#err'+i).html('');
			}
			/** set main category **/
				function set_main_cate(mc_id,mc_name)
				{
					$('#category_id').val(mc_id);
					$('#sub_category_id').val('');
					$('#cate_name').html(mc_name);
					$('.maincatA').removeClass('active');
					$('#itemSearch').val('');
					$('#mainCatSelId_'+mc_id).addClass('active');
					load_more(1);   //load items basec on main category
					$('html, body').animate({
						'scrollTop' : 175
					});
					//autocomplete_fun();
					
					get_pro_count(mc_id,0);
				}
				
				function get_pro_count(mc_id,sc_id){
					var text = document.getElementById('itemSearch').value;
					var searchIDs = $("input[name='food_type_filter[]']:checked").map(function()
					{
					  return $(this).val();
					}).get(); 
					if(searchIDs!='')
					{ 
						var pro_veg = searchIDs; 
					}
					else 
					{ 
						var pro_veg = ''; 
					} 
					var st_id = $('#store_id').val();
					var mc_id = mc_id;
					$.ajax({
						type:'post',
						url :"{{ url('get-pro-count') }}",
						beforeSend: function() {
							$(".loading-image").show();
						},
						data:{'mc_id':mc_id,'st_id':st_id,'sc_id':sc_id,'text':text,'pro_veg':pro_veg},
						
						success:function(response){	
							$('#cate_item').html(response);
						}
					}); 
				}
				
				function set_sub_cate(mc_id,sc_id,mc_name,sc_name)
				{	
					$('#category_id').val(mc_id);
					$('#cate_name').html(mc_name+' '+'<i class="fa fa-angle-double-right"></i>'+' '+sc_name);
					$('#sub_category_id').val(sc_id);
					$('.maincatA').removeClass('active');
					$('.subcatA').removeClass('active');
					$('#mainCatSelId_'+mc_id).addClass('active');
					$('#subCatSelId_'+mc_id+'_'+sc_id).addClass('active');
					$('#itemSearch').val('');
					load_more(1);   //load items basec on main category
					$('html, body').animate({
						'scrollTop' : 175
					});
					get_pro_count(mc_id,sc_id);
				}
				function show_all()
				{
					$('#category_id').val('');
					$('#sub_category_id').val('');
					$('#food_veg_nonveg').val('');
					$('#vegSearch').css({'background-color': '#f8f9fad4','color':'black'});
					$('#nonvegSearch').css({'background-color': '#f8f9fad4','color':'black'});
					$('#cate_name').html("@lang(Session::get('front_lang_file').'.FRONT_ALL')");
					$('#cate_item').html('<?php echo $mc_product_count;?>');
					$('.maincatA').removeClass('active');
					$('#itemSearch').val('');
					load_more(1);   
					$('html, body').animate({
						'scrollTop' : 175
					});
				}
			</script>
			
			<script>
				var dir = $("#dir").val();
				var dirVar = false;
				if(dir == 'rtl'){dirVar=true;}
				$("#bannerRest").slick({
					autoplay: true,
					dots: true,
					rtl: dirVar,
					variableWidth: false
				});
				// $(".regular").slick({
				// 	dots: true,
				// 	infinite: true,
				// 	autoplay:true,
				// 	slidesToShow: 1,
				// 	slidesToScroll: 1
				//   });
			</script>
			
			<script>
				function addToCart(gotVal)
				{
					var pro_price 	= $('#pro_price_hid'+gotVal).val();
					var item_id 	= $('#product_id'+gotVal).val();
					var qty 		= $('#count'+gotVal).val();
					var st_id 		= $('#store_id'+gotVal).val();
					var tax			= $('#tax_hid'+gotVal).val();
					var currency 	= $('#currency_hid'+gotVal).val();
					var max_qty 	= $('#max_qty'+gotVal).val();
					
					
					var choice_list = [];
					if(qty.trim() == 0 || qty.trim() == '')
					{
						$('#err'+gotVal).html("Enter quantity").show();
						$('#err'+gotVal).css({'color' : 'red'});
						return false;
					}
					$.each($('input[name="choice_list'+gotVal+'[]"]:checked'), function(){            
						choice_list.push($(this).val());
					});
					//alert(item_id+'\n'+qty+'\n'+st_id+'\n'+tax+'\n'+currency+'\n'+pro_price);
					//alert(gotVal);
					$.ajax({
						url: '{{url('')}}/add_cart_item',
						data : {'pro_price' : pro_price,'item_id' : item_id,'qty' :qty,'st_id' : st_id,'tax':tax,'currency':currency,'choice_list':choice_list,'max_qty':max_qty},
						type: "post",
						datatype: "html",
						beforeSend: function()
						{
							$('#loader_'+gotVal).show();
							// $('#orderNowBtn'+gotVal).hide();
						},
						success: function(res){
							var output = res.split('`');
							if(output[0]=='0')
							{
								$('#err'+gotVal).html(output[1]).show().fadeOut(3000);
								$('#err'+gotVal).css({'color' : 'red'});
								$('.cart-tot').html(output[2]);
								$('#quick-cart-product-count').html(output[2]);
							}
							else if(output[0]=='2')
							{
								$('#err'+gotVal).html(output[1]).show();
								$('#err'+gotVal).css({'color' : 'red'});
								$('.cart-tot').html(output[2]);
								$('#quick-cart-product-count').html(output[2]);
							}
							else if(output[0]=='3')
							{
								$('#err'+gotVal).html(output[1]).show().fadeOut(5000);
								$('#err'+gotVal).css({'color' : 'red'});
								$('.cart-tot').html(output[2]);
								$('#quick-cart-product-count').html(output[2]);
							}
							else
							{
								var cart = $('.basket_pofi');
								var imgtodrag = $('#item_quick_image_'+gotVal).find("img").eq(0);
					
								if (imgtodrag) {
									var imgclone = imgtodrag.clone()
										.offset({
										top: imgtodrag.offset().top,
										left: imgtodrag.offset().left
									})
									.css({
										//'opacity': '0.5',
											'position': 'absolute',
											'height': '235px',
											'width': '235px',
											'z-index': '9999'
									})
									.appendTo($('body'))
										.animate({
										'top': cart.offset().top + 10,
											'left': cart.offset().left + 10,
											'width': 75,
											'height': 75
									}, 500, 'easeInOutExpo');
									
									setTimeout(function () {
										$('.modal').modal('hide');
										/*cart.effect("shake", {
											times: 1
										}, 200);*/
									}, 600);

									imgclone.animate({
										'width': 0,
											'height': 0
									}, function () {
										$(this).detach()
									});
								}
								//$('.modal').modal('hide');
								//$('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+output[1]+'</strong></div>');
								$('.cart-tot').html(output[2]);
								$('#quick-cart-product-count').html(output[2]);
								$('.cart-amt').html(currency+''+output[3]);
								
								
		
							}
							$('#loader_'+gotVal).hide();
							// $('#orderNowBtn'+gotVal).show();
						},
						error: function(xhr, status, error) {
							window.location.href='<?php echo url('clear_user_session');?>';
						}
					})
					
				}
			</script>
			
			<script>
				
				$(window).scroll(function(){
					if ($(window).scrollTop() >150) {
						$('.header-cart-sec').addClass('fixed-header-cart-sec');	
					}
					else {
						$('.header-cart-sec').removeClass('fixed-header-cart-sec');					
					}
				});
				
			</script>

				
			
			@endsection
			@stop
			
			
			
				