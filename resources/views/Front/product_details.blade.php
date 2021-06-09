@extends('Front.layouts.default')
@section('content')

<link rel="stylesheet" href="{{url('')}}/public/front/css/xzoom.css">
<link rel="stylesheet" href="{{url('')}}/public/front/css/magnific-popup.css">	


<div id="mySuxesMsg"></div> 	

@unless(!empty($get_product_details))
	@lang(Session::get('front_lang_file').'.NO_DATA_FOUND')
@else
<div class="main-sec">
<div class="section12">
	<div class="container">
		<div class="row">	
			@php 
				$img = explode('/**/',$get_product_details->pro_images); 
				$url = url(''); 
				$path = $url.'/public/images/noimage/'.$no_item;
			@endphp
			@if(count($img) > 0)
				@php $filename = public_path('images/store/products/').$img[0]; @endphp
				@if($img[0] != '' && file_exists($filename))
					@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
				@endif
			@endif
			<div class="col-md-5">					 
				<div class="xzoom-container">
					<img class="xzoom5" id="xzoom-magnific" src="{{$path}}" xoriginal="{{$path}}" />
					<div class="xzoom-thumbs">
						@if(count($img) > 0)
							@foreach($img as $im)
								@php $filename = public_path('images/store/products/').$im; @endphp
								@if($im != '' && file_exists($filename))
									@php $path = $url.'/public/images/store/products/'.$im; @endphp
									<a href="{{$path}}"><img class="xzoom-gallery5" width="80" src="{{$path}}"  title="{{$get_product_details->item_name}}"></a>
								@endif
							@endforeach
						@endif
					</div>
				</div>  				
			</div>
			<div class="col-md-7 quick-view-detail">
				<div class="product-name">
					<h3>{{$get_product_details->item_name}}</h3>
					<p>{{$get_product_details->contains}}</p>
				</div>
				<div class="price-box price-box-store">
					<h4>
						@if($get_product_details->pro_has_discount=='yes') 
							{{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_discount_price,2)}}
						<span>{{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_original_price,2)}}</span>
						@else
							{{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_original_price,2)}}
						@endif
						
						@if($get_product_details->pro_had_tax == 'Yes')
							<p style="font-size: initial;color: black;">(Exc&nbsp;{{$get_product_details->pro_tax_name}}&nbsp;{{$get_product_details->pro_tax_percent}})</p>
						@endif
				 	</h4>
				</div>
				<div class="ratings">
					<div class="rating">      
					@php $result = $get_product_details->avg_val; @endphp
						@if($result > 0) @for($i=1;$i<=$result;$i++) <i class="fa fa-star"></i>@endfor @endif
					</div>
					@if($get_product_details->stock > 0)
						<p class="">  @lang(Session::get('front_lang_file').'.FRONT_AVAILABLE') : <span>{{$get_product_details->stock}}   @lang(Session::get('front_lang_file').'.FRONT_INSTOCK')</span></p>
					@else
						<button  class="order-now-btn">  @lang(Session::get('front_lang_file').'.FRONT_SOLDOUT')</button>
					@endif
				</div>
				@if($get_product_details->stock > 0)
				<div class="add-minus-cart">
					{{ Form::open(['method' => 'post','url' => 'add_cart_product'])}}
					{{ Form::hidden('item_id',$get_product_details->pro_id)}}
					@if($get_product_details->pro_has_discount == 'yes')
				 		{{Form::hidden('pro_price',$get_product_details->pro_discount_price,['id' => 'pro_price'])}}
					@else
						{{Form::hidden('pro_price',$get_product_details->pro_original_price,['id' => 'pro_price'])}}
				 	@endif
					{{ Form::hidden('st_id',$get_product_details->pro_store_id,['id' => 'store_id'])}}
					{{ Form::hidden('item_id',$get_product_details->pro_id,['id' => 'product_id'])}}
					{{ Form::hidden('currency',$get_product_details->pro_currency,['id' => 'currency'])}}
					{{ Form::hidden('max',($get_product_details->pro_quantity - $get_product_details->pro_no_of_purchase),['id' => 'max_qty'])}}
					{{ Form::hidden('tax',$get_product_details->pro_tax_percent,['id' => 'tax_hid'])}}
					<div id="input_div"> 
						<h5>@lang(Session::get('front_lang_file').'.FRONT_QUANTITY'):</h5>
						{{ Form::button('-',['id' => 'moins' ,'onclick'=> 'minus()'])}}
						{{ Form::text('qty','1',['id' => 'count','maxlength' =>'5','class' => 'quantity'])}}
						{{ Form::button('+',['id' => 'plus','onclick'=> 'pluss()'])}}
						
					</div>
					<div class="order-now-btn order-now-btn-store">
						@if(Session::get('customer_id'))
							<img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="max-width:61px;display:none" id="loader" /><button type="button"  onclick="addToCart();" id="orderNowBtn">@lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button>
						@else
						<a href="#" data-toggle="modal" data-target="#myModal">
							<button>@lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button>
						</a>
						@endif
					</div>
					<div id="err" style="float:left;width:100%;font-size: 13px;"></div>
					{{ Form::close()}}
				</div>
				
				@endif
				<div class="wishlist-cont">
					<div class="product-cart-option">
						@if(Session::has('customer_id') == 1)
						@php 
							$prodInWishlist = DB::table('gr_wishlist')->where('ws_pro_id','=',$get_product_details->pro_id)->where('ws_cus_id','=',Session::get('customer_id'))->first(); 
						@endphp
							@if(empty($prodInWishlist)===true)

					<a href="javascript:;" id="wishId_{{$get_product_details->pro_id}}"  onclick="addtowishlist_ajax({{$get_product_details->pro_id}},1,'add','1')"><i class="fa fa-heart-o"></i>@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')</a>
					@else	
					<a href="javascript:;" id="wishId_{{$get_product_details->pro_id}}"  onclick="addtowishlist_ajax({{$get_product_details->pro_id}},1,'remove','1')"><i class="fa fa-heart"></i>@lang(Session::get('front_lang_file').'.FRONT_REMOVE_FROM_WISHLIST')</a>

					@endif
						@else
							<a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-heart-o"></i>@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')</a>
						@endif
					</div>									
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="product-description product-description-store">
					<ul class="tabs">
						<li class="tab-link current" data-tab="tab-1">@lang(Session::get('front_lang_file').'.FRONT_DESCRIPTION')</li>
						<li class="tab-link" data-tab="tab-2">@lang(Session::get('front_lang_file').'.FRONT_SPECIFICATION')</li>
						<li class="tab-link" data-tab="tab-3">@lang(Session::get('front_lang_file').'.FRONT_REVIEW')</li>								
					</ul>
					
					<div id="tab-1" class="tab-content current" style="max-height: 300px;overflow-y: scroll;">
						{!!ucfirst($get_product_details->desc)!!}
					</div>
					<div id="tab-2" class="tab-content">
						<ul>
							
								@forelse($get_specifi_details as $spec_det)
								<li><i class="fa fa-asterisk"></i>{{$spec_det->spec_title}} {!! $spec_det->spec_desc !!}</li>
								@empty
								<li>@lang(Session::get('front_lang_file').'.NO_DATA_FOUND')</li>
								@endforelse		
							
						</ul>
					</div>
					<div id="tab-3" class="tab-content">
						<ul>	
								
								@forelse($get_review_details as $reviewdet)
								@php $path = $url.'/public/images/noimage/default_user.png'; @endphp
									@if($reviewdet->cus_image != '')
										@php $filename = public_path('images/customer/').$reviewdet->cus_image; @endphp
										@if(file_exists($filename))
											@php $path = $url.'/public/images/customer/'.$reviewdet->cus_image; @endphp
										@endif
									@endif
									<li>
										<div class="custom-img">
											<img src="{{$path}}">
										</div>
										<div class="custom-des">
											<h5>{{$reviewdet->cus_fname.' '.$reviewdet->cus_lname}}</h5>
											<p>{{$reviewdet->review_comments}}</p>
											<p>@if($reviewdet->review_rating > 0) @for($i=1;$i<=$reviewdet->review_rating;$i++) <i class="fa fa-star"></i>@endfor @endif</p>
											<p>{{date('m/d/Y',strtotime($reviewdet->created_date))}}</p>
										</div>
									</li>
								@empty
									<li>@lang(Session::get('front_lang_file').'.NO_DATA_FOUND')</li>
								@endforelse
							
						</ul>
					</div>							
				</div>
			</div>
			
			@if(count($related_pdtDetails) > 0 )
			<div class="col-md-12 related-item  related-item-store">
				<h4>Related Products</h4>
				<section class="regular slider your-carousel">
					@foreach($related_pdtDetails as $relpdt)
						@php $img = explode('/**/',$relpdt->pro_images); $url = url(''); @endphp
						@if(count($img) > 0)
							@php $filename = public_path('images/store/products/').$img[0]; @endphp
							@if($img[0] != '' && file_exists($filename))
								@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
							@endif
						@else
							@php $path = $url.'/public/images/noimage/'.$no_item; @endphp
						@endif
					<div class="related-item-sec">
						<div class="product-img-sec">
							<a href="{{url('').'/product-details/'.base64_encode($relpdt->pro_id)}}">
								<img src="{{$path}}">
							</a>
						</div>
						<div class="product-details">
							@if($relpdt->pro_has_discount=='yes')
								<h5>{{$relpdt->pro_currency.' '.number_format($relpdt->pro_discount_price,2)}}<span>{{$relpdt->pro_currency.' '.number_format($relpdt->pro_original_price,2)}}</span></h5>
							@else
								<h5>{{$relpdt->pro_currency.' '.number_format($relpdt->pro_original_price,2)}}</h5>
							@endif
							<p><a href="{{url('').'/product-details/'.base64_encode($relpdt->pro_id)}}">{{ucfirst(str_limit($relpdt->item_name,20))}}</a></p>
							<p>{{ucfirst(str_limit($relpdt->contains,20))}}</p>
							<a href="{{url('').'/product-details/'.base64_encode($relpdt->pro_id)}}"><button><i class="fa fa-shopping-cart"></i> @lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button></a>
						</div>
					</div>
					@endforeach
				</section>
			</div>
			@endif
		</div>
	</div>
</div>
</div>
@endunless
@section('script')

<script>
	$(document).ready(function(){
		
		$('ul.tabs li').click(function(){
			var tab_id = $(this).attr('data-tab');
			
			$('ul.tabs li').removeClass('current');
			$('.tab-content').removeClass('current');
			
			$(this).addClass('current');
			$("#"+tab_id).addClass('current');
		})
		
	})
</script>

<!-- ADD/MINUS PLUGIN -->
<script>
			@if($get_product_details->stock > 0)
			var max = document.getElementById("max_qty").value;
			$('.quantity').bind('keyup blur',function(){ 
			var node = $(this);
			node.val(node.val().replace(/[^0-9]/g,'') ); 
			});

			function pluss(){
			var countEl = document.getElementById("count").value;
			if(parseInt(countEl) < parseInt(max) )
			{
				countEl++;
			}
			$('#count').val(countEl);
			}
			function minus(){
				var countEl = document.getElementById("count").value;
				if (countEl > 1) { 
				countEl--;
				$('#count').val(countEl);
			  }  
			}

			function addToCart()
			{	
				var out = true;
				var countEl = document.getElementById("count").value;
				var pro_id = document.getElementById("product_id").value;
				var st_id = document.getElementById("store_id").value;
				if(countEl == 0 || countEl == '')
				{	
					$('#count').css({'border' : '1px solid red'});
					return false;
				}
				else if(parseInt(countEl) > parseInt(max))
				{
					$('#count').css({'border' : '1px solid red'});
					$('#err').html("@lang(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')");
					$('#err').css({'color' : 'red'});
					return false;
				}
				else
				{	

					var pro_price 	= $('#pro_price').val();
					var item_id 	= $('#product_id').val();
					var qty 		= $('#count').val();
					var st_id 		= $('#store_id').val();
					var tax			= $('#tax_hid').val();
					var currency 	= $('#currency').val();
					var max_qty 	= $('#max_qty').val();

					$.ajax({
						url: '{{url('')}}/add_cart_product',
						data : {'pro_price' : pro_price,'item_id' : item_id,'qty' :qty,'st_id' : st_id,'tax':tax,'currency':currency,'max_qty':max_qty},
						type: "post",
						datatype: "html",
						beforeSend: function()
						{
							$('#loader').show();
							$('#orderNowBtn').hide();
						},
						success: function(res){
							var output = res.split('`');
							if(output[0]=='0')
							{
								$('#err').html(output[1]).show();
								$('#err').css({'color' : 'red'});
								$('.cart-tot').html(output[2]);
							}
							else
							{
								$('.modal').modal('hide');
								$('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+output[1]+'</strong></div>');
								$('.cart-tot').html(output[2]);
								$('html, body').animate({
									'scrollTop' : '0'
								});
							}
							$('#loader').hide();
							$('#orderNowBtn').show();
						}
					})
					/*$.ajax({
						'type' : 'get',
						'async' : false,
						'data' : {'pro_id' : pro_id,'st_id' : st_id},
						'url'	: '<?php echo url('cart_update_chk_qty'); ?>',
						success:function(response)
						{
							var total_count = parseInt(countEl)+parseInt(response);
							
							if(total_count > max)
							{
								$('#count').css({'border' : '1px solid red'});
								$('#err').html("@lang(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')&nbsp;@lang(Session::get('front_lang_file').'.FRONT_HAVE_CART')&nbsp;" + response);
								$('#err').css({'color' : 'red'});
								out = false;
								
							}
							else
							{
								out =  true;
								
							}
						}
					});
					
					return out;*/
				}
			}
			@endif
</script>




<script src="{{url('')}}/public/front/js/magnific-popup.js"></script>
<script src="{{url('')}}/public/front/js/xzoom.min.js"></script>
<script src="{{url('')}}/public/front/js/setup.js"></script>


<script>
	$(".regular").slick({
		dots: true,
		infinite: true,
		autoplay: true,
		slidesToShow: 5,
		slidesToScroll: 1,
		
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
				slidesToShow: 2,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			}
			
			]
		
	});
</script>


@endsection
@stop