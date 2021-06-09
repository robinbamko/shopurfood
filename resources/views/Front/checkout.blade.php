@extends('Front.layouts.default')
@section('content')
<style>
.section11{background: #f8f7f3;}
.checkoutDetail .panel-heading a{padding:15px 20px 12px;border-radius: 20px;box-shadow: none !important;background: transparent;
color: #383757;border: 1px solid #383757;transition: cubic-bezier(1,0,0,1) 0.5s;}
.checkoutDetail .panel-heading a:hover {background: #383757; box-shadow: 0px -3px 0px #383757;}
.checkoutDetail .panel-heading a:not(.collapsed)
{
	background: #383757; box-shadow: 0px -3px 0px #383757;border-bottom-left-radius: 0px;
border-bottom-right-radius: 0px;
}
.checkoutDetail .checkout-add{background:#fff; border:0; font-family: 'TruenoLt'; font-size:14px;}
.checkoutDetail .shipping-details-inner, .checkoutDetail .shipping-method-inner{background:#fff; border-bottom:0;}
.input-field label{font-size:13px;}
.checkoutDetail .checkout-add .input-field input, .checkoutDetail .checkout-add .input-field input[type="text"], .checkoutDetail .checkout-add .input-field select{padding:5px 10px;}
.shipping-details table tr th:nth-child(2){width:36%;}
.shipping-details button{background:transparent!important;}
.shipping-details i{color: #ff5215; font-size: 25px; float: right; margin: 10px 0;}
.panel-heading {
	padding: 20px 15px;
	border-bottom: 1px solid #000;
}
.panel.panel-default {
	margin-bottom: 20px;
}
.panel-heading h4 {
	margin: 0;
	font-size: 16px;
	font-family: 'TruenoSBd';
}
.collapse-group {
	padding: 10px;
	width: 100%;
	margin-bottom: 10px;
}
.panel-title .trigger:before {
	content: '\e082';
	font-family: 'Glyphicons Halflings';
	vertical-align: text-bottom;
}

.panel-title .trigger.collapsed:before {
	content: '\e081';
}

#phoneNum1,#phoneNum2
{
	padding-left:50px;
}
.checkoutDetail .checkout-add .input-field input, .checkoutDetail .checkout-add .input-field select {
    padding:5px 10px;
    height: auto;
}
.price-right{ background: #f5f5f5;  border-bottom: 0px solid #ccc; font-size: 15px;}
.price-right h5{ background: #ff5215; font-family:'Poppins-SemiBold';
    color: #fff;
    box-shadow: 0px -3px 0px #d2810a; font-size: 16px; padding: 15px 20px 10px; text-transform: uppercase;}
.price-right p{ padding: 0px 0px 15px 10px;}


.fixedElement {
	background: #fff;
	border-radius: 15px;
	overflow: hidden;
	top:0;
	width:100% !important;
	z-index:9;
	position: sticky !important;
	box-shadow: 3px 4px 13px #ddd;
}
.detailTD p {font-weight: 400;color: #616161;}
.detailTD small {color: #616161;}
.detailTD p.title {font-weight: 400;margin-bottom: 5px;}
.detailTD h5 {color: #ff5215;font-size: 1rem;margin-bottom: 0px;}
.price-right .col-md-5 {text-align: right;}
.price-right .col-md-5 h6 {color: #ff5215;padding: 10px;font-weight: 400;margin: 0px;}
.price-right .col-md-5 h6 #total_amt {font-weight: 600;}
.price-right .totDiv  {background: #fff;}

.price-right  p {padding: 10px;}
.checkout_verify {background: #a7ea7e;color: #fff;padding: 4%;}
.checkoutDetail .radiolabel input[type="checkbox"]  {visibility: hidden;}
.checkoutDetail .radiolabel input[type="checkbox"]:checked + .fa {visibility: visible;
color: #fff;
background: #ff5215;
top: 0px;
left: 0px;
padding: 2px;}
.input-field input {height: 35px;margin-bottom: 8px;}
.checkoutDetail input[type="submit"], .checkoutDetail button, .checkoutDetail .btn {background-color: #ff5215;}
.checkoutDetail .radiolabel span {border: 1px solid #ff5215;}
.checkoutDetail .radiolabel input[type="radio"]:checked + .fa {background: #ff5215;}

/*.checkout_verify span*/
</style>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

@if($errors->any())
<div class="alert alert-warning alert-dismissible" style="color: #721c24;background-color: #f8d7da;text-align: center;font-weight: bold;">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

<div class="main-sec">
<div id="mySuxesMsg">
	@if(session()->has('message'))
		<div class="alert alert-{{ session('message') }}">
			{{ Session::get('message') }}
		</div>
	@endif
</div>
<div class="section11">
	<div class="container">
		
			@php $sub_total = 0; $km_del_fee = 0; 
				$user_lat = Session::get('search_latitude');
				$user_long = Session::get('search_longitude');
			@endphp
			<div class="collapse-group checkoutDetail">
				{{ Form::open(['method' => 'post','id' => 'validate_form'])}}
				<div class="row">	
					<div class="col-md-8 col-xs-12">


					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" href="#collapseOne"  aria-controls="collapseOne" class="trigger">
									@lang(Session::get('front_lang_file').'.ADMIN_SHIP_ADDR') 
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse show" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
								
								<div class="checkout-add">
									@if($SELF_PICKUP_STATUS==1)
									<div class="input-field">
										<div class="form-left">
											<label>@lang(Session::get('front_lang_file').'.ADMIN_SELF_PICKUP')*</label>
											{{ Form::select('ord_self_pickup',['0' => 'No','1' => 'Yes'],'',['id' =>'ord_self_pickup','onChange' => 'show_ship(this.value);'])}}
										</div>
									</div>
									@else
									{{Form::hidden('ord_self_pickup','0',['id' =>'ord_self_pickup'])}}
									@endif
								<div class="shipAddressField" style="display:none">
									{{-- set shipping address --}}
									@php $name = $lname = $mail = $ph1 = $ph2 = $addr = $lat = $lon = '' ; 
										 $wallet_amt	= 0; $user_payment_status =  1;	$mail_verify = 0;
										 $prof_mail = $prof_phone = '';
										 $redirect_url = 'checkout';
									@endphp
									@if(empty($shipping_details) === false)
										@php 
											$name = $shipping_details->sh_cus_fname;
											$lname = $shipping_details->sh_cus_lname;
											$mail = $shipping_details->sh_cus_email;
											$sh_building_no = $shipping_details->sh_building_no;
											$ph1 = $shipping_details->sh_phone1;
											$ph2 = $shipping_details->sh_phone2;
											$addr = $shipping_details->sh_location;
											$lat = $shipping_details->sh_latitude;
											$lon = $shipping_details->sh_longitude;
											
										@endphp
									@endif
									@if(empty($customer_details) === false)
										@php 
										$wallet_amt = number_format(($customer_details->cus_wallet - $customer_details->used_wallet),2);
										$mail_verify = $customer_details->mail_verify;
										$prof_phone = $customer_details->cus_phone1;
										$prof_mail = $customer_details->cus_email;
										@endphp
										@if($customer_details->cus_paymaya_status=='Unpublish' && $customer_details->cus_netbank_status=='Unpublish')
											@php $user_payment_status = 0;												   
											@endphp
										@endif
									@endif
									
									{{-- set shipping address ends--}}
									{{ Form::checkbox('check_addr','','',['id' => 'same_address'])}}
									{{ Form::label('same_address', __(Session::get('front_lang_file').'.ADMIN_SAME_SHIP_ADDR'))}}
									<div class="input-field">
										<div class="form-left">
											<label>@lang(Session::get('front_lang_file').'.FRONT_FIRST_NAME')*</label>
											{{ Form::text('name','',['id' => 'name','placeholder' => __(Session::get('front_lang_file').'.FRONT_FIRST_NAME'),'required'])}}
										</div>
										<div class="form-right">
											<label>@lang(Session::get('front_lang_file').'.FRONT_LAST_NAME') *</label>
											{{ Form::text('lname','',['id' => 'lname','placeholder' => __(Session::get('front_lang_file').'.FRONT_LAST_NAME')])}}
										</div>
									</div>	
									<div class="input-field">
										<div class="form-left">
											{{--  ,'onchange'=>'send_verify_mail(this.value);' --}}
											<label>@lang(Session::get('front_lang_file').'.ADMIN_REG_MAIL')*</label>
											{{ Form::email('mail','',['id' => 'mail','placeholder' => __(Session::get('front_lang_file').'.ADMIN_REG_MAIL'),'onchange'=>'send_verify_mail(this.value);'])}}

										</div>
									</div>
									<div class="input-field">
										<div class="form-left">
											<!--,'onchange'=>'check_mobile_with_otp();'-->
											<label>@lang(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE')*</label>
											{{ Form::text('phone1','',['id' => 'phoneNum1','placeholder' => __(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE'),'onkeyup'=>'validate_phone(\'phoneNum1\');'])}}
											<span id="new_phone_err" style="color:red"></span>
										</div>
										<div class="form-right">
											<label>@lang(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')</label>
											{{ Form::text('phone2','',['id' => 'phoneNum2','placeholder' => __(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER'),'onkeyup'=>'validate_phone(\'phoneNum2\');'])}}
										</div>
									</div>						
									<div class="input-field">
										<label>@lang(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDRESS')* 
											<!--<small style="color:#7f1900">(@lang(Session::get('front_lang_file').'.FRONT_ONLY_TO_SEARCH_ADDR'))</small>--></label>
										
										{{ Form::text('address',$addr,['id' => 'address','readonly'=>'readonly','placeholder' => __(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDRESS'),])}}
									</div>
									<div class="input-field">
										<label>@lang(Session::get('front_lang_file').'.LANDMARK')</label>
										{{ Form::textarea('sh_building_no','',['id' => 'sh_building_no','placeholder' => __(Session::get('front_lang_file').'.LANDMARK_PLACEHOLDER')])}}
									</div>
									
									<div class="input-field" style="display:none">
										<div id="us3" style="width: 100%; height: 400px;"></div>
									</div>
									<div class="input-field">
										<div class="form-left">
											<label>@lang(Session::get('front_lang_file').'.ADMIN_LATITUDE')*</label>
											{{ Form::text('lat',Session::get('search_latitude'),['id' => 'latitude','readonly','placeholder' => __(Session::get('front_lang_file').'.ADMIN_LATITUDE'),'required'])}}
											
										</div>
										<div class="form-right">
											<label>@lang(Session::get('front_lang_file').'.ADMIN_LONGITUDE')*</label>
											{{ Form::text('long',Session::get('search_longitude'),['id' => 'longitude','readonly','placeholder' => __(Session::get('front_lang_file').'.ADMIN_LONGITUDE'),'required'])}}
										</div>
									</div>	
									
								</div>	
								</div>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingTwo">
							<h4 class="panel-title">
								
								<a role="button" data-toggle="collapse" href="#collapseTwo"  aria-controls="collapseTwo" class="trigger">
									@lang(Session::get('front_lang_file').'.ADMIN_ORDER_REVIEW')											  
								</a>
								<!--<span><a href="{{url('cart')}}">Edit Cart </a></span>-->
							</h4>
							
						</div>
						<div id="collapseTwo" class="panel-collapse show" role="tabpanel" aria-labelledby="headingTwo">
							<div class="panel-body">
								<div class="shipping-details-inner shipping-details">
									<a href="{{url('cart')}}" title="@lang(Session::get('front_lang_file').'.FRONT_ED_CART')">
										<i class="fa fa-edit"></i>
									<!--@lang(Session::get('front_lang_file').'.FRONT_ED_CART')--> </a>
									@php $currency = "USD";	$refund_status_arr = array(); @endphp
									<div class="table-responsive">
									<table class="table">
										<thead>
											<th>@lang(Session::get('front_lang_file').'.FRONT_IMAGE')</th>
											<th>@lang(Session::get('front_lang_file').'.FRONT_DETA')</th>
											<th>@lang(Session::get('front_lang_file').'.FRONT_PO_DATE')</th>
											<th>@lang(Session::get('front_lang_file').'.FRONT_QTY')</th>
											<th>@lang(Session::get('front_lang_file').'.FRONT_PRCE')</th>
										</thead>
										@if(count($get_cart_details) > 0 )
											
								 		@foreach($get_cart_details as $key=>$value)
								 		@php $splitStore = explode('~`',$key);
								 			 $user
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
										
										{{--  calculate delivery fee bacsed on kilometer ends --}}
										@foreach($value as $pdtDetail)
											@php $currency = $pdtDetail->cart_currency;
												$path = url('').'/public/images/noimage/'.$no_item;
												array_push($refund_status_arr,$pdtDetail->refund_status);
											 @endphp
											@if($pdtDetail->cart_type=='1')   {{-- Product cart type --}}
											@php $img = explode('/**/',$pdtDetail->pro_images); $url = url(''); $pdtUrl = url('').'/product-details/'.base64_encode($pdtDetail->pro_id); @endphp
												@if(count($img) > 0)
												@php $filename = public_path('images/store/products/').$img[0]; @endphp
													@if($img[0] != '' && file_exists($filename))
														@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
													@endif
												
												@endif
											@else 			{{-- Item cart type --}}
												@php 
												$img = explode('/**/',$pdtDetail->pro_images); $url = url('');  $pdtUrl = url('').'/'.$pdtDetail->store_slug.'/item-details/'.$pdtDetail->pro_item_slug; 
												@endphp
												@if(count($img) > 0)
													@php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
													@if($img[0] != '' && file_exists($filename))
														@php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
													@endif
												
												@endif
											@endif

												<tr class="detailTD">
													<td>								
														<img src="{{$path}}" width="80">								
													</td>
													<td >								
														<p class="title"><a href="{{$pdtUrl}}" target="_blank">{{$pdtDetail->item_name}}</a></p>
														<h5 class="price">{{$pdtDetail->cart_currency.' '.number_format($pdtDetail->cart_unit_amt,2)}}</h5>
														@php $selectedChoice = array(); @endphp
														@if($pdtDetail->cart_had_choice=='Yes')
															@php $choices = json_decode($pdtDetail->cart_choices_id,true);  @endphp
															@if(count($choices) > 0 )
																@php $pluscount=1; @endphp
																<span>
																	@foreach($choices as $choice)
																		@php array_push($selectedChoice,$choice['choice_id']); @endphp
																		@php  $choiceName = get_choice_name($choice['choice_id']); @endphp
																		@if(empty($choiceName) === false )
																			@php $display_choice=$choiceName->ch_name; @endphp
																		@else
																			@php $display_choice=choice['choice_id']; @endphp
																		@endif
																		{{$display_choice.'( '.$pdtDetail->cart_currency.' '.number_format(($pdtDetail->cart_quantity*$choice['choice_price']),2).' )'}} @if(count($choices)!=$pluscount) + @endif
																		@php $pluscount++; @endphp
																	@endforeach
																</span>
															@endif
														@endif
														<small>@lang(session::get('front_lang_file').'.FRONT_EXCL_TAX') : {{$pdtDetail->cart_currency.' '.number_format($pdtDetail->cart_tax,2)}}</small>
														<p style="color:#7f1900;">{{ucfirst($pdtDetail->cart_spl_req)}}</p>
													</td>
													<td>
														<p>{{($pdtDetail->cart_pre_order !='') ? $pdtDetail->cart_pre_order : "-"}}</p>
													</td>
													
													<td>	
														<p>{{$pdtDetail->cart_quantity}}</p>
													</td>
													@php $sub_total +=$pdtDetail->cart_total_amt; @endphp
													<td>
														<h5 class="price">
															{{$pdtDetail->cart_currency.' '.number_format($pdtDetail->cart_total_amt,2)}}
														</h5>
													</td>
													
												</tr>
											@endforeach
										@endforeach
										
										@else
											@lang(Session::get('front_lang_file').'.ADMIN_NO_ITEM')
										@endif
									</table>	
									</div>										
								</div>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingThree">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" href="#collapseThree"  aria-controls="collapseThree" class="trigger">
									@lang(Session::get('front_lang_file').'.ADMIN_PAYMENT_METHOD')
								</a>
							</h4>
						</div>

						<div id="collapseThree" class="panel-collapse show" role="tabpanel" aria-labelledby="headingThree">
							<div class="panel-body" >
								{{-- Show wallet --}}

								@if($wallet_amt > 0 )
								<label class="radiolabel" for="use_wallet">
									<span class="pay-check">
										{{ Form::checkbox('use_wallet','1','',['id' => 'use_wallet'])}}
										<i class="fa fa-check" aria-hidden="true"></i>
									</span>
									<!-- {{ Form::label('use_wallet','Use Wallet')}} -->
									<div class="payment-cont">@lang(Session::get('front_lang_file').'.FRONT_USE_WALLET') {{$currency}}&nbsp;{{$wallet_amt}}</div>
								</label>
								@endif
								
								{{-- Show All Payment --}}
								@if(empty($get_payment_details) === false)
								<div class="shipping-method-inner" id="orlay" style="display:block;">
									<ul>
										
										@if($get_payment_details->cod_status == 1)
										<li>
											<label class="radiolabel" for="cod">
												<span class="pay-check">
													<input name="paymentMode" type="radio" id="cod" value="cod" required>
													<i class="fa fa-check" aria-hidden="true"></i>
												</span>
												<div class="payment-cont">@lang(Session::get('front_lang_file').'.ADMIN_COD')</div>
											</label>
										</li>
										@endif
										@if($get_payment_details->paymaya_status == 1)

										<li>
											<label class="radiolabel" for="paymaya">
												<span class="pay-check">
													<input name="paymentMode" type="radio" id="paymaya" value="paymaya" required>
													<i class="fa fa-check" aria-hidden="true"></i>
												</span>
												<div class="payment-cont">@lang(Session::get('front_lang_file').'.ADMIN_PAYMAYA')</div>
											</label>
										</li>
										@endif
										@if($get_payment_details->paynamics_status == 1)
										<li>
											<label class="radiolabel" for="paynamics">
												<span class="pay-check">
													<input name="paymentMode" type="radio" id="paynamics" value="paynamics" required>
													<i class="fa fa-check" aria-hidden="true"></i>
													</span>
												<div class="payment-cont">@lang(Session::get('front_lang_file').'.ADMIN_PAYNAMICS')</div>
											</label>
										</li>
									@endif
									</ul>
								</div>
								@endif
							</div>

							<div id="stripedetails" style="display:none">
								<div class="panel-body" >
									{{-- Show All Payment --}}
									@if(empty($get_payment_details) === false)
										<div class="shipping-method-inner" id="orlay" style="display:block;">
											<div class="input-field">
												<label id="Stripeerrors" style="display: none"><small style="color:red">@lang(Session::get('front_lang_file').'.FRONT_PLS_ENTER_CREDIT')</small></label>
												<div class="form-left">
													<label>@lang(Session::get('front_lang_file').'.FRONT_CARD_NUMBER')</label>
													<input  placeholder="@lang(Session::get('front_lang_file').'.FRONT_ENTER_CARD_NUMBER')" id="card_no" maxlength="16" minlength="16" name="card_no" type="text" value="" class="stripenumberic error" aria-invalid="true">
												</div>
												<div class="form-right">
													<label>@lang(Session::get('front_lang_file').'.FRONT_CARD_EXP_MONTH')</label>
													<input id="ccExpiryMonth" placeholder="@lang(Session::get('front_lang_file').'.EX-MONTH')"  maxlength="2" minlength="1" name="ccExpiryMonth" type="text" value="" class="stripenumberic error" aria-invalid="true">
												</div>
											</div>


											<div class="input-field">
												<div class="form-left">
													<label>@lang(Session::get('front_lang_file').'.FRONT_CARD_EXP_YEAR')</label>
													<input  placeholder="@lang(Session::get('front_lang_file').'.EX-YEAR')" id="ccExpiryYear" name="ccExpiryYear" minlength="4" maxlength="4" type="text" value="" class="stripenumberic error " aria-invalid="true">
													<input id="publishkey" name="publishkey" type="hidden" value="{{$paymentgatewaydetails->cus_paynamics_clientid}}">
												</div>
												<div class="form-right">
													<label>@lang(Session::get('front_lang_file').'.FRONT_CVV')</label>
													<input id="cvvNumber" placeholder="@lang(Session::get('front_lang_file').'.FRONT_ENTER_CVV')" minlength="3" maxlength="3" name="cvvNumber" type="text" value="" class="stripenumberic error" aria-invalid="true">
												</div>
											</div>
										</div>
									@endif
								</div>
							</div>
							
						</div>
					</div>

					</div>
					<div class="col-md-4 col-xs-12">
						<div class="price-right fixedElement">
							<h5>@lang(Session::get('front_lang_file').'.ADMIN_PRICE_DETAILS')</h5>
							<div class="row">
								<div class="col-md-7 col-7">
									<p>@lang(Session::get('front_lang_file').'.ADMIN_ORDER_SUBTOTAL')</p>
								</div>
								<div class="col-md-5 col-5">
									<h6>{{$currency}}&nbsp;{{number_format($sub_total,2)}}</h6>
									<input type="hidden" id="subTotalHid" value="{{$sub_total}}" />
								</div>
							</div>
							
							
							<div class="row">
								<div class="col-md-7 col-7">
									<p>@lang(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE')</p>
								</div>
								<div class="col-md-5 col-5">
									<h6>	
										{{-- if delivery is based on kilometer means  --}}
										@if(empty($delivery_fee_set) === false)
											@if($delivery_fee_set->gs_delivery_fee_status=='1' && $delivery_fee_set->gs_del_fee_type == 'km_fee')
											 @php $delivery_fee = $km_del_fee; @endphp
											@endif
										@endif
									@php $del_fee = number_format($delivery_fee,2); @endphp
									{{$delivery_fee_curr}}&nbsp;<span id="deli_fee">{{number_format($delivery_fee,2)}}</span>
									</h6>
									<input name="final_del_fee" type="hidden" id="delFeeHid" value="{{$delivery_fee}}" />
								</div>
							</div>
							
							<div style="display:none" id="show_wallet">
								<div class="row">
									<div class="col-md-7 col-7">
										<p>	@lang(Session::get('front_lang_file').'.ADMIN_UESD_WALLET')</p>
									</div>
									<div class="col-md-5 col-5">
										
										<h6>{{$currency}}&nbsp;<span id="use_wallet_amt"></span>
										{{ Form::hidden('wallet_amt',$wallet_amt,['id' => 'use_wallet_bal'])}}</h6>
										<input type="hidden" id="walletUsedHid" value="0.00" />
									</div></div>
									
									
									<div>
										<div class="row">
											<div class="col-md-7 col-7">
											<p>	@lang(Session::get('front_lang_file').'.ADMIN_WA_BAL')</p></div>
											<div class="col-md-5 col-5">
												
												<h6>{{$currency}}&nbsp;<span id="wa_bal"></span></h6>
												<input type="hidden" id="balanceWalletHid" value="0.00" />
											</div>
										</div>
									</div>
							</div>
							<div class="row totDiv" >
								<div class="col-md-7 col-7">
								<p><strong>@lang(Session::get('front_lang_file').'.ADMIN_TOTAL')</strong></p></div>
								<div class="col-md-5 col-5">
									<h6>@php $total = $sub_total + $delivery_fee; @endphp 
										{{Form::hidden('wallet_used_total',$total,['id' => 'wallet_used_total'])}}
									<b>{{$currency}}</b>&nbsp;<span id="total_amt">{{number_format(($sub_total + $delivery_fee),2)}}</span></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 col-xs-12">
						<div id="payment_err" style="display:none">
							<span style="color: red;font-size: 14px;" class="tooltip-demo" aria-hidden="true" title="If order cancelled by you or merchant, refund amount will be send through your added payment details. So please add payment details"> @lang(Session::get('front_lang_file').'.FRONT_FORCE_TOENTER_PAYMENTDET'). 
							<i class="fa fa-info-circle" aria-hidden="true"></i></i></span>
							<br />
							<a href="{{url('').'/user-payment-settings'}}" class="btn"   style="font-size: 14px" target="_blank">@lang(Session::get('front_lang_file').'.FRONT_CLICK_HERE')</a>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div id="checkout_verify" style="float:right">
							{{-- Form::submit(__(Session::get('front_lang_file').'.ADMIN_CONTINUE'),['id' => 'checkout_btn'])--}}
						</div>
					</div>
				</div>

				{{ Form::close()}}
			
			
		</div>
	</div>
</div>

</div>

			{{-- mail verify modal --}}
			<div id="mail_verify_modal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						
						<div class="modal-body">							
							
					<p><b>@lang(Session::get('front_lang_file').'.ADMIN_OTP_SEND_INMAIL')</b> <br>  @lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_VERIFY_MAIL')</p><br>
							<div class="verify-mail-left">
							
						{{ Form::text('code','',['class' => 'form-control','required','placeholder' => __(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP'),'required','id' => 'enter_code'])}}

							<span id="code_err" style="font-size: 14px;"></span></div>
							<div class="show_code"></div>
							<div class="verify-mail-right">
						

						{{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_SUBMIT_OTP'),['class' => 'btn btn-success','onClick' =>'return chk_mail_otp()'])}} 
						{{ Form::button(__(Session::get('front_lang_file').'.FRONT_CANCEL'),['class' => 'btn btn-success','onClick' =>'javascript:window.location.reload();'])}}
						</div>					

							{{-- <p>@lang(Session::get('front_lang_file').'.ADMIN_DID_YOU_RECEIVE_MAIL') <a href="#" onClick = "send_verify_mail()" style="color: #7f1900;font-size:13px;">@lang(Session::get('front_lang_file').'.ADMIN_CLICK_RESEND')</a></p>	 --}}						


							
						</div>
						
					</div>
					
				</div>
			</div>

			{{-- Mobile verification mail --}}
		<div id="mobile_verify_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-body">              
              
              <p><b>@lang(Session::get('front_lang_file').'.FRONT_MOB_VERIFY_MSG')</b></p>
              <div class="verify-mail-left">
              {{ Form::text('code','',['class' => 'form-control','required','placeholder' => __(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP'),'required','id' => 'enter_code_mobile'])}}
              <span id="code_err_mob" style="font-size: 14px;color:red"></span></div>
              <div class="verify-mail-right">
              {{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_SUBMIT_OTP'),['class' => 'btn btn-success','onClick' =>'return chk_mob_otp()'])}} 
              {{ Form::button(__(Session::get('front_lang_file').'.FRONT_CANCEL'),['class' => 'btn btn-success','onClick' =>'javascript:window.location.reload();'])}}
          		</div>             
              
              <div class="show_code_mobile"></div>            
            </div>
            
          </div>
          
        </div>
      </div>
	@section('script')
		<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
		<script>
		$(document).ready(function()
		{
			$('.shipAddressField').show();
		});
		</script>
		<?php /* 
		@if($lat == '' || $lon == '')
		<script>
			$('#us3').locationpicker({
				location: {
					latitude: {{Session::get('search_latitude')}},
					longitude: {{Session::get('search_longitude')}}
				},
				radius: 300,
				inputBinding: {
					latitudeInput: $('#latitude'),
					longitudeInput: $('#longitude'),
					radiusInput: $('#us3-radius'),
					locationNameInput: $('#us3-address-new')
				},
				enableAutocomplete: false,
				 markerDraggable: false,
				onchanged: function(currentLocation, radius, isMarkerDropped) {
					// Uncomment line below to show alert on each Location Changed event
					//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
				}
			});
		</script>
		@else
		<script>
			$('#us3').locationpicker({
				location: {
					latitude: {{Session::get('search_latitude')}},
					longitude: {{Session::get('search_longitude')}}
				},
				radius: 300,
				inputBinding: {
					latitudeInput: $('#us3-lat'),
					longitudeInput: $('#us3-lon'),
					radiusInput: $('#us3-radius'),
					locationNameInput: $('#us3-address-new')
				},
				enableAutocomplete: false,
				markerDraggable: false,
				onchanged: function(currentLocation, radius, isMarkerDropped) {
					// Uncomment line below to show alert on each Location Changed event
					//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
				}
			});
		</script>
		@endif 
		*/ ?>
		<script>
			$.validator.addMethod("jsPhoneValidation", function(value, element) { 
				var defaultDial = '{{Config::get('config_default_dial')}}';
				//console.log('=='+value.substr(0, (defaultDial.trim().length))+'--'+value.trim()+'\n');
				return value.substr(0, (defaultDial.trim().length)) != value.trim()
			}, "No space please and don't leave it empty");
			$("#validate_form").validate({
				//onkeyup: true,
				onfocusout: function(element) {
					this.element(element);
				},
				rules: {
					"ord_self_pickup":"required",

					"name": {
						required: {
							depends: function(element) {
								
								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; } 
							}
						},
					},
					"lname": {
						required: {
							depends: function(element) {
								
								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; } 
							}
						},
					},
					"mail": {
						required: {
							depends: function(element) {

								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; }
							}
						},
					},
					"phone1": {
						jsPhoneValidation : {
							depends: function(element) {
								
								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; } 
							}
						},
					},
					/*"phone2": {
						jsPhoneValidation : {
							depends: function(element) {
								
								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; } 
							}
						},
					},*/
					"address": {
						required: {
							depends: function(element) {
								
								if($('#ord_self_pickup').val()=='0'){  return true; } else { return false; } 
							}
						},
					},
					"paymentMode": { required:true }
					
				},
				messages: {
					ord_self_pickup : "@lang(Session::get('front_lang_file').'.ADMIN_SEL_PICKUP')",
					name: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_FNAME')",
					lname: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_LNAME')",
					mail: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')",
					phone1: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')",
					//phone2: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')",
					address: "@lang(Session::get('front_lang_file').'.ADMIN_ENTER_LOCATION')",
					paymentMode: "@lang(Session::get('front_lang_file').'.ADMIN_SELECT_PAYMENT_METHOD')",
				},
				errorPlacement: function(error, element) 
				{
					if ( element.is(":radio") ) 
					{
						error.appendTo( $(element).parents('.shipping-method-inner') );
					}
					else 
					{ // This is the default behavior 
						error.insertAfter( element );
					}
				}
			});
			
			$('input[name="paymentMode"]').click(function() {
                var radioValue = $("input[name='paymentMode']:checked").val();
                var redirect = '{{$redirect_url}}';
                if ($.trim(radioValue) == "cod")
                {
                    $("#stripedetails").css({'display' : 'none'});
                    $('#Stripeerrors').css({'display' : 'none'});
                    $('#checkout_btn').css({'display' : 'block'});
                    $('#payment_err').css({'display' : 'none'});
                    //$('#validate_form').attr("action", "{{url('cod_checkout')}}");
                }
                else if ($.trim(radioValue) == "paymaya")
                {
                	
                    $("#stripedetails").css({'display' : 'none'});
                    $('#Stripeerrors').css({'display' : 'none'});
                    $('#checkout_btn').css({'display' : 'block'});
                    <?php if(in_array('Yes',$refund_status_arr) && $user_payment_status == 0)
                    { ?>

                    //$('#checkout_btn').css({'display' : 'none'});
                    //$('#payment_err').css({'display' : 'block'});
                    bootbox.confirm({
								    message: "@lang(Session::get('front_lang_file').'.FRONT_FORCE_TOENTER_PAYMENTDET')",
								    buttons: {
								        confirm: {
								            label: 'Update',
								            className: 'btn-success'
								        },
								        cancel: {
								            label: 'Skip',
								            className: 'btn-danger'
								        }
								    },
								    callback: function (result) {
								        if (result == true) {
											$.ajax({
													'type' : 'GET',
													'url' : '{{ url('set-redirect-url') }}',
													'data'  : {'redirect_url' : redirect},
													success:function(response)
													{
														console.log(response);
														location.href = '{{url('user-payment-settings')}}';
													}
												});
											
										}
										else {
											//location.reload();
										}
								    }
								});
                    
                    <?php }
                    else{
                    ?>
                    $('#checkout_btn').css({'display' : 'block'});
                    $('#payment_err').css({'display' : 'none'});
                    <?php } ?>
                    //$('#validate_form').attr("action","{{url('paymaya_checkout')}}");
                }
                else if ($.trim(radioValue) == "paynamics")
                {
                    if($('#stripedetails').css('display') == 'none') {
                        $("#stripedetails").css({'display': 'block'});
                    }
					<?php if(in_array('Yes',$refund_status_arr) && $user_payment_status == 0)
                    { ?>

                    //$('#checkout_btn').css({'display' : 'none'});
                    //$('#payment_err').css({'display' : 'block'});
                    bootbox.confirm({
								    message: "@lang(Session::get('front_lang_file').'.FRONT_FORCE_TOENTER_PAYMENTDET')",
								    buttons: {
								        confirm: {
								            label: 'Update',
								            className: 'btn-success'
								        },
								        cancel: {
								            label: 'Skip',
								            className: 'btn-danger'
								        }
								    },
								    callback: function (result) {
								        if (result == true) {
											$.ajax({
													'type' : 'GET',
													'url' : '{{ url('set-redirect-url') }}',
													'data'  : {'redirect_url' : redirect},
													success:function(response)
													{
														console.log(response);
														location.href = '{{url('user-payment-settings')}}';
													}
												});
											
										}
										else {
											//location.reload();
										}
								    }
								});
                    <?php }
                    else{
                    ?>
                    $('#checkout_btn').css({'display' : 'block'});
                    $('#payment_err').css({'display' : 'none'});
                    <?php } ?>
				}
			});


			//Here to change by karthik on 17122018
            function Submitform(){

                var radioValue = $("input[name='paymentMode']:checked").val();
                var checkValue = $("input[name='use_wallet']:checked").val();
               
                if ($.trim(radioValue) == "cod")
                {
                    $('#validate_form').attr("action", "{{url('cod_checkout')}}");
                    //console.log("cod");
                    $('#validate_form').submit();

                }
                else if ($.trim(radioValue) == "paymaya")
                {
                    $('#validate_form').attr("action","{{url('paymaya_checkout')}}");
                    $('#validate_form').submit();

                }
                else if ($.trim(radioValue) == "paynamics")
                {
                    var errormsg="";

                    if($("#card_no").val()=="")
                        errormsg="Please Fill Cardno";

                    if($("#ccExpiryMonth").val()=="")
                        errormsg="Please Expiry Month";
                    if($("#ccExpiryYear").val()=="")
                        errormsg = "Please Fill Expiry Year";
                    if($("#cvvNumber").val()=="")
                        errormsg="Please Fill cvvnumber";

                    if(errormsg=="") {
                        $('#Stripeerrors').css({'display' : 'none'});
                        var $form = $('#validate_form');
                        var publickey=$('#publishkey').val();
                        Stripe.setPublishableKey(publickey);
                        Stripe.createToken({
                            number: $("#card_no").val(),
                            cvc: $("#cvvNumber").val(),
                            exp_month: $("#ccExpiryMonth").val(),
                            exp_year: $("#ccExpiryYear").val(),
                            address_zip: $("#expyear").val()
                        }, stripeResponseHandler);

                        function stripeResponseHandler(status, response) {
                            var $form = $('#validate_form');
                            if (response.error) {

                            } else {
                                var token = response.id;
                                $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                                $('#validate_form').attr("action","{{url('paynamics_checkout')}}");
                                $('#validate_form').submit();
                            }
                        }
                    }
                    else{
                        $('#Stripeerrors').css({'display' : 'block'});
                    }
                }
                else if($.trim(checkValue) == 1)
                {	
                	$('#validate_form').submit();
                }
                else
                {
                	$('#validate_form').submit();
                }
			}


			/* check mail verification status */
			$(document).ready(function(){
				chk_mail_status();
			});
			function chk_mail_status(){
				<?php
				//Bcoz mailverification is blocked thats y hardcore
                  $mail_verify=1; 
				if($MAIL_VERIFY_STATUS == 1 && $mail_verify == 0)
				{   ?>
				//$('#checkout_verify').html('<span style="color:red">@lang(Session::get('front_lang_file').'.FRONT_HV_TO_VERIFY')</span><div class="show_code"></div>{{Form::button(__(Session::get('front_lang_file').'.FRONT_VERIFY'),["data-toggle" => "modal","data-target" => "#mail_verify_modal","onClick" => "send_verify_mail("<?php echo Session::get('customer_mail'); ?>")"])}}');
				$('#checkout_verify').html('<span style="color:red">@lang(Session::get('front_lang_file').'.FRONT_HV_TO_VERIFY')</span>{{Form::button(__(Session::get('front_lang_file').'.FRONT_VERIFY'),["onClick" => "send_verify_mail("<?php echo Session::get('customer_mail'); ?>")"])}}');
					return false;
				<?php } else { ?>
					$('#checkout_verify').html('<button onclick="Submitform()" type="button" id="checkout_btn">@lang(Session::get('front_lang_file').'.ADMIN_CONTINUE')</button>');
						<?php //
				//{{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_CONTINUE'),["id" => "checkout_btn"])}}' ?>

					<?php } ?>
			}

			function check_mobile_with_otp()
			{

		        var cus_phone1 = $('#phoneNum1').val();
		        var exist_ship_phone1 = '<?php echo $ph1; ?>';
		        var exist_prof_phone1 = '<?php echo $prof_phone; ?>';
		       
		        
		        $('#new_phone_err').html("");
		        $('#code_err_mob').html("");
		        if(cus_phone1 == '')
		        {
		         	$('#new_phone_err').html("@lang(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')");
		        }
		        if(exist_ship_phone1.trim() != cus_phone1.trim() && exist_prof_phone1.trim() != cus_phone1.trim())
		        {
		         	$('#mobile_verify_modal').modal({backdrop: 'static',keyboard: false });
		         	$.ajax({
							'type' : 'POST',
							'url' : '{{ url('send_verification_msg') }}',
							'data'  : {'_token' : '{{csrf_token()}}','phone' : cus_phone1},
							success:function(response)
							{
								var data = response.split('`');
								if(data[0] == "success")
								{
									$('.show_code_mobile').html("<input type='hidden' name='verify_code' value='"+data[1]+"' id='verify_code_mobile'>");
								}
								else if(data[0] == "fail")           
								{
									$('#code_err_mob').html("@lang(Session::get('front_lang_file').'.FRONT_TWILIO_ERR')");
									//$('#mobile_verify_modal').modal('hide');
									setTimeout(function() { $('#mobile_verify_modal').modal('hide'); window.location.reload();}, 7000);
								}
							},
							error: function(xhr, status, error) {
								var err = eval("(" + xhr.responseText + ")");
								alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
							}
						});
		        }
		    }
			/* send verify mail  */
			function send_verify_mail(val) 
			{
				
				var exist_ship_mail = '<?php echo $mail; ?>';
		        var exist_prof_mail = '<?php echo $prof_mail; ?>';
				var mail = val;
				/*alert(mail+'//'+exist_ship_mail+'//'+exist_prof_mail);
				alert(mail.trim() != '' && (exist_ship_mail.trim() != mail.trim() && exist_prof_mail.trim() != mail.trim()));  return false;*/
				if(mail.trim() != '' && (exist_ship_mail.trim() != mail.trim() && exist_prof_mail.trim() != mail.trim()))
				{	
					$('#mail_verify_modal').modal({backdrop: 'static',keyboard: false });
					$.ajax({
					'type' : 'POST',
					'url'	: '{{ url('send_verification_mail') }}',				
					'data'	: {'_token' : '{{csrf_token()}}','mail' : mail},
					success:function(response)
					{
						var data = response.split('`');
						if(data[0] == "success")
						{
							$('.show_code').html("<input type='hidden' name='verify_code' value='"+data[1]+"' id='verify_code'>");
						}	

						},
					error: function(xhr, status, error) {
					  var err = eval("(" + xhr.responseText + ")");
					  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);					
					}
				});
				}
				
			}
			/* check mobile otp */
	       function chk_mob_otp()
	       {
	        var added_code = document.getElementById('verify_code_mobile').value;
	        var enter_code = document.getElementById('enter_code_mobile').value;
	        //alert(added_code); return false;
	        if(enter_code.trim() == '')
	        {
	          $('#enter_code').css({'border' : '1px solid red'});
	          $('#code_err_mob').html("@lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP')").css({'color':'red'});
	          return false;
	        }
	        else if(added_code.trim() != enter_code.trim())
	        {
	        	$('#enter_code').css({'border' : '1px solid red'});
		          $('#code_err_mob').html("@lang(Session::get('front_lang_file').'.FRONT_INCORRECT_OTP')").css({'color':'red'});
		          return false;
	        }
	        else if(added_code.trim() == enter_code.trim())
	        {
	          $('#mobile_verify_modal').modal('hide');
	          $('#new_phone_err').html("@lang(Session::get('front_lang_file').'.FRONT_PH_VERIFIED')").css({'color':'green'});
	            return true;
	        }
	       }
			/* verify otp */
			function chk_mail_otp()
			{
				var added_code = document.getElementById('verify_code').value;
				var enter_code = document.getElementById('enter_code').value;
				//alert(added_code); return false;
				if(enter_code.trim() == '')
				{
					$('#enter_code').css({'border' : '1px solid red'});
					$('#code_err').html("Enter code").css({'color':'red'});
					return false;
				}
				else if(added_code.trim() == enter_code.trim())
				{
					$.ajax({
						'type' : 'POST',
						'url'	: '{{ url('save_verify_status')}}',
						success:function(response)
						{ 
							$('#mail_verify_modal').modal('hide');
							<?php $mail_verify = 1; ?>
							//$('#common_err').hide();
							$('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+"@lang(Session::get('front_lang_file').'.FRONT_VR_SUCCESS')"+'</strong></div>');
							$('html, body').animate({
								'scrollTop' : '0'
							});
							//$('#orlay').show();
							//$('#checkout_btn').css({'pointer-events':''});
							$('#checkout_verify').html('<button onclick="Submitform()" type="button" id="checkout_btn">@lang(Session::get('front_lang_file').'.ADMIN_CONTINUE')</button>');
							return false;
						}
					});
				}
				else
				{
					$('#enter_code').css({'border' : '1px solid red'});
					$('#code_err').html("Code entered is incorrect").css({'color':'red'});
					return false;
				}
			}
		</script>
		<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
		
		<script>
			$(".open-button").on("click", function() {
				$(this).closest('.collapse-group').find('.collapse').collapse('show');
			});
			
			$(".close-button").on("click", function() {
				$(this).closest('.collapse-group').find('.collapse').collapse('hide');
			});
			@php 
			$same_address_ph1=($ph1=='')?$default_dial:$ph1;
			$same_address_ph2=($ph2=='')?$default_dial:$ph2;
			@endphp
			$('input[name="check_addr"]').click(function() {
				var check = $("#same_address").is(":checked");
				if (check == true) {
					$('#name').val('{{$name}}');
					$('#lname').val('{{$lname}}');
					$('#mail').val('{{$mail}}');
					$('#mail').attr('readonly', true);
					//$('#address').val('{{$addr}}');
					$('#phoneNum1').val('{{$same_address_ph1}}');
					$('#phoneNum1').attr('readonly', true);
					$('#phoneNum2').val('{{$same_address_ph2}}');
					$('#phoneNum2').attr('readonly', true);
					$('#sh_building_no').val('{{$sh_building_no}}');
					//$('#latitude').val('{{$lat}}');
					//$('#longitude').val('{{$lon}}');
				} else {
					$('#name').val('');
					$('#lname').val('');
					$('#mail').val('');
					$('#mail').attr('readonly', false);
					//$('#address').val('');
					$('#phoneNum1').val('{{$default_dial}}');
					$('#phoneNum1').attr('readonly', false);
					$('#phoneNum2').val('{{$default_dial}}');
					$('#phoneNum2').attr('readonly', false);
					$('#sh_building_no').val('');
					//$('#latitude').val('');
					//$('#longitude').val('');
				}
			});
			
			function validate_phone(gotId) {
				
				var element = document.getElementById(gotId);
				//alert();
				if(element.value=='' || element.value.length < 3)
				{
				
				$('#'+gotId).val('{{$default_dial}}');
				
				}
				element.value = element.value.replace(/[^0-9 +]+/, '');
			}

			
			$('input[name="use_wallet"]').click(function(){	
				if($(this).is(":checked"))	
				{
					wallet_fun('1');
				}
				else
				{
					wallet_fun('0');
				}
			});
			function wallet_fun(gotVal)
			{
				if(gotVal==1)	
				{
					$('#show_wallet').show();
					$('.bal_wallet').show();
					var wallet_amount = '{{str_replace( ',', '', $wallet_amt)}}';
					var subtotalHide = ($('#subTotalHid').val()).replace(/\,/g,'');
					var delfeeHide = ($('#deli_fee').html()).replace(/\,/g,'');
					var total_amount = (parseFloat(subtotalHide)+parseFloat(delfeeHide)).toFixed(2);
					//alert(parseFloat(wallet_amount) > parseFloat(total_amount));
					//alert(total_amount);
					if(parseFloat(wallet_amount) > parseFloat(total_amount))
					{
						$('#use_wallet_amt').html(total_amount);
						$('#walletUsedHid').val(total_amount);
						$('#use_wallet_bal').val(total_amount);
					}
					else
					{
						$('#use_wallet_amt').html(wallet_amount);
						$('#walletUsedHid').val(wallet_amount);
						$('#use_wallet_bal').val(wallet_amount);
					}
					
				}
				else
				{
					$('#show_wallet').hide();
					$('.bal_wallet').hide();
					$('#walletUsedHid').val('0.00');
					$('#use_wallet_amt').html('0.00');
					
				}
				//show_ship($('#ord_self_pickup').val());
				calculateSum();
			}
		</script>
		<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#phoneNum1").intlTelInput({
					onlyCountries: ["{{$default_country_code}}"]
				});
				$("#phoneNum2").intlTelInput({
					onlyCountries: ["{{$default_country_code}}"]
				});
				$('.tooltip-demo').tooltip({placement:'top'})
				
				// alert('{{$default_dial}}');
				var element = document.getElementById('phoneNum1');
				if (element.value == '') {
					element.value = '{{$default_dial}}';
				}
				
				var element2 = document.getElementById('phoneNum2');
				if (element2.value == '') {
					element2.value = '{{$default_dial}}';
				}
			});
			function show_ship(gotVal)
			{
				if(gotVal==1) 
				{
					//SELF PICK UP.
					var delFee = 0.00;
					$('.shipAddressField').hide();
				}
				else
				{
					var delFee = '{{number_format($del_fee,2)}}';
					$('.shipAddressField').show();
				}
				$('#delFeeHid').val(delFee);
				$('#deli_fee').html(delFee);
				if($('input[name="use_wallet"]').is(":checked"))	
				{
					wallet_fun('1');
				}
				else
				{
					wallet_fun('0');
				}
				calculateSum();
			}
			function calculateSum()
			{
				var walletUsedHid=($('#walletUsedHid').val()).replace(/\,/g,'');
				
				var balanceWalletHid=$('#balanceWalletHid').val();
				var delFeeHid=($('#delFeeHid').val()).replace(/\,/g,'');
				var subTotalHid=($('#subTotalHid').val()).replace(/\,/g,'');
				var wallet_amount = '{{str_replace( ',', '', $wallet_amt)}}';
				var total_amt = ((parseFloat(subTotalHid)+parseFloat(delFeeHid))-parseFloat(walletUsedHid));
				
				$('#total_amt').html(total_amt.toFixed(2));
				$('#wallet_used_total').val(total_amt.toFixed(2));
				
				var grandSub = parseFloat(subTotalHid) + parseFloat(delFeeHid);
				if(parseFloat(wallet_amount) > parseFloat(grandSub))
				{
					$('#wa_bal').html((parseFloat(wallet_amount)-(parseFloat(subTotalHid)+parseFloat(delFeeHid))).toFixed(2));
				}
				else
				{
					$('#wa_bal').html("0.00");
				}
				if(parseFloat(total_amt) <= 0)
				{
					$('#orlay').hide();
					$('#validate_form').attr("action", "{{url('wallet_checkout')}}");
				}
				else
				{
					$('#orlay').show();
				}
			}
			
			
		</script>
		<script type="text/javascript">
			if (jQuery(window).width() > 768) {	
			$(window).scroll(function(e){ 
				  var $el = $('.fixedElement'); 
				  var isPositionFixed = ($el.css('position') == 'fixed');
				  if ($(this).scrollTop() > 200 && !isPositionFixed){ 
				    $el.css({'position': 'fixed', 'top': '75px', 'width':'25%'}); 
				  }
				  if ($(this).scrollTop() < 200 && isPositionFixed){
				    $el.css({'position': 'static', 'top': '0px' , 'width':'100%'}); 
				  } 
				});
		}
		</script>

<script>
    $(".stripenumberic").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            $("#errmsg").html("Digits Only").show().fadeOut("slow");
            return false;
        }
    });
</script>
	@endsection
@stop