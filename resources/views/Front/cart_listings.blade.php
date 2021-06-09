
@extends('Front.layouts.default')
@section('content')
	<style>
		.multi-checkbox {
			display: block;
			position: relative;
			padding-left: 35px;
			margin-bottom: 12px;
			cursor: pointer;
			font-size: 13px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		.multi-checkbox input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}
		.checkmark {
			position: absolute;
			top: 0;
			left: 0;
			height: 18px;
			width: 18px;
			background-color: #ccc;
		}
		.multi-checkbox:hover input ~ .checkmark {
			background-color: #ccc;
		}

		.multi-checkbox input:checked ~ .checkmark {
			background-color: #ff5215;
		}
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}
		.multi-checkbox input:checked ~ .checkmark:after {
			display: block;
		}
		.multi-checkbox .checkmark:after {
			left: 7px;
			top: 3px;
			width: 5px;
			height: 10px;
			border: solid white;
			border-width: 0 3px 3px 0;
			-webkit-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			transform: rotate(45deg);
		}

		.preOrderDatePicker
		{
			height:30px;
			font-size:13px;
			margin-left:5px;
		}
		.preorder-btn
		{
			padding: 2px 10px;
			color: #fff;
			font-size: 14px;
			height: 30px;
			border-radius:0;
		}


		.datepicker > .datepicker_inner_container
		{
			border:0!important;
			background-color: #fff!important;
			font-family: 'Roboto', sans-serif!important;
		}
		.datepicker > .datepicker_inner_container > .datepicker_calendar > .datepicker_table > tr > td.active
		{
			background-color: #ff5215 !important;
			border-bottom: 0!important;
			border-radius: 100%;
		}
		.datepicker > .datepicker_inner_container > .datepicker_calendar
		{
			background-color: #fff!important;
			border:0!important;
			margin-bottom:0!important;
			border-right: 1px solid #ddd!important;
		}
		.datepicker > .datepicker_inner_container > .datepicker_timelist
		{
			width:70px!important;
			text-align:center!important;
			background-color: #fff!important;
		}
		.datepicker > .datepicker_inner_container > .datepicker_calendar > .datepicker_table > tr > td
		{
			padding:5px 7px!important;
		}
		.datepicker > .datepicker_inner_container > .datepicker_timelist > div.timelist_item.time_in_past
		{
			color: #aaa!important;
		}
		.datepicker > .datepicker_inner_container > .datepicker_calendar > .datepicker_table > tr > td.today
		{
			border-bottom: 0!important;
		}
		.datepicker > .datepicker_header
		{
			color: #fff!important; background:#ff5215!important;
		}
		.datepicker > .datepicker_header > .icon-home
		{
			top: -3px!important;
		}
		.datepicker > .datepicker_header > a
		{
			color: #fff!important;
			font-size:17px!important;
			fill:#fff;
		}
		.datepicker > .datepicker_header > a:active
		{
			background:transparent!important;
		}
		.back-to-shop a:after{/*content: '';  position: absolute; top: -4px; width: 33px; height: 33px;     right: 190px; z-index: 1; background:#7f1900;     transform: scale(0.707) rotate(45deg);*/ }
		.row-top{ /*background: #f0f0f0; box-shadow: 0px 0px 8px #bdbdbd;*/ padding-top: 20px;}
		.store-err-msg{position:absolute; right:65px; color:#ff5215;}
		
	</style>

	<div class="main-sec">
		<div id="mySuxesMsg"></div>
		<div class="section13">
			<div class="container">
				<div >
					<div class="row row-top">

						<div class="col-sm-6 add-item">
							<p>@lang(Session::get('front_lang_file').'.FRONT_YOU_HAVE_ADDED') <span id="total_cart_count">{{$pdtCount}} </span>@lang(Session::get('front_lang_file').'.FRONT_ITEMS')</p>
						</div>

						<div class="col-sm-6">
							<div class="back-to-shop">
								<a href="{{url('')}}"><i class="fa fa-shopping-bag fa-lg" aria-hidden="true"></i> @lang(Session::get('front_lang_file').'.FRONT_BACKTO_SHOPPING')</a>
								@if(count($pdtsInCart) > 1 )
								<a href="{{url('remove_all_item')}}" id="remove_all_btn" class="btn btn-danger" style="background-color: #c82333;border-color: #bd2130;color:#fff" onclick="return remove_all_from_cart();"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i> @lang(Session::get('front_lang_file').'.FRONT_REMOVE_ALL_CART')</a>
							@endif
							</div>
						</div> 
						@php $delivery_fee = 0; $delfee_exist = 0; $total_rest_count = 0; $total_invalid_deliver = 0; @endphp
						@if($pdtCount > 0 )
							@php $sub_total = 0; $tax_total =  0; $km_del_fee = 0; 
								$user_lat = Session::get('search_latitude');
								$user_long = Session::get('search_longitude');
								
							@endphp
							@if(!empty($shippingDet))
							@php 
								$user_lat = $shippingDet->sh_latitude;
								$user_long= $shippingDet->sh_longitude;
								
							@endphp
							@endif
							<div class="col-md-12 cart-accordion">
								<div class="clearfix"></div>
								@php $i = 0; @endphp
								<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
									@foreach($pdtsInCart as $key=>$value)
										@php 
											$total_rest_count++;
											$splitStore = explode('~`',$key);
											//print_r($splitStore); exit;
											$shop_total_order_amount = 0; //For checking minimum order amount
											$st_del_fee = 0;
										@endphp
										{{--  calculate delivery fee bacsed on kilometer --}}
										@if(empty($delivery_fee_set) === false)
											@if($delivery_fee_set->gs_delivery_fee_status=='1' && $delivery_fee_set->gs_del_fee_type == 'km_fee')
												@php 
													$st_lat = $splitStore[7];
													$st_long = $splitStore[8];
													$kilometer = calculate_distance($user_lat,$user_long,$st_lat,$st_long);
													//echo $kilometer[0]->distance;
													$st_del_fee = $delivery_fee_set->gs_km_fee * $kilometer[0]->distance;
													$km_del_fee+=$st_del_fee;
												@endphp

											@endif
										@endif
										<div class="panel panel-default" id="store_list_{{$splitStore[1]}}">
											<div class="panel-heading" role="tab" id="headingOne">
												<h4 class="panel-title">
													<a  class="cart-item-title" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$splitStore[1]}}" aria-expanded="true" aria-controls="collapseOne">
														{{$splitStore[0]}}
														@if($splitStore[9] > $splitStore[10])
															@php $total_invalid_deliver++; @endphp
															<input type="hidden" name="not_delivered_items[]"  value="{{$splitStore[1]}}" />
															<span class="store-err-msg"> @lang(Session::get('front_lang_file').'.DELIVERY_NOTAVAIL_TOUR_SHIPLOC')</span>
														@endif
													</a>
													{{ Form::hidden('store_name[]',$splitStore[0])}}
													{{ Form::hidden('st_delfee',$st_del_fee,['id' => 'st_delfee'.$splitStore[1]])}}
													
													@php $min_order = $splitStore[4];

													@endphp
													
												</h4>
											</div>

											<div id="collapseOne{{$splitStore[1]}}" class="panel-collapse collapse in show" role="tabpanel" aria-labelledby="headingOne">
												<div class="panel-body">
													<ul>
														@if($splitStore[2]=='1' && $splitStore[6]=='1')
															<li>
																<form class="form-inline" action="">
																	<div class="checkbox">
																		<label class="multi-checkbox"> @lang(Session::get('front_lang_file').'.FRONT_PRE_ORDER')</span>
																			<input type="checkbox" name="pre_order_checkbox[]" id="pochk_{{$splitStore[1]}}" value="{{$splitStore[1]}}" data-sttype="{{$splitStore[2]}}">
																			<span class="checkmark"></span>

																		</label>
																	</div>
																	
																	<div class="dtmebtn-div">
																	<div class="form-group" class="" style="display:none" id="preorderDateDiv_{{$splitStore[1]}}">
																		<label for="email">&nbsp;</label>
																		<input type="text" class="form-control preOrderDatePicker" id="preorderDate_{{$splitStore[1]}}" placeholder="date" autocomplete="off">
																	</div>
																	<button type="button" class="btn btn-default preorder-btn" style="display:none" id="preorderBtnDiv_{{$splitStore[1]}}" onclick="save_preorder('{{$splitStore[1]}}')">@lang(Session::get('front_lang_file').'.ADMIN_SAVE')</button></div>&nbsp;<span id="pre_order_msg_{{$splitStore[1]}}" class="preorderErrorMessage"></span>
																	<input type="hidden" id="pre_ajaxErr_{{$splitStore[1]}}" class="preajax_error_class" value="{{$splitStore[1]}}" data-ajaxErrCode="" data-ajaxErrMsg="" />
																</form>
																<span id="err{{$i}}"></span>
																
																<?php 	
																/*<script>
																	disable_date_time('{{$splitStore[1]}}');
																</script>*/ 
																?>
															
															</li>
														@endif
														{{ Form::hidden('sh_status['.$i.']',$splitStore[5],['id' => 'shop_status','data-storename'=>$splitStore[0],'data-storeid' => $splitStore[1]])}}
														{{-- cart_type - 1 for product cart, cart_type - 2 for item cart--}}
														@foreach($value as $pdtDetail)

															@php $path = url('').'/public/images/noimage/'.$no_item; @endphp
															@if($pdtDetail->cart_type=='1')
																@php $img = explode('/**/',$pdtDetail->pro_images); $url = url(''); $pdtUrl = url('').'/product-details/'.base64_encode($pdtDetail->pro_id); @endphp
																@if(count($img) > 0)
																	@php $filename = public_path('images/store/products/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
																	@endif

																@endif
																
															@else
																@php $img = explode('/**/',$pdtDetail->pro_images); $url = url('');  $pdtUrl = url('').'/'.$pdtDetail->store_slug.'/item-details/'.$pdtDetail->pro_item_slug; @endphp
																@if(count($img) > 0)
																	@php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
																	@endif

																@endif

															@endif

															<li id="cart_list_{{$pdtDetail->cart_id}}">
																<div class="cart-img" @if($pdtDetail->pro_type=='2' && $pdtDetail->pro_had_choice=='1') data-toggle="modal" data-target="#choice-modal_{{$pdtDetail->cart_id}}" style="cursor:zoom-in" @endif >
																	<img src="{{$path}}" width="80">
																</div>
																<div class="cart-pdt-detail" >
																	<p><a href="{{$pdtUrl}}" target="_blank">{{$pdtDetail->item_name}}</a></p>
																	<h5>{{$pdtDetail->cart_currency.' '.number_format($pdtDetail->cart_unit_amt,2)}}</h5>

																	@php
																		$selectedChoice = array();
                                                                        $choices = json_decode($pdtDetail->cart_choices_id,true);
																	@endphp
																	<div id="choiceDivToggle{{$pdtDetail->cart_id}}" @if($pdtDetail->cart_had_choice=='Yes' && count($choices) > 0) @else style="display:none" @endif>
																		@php $pluscount=1; @endphp
																		<span @if($pdtDetail->pro_type=='2' && $pdtDetail->pro_had_choice=='1') data-toggle="modal" data-target="#choice-modal_{{$pdtDetail->cart_id}}" style="cursor:zoom-in" @endif id="choice_array_{{$pdtDetail->cart_id}}">
																			@if(sizeof($choices)>0)
																				@foreach($choices as $choice)
																					@php array_push($selectedChoice,$choice['choice_id']); @endphp
																					@php  $choiceName = get_choice_name($choice['choice_id']); @endphp
																					@if(empty($choiceName) === false )
																						@php $display_choice=$choiceName->ch_name; @endphp
																					@else
																						@php $display_choice=choice['choice_id']; @endphp
																					@endif
																					{{$display_choice.'( '.$pdtDetail->cart_currency.' '.number_format(($choice['choice_price']),2).' )'}} @if(count($choices)!=$pluscount) + @endif
																					@php $pluscount++; @endphp
																				@endforeach
																			@endif
																		</span>
																	</div>

																	<p>@lang(Session::get('front_lang_file').'.FRONT_EXCL_TAX') : {{$pdtDetail->cart_currency}}&nbsp;<span id="cart_tax_{{$pdtDetail->cart_id}}"> {{
													number_format($pdtDetail->cart_tax,2)}}</span></p>
																	@if($splitStore[2]=='1')
																		<a href="#" class="" data-toggle="modal" data-target="#spl-request_{{$pdtDetail->cart_id}}" style="color: #383757;"> <span id="content_{{$pdtDetail->cart_id}}"> {{($pdtDetail->cart_spl_req == '') ? __(Session::get('front_lang_file').'.FRONT_ADD_SPCIAL_NOTES') : $pdtDetail->cart_spl_req }}</span></a>
																	@endif

																</div>
																<div class="res-full">
																	<div class="cart-counter">
																		<div class="handle-counter" id="handleCounter_{{$pdtDetail->cart_id}}">
																			<button class="counter-minus" id="counter-minus-{{$pdtDetail->cart_id}}" onClick="minus({{$pdtDetail->cart_id}});" @if($pdtDetail->cart_quantity==1) style="   " disabled="disabled" @endif><i class="fa fa-minus" aria-hidden="true"></i></button>
																			<input type="text" class="cart_qtyClass" value="{{$pdtDetail->cart_quantity}}" id="cart_qty_{{$pdtDetail->cart_id}}" data-cartid="{{$pdtDetail->cart_id}}" data-max="{{$pdtDetail->stock}}">
																			<button class="counter-plus" id="counter-plus-{{$pdtDetail->cart_id}}" onClick="pluss({{$pdtDetail->cart_id}});" @if($pdtDetail->cart_quantity>=$pdtDetail->stock) style="" disabled="disabled" @endif><i class="fa fa-plus" aria-hidden="true"></i></button>
																		</div>
																		<div id="stockExceeds_{{$pdtDetail->cart_id}}" class="stockExceedClass" style="color:red; @if($pdtDetail->cart_quantity<$pdtDetail->stock) display:none; @endif ">@lang(Session::get('front_lang_file').'.FRONT_REACHED_MAX_QTY')</div>
																		@php $sub_total +=$pdtDetail->cart_total_amt;

																		@endphp
																	</div>
																	<div class="cart-price">
																		{{$pdtDetail->cart_currency}}&nbsp;<span id="cart_price_{{$pdtDetail->cart_id}}">{{number_format($pdtDetail->cart_total_amt,2)}}</span><br>

																	</div>
																	<div class="cart-remove">
																		{{-- <a href="{{url('').'/remove_cart/'.$pdtDetail->cart_id}}"> --}}
																		<i class="fa fa-times-circle" onClick="remove_cart('{{$pdtDetail->cart_id}}','{{$pdtDetail->cart_total_amt}}','{{$splitStore[1]}}');"></i>
																		{{-- </a> --}}
																	</div>
																</div>

															</li>
															{{-- Check minimum order amount --}}
															@php $shop_total_order_amount += $pdtDetail->cart_total_amt; @endphp



														<!-- Choice Modal -->
															@if($pdtDetail->pro_type=='2' && $pdtDetail->pro_had_choice=='1')
																<div id="choice-modal_{{$pdtDetail->cart_id}}" class="modal fade choice-modal" role="dialog">

																	<div class="modal-dialog">
																		
																		<!-- Modal content-->
																		<div class="modal-content">
																			<div class="modal-header">
																				<button type="button" class="close" data-dismiss="modal">&times;</button>
																				<img src="{{$path}}">
																			</div>
																			<div class="modal-body">
																				<div class="choice-modal-head">
																					<h2>{{$pdtDetail->item_name}}</h2>
																					<p>{{$pdtDetail->contains_name}}</p>
																				</div>
																				<div class="choice-modal-content">
																					<h4>@lang(Session::get('front_lang_file').'.ADMIN_ADD_CHOICE')</h4>
																					@php $pdt_choices = get_choices($pdtDetail->pro_id); @endphp
																					@if(count($pdt_choices) > 0 )
																						<ul>
																							@foreach($pdt_choices as $pdt_choice)
																								@php
																									if($pdt_choice->pc_price==''){
																										$pdt_choice_pc_price='0.00';
																									}else{
																										$pdt_choice_pc_price=$pdt_choice->pc_price;
																									}
																								@endphp
																								<li>
																									<label class="multi-checkbox" >{{$pdt_choice->ch_name}} <span>{{$pdtDetail->pro_currency.' '.$pdt_choice_pc_price}}</span>
																										<input type="checkbox" name="altered_choice_{{$pdtDetail->cart_id}}" value="{{$pdt_choice->ch_id}}~{{$pdt_choice_pc_price}}~{{$pdtDetail->pro_currency}}~{{$pdt_choice->ch_name}}" @if(in_array($pdt_choice->ch_id,$selectedChoice)) checked @endif>
																										<span class="checkmark"></span>
																									</label>
																								</li>
																							@endforeach
																						</ul>

																						<button class="choice-modal-btn" onclick="update_my_choice('{{$pdtDetail->pro_id}}','{{$pdtDetail->cart_id}}');">@lang(Session::get('front_lang_file').'.FRONT_UPDATE_CART')</button>
																					@endif

																				</div>
																			</div>

																		</div>

																	</div>
																</div>
															@endif

															{{-- special request modal --}}
															@if($splitStore[2]=='1')
																<div id="spl-request_{{$pdtDetail->cart_id}}" class="modal fade choice-modal" role="dialog">

																	<div class="modal-dialog">
																		
																		<!-- Modal content-->
																		<div class="modal-content">
																			<div class="modal-header">
																				<button type="button" class="close" data-dismiss="modal">&times;</button>
																				<img src="{{$path}}">
																			</div>
																			<div class="modal-body">
																				<div class="choice-modal-head">
																					<h2>{{$pdtDetail->item_name}}</h2>
																					<p>{{$pdtDetail->contains_name}}</p>
																				</div>
																				<div class="choice-modal-content">
																					<h4>@lang(Session::get('front_lang_file').'.ADMIN_ADD_SPL_REQ')</h4>

																					{{ Form::textarea('spl_request',$pdtDetail->cart_spl_req,['style'=>'width:100%','id'=>'req_'.$pdtDetail->cart_id,'rows'=>'3','class'=>'form-control'])}}
																					<button class="choice-modal-btn" onclick="update_spl_req('{{$pdtDetail->pro_id}}','{{$pdtDetail->cart_id}}');">@lang(Session::get('front_lang_file').'.ADMIN_UPDATE_NOTE')</button>
																				</div>

																			</div>

																		</div>

																	</div>
																</div>
															@endif
														@endforeach
														{{-- Check minimum order amount --}}
														{{ Form::hidden('',$min_order,['id' => 'or_minimum_amt'.$splitStore[1]])}}

														<li id="show_min_err{{$splitStore[1]}}" style="{{(($min_order > $shop_total_order_amount)) ? '' : 'display:none'}}" class="show_min_err">
															<span style="color:red">{{$splitStore[3]}}&nbsp;<span id="show_min_amt{{$splitStore[1]}}" data-min="{{$min_order}}">{{number_format($min_order - $shop_total_order_amount,2)}}</span>&nbsp;@lang(Session::get('front_lang_file').'.FRONT_MIN_OR_AMT')</span>
														</li>
													</ul>
												</div>
											</div>

										</div>

										@php $i++; @endphp

									@endforeach
								</div>
							</div>


							<div class="col-md-12 cart-total">
								<table>
									<tr>
										<td>@lang(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')</td>
										<td>{{$pdtDetail->cart_currency}}&nbsp;<span id="sub_total">{{number_format($sub_total,2)}}</span></td>
									</tr>
									
									@if(empty($delivery_fee_set) === false)
										@if($delivery_fee_set->gs_delivery_fee_status=='1' && $delivery_fee_set->gs_del_fee_type == 'common_fee')
											@php $delivery_fee = $delivery_fee_set->gs_delivery_fee; @endphp
										@elseif($delivery_fee_set->gs_delivery_fee_status=='1' && $delivery_fee_set->gs_del_fee_type == 'km_fee')
											@php 
											$delivery_fee = $km_del_fee; 
											$delfee_exist = 1;
											@endphp
										@endif
										
										<tr>
											
											<td>@lang(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE')</td>
											<td>{{$pdtDetail->cart_currency}}&nbsp;<span id="total_del_fee">{{number_format($delivery_fee,2)}}</span></td>
										</tr>

									@endif

									<tr>
										<td>@lang(Session::get('front_lang_file').'.ADMIN_TOTAL')</td>
										<td>{{$pdtDetail->cart_currency}}&nbsp;<span id="grant_total">{{number_format(($sub_total+$delivery_fee+$tax_total),2)}}</span></td>
									</tr>
									<tr>

										<td colspan="2">
											{{ Form::button(__(Session::get('front_lang_file').'.FRONT_CHECK_OUT'),['onClick' => 'chk_pre_order()']) }}
										</td>
									</tr>
								</table>
							</div>
						@else
							<div class="emt-cart"><!-- <i class="fa fa-shopping-cart fa-lg" aria-hidden="true"></i> -->
								<img src="{{url('')}}/public/front/images/empty-cart.png"   alt="" ><div> @lang(Session::get('front_lang_file').'.YOUR_CART-EMPTY')</div></div>
						@endif

					</div>
				</div>
			</div>
		</div>


	</div>


	<!-- Modal -->
	<div id="spl-request" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">@lang(Session::get('front_lang_file').'.FRONT_SPECIAL_REQ')</h4>
				</div>
				<div class="modal-body">
					<textarea style="width:100%; height:80px;"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default">@lang(Session::get('front_lang_file').'.FRONT_ADD')</button>
				</div>
			</div>

		</div>
	</div>

@section('script')


	<script>
        $(document).ready(function() {

            $(".toggle-accordion").on("click", function() {
                var accordionId = $(this).attr("accordion-id"),
                    numPanelOpen = $(accordionId + ' .collapse.in').length;

                $(this).toggleClass("active");

                if (numPanelOpen == 0) {
                    openAllPanels(accordionId);
                } else {
                    closeAllPanels(accordionId);
                }
            })

            openAllPanels = function(aId) {
                console.log("setAllPanelOpen");
                $(aId + ' .panel-collapse:not(".in")').collapse('show');
            }
            closeAllPanels = function(aId) {
                console.log("setAllPanelclose");
                $(aId + ' .panel-collapse.in').collapse('hide');
            }
            $('.cart_qtyClass').bind('keyup blur',function(){
                var node = $(this);
				var val_without_space = node.val().replace(/[^0-9]/g,'');
				if(val_without_space=='' || val_without_space=='0' ){ node.val(1);} else {  node.val(val_without_space); }
            });
            $('.cart_qtyClass').change(function(){
                var cart_id = $(this).attr('data-cartid');
                var max_qty = $('#cart_qty_'+cart_id).attr('data-max');
                var current_qty = $('#cart_qty_'+cart_id).val();
				if(current_qty!=''){
					var min_qty = 1;
					if(parseInt(current_qty) > parseInt(max_qty) )
					{
						$('#stockExceeds_'+cart_id).show();
						$('#cart_qty_'+cart_id).val(max_qty);
						update_my_cart(max_qty,cart_id);
					}
					else if(parseInt(current_qty) <= parseInt(min_qty))
					{
						alert('reached min quantity');
						update_my_cart(current_qty,cart_id);
					}
					else
					{
						update_my_cart(current_qty,cart_id);
					}
				}
            });
        });
	</script>




	<script src="{{url('')}}/public/front/js/handleCounter.js"></script>
	<!-- ADD/MINUS PLUGIN -->
	<script>
        function pluss(cart_id){
            var max_qty = $('#cart_qty_'+cart_id).attr('data-max');
            var current_qty = $('#cart_qty_'+cart_id).val();
            var plus_qty = parseInt(current_qty)+1;
            if(parseInt(current_qty) >= parseInt(max_qty) )
            {
                $('#stockExceeds_'+cart_id).show();
                $('#counter-plus-'+cart_id).attr('disabled');
                $('#counter-plus-'+cart_id).css({'background':'transparent'});
            }
            else
            {
                $('#cart_qty_'+cart_id).val(plus_qty);
                $('#stockExceeds_'+cart_id).hide();
                $('#counter-plus-'+cart_id).removeAttr('disabled').css({'background':'transparent'});
                $('#counter-minus-'+cart_id).removeAttr('disabled').css({'background':'transparent'});
                update_my_cart(plus_qty,cart_id);
            }
        }
        function minus(cart_id){
            var current_qty = $('#cart_qty_'+cart_id).val();
            var minus_qty = parseInt(current_qty)-1;
            var min_qty = 1;
            if(parseInt(current_qty) <= parseInt(min_qty) )
            {
                $('#counter-minus-'+cart_id).attr('disabled');
                $('#counter-minus-'+cart_id).css({'background':'transparent'});
                $('#stockExceeds_'+cart_id).html("@lang(Session::get('front_lang_file').'.FRONT_REACHED_MIN_QTY')").show();
            }
            else
            {
                $('#cart_qty_'+cart_id).val(minus_qty);
                $('#stockExceeds_'+cart_id).hide();
                $('#counter-minus-'+cart_id).removeAttr('disabled').css({'background':'transparent'});
                $('#counter-plus-'+cart_id).removeAttr('disabled').css({'background':'transparent'});
                update_my_cart(minus_qty,cart_id);
            }
        }
        $(function ($) {
			@if(count($pdtsInCart) > 0 )
			@foreach($pdtsInCart as $key=>$value)
			@if(count($value) > 0 )
			@foreach($value as $pdtDetail)
            /*$('#handleCounter_{{$pdtDetail->cart_id}}').handleCounter({
							minimum: 1,
							maximize: {{$pdtDetail->stock}},
							onChange: function(e){
								alert('s');
								if(parseInt(e) < parseInt({{$pdtDetail->stock}})) {
									$('#stockExceeds_{{$pdtDetail->cart_id}}').hide();
								}
								else
								{
									$('#stockExceeds_{{$pdtDetail->cart_id}}').show();
								}

								if($('#handleCountText_{{$pdtDetail->cart_id}}').val()!=0)
								{
									update_my_cart(e,{{$pdtDetail->cart_id}});
								}
							},
							onMaximize: function(e) {
								console.log('Reached maximize '+e);
								$('#stockExceeds_{{$pdtDetail->cart_id}}').show();
							}
						});*/
			@if ($pdtDetail->cart_pre_order !== NULL && $pdtDetail->cart_pre_order !== '0000-00-00 00:00:00')
            $('#preorderDateDiv_{{$pdtDetail->store_id_is}}').show();
            $('#preorderBtnDiv_{{$pdtDetail->store_id_is}}').show();
            $('#pochk_{{$pdtDetail->store_id_is}}').attr('checked','checked');
            $('#preorderDate_{{$pdtDetail->store_id_is}}').val('{{date("Y-m-d H:i",strtotime($pdtDetail->cart_pre_order))}}');

			@endif

			@endforeach
			@endif
			@endforeach
			@endif
        });

        function update_my_choice(pro_id,cart_id)
        {
            //$('#handleCountText_'+cart_id).val('1');
            var checkedVals = $('input:checked[name="altered_choice_'+cart_id+'"]').map(function() { return this.value; }).get();
            //alert(checkedVals.join(",")); return false;
            var sub_total =  $('#sub_total').text().replace(/,/g , '');
            var grant_total = $('#grant_total').text().replace(/,/g , '');
            var item_total = $('#cart_price_'+cart_id).text().replace(/,/g , '');
			//$(this).html($(this).html().replace(/,/g , ''));
            $.ajax({
                'data' : {'checkedVals':checkedVals.join(","),'cart_id':cart_id},
                'type' : 'post',
                'url'  : '<?php echo url('update-cart-choice')?>',
                success:function(data){	

                    $('#choice-modal_'+cart_id).modal('hide');
                    data = jQuery.parseJSON(data);
                    //console.log(data); return false;

                    $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+data.msg+'</strong></div>');
                    var diff_amt = data.item_total_amt - item_total;
                   // alert(data.item_total_amt+'\n'+item_total+'\n'+diff_amt);
                    /* Update sub total and grant total */
                    if(diff_amt > 0)
                    {
                        sub_total = parseFloat(sub_total) + Math.abs(diff_amt);
                        grant_total = parseFloat(grant_total) + Math.abs(diff_amt);
                    }
                    /* decrease quantity */
                    else
                    {
                        sub_total = parseFloat(sub_total) - Math.abs(diff_amt);
                        grant_total = parseFloat(grant_total) - Math.abs(diff_amt);
                    }
                    /* check minimum order */
                    var mini_order = $('#show_min_amt'+data.store_id).attr('data-min');
                    //alert(parseInt(mini_order) - data.store_total_amount);
                    if(parseInt(mini_order) > data.store_total_amount)
                    {
                        $('#show_min_amt'+data.store_id).html((mini_order - data.store_total_amount).toFixed(2));
                        $('#show_min_err'+data.store_id).show();

                    }
                    else if(parseInt(mini_order) <= data.store_total_amount)
                    {
                        //$('li').remove('#show_min_err'+data.store_id);
                        $('#show_min_err'+data.store_id).css({'display' : 'none'});

                    }
                    //alert(data.exist_cart_id);
                    /* updating exist cart id with same choice list */
                    if(data.exist_cart_id != '0')
                    {
                        $( "li" ).remove("#cart_list_"+cart_id);
                        $('#cart_price_'+data.exist_cart_id).html(number_format(data.item_total_amt, 2, '.', ','));

                        $('#cart_tax_'+data.exist_cart_id).html(number_format(data.tax, 2, '.', ','));
                        $('#choice_array_'+data.exist_cart_id).html(data.choice_list);
                        $('#cart_qty_'+data.exist_cart_id).val(data.quantity);
                    }
                    else
                    {
                        $('#cart_price_'+cart_id).html(number_format(data.item_total_amt, 2, '.', ','));
                        $('#cart_tax_'+cart_id).html(number_format(data.tax, 2, '.', ','));
                        $('#choice_array_'+cart_id).html(data.choice_list);
                        $('#choiceDivToggle'+cart_id).show();
                        $('#cart_qty_'+cart_id).val(data.quantity);
                    }
					$('.cart-amt').html(number_format(sub_total, 2, '.', ','));
                    $('#sub_total').text(sub_total.toFixed(2));
                    $('#grant_total').text(grant_total.toFixed(2));
                    $('html, body').animate({
                        'scrollTop' : '0'
                    });
                    //window.location.reload();
                    return false;
                }

            });

        }
        function update_my_cart(qty,cart_id)
        {

            var sub_total =  $('#sub_total').text().replace( /,/g, "" );
            var grant_total = $('#grant_total').text().replace( /,/g, "" );
            var item_total = $('#cart_price_'+cart_id).text().replace( /,/g, "" );

            $.ajax({
                'data' : {'qty':qty,'cart_id':cart_id},
                'type' : 'post',
                'datatype' : 'json',
                'url'  : '<?php echo url('update-cart-items')?>',
                success:function(data)
                {	data = jQuery.parseJSON(data);
                    if(data.action == "success")
                    {

                        $('#cart_price_'+cart_id).html(number_format(data.item_total_amt, 2, '.', ','));
                        $('#cart_tax_'+cart_id).html(number_format(data.tax, 2, '.', ','));
                        $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+data.msg+'</strong></div>');
                        var diff_amt = data.item_total_amt - item_total;
                        /* Update sub total and grant total */
                        /* increase quantity */
                        if(diff_amt > 0)
                        {
                            sub_total = parseFloat(sub_total) + Math.abs(diff_amt);
                            grant_total = parseFloat(grant_total) + Math.abs(diff_amt);
                        }
                        /* decrease quantity */
                        else
                        {
                            sub_total = parseFloat(sub_total) - Math.abs(diff_amt);
                            grant_total = parseFloat(grant_total) - Math.abs(diff_amt);
                        }
                        /* check minimum order */
                        var mini_order = $('#show_min_amt'+data.store_id).attr('data-min');
                        //alert(parseInt(mini_order) +'/'+ data.store_total_amount);
                        //alert(data.store_total_amount);
                        if(parseInt(mini_order) > data.store_total_amount)
                        {
                            $('#show_min_amt'+data.store_id).html((mini_order - data.store_total_amount).toFixed(2));
                            $('#show_min_err'+data.store_id).show();
                        }
                        else if(parseInt(mini_order) <= data.store_total_amount)
                        {
                            $('#show_min_err'+data.store_id).css({'display' : 'none'});
                        }
						$('.cart-amt').html(number_format(sub_total, 2, '.', ','));
                        $('#sub_total').text(number_format(sub_total, 2, '.', ','));
                        $('#grant_total').text(number_format(grant_total, 2, '.', ','));
                        $('html, body').animate({
                            'scrollTop' : '0'
                        });
                        //window.location.reload();
                        return false;
                    }
                    else if(data.action == "failed")
                    {
                        $('#cart_qty_'+cart_id).val(parseInt(qty)-1);
                        $('#stockExceeds_'+cart_id).show();
                    }

                    },
				error: function(xhr, status, error) {
					var err = eval("(" + xhr.responseText + ")");
					alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
				}

            });
        }
        function number_format2 (number, decimals, dec_point, thousands_sep) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
        function number_format(number, decimals, dec_point, thousands_point) {

            if (number == null || !isFinite(number)) {
                throw new TypeError("number is not valid");
            }

            if (!decimals) {
                var len = number.toString().split('.').length;
                decimals = len > 1 ? len : 0;
            }

            if (!dec_point) {
                dec_point = '.';
            }

            if (!thousands_point) {
                thousands_point = ',';
            }

            number = parseFloat(number).toFixed(decimals);

            number = number.replace(".", dec_point);

            var splitNum = number.split(dec_point);
            splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
            number = splitNum.join(dec_point);

            return number;
        }
		
        /* add special request */
        function update_spl_req(pro_id,cart_id)
        {
            var content = document.getElementById('req_'+cart_id).value;
            if(content.trim() == '')
            {
                $('#req_'+cart_id).css({'border':'1px solid red'});
				return false;
            }

            $.ajax({
                'type'	:'POST',
                'data' 	: {'_token' : "{{csrf_token()}}",'cart_id':cart_id,'request_content':content},
                'url'	: '{{url('update_spl_req')}}',
                success:function(response)
                {
                    $('#req_'+cart_id).val(content);
                    $('#content_'+cart_id).html(content);
                    $('#spl-request_'+cart_id).modal('hide');
                    $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+"@lang(Session::get('front_lang_file').'.ADMIN_UPDATE_SL_REQ')"+'</strong></div>');
                    $('html, body').animate({
                        'scrollTop' : '0'
                    });

                    },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
                }
            });

        }

        /* remove cart*/
        function remove_cart(cart_id,cart_amount,store_id)
        {
			

            var cart_amount = $('#cart_price_'+cart_id).text().replace( /,/g, "" );
            var sub_total =  $('#sub_total').text().replace( /,/g, "" );
            var grant_total = $('#grant_total').text().replace( /,/g, "" );
            var grant_del_fee = '<?php echo $delivery_fee; ?>';
            var delfee_exist = '<?php echo $delfee_exist; ?>';
            var st_del_fee = $('#st_delfee'+store_id).val();
            //alert(grant_del_fee+'//'+st_del_fee+'//'+delfee_exist);

            // var sub_total =  $('#sub_total').text();
            // var grant_total = $('#grant_total').text();


            $.ajax({
                'data' : {'cart_id':cart_id,'store_id':store_id},
                'type' : 'post',
                'datatype' : 'json',
                'url'  : '<?php echo url('remove_cart')?>',
                success:function(response)
                {
                    data = response.split('`');
                    $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+data[0]+'</strong></div>');

                    sub_total = parseFloat(sub_total) - parseFloat(cart_amount);

                    grant_total = parseFloat(grant_total) - parseFloat(cart_amount);

                    $('#sub_total').text(number_format(sub_total, 2, '.', ','));
                    $('#grant_total').text(number_format(grant_total, 2, '.', ','));

                    // $('#sub_total').text(sub_total.toFixed(2));
                    // $('#grant_total').text(grant_total.toFixed(2));
                    $('#total_cart_count').text(data[1]);
                    $('#cart_count').html(data[1]);
					if(data[1] > 1){
						$('#remove_all_btn').show();
					}else{
						$('#remove_all_btn').hide();
					}
                    $('.cart-tot').html(data[1]);
                    $('#quick-cart-product-count').html(data[1]);
                    $('.cart-amt').html(number_format(sub_total, 2, '.', ','));
                    /* check minimum order */
                    // window.location.reload();
                    var mini_order = document.getElementById('or_minimum_amt'+store_id).value;
                    //alert(parseInt(mini_order) - data[2]);
                    if(parseInt(mini_order) > data[3])
                    {
                        $('#show_min_amt'+store_id).html((mini_order - data[3]).toFixed(2));
                        $('#show_min_err'+store_id).show();

                    }
                    else if(parseInt(mini_order) <= data[3])
                    {
                        //$('li').remove('#show_min_err'+data.store_id);
                        $('#show_min_err'+store_id).css({'display' : 'none'});

                    }
                    /* if no item in store then remove that store div */
                    //alert(data[2]); return false;
                    if(data[2] == 0)
                    {
                        //$("div").remove("#store_list_"+store_id);
						$("#store_list_"+store_id).animate({width: 0,height:0, "padding-left": 0, "padding-right": 0, "margin-left": 0, "margin-right": 0}, 500, function() {
							$( "div" ).remove("#store_list_"+store_id);
						});
                        /* subtract store delivery fee if all items in store are removed */
                        if(delfee_exist.trim() == 1)
                        {
                        	var diff_del_fee = parseFloat(grant_del_fee) - parseFloat(st_del_fee);
                        	$('#total_del_fee').text(number_format(diff_del_fee, 2, '.', ','));
                        }
                        
                    }
                    else	/* else remove prticular product */
                    {
						$("#cart_list_"+cart_id).animate({width: 0,height:0, "padding-left": 0, "padding-right": 0, "margin-left": 0, "margin-right": 0}, 500, function() {
							$( "li" ).remove("#cart_list_"+cart_id);
							
						});
                       //$( "li" ).remove("#cart_list_"+cart_id);
                    }

                    /*$('html, body').animate({
                        'scrollTop' : '0'
                    });*/
                    if(data[1] == 0)
                    {
                        window.location.reload();
                    }
                    return false;

				},
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
                }

            });
        }



		function clearSaveCart(){
			if(confirm("@lang(Session::get('front_lang_file').'.ITEM_NOT_DELIVERED')")){
				var list_values = $("input[name='not_delivered_items[]']").map(function () {
					return $(this).val();
				}).get();
				$.ajax({
					type:'post',
					async : false,
					url :"<?php echo url("clearSavecart"); ?>",
					data:{list_values:list_values},
					success:function(response){
						window.location.reload();
					}
				});
			}else{
				return false; 
			}
			//return false;
		}
        /** check pre order for restaurant closed days **/
		
		function chk_pre_order()
        {
			var store_id = $("input[name='pre_order_checkbox[]']:checked").map(function () { return $(this).val(); }).get();
			 var attListArray = new Array();
			 var i=0;
			var preorderCount = $("input[name='pre_order_checkbox[]']:checked").length;
			var error_count = 0;
			$('.preorderErrorMessage').html('');
			if(preorderCount > 0 ){
				$("input[name='pre_order_checkbox[]']:checked").each(function (){
					attListArray[i] = $('#preorderDate_'+$(this).val()).val();
					i++;
				});
				
				
				$.ajax({
					'data' : {'selectedDate':attListArray,'store_id':store_id},
					'type' : 'post',
					'dataType': 'json',
					'async':false,
					'url'  : '<?php echo url('save-check-pre-order')?>',
					beforeSend : function()
					{
						$('.preorderErrorMessage').html('');
					},
					success:function(data)
					{
						$.each(data, function(idx, obj) {
							if(obj.status=='failed'){
								$('#pre_order_msg_'+obj.store_id).html(obj.message).css({'color':'#7f1900','font-size': '16px','cursor' : 'default'});
								error_count++;
							}
						});
					},
					error: function(xhr, status, error) {
					  var err = eval("(" + xhr.responseText + ")");
					  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
					}
			   });
			}
		   if(error_count > 0 )
		   {
				return false;
		   }
			var invalid_delivery_rest = '{{$total_invalid_deliver}}';
			if(invalid_delivery_rest > 0 ){
				clearSaveCart();
			}
			else{
				/*var arr = $('input[name="sh_status[]"]').val();*/
				var val = [];
				var count = 0;
				var ajax_count = 0;
				var err_count = $(".show_min_err:visible").length;
				$('input[name^="sh_status"]').each(function(i) {
					var t = $('#pochk_'+$(this).attr('data-storeid')).is(':checked');
					var type = $('#pochk_'+$(this).attr('data-storeid')).attr('data-sttype');
					val[i] = $(this).val();
					if(val[i] == "Closed" && t == false && !isNaN(type))
					{
						$('#err'+(i)).addClass('error-preorder').html('<i class="fa fa-exclamation-circle"></i>@lang(Session::get('front_lang_file').'.FRONT_NOW') '+	$(this).attr('data-storename') +" @lang(Session::get('front_lang_file').'.FRONT_SELECT_PRE_ORDER')");
						$('#err'+(i)).css({'color' : 'red'});
						count++;
					}
					
				});

				
				if(parseInt(count) > 0)
				{
					return false;
				} 
				else if(parseInt(err_count) > 0)
				{
					//alert("Update Cart Amount Above the Minimum Order Amount");
					$('#mySuxesMsg').html('<div class="alert alert-danger text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+'@lang(Session::get('front_lang_file').'.FRONT_UPDATE_CART_MINIMUM')'+'</strong></div>');
					$('html, body').animate({
						'scrollTop' : '0'
					});
					return false;
				}
				else
				{
					window.location.href="<?php echo url('checkout'); ?>";
				}
			}

        }
	</script>
	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/jquery.simple-dtpicker.css" />
	<script src="{{url('')}}/public/admin/assets/scripts/jquery.simple-dtpicker.js"></script>
	<script type="text/javascript">
        var whole_arr = [{'date' : '2018-11-03',time :'15'}];
        $('.preOrderDatePicker').appendDtpicker({
            "autodateOnStart": true,
			"closeOnSelected": true,
            "futureOnly": true,
            //"minTime":"00:00",
            //"maxTime":"05:00"
            //"minTime":["08:30", "08:30", "08:30", "08:30", "08:30"],
            //"maxTime":["05:30", "05:30", "05:30", "05:30", "05:30"],
           // "allowWdays": [1, 2, 3, 4, 5], // 0: Sun, 1: Mon, 2: Tue, 3: Wed, 4: Thr, 5: Fri, 6: Sat
            "disableTime":whole_arr,
			
        });

	</script>

	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
    <script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>
    
	<script>
        $(document).ready(function () {
            $(".preOrderDatePicker").datepicker({
                format: 'mm/dd/yyyy',
                startDate: '+0d',
                autoclose: true
            });
            
            $('input[name="pre_order_checkbox[]"]').click(function() {
                
                if($(this).is(':checked'))
                {
                    
                    $('#preorderDateDiv_'+$(this).val()).show();
                    $('#preorderBtnDiv_'+$(this).val()).show();

                }
                else
                {
                    $('#preorderDateDiv_'+$(this).val()).hide();
                    $('#preorderBtnDiv_'+$(this).val()).hide();
                    remove_preorder($(this).val());
                }

            });
			$('textarea[name=spl_request]').keyup(function(){
				 $(this).css({'border':'1px solid #ced4da'});
			});
        });
		
		
        function save_preorder(store_id)
        {
            var selectedDate = $('#preorderDate_'+store_id).val();
            if(selectedDate=='')
            {
                alert('Please select date');
                return false;
            }
            $.ajax({
                'data' : {'selectedDate':selectedDate,'store_id':store_id},
                'type' : 'post',
				'async': false,
                'url'  : '<?php echo url('save-pre-order')?>',
                beforeSend : function()
                {
                    $('#pre_order_msg_'+store_id).css({'cursor': 'no-drop'});
                },
                success:function(data)
				{
					var split_data = data.split('`~');
					if(split_data[0]=='0'){
						$('#pre_ajaxErr_'+store_id).attr('data-ajaxErrCode', split_data[0]);
						$('#pre_ajaxErr_'+store_id).attr('data-ajaxErrMsg', split_data[1]);
					} else{
						$('#pre_ajaxErr_'+store_id).attr('data-ajaxErrCode', '');
						$('#pre_ajaxErr_'+store_id).attr('data-ajaxErrMsg', '');
					}
					$('.pre-header').focus();
					$('#pre_order_msg_'+store_id).html(split_data[1]).css({'color':'#7f1900','font-size': '16px','cursor' : 'default'});
					//window.location.reload();
				},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
				}
		   });
        }
        function remove_preorder(store_id)
        {
            $.ajax({
			'data' : {'store_id':store_id},
			'type' : 'post',
			'url'  : '<?php echo url('remove-pre-order')?>',
			success:function(data)
			{
				window.location.reload();
			}
		  });
        }
		function remove_all_from_cart(){
			if(confirm("@lang(Session::get('front_lang_file').'.FRONT_REMOVE_ALL_CONFIRM')")){
				return true;
			}else{
				return false;
			}
		}
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
        $('.tooltip-demo').tooltip({placement:'top'})
	</script>





@endsection
@stop