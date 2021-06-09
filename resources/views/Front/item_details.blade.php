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
                        @php $url = url(''); $path = $url.'/public/images/noimage/'.$no_item; @endphp
                        @php $img = explode('/**/',$get_product_details->pro_images);  @endphp
                        @if(count($img) > 0)
                            @php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
                            @if($img[0] != '' && file_exists($filename))
                                @php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
                            @endif
                        @endif
                        <div class="col-md-5">
                            <div class="xzoom-container">
                                <img class="xzoom5" id="xzoom-magnific" src="{{$path}}" xoriginal="{{$path}}" />
                                <div class="xzoom-thumbs">
                                    @if(count($img) > 0)
                                        @foreach($img as $im)
                                            @php $filename = public_path('images/restaurant/items/').$im; @endphp
                                            @if($im != '' && file_exists($filename))
                                                @php $path = $url.'/public/images/restaurant/items/'.$im; @endphp
                                                <a href="{{$path}}"><img class="xzoom-gallery5" width="80" src="{{$path}}"  title="{{$get_product_details->item_name}}"></a>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 quick-view-detail">
                            <div class="product-name">
                                <h3><span>{{$get_product_details->item_name}}</span><span>({{ $get_product_details->contains }})</span></h3>
                                <p><?php echo str_limit(strip_tags($get_product_details->desc),150) ?></p>
                            </div>
                            <div class="price-box">
                                <h4>
                                    @if($get_product_details->pro_has_discount=='yes')
                                        {{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_discount_price,2)}}
                                        <span>{{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_original_price,2)}}</span>
                                    @else
                                        {{$get_product_details->pro_currency.' '.number_format($get_product_details->pro_original_price,2)}}
                                    @endif
                                    @if($get_product_details->pro_had_tax == 'Yes')
                                        <p style="font-size: 14px;margin:10px 0 0;">(@lang(Session::get('front_lang_file').'.FRONT_EXC'):  {{$get_product_details->pro_tax_name}}&nbsp;{{$get_product_details->pro_tax_percent}}) @lang(Session::get('front_lang_file').'.FRONT_IN_TAX').</p>

                                    @endif
                                </h4>
                            </div>
                            @if($get_product_details->store_closed =="Closed" && $get_product_details->st_pre_order==1)
                                <div>
                                    <img src="{{url('')}}/public/front/images/shop_closed.png" style="width:100px;height:100px;float:left">
                                    <p style="color:brown;float:right;padding:40px;padding-right: 0px;">
                                        @lang(Session::get('front_lang_file').'.ADMIN_PRE_OR_AVAIL')
                                    </p>
                                </div>
                            @endif
                            <div class="ratings">
                                <div class="rating">
                                    @php $result = $get_product_details->avg_val; @endphp
                                    @if($result > 0) @for($i=1;$i<=$result;$i++) <i class="fa fa-star"></i>@endfor @endif
                                </div>
                                @if($get_product_details->stock > 0)
                                    <p class="">  @lang(Session::get('front_lang_file').'.FRONT_AVAILABLE') : <span>{{$get_product_details->stock}}   @lang(Session::get('front_lang_file').'.FRONT_INSTOCK')</span></p>
                                @else
                                    <div class="order-now-btn"><button  class="">  @lang(Session::get('front_lang_file').'.FRONT_SOLDOUT')</button></div>
                                @endif
                            </div>
                            @if($get_product_details->stock > 0)
                                {{ Form::open(['method' => 'post','url' => 'add_cart_item'])}}
                                @if($get_product_details->pro_has_discount == 'yes')
                                    {{Form::hidden('pro_price',$get_product_details->pro_discount_price,['id' => 'pro_price'])}}
                                @else
                                    {{Form::hidden('pro_price',$get_product_details->pro_original_price,['id' => 'pro_price'])}}
                                @endif
                                {{ Form::hidden('st_id',$get_product_details->pro_store_id,['id' => 'store_id'])}}
                                {{ Form::hidden('item_id',$get_product_details->pro_id,['id' => 'product_id'])}}
                                {{ Form::hidden('currency',$get_product_details->pro_currency,['id' => 'currency'])}}
                                {{ Form::hidden('had_choice',$get_product_details->pro_had_choice)}}
                                {{ Form::hidden('max',($get_product_details->pro_quantity - $get_product_details->pro_no_of_purchase),['id' => 'max_qty'])}}
                                {{ Form::hidden('tax',$get_product_details->pro_tax_percent,['id' => 'tax_hid'])}}
                                @if(count($get_choice_details) > 0 )
                                    <div class="multi-select">
                                        @foreach($get_choice_details as $choice)
                                            <label class="multi-checkbox">{{$choice->choice_name}}  <span>{{$choice->pro_currency.' '.number_format($choice->pc_price,2)}}</span>
                                                <input type="checkbox" name="choice_list[]" value="{{$choice->pc_choice_id}}">
                                                <span class="checkmark"></span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="add-minus-cart">

                                    <div id="input_div">
                                        <h5>@lang(Session::get('front_lang_file').'.FRONT_QUANTITY'):</h5>
                                        <div class="input-group" style="width: auto;">
                                            {{ Form::button('-',['id' => 'moins' ,'onclick'=> 'minus()'])}}
                                            {{ Form::text('qty','1',['id' => 'count','maxlength' =>'5','class' => 'quantity','onkeypress'=>'return isNumberKey(event)'  ])}}
                                            {{ Form::button('+',['id' => 'plus','onclick'=> 'pluss()'])}}
                                        </div>
                                    </div>
                                    @if($get_product_details->store_closed =="Closed" && $get_product_details->st_pre_order==0)
                                        <div class="order-now-btn" style="color:#7f1900">
                                            @lang(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED')
                                        </div>
                                    @else
                                        <div class="order-now-btn">
                                            @if(Session::get('customer_id'))

                                                <img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="max-width:61px;display:none" id="loader" /><button type="button"  onclick="addToCart();" id="orderNowBtn">@lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button>
                                            @else
                                                <a href="#" data-toggle="modal" data-target="#myModal">
                                                    <button>@lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                    <div id="err" style="float:left;width:100%;font-size: 16px;font-weight:bold;"></div>
                                </div>
                                {{ Form::close()}}
                            @endif
                            <div class="wishlist-cont">
                                <div class="product-cart-option">

                                    @if(Session::has('customer_id') == 1)
                                        @php
                                            $prodInWishlist = DB::table('gr_wishlist')->where('ws_pro_id','=',$get_product_details->pro_id)->where('ws_cus_id','=',Session::get('customer_id'))->first(); @endphp
                                        @if(empty($prodInWishlist)===true)
                                           <!--  <a href="#" onclick="addtowish({{$get_product_details->pro_id}},2)"><i class="fa fa-heart-o"></i>@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')</a> -->

                    <a href="javascript:;" id="wishId_{{$get_product_details->pro_id}}"  onclick="addtowishlist_ajax({{$get_product_details->pro_id}},2,'add','1')"><i class="fa fa-heart-o"></i>@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')</a>

                                        @else
                         <!-- <a href="{{ url('remove_wish_product').'/'.base64_encode($prodInWishlist->ws_id) }}"><i class="fa fa-heart" aria-hidden="true"></i>@lang(Session::get('front_lang_file').'.FRONT_REMOVE_FROM_WISHLIST')</a> -->

              <a href="javascript:;" id="wishId_{{$get_product_details->pro_id}}"  onclick="addtowishlist_ajax({{$get_product_details->pro_id}},2,'remove','1')"><i class="fa fa-heart"></i>@lang(Session::get('front_lang_file').'.FRONT_REMOVE_FROM_WISHLIST')</a>

                                        @endif
                                    @else
                                        <a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-heart-o"></i>@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')</a>



                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="product-description">
                                <ul class="tabs">
                                    <li class="tab-link current" data-tab="tab-1">@lang(Session::get('front_lang_file').'.FRONT_DESCRIPTION')</li>
                                    <li class="tab-link" data-tab="tab-2">@lang(Session::get('front_lang_file').'.FRONT_SPECIFICATION')</li>
                                    <li class="tab-link" data-tab="tab-3">@lang(Session::get('front_lang_file').'.FRONT_REVIEW')</li>
                                </ul>

                                <div id="tab-1" class="tab-content current" style="max-height: 300px;overflow-y: auto;">
                                    {!!ucfirst(strip_tags($get_product_details->desc))!!}
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
                                            @if($reviewdet->cus_image!= '')
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
 <?php
 echo '<input id="dir" type="hidden" value="'.$dir.'">';
 ?>
                        @if(count($related_pdtDetails) > 0 )
                            <div class="col-md-12 related-item ">
                                <h4>@lang(Session::get('front_lang_file').'.FRONT_RELATED_PRODUCTS')</h4>
                                <section class="regular slider your-carousel" dir="<?php echo $dir; ?>">
                                    @foreach($related_pdtDetails as $relpdt)
                                        @php $img = explode('/**/',$relpdt->pro_images); $url = url(''); @endphp
                                        @if(count($img) > 0)
                                            @php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
                                            @if($img[0] != '' && file_exists($filename))
                                                @php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
                                            @endif
                                        @else
                                            @php $path = $url.'/public/images/noimage/'.$no_item; @endphp
                                        @endif
                                        <div class="related-item-sec">
                                            <div class="product-img-sec">
                                                <a href="{{url('').'/'.$relpdt->store_slug.'/item-details/'.$relpdt->pro_item_slug}}">
                                                    <img src="{{$path}}">
                                                </a>
                                            </div>
                                            <div class="product-details">
                                                @if($relpdt->pro_has_discount=='yes')
                                                    <h5>{{$relpdt->pro_currency.' '.number_format($relpdt->pro_discount_price,2)}}<span class="old-price">{{$relpdt->pro_currency.' '.number_format($relpdt->pro_original_price,2)}}</span></h5>
                                                @else
                                                    <h5><span >{{$relpdt->pro_currency.' '.number_format($relpdt->pro_original_price,2)}}</span></h5>
                                                @endif
                                                <p><a href="{{url('').'/'.$relpdt->store_slug.'/item-details/'.$relpdt->pro_item_slug}}">{{ucfirst(str_limit($relpdt->item_name,20))}}</a></p>
                                                <p class="itm-con">{{ucfirst(str_limit($relpdt->contains,20))}}</p>
                                                <a href="{{url('').'/'.$relpdt->store_slug.'/item-details/'.$relpdt->pro_item_slug}}"><button><i class="fa fa-shopping-cart"></i> @lang(Session::get('front_lang_file').'.FRONT_ORDER_NOW')</button></a>
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
{{--<script src='{{url('public/front/js')}}/jquery-ui.min.js'></script>--}}
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
			//alert('Yes');
            var node = $(this);
			var val_without_space = node.val().replace(/[^0-9]/g,'');
			if(val_without_space=='' || val_without_space=='0'){ node.val(1);} else {  node.val(val_without_space); }
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
		function isNumberKey(evt)
		{
		    var charCode = (evt.which) ? evt.which : event.keyCode;
		    if (charCode > 31 && (charCode < 48 || charCode > 57 ) )
		    {
		       /*  if(charCode!=46)*/
		            return false; 
		    }
		    
		 return true;
		  
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
                var choice_list = [];
                $.each($('input[name="choice_list[]"]:checked'), function(){
                    choice_list.push($(this).val());
                });
                $.ajax({
                    url: '{{url('')}}/add_cart_item',
                    data : {'pro_price' : pro_price,'item_id' : item_id,'qty' :qty,'st_id' : st_id,'tax':tax,'currency':currency,'choice_list':choice_list,'max_qty':max_qty},
                    type: "post",
                    datatype: "json",
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
							$('#quick-cart-product-count').html(output[2]);
                            $('.cart-amt').html(currency+' '+output[3]);
                        }
						else if(output[0]=='2')
						{
							$('#err').html(output[1]).show();
							$('#err').css({'color' : 'red'});
							$('.cart-tot').html(output[2]);
							$('#quick-cart-product-count').html(output[2]);
                            $('.cart-amt').html(currency+' '+output[3]);
						}
						else if(output[0]=='3')
						{
							$('#err').html(output[1]).show().fadeOut(5000);
							$('#err').css({'color' : 'red'});
                            $('.cart-tot').html(output[2]);
							$('#quick-cart-product-count').html(output[2]);
							$('.cart-amt').html(currency+' '+output[3]);
						}
                        else
                        {
							var cart = $('.basket_pofi');
							var imgtodrag = $('.xzoom-container').find("img").eq(0);
				
							if (imgtodrag) {
								var imgclone = imgtodrag.clone()
									.offset({
									top: imgtodrag.offset().top,
									left: imgtodrag.offset().left
								})
								.css({
									//'opacity': '0.5',
										'position': 'absolute',
										'height': '445px',
										'width': '445px',
										'z-index': '9999'
								})
								.appendTo($('body'))
									.animate({
									'top': cart.offset().top + 10,
										'left': cart.offset().left + 10,
										'width': 75,
										'height': 75
								}, 1000, '');//easeInOutExpo
								
								setTimeout(function () {
									$('.modal').modal('hide');
									/*cart.effect("shake", {
										times: 1
									}, 200);*/
								}, 1500);

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
                            $('.cart-amt').html(currency+' '+output[3]);
                            $('html, body').animate({
                                'scrollTop' : '0'
                            });
                            //window.location.reload();
                        }
						
						$('#loader').hide();
						$('#orderNowBtn').show();
                    },
					error: function(xhr, status, error) {
					  window.location.href='<?php echo url('clear_user_session');?>';
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
					});*/

                //return out;
            }
        }
        @endif
    </script>



    <script src="{{url('')}}/public/front/js/magnific-popup.js"></script>
    <script src="{{url('')}}/public/front/js/xzoom.min.js"></script>
    <script src="{{url('')}}/public/front/js/setup.js"></script>


    <script>
        var dir = $("#dir").val();
        if(dir == 'rtl'){var sDir = true;}else{var sDir = false;}
        $(".regular").slick({
            dots: true,
            infinite: true,
            autoplay: true,
            slidesToShow: 4,
            slidesToScroll: 1,
            rtl: sDir,
            variableWidth: false,

            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
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
    </script>


@endsection
@stop