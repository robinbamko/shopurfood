@extends('Front.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style type="text/css">
	    .invoice-section .location {border: 1px solid #383757;border-radius: 20px;overflow: hidden;}
    .invoice-section .panel-heading {background: #383757;color: #ffffff;border: 0px;}
    table.table .table {background-color: #f1f1f1;}
    .main-content {background: #ffffff;}
    .invoice-section .panel-body {background: #fff;}
    .table td, .table th {border-top: 1px solid #cfcfcf;}
    .table tr:first-child td, .table tr:first-child th {border-top: 0px solid #cfcfcf;}
</style>
<div class="main-sec">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12 invoice-section">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>

							{{-- Display error message--}}

							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i>@lang(session::get('front_lang_file').'.LOAD')</button>
								</div>
								
								@php $sub_total=$grand_total=$tax_total=$shipping_total=0 @endphp
								<div>
									{{--@foreach($Invoice_Order as $Order)--}}
									@php $Order = $Invoice_Order[0]; 
										 $payment_status = $Invoice_Order[0]->payment_status;
										 $trans_id = Request::segment(2);
										 $redirect_url ='order-details/'.$trans_id;
										
									@endphp
									<!-- info row -->
									
									<p style="color:red;display:none" id="cancel_err">@lang(Session::get('front_lang_file').'.FRONT_ATLEAST_ONE')</p>
									<!-- Table row -->
									<div class="row">
										<div class="col-lg-12 col-md-12 col-xs-12 table invoice-table">
											<table class="table table-striped">
												<thead>
													<tr>

														<th><!--<label class="multi-checkbox"><input type="checkbox" id="checkAll"><span class="checkmark"></span></label>-->&nbsp;</th>

														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_RESTSTORE_NAME')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_ITEMRPDT_NAME')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_QTY')) ? trans(Session::get('front_lang_file').'.ADMIN_QTY') : trans($FRONT_LANGUAGE.'.ADMIN_QTY')}}</th>
														<th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PRICE')) ? trans(Session::get('front_lang_file').'.ADMIN_PRICE') : trans($FRONT_LANGUAGE.'.ADMIN_PRICE')}}</th>
														<th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TAX')) ? trans(Session::get('front_lang_file').'.ADMIN_TAX') : trans($FRONT_LANGUAGE.'.ADMIN_TAX')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_SUBTOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_SUBTOTAL')}}</th>
													</tr>
												</thead>

												<tbody class="checkoutDetail">
													@foreach($Invoice_Order as $Order_sub)
													@php
														$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
													@endphp
													<tr >
														<td>
														@if($Order_sub->ord_mer_cancel_status=='No')
															<a href="javascript:;" data-toggle="tooltip"  data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_NOT_ALLOWED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_NOT_ALLOWED') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_NOT_ALLOWED')}}!">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_NOT_ALLOWED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_NOT_ALLOWED') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_NOT_ALLOWED')}}!</a> 
														@else
															@if($Order_sub->ord_cancel_status=='1')
																@if($Order_sub->ord_status==3)
																	<a href="javascript:;" data-toggle="modal" data-target="#cancelReasonModal_{{$Order_sub->ord_id}}" style="color:red;font-weight:bold;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('front_lang_file').'.MER_REJECTED_REASON') : trans($FRONT_LANGUAGE.'.MER_REJECTED_REASON')}}!">{{(Lang::has(Session::get('front_lang_file').'.MER_REJECTED')) ? trans(Session::get('front_lang_file').'.MER_REJECTED') : trans($FRONT_LANGUAGE.'.MER_REJECTED')}}!</i></a> 
																@else
																	<a href="javascript:;" data-toggle="modal" data-target="#cancelReasonModal_{{$Order_sub->ord_id}}" style="color:red;font-weight:bold;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.MER_CANCELLED_REASON')) ? trans(Session::get('front_lang_file').'.MER_CANCELLED_REASON') : trans($FRONT_LANGUAGE.'.MER_CANCELLED_REASON')}}!">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($FRONT_LANGUAGE.'.FRONT_CANCELLED')}}!</i></a> 
																@endif
															@elseif($Order_sub->ord_status > 1)
																<a href="javascript:;" data-toggle="tooltip"  data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.FRONT_UNABLE_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_UNABLE_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_UNABLE_CANCEL')}}!">{{(Lang::has(Session::get('front_lang_file').'.FRONT_UNABLE_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_UNABLE_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_UNABLE_CANCEL')}}!</a> 
															@else
																	<div -class="shipping-method-inner" id="orlay" style="display:block;">
																		<label class="radiolabel" for="cod_{{$Order_sub->ord_id}}">
																	<span class="pay-check">
																		<input name="item_det[]" type="radio" id="cod_{{$Order_sub->ord_id}}"required value="{{$Order_sub->ord_id}}">
																		<i class="fa fa-check" aria-hidden="true"></i>
																	</span>
																		</label>
																	</div>
															@endif
														@endif
															
														@if($Order_sub->ord_cancel_status=='1')
															<div id="cancelReasonModal_{{$Order_sub->ord_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<!-- Modal content-->
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">
																			@if($Order_sub->ord_status==3)
																				{{(Lang::has(Session::get('front_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('front_lang_file').'.MER_REASON_TO_REJECT') : trans($FRONT_LANGUAGE.'.MER_REASON_TO_REJECT')}}
																			@else	
																				{{(Lang::has(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_REASON_TO_CANCEL')}}
																			@endif
																			
																			</h4>
																		</div>
																		<div class="modal-body">
																			
																			<p> {!!$Order_sub->ord_cancel_reason!!}</p>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('front_lang_file').'.FRONT_CLOSE')</button>
																		</div>
																	</div>
																</div>
															</div>
														@endif
														</td>
														<td @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>
															{{$Order_sub->st_store_name}} 
															<a data-toggle="modal" data-target="#myModal_{{$Order_sub->ord_merchant_id}}" ><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_REFUND')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_REFUND') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_REFUND')}}!"></i></a>
															 <br>{{$Order_sub->st_address}}
															@php 
																$cancellation_policy = get_cancellation_policy($Order_sub->ord_merchant_id);  

															@endphp
															
															<!-- Modal -->
															<div id="myModal_{{$Order_sub->ord_merchant_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<!-- Modal content-->
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_REFUND')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_REFUND') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_REFUND')}}</h4>
																		</div>
																		<div class="modal-body">
																			<h5>{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_POLICY')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_POLICY') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_POLICY')}}</h5>
																			<p>@if(empty($cancellation_policy)===false) {!!$cancellation_policy->mer_cancel_policy!!} @endif</p>
																			<hr />
																			<h5>{{(Lang::has(Session::get('front_lang_file').'.FRONT_REFUND')) ? trans(Session::get('front_lang_file').'.FRONT_REFUND') : trans($FRONT_LANGUAGE.'.FRONT_REFUND')}}</h5>
																			<p>{{$Order_sub->ord_refund_status}}</p>
																			<h5>{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_ALLOWED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_ALLOWED') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_ALLOWED')}}</h5>
																			<p>{{$Order_sub->ord_mer_cancel_status}}</p>




																			</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('front_lang_file').'.FRONT_CLOSE')</button>
																		</div>
																	</div>
																</div>
															</div>
														</td>
														<td @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>{{$Order_sub->pro_item_name}}</td>
														<td @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>{{$Order_sub->ord_quantity}}</td>
														<td  @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>{{$Order_sub->ord_unit_price}} {{$Order_sub->ord_currency}}</td>
														<td  @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>{{$Order_sub->ord_tax_amt}} {{$Order_sub->ord_currency}}</td>
														<td  @if($Order_sub->ord_cancel_status=='1') style="text-decoration:line-through"  @endif>{{number_format($calc_sub_total,2)}} {{$Order_sub->ord_currency}}</td>
													</tr>
													<!-- Start Showing choices in Invoice -->
													@if($Order_sub->ord_had_choices=="Yes")
														@if(count($choices)>0)
														<tr><td colspan="5" align="right" class="table-includes"><h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_INCLUDES')) ? trans(Session::get('front_lang_file').'.ADMIN_INCLUDES') : trans($FRONT_LANGUAGE.'.ADMIN_INCLUDES')}} :</h5></td><td colspan="2">
															<table class="table table-bordered">
																@foreach($choices as $key=>$val)
																	@php
																		$split_key = explode('`',$key);
																		if($split_key[1]==$Order_sub->ord_id){
																	@endphp
																	<tr><td>{{$split_key[0]}}</td><td style="text-align:right">{{$val}} {{$Order_sub->ord_currency}}</td></tr>
																	@php
																		}
																	@endphp
																@endforeach
															</table>
														</td>
														</tr>
														@endif
													@endif
													<!-- End Showing choices in Invoice -->
													@endforeach
													
													
													
												</tbody>
											</table>
										</div>
										<!-- /.col -->
										<div class="col-md-12">
									<!-- this row will not appear when printing -->
										@php $get_statuses=get_status_count_byTransIdOnly($transid); @endphp
										@if(empty($get_statuses)==false)
											@php $getTotalCancelled = $get_statuses->rejected+$get_statuses->cancelled; @endphp
											@if($get_statuses->totals == $get_statuses->cancelled)
											@elseif($get_statuses->totals == $get_statuses->rejected)
											@elseif($get_statuses->totals == $getTotalCancelled)	
											@else
												<button class="btn btn-success pull-right" onclick="cancel_orders();" type="button">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_ORDERS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_ORDERS') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_ORDERS')}}</button>
											@endif
										@else
										@endif
										
									</div>
									</div>
									<!-- /.row -->
									
									
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
	<!-- Modal -->
	<div class="modal fade" id="orderRejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body" id="myBody">
					<input type="hidden" id="modal_order_id" value=''>
					{!! Form::textarea('modal_reject_rsn','',array('placeholder'=> (Lang::has(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL') : trans($FRONT_LANGUAGE.'.MER_REASON_TO_REJECT'),'class'=>'form-control','id'=>'modal_reject_rsn')) !!}
					<div id="reason_err"></div>
				</div>
				<div class="modal-footer">
					@php 
						$rejectBtn = (Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_ORDERS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_ORDERS') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL_ORDERS');
						$closeBtn = (Lang::has(Session::get('front_lang_file').'.FRONT_CLOSE')) ? trans(Session::get('front_lang_file').'.FRONT_CLOSE') : trans($FRONT_LANGUAGE.'.FRONT_CLOSE');
					@endphp
					{!! Form::button($rejectBtn,['class' => 'btn btn-xs btn-danger' ,'onclick'=>"javascript:reject_order_submit();",'id'=>'rejectOrderBtn'])!!}
					<img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="width:23%;display:none" id="loader" />
					{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
				</div>
			</div>
		</div>
	</div>
	<!-- /.panel-body -->

@endsection
@section('script')
<script>
	$(document).ready(function(){
		$("#checkAll").click(function(){
			$('.myCheckboxes').not(this).prop('checked', this.checked);
		});
		
		$(".myCheckboxes").change(function(){
				$('#checkAll').prop('checked', false);
		});
		
	});
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});
	function cancel_orders()
	{	
		var status_payment = '{{$payment_status}}';
		var searchIDs = $("input[name='item_det[]']:checked").map(function(){
		  return $(this).val();
		}).get(); 
		if(searchIDs!='')
		{   
				var redirect = '{{$redirect_url}}';		
				if(status_payment == 0)
				{
					bootbox.confirm({
						    message: "@lang(Session::get('front_lang_file').'.FRONT_FORCE_TOENTER_PAYMENTDET_CANCEL')",
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
									$('#modal_order_id').val(searchIDs);
									$('#orderRejectModal').modal();
								}
						    }
						});
				}
				else
				{
					$('#modal_order_id').val(searchIDs);
					$('#orderRejectModal').modal();
				}
				
		}
		else
		{
			$('#cancel_err').show();
		}
		//console.log(searchIDs);
	}
	function reject_order_submit()
	{
		var reason = $('#modal_reject_rsn').val();
		var orderId = $('#modal_order_id').val();
		if(reason.trim() == '')
		{
			$('#reason_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_REASON')").css({'color':'red'});
			return false;
		}

		$.ajax({
			type: 'post',
			data: {"reason":reason,"orderId":orderId},
			url: '{{url("customer-cancel-status")}}',
			beforeSend: function() {
				$('#rejectOrderBtn').hide();
				$('#loader').show();
			},
			success: function(responseText){ 
				window.location.reload();
			}		
		});
	}
</script>	
@endsection