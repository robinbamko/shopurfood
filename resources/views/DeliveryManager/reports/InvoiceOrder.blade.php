@extends('DeliveryManager.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style type="text/css">
	@media print
	{
	#sidebar-nav,.navbar { visibility: hidden; }
	.div * { visibility: visible; }
	.div2 { position: absolute; top: 40px; left: 30px; }
	.main{width:100%!important;}
	footer{display: none;}
	}
</style>
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12">
						<div class="location panel invoice-panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>
							{{-- Display error message--}}
							
							
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								@php $sub_total=$grand_total=$tax_total=$shipping_total=0 @endphp
								<div style="">
								{{--@foreach($Invoice_Order as $Order)--}}
									@php $Order = $Invoice_Order[0]; @endphp
										<!-- info row -->
										
										<div class="row invoice-info">
											<!-- /.col -->
											<div class="col-sm-5 invoice-col">
												<?php
													
													if($Order->ord_shipping_cus_name!='' && $Order->ord_shipping_address!=''  && $Order->ord_shipping_mobile!='')
													{
														$OrderCustomerName = $Order->ord_shipping_cus_name;
														$OrderCustomerAddress = $Order->ord_shipping_address;
														$OrderCustomerAddress1 = $Order->ord_shipping_address1;
														$OrderCustomerMobile = $Order->ord_shipping_mobile;
														$OrderCustomerEmail = $Order->order_ship_mail;
													}
													else
													{
														$OrderCustomerName = $Order->cus_fname.' '.$Order->cus_lname;
														$OrderCustomerAddress = $Order->cus_address;
														$OrderCustomerAddress1 = '';
														$OrderCustomerMobile = $Order->cus_phone1;
														$OrderCustomerEmail = $Order->cus_email;
													}
												?>
												<h2>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TO')}}</h2>
												<address>
													<strong>{{ucfirst($OrderCustomerName)}}</strong>
													@if($OrderCustomerMobile != '')<br>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADDRESS')}} : {!!($OrderCustomerAddress1 != '') ? $OrderCustomerAddress1.'<br>' : '' !!}{{$OrderCustomerAddress}}@endif
													
													@if($OrderCustomerMobile != '')<br>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PHONE')}} : {{$OrderCustomerMobile}}@endif
													
													@if($OrderCustomerEmail != '')<br>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL')}} : {{$OrderCustomerEmail}}@endif
												</address>
												
											</div>
											<!-- /.col -->
											<div class="col-sm-5 invoice-col invoice-ord-id">
												
												<b>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_PAID_ON')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_PAID_ON') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMENT_PAID_ON')}} :</b> {{date('m/d/Y',strtotime($Order->ord_date))}}
												<br>
												<b>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_ID')}} :</b> {{$Order->ord_transaction_id}}
												<br>
												
											</div>
											<!-- /.col -->
										</div>
										<!-- /.row -->
										
										<!-- Table row -->
										<div class="row">
											<div class="col-xs-12 table invoice-tble">
												<table class="table table-striped">
													<thead>
														<tr>
															<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_RESTSTORE_NAME')}}</th>
															<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ITEMRPDT_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ITEMRPDT_NAME')}}</th>
															<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PRE_ORDER_DATE')}} </th>
															<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_QTY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_QTY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_QTY')}}</th>
															<th >{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UNIT_PRICE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UNIT_PRICE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UNIT_PRICE')}}</th>
															<th >{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TAX')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TAX') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TAX')}}</th>
															<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SUBTOTAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SUBTOTAL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SUBTOTAL')}}</th>
														</tr>
													</thead>
													<tbody>
														@foreach($Invoice_Order as $Order_sub)
															@php
															$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
															$sub_total +=$calc_sub_total;
															$shipping_total =$Order_sub->ord_delivery_fee;
															//$grand_total +=($Order_sub->ord_sub_total)+($Order_sub->ord_tax_amt)+($Order_sub->ord_delivery_fee);
															@endphp
														<tr>
															<td align="center">{{$Order_sub->st_store_name}} <br>{{$Order_sub->st_address}}</td>
															<td align="center">
																{{$Order_sub->pro_item_name}}
																@if($Order_sub->ord_spl_req != '')
									                                <br>
									                                  <a href="" class="" data-toggle="modal" data-target="#spl_nt{{$Order_sub->ord_id}}">
									                                  	{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_VIEW_SPL_NT')) ? trans(Session::get('DelMgr_lang_file').'.DEL_VIEW_SPL_NT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_VIEW_SPL_NT')}}
									                                  </a>
									                                  {{-- spl req popup --}}
									                                  <div id="spl_nt{{$Order_sub->ord_id}}" class="modal fade choice-modal" role="dialog">
									                                    <div class="modal-dialog">
									                                      
									                                      <!-- Modal content-->
									                                      <div class="modal-content">
									                                        <div class="modal-header">
									                                        	<button type="button" class="close" data-dismiss="modal">&times;</button>
									                                          {{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_VIEW_SPL_NT')) ? trans(Session::get('DelMgr_lang_file').'.DEL_VIEW_SPL_NT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_VIEW_SPL_NT')}}
									                                        </div>
									                                        <div class="modal-body">
									                                          
									                                          <div class="choice-modal-content">
									                                            {{$Order_sub->ord_spl_req}}
									                                            
									                                          </div>
									                                        </div>
									                                        
									                                      </div>
									                                      
									                                    </div>
									                                  </div>
									                                @endif
															</td>
															<td align="center">
															@if($Order->ord_pre_order_date != '')
															{{date('m/d/Y h:i A',strtotime($Order->ord_pre_order_date))}}
															@else
															-
															@endif
															</td>
															<td align="center">{{$Order_sub->ord_quantity}}</td>
															<td align="center">{{$Order_sub->ord_unit_price}} {{$Order_sub->ord_currency}}</td>
															<td align="center">{{$Order_sub->ord_tax_amt}} {{$Order_sub->ord_currency}}</td>
															<td align="center">{{number_format($calc_sub_total,2)}} {{$Order_sub->ord_currency}}</td>
														</tr>
														<!-- Start-Sathyaseelan Showing choices in Invoice -->
														@if($Order_sub->ord_had_choices=="Yes")
														
															@if(count($choices[$Order_sub->ord_id])>0)
																<tr><td colspan="5" align="right"><h2>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INCLUDES')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INCLUDES') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_INCLUDES')}} : </h2></td><td colspan="2">
																<table class="table table-bordered">
																@foreach($choices[$Order_sub->ord_id] as $key=>$val)
																	@php $sub_total +=$val;  $grand_total +=$val; @endphp
																	<tr><td>{{$key}}</td><td style="text-align:right">{{$val}} {{$Order_sub->ord_currency}}</td></tr>
																@endforeach
																</table>
																</td>
																</tr>
															@endif
														@endif
														<!-- End-Sathyaseelan Showing choices in Invoice -->
														@endforeach
														
														
														
													</tbody>
												</table>
											</div>
											<!-- /.col -->
										</div>
										<!-- /.row -->
										
										<div class="row">
											<!-- accepted payments column -->
											<div class="col-md-6 col-sm-6 col-xs-12">
												<p class="lead">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_METHODS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_METHODS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMENT_METHODS')}} :</p>
												<!--img src="images/visa.png" alt="Visa">
													<img src="images/mastercard.png" alt="Mastercard">
													<img src="images/american-express.png" alt="American Express">
												<img src="images/paypal.png" alt="Paypal"-->
												
												<p>{{$Order->ord_pay_type}}</p>
											</div>
											<!-- /.col -->
											<div class="col-md-6 col-sm-6 col-xs-12 invoice-tble-tot">
												
												<div class="">
													<table class="table">
														<tbody>
															<tr>
																<th style="width:68%">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SUBTOTAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SUBTOTAL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SUBTOTAL')}} :</th>
																<td align="right">{{number_format($sub_total,2)}} {{$Order_sub->ord_currency}}</td>
															</tr>

															<tr>
																<th>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TOTAL')}} :</th>
																<td align="right">{{number_format($sub_total,2)}} {{$Order_sub->ord_currency}}</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<!-- /.col -->
										</div>
										
										<!-- /.row -->
										
										<!-- this row will not appear when printing -->
										<div class="row no-print">
											<div class="col-xs-12">
												<button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PRINT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PRINT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PRINT')}}</button>

											</div>
										</div>
										
									
								</div>
								
							</div>
							{{--Manage page ends--}}
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>
<div id="myModal" class="modal">
	<span class="close">&times;</span>
	<img class="modal-content" id="img01">
	<div id="caption"></div>
</div>


@endsection