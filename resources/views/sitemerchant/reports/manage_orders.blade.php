@extends('sitemerchant.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
	table.dataTable
	{
		margin-top: 20px !important;
	}
	#location_table .dataTables_length label{ margin:0; }
</style>


<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>
							{{-- Display error message--}}
							
							@if ($errors->has('errors')) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<i class="fa fa-check-circle"></i>
								{{ $errors->first('errors') }}
								
							</div>
							@endif
							@if ($errors->has('upload_file')) 
							<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p> 
							@endif
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
							</div>
							@endif
							<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<i class="fa fa-times-circle"></i>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
							</div>
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div>
									<div class="cal-search-filter">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('mer-manage-orders'),'id'=>'validate_form']) !!}
											<div class="row">
												<br>
												<div class="col-sm-3 col-md-3">
													<div class="item form-group">
														<div class="col-sm-6 date-top">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_STATUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_STATUS')}} : </div>
														<div class="col-sm-6 place-size">
															@php
															$ordStatusArray = order_status_array('mer_lang_file',$MER_OUR_LANGUAGE);
															$ordStatusArray['9']='Cancelled';
															@endphp
												
															{{ Form::select('ord_status',$ordStatusArray,$ord_status,['class' => 'form-control' , 'style' => 'width:100%','id'=>'ord_status'] ) }}
														</div>
													</div>
												</div>
												<div class="col-sm-3 col-md-3">
													<div class="item form-group">
														<div class="col-sm-5 date-top"> {{(Lang::has(Session::get('mer_lang_file').'.MER_FROM_DATE')) ? trans(Session::get('mer_lang_file').'.MER_FROM_DATE') : trans($MER_OUR_LANGUAGE.'.MER_FROM_DATE')}} :  </div>
														<div class="col-sm-7 place-size">
															<span class="icon-calendar cale-icon"></span>
															{!! Form::text('from_date',$from_date,['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'startDatePicker','required'=>"required" ,'readonly'=>"readonly"]) !!}
														</div>
													</div>
												</div>
												<div class="col-sm-3 col-md-3">
													<div class="item form-group">
														<div class="col-sm-5 date-top">{{(Lang::has(Session::get('mer_lang_file').'.MER_TO_DATE')) ? trans(Session::get('mer_lang_file').'.MER_TO_DATE') : trans($MER_OUR_LANGUAGE.'.MER_TO_DATE')}} :  </div>
														<div class="col-sm-7 place-size">
															<span class="icon-calendar cale-icon"></span>
															{!! Form::text('to_date',$to_date,['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'endDatePicker','required'=>"required" ,'readonly'=>"readonly"]) !!}
														</div>
													</div>
												</div>
												<div class="col-sm-3 col-md-3">
													<div class="form-group">
														<div class="col-sm-6 col-md-6">
															<input type="submit" name="submit" class="btn btn-block btn-success" style="padding:6px 6px;" value=" Search  ">
														</div>
														<div class="col-sm-6 col-md-6">
															<a href="{{url('mer-manage-orders')}}"><button type="button" name="reset" class="btn btn-block btn-info" style="padding:6px 6px;"> Reset </button></a>
														</div>
													</div>
												</div>
												
											</div>{!! Form::close() !!}</div>
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_ID') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_DATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_DATE')}}
											</th> 
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_PRE_OR_DT')) ? trans(Session::get('mer_lang_file').'.MER_PRE_OR_DT') : trans($MER_OUR_LANGUAGE.'.MER_PRE_OR_DT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_AMOUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_AMOUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PAYMENT_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAYMENT_STATUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_PAYMENT_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_INVOICE')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVOICE') : trans($MER_OUR_LANGUAGE.'.ADMIN_INVOICE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACTIONS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ACTIONS')}}
											</th>
										</tr>
									</thead>
									<tbody>
										
										@if(count($orderdetails) > 0)
										@php $i = 1; @endphp
										@foreach($orderdetails as $details)
										<tr>
											<td width="5%">{{$i}}</td>
											<td width="22%">{{ ucfirst($details->cus_fname).' '.$details->cus_lname}} @if($details->ord_merchant_viewed==0) <span class="badge bg-danger">New</span> @endif</td>
											<td width="8%">{{ $details->ord_transaction_id }}</td>
											<td  width="10%">{{ date('m/d/Y',strtotime($details->ord_date)) }}</td>
											<td  width="10%">@if($details->ord_pre_order_date!=NULL) {{ date('m/d/Y h:i A',strtotime($details->ord_pre_order_date)) }} @endif</td>
											<td width="15%">{{ number_format(($details->revenue),2).' '.$details->ord_currency }}</td>
											<td width="10%">{{ $details->ord_payment_status }}</td>
											<td width="8%"><a href="{{url('mer-admin-invoice-order/'.base64_encode($details->ord_transaction_id))}}" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW')}}</a></td>
											<td width="12%">
												<a href="{{url('mer-order-details/'.base64_encode($details->ord_transaction_id))}}" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_VIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_VIEW')}}</a>
											</td>
											<?php /* <td width="14%"> 
												<!-- IF ORDER IS SELF PICKUP, MERCHANT CAN ACCEPT / REJECT AND CAN CHANGE THE STATUS (ORDER PLACED) -->
												{{--@if($details->ord_self_pickup=='1')
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
												@else--}}
												@if($details->ord_cancel_status == '1' && $details->ord_status=='3')
												<a href="javascript:;" data-toggle="modal" data-target="#viewRejectReasonModal_{{$details->ord_transaction_id}}">
															<span data-toggle="tooltip" title="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REJECT_REASON')) ? trans(Session::get('mer_lang_file').'.ADMIN_REJECT_REASON') : trans($MER_OUR_LANGUAGE.'.ADMIN_REJECT_REASON')}}">
																{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}
															</span>
															</a>
														<!-- Modal -->
														<div class="modal fade" id="viewRejectReasonModal_{{$details->ord_transaction_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	
																	<div class="modal-body" id="myBody">
																		<input type="hidden" id="modal_order_id" value=''>
																		{{$details->ord_reject_reason}}
																		
																	</div>
																	<div class="modal-footer">
																		@php 
																			$closeBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE');
																		@endphp
																		{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
																	</div>
																</div>
															</div>
														</div>
														<!-- EOF MODAL -->
												@elseif($details->ord_cancel_status == '1')
													<a href="javascript:;" data-toggle="modal" data-target="#viewCancelReasonModal_{{$details->ord_transaction_id}}" ><span data-toggle="tooltip" title="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON') : trans($MER_OUR_LANGUAGE.'.ADMIN_CANCEL_REASON')}}">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_USER_CANCEL')) ? trans(Session::get('mer_lang_file').'.ADMIN_USER_CANCEL') : trans($MER_OUR_LANGUAGE.'.ADMIN_USER_CANCEL')}}</span>
													</a>
													<!-- Modal -->
														<div class="modal fade " id="viewCancelReasonModal_{{$details->ord_transaction_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	
																	<div class="modal-body" id="myBody">
																		<input type="hidden" id="modal_order_id" value=''>
																		{{ucfirst($details->ord_cancel_reason)}}
																		
																	</div>
																	<div class="modal-footer">
																		@php 
																			$closeBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE');
																		@endphp
																		{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
																	</div>
																</div>
															</div>
														</div>
														<!-- EOF MODAL -->

												@else
													@if($details->ord_status=='1')
														@php 
															$acceptBtn = (Lang::has(Session::get('mer_lang_file').'.MER_ACCEPT')) ? trans(Session::get('mer_lang_file').'.MER_ACCEPT') : trans($MER_OUR_LANGUAGE.'.MER_ACCEPT');
															$rejectBtn = (Lang::has(Session::get('mer_lang_file').'.MER_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REJECT');
														@endphp
														<div id="btnDiv_{{$details->ord_transaction_id}}">
														{!! Form::button($acceptBtn,['class' => 'btn btn-xs btn-success' ,'onclick'=>"javascript:accept_order('$details->ord_transaction_id','2');"])!!}
														{!! Form::button($rejectBtn,['class' => 'btn btn-xs btn-danger' ,'onclick'=>"javascript:reject_order('$details->ord_transaction_id','3');"])!!}
														</div>
														<img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="width:23%;display:none" id="loader_{{$details->ord_transaction_id}}" />
													@elseif($details->ord_status=='2')
														@if($details->ord_self_pickup=='1')
															@php $changeStatusArray = array(
																			''=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT'),
																			'1'=>(Lang::has(Session::get('mer_lang_file').'.MER_ORDER_PLACED')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_PLACED') : trans($MER_OUR_LANGUAGE.'.MER_ORDER_PLACED'),
																			'3'=>(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED'),
																			'8'=>(Lang::has(Session::get('mer_lang_file').'.MER_DELIVERED')) ? trans(Session::get('mer_lang_file').'.MER_DELIVERED') : trans($MER_OUR_LANGUAGE.'.MER_DELIVERED'));
															@endphp
														@else
															@php $changeStatusArray = array(
																			''=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT'),
																			'1'=>(Lang::has(Session::get('mer_lang_file').'.MER_ORDER_PLACED')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_PLACED') : trans($MER_OUR_LANGUAGE.'.MER_ORDER_PLACED'),
																			'3'=>(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED'),
																			'4'=>(Lang::has(Session::get('mer_lang_file').'.MER_PREPARE_DELIVER')) ? trans(Session::get('mer_lang_file').'.MER_PREPARE_DELIVER') : trans($MER_OUR_LANGUAGE.'.MER_PREPARE_DELIVER'));
															@endphp
														@endif
														{{ Form::select('change_status',$changeStatusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'change_status','onchange'=>'change_status(this.value,\''.$details->ord_transaction_id.'\')'] ) }}
													@elseif($details->ord_status=='3')
														<a href="javascript:;" data-toggle="modal" data-target="#viewRejectReasonModal_{{$details->ord_transaction_id}}">
															<span data-toggle="tooltip" title="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REJECT_REASON')) ? trans(Session::get('mer_lang_file').'.ADMIN_REJECT_REASON') : trans($MER_OUR_LANGUAGE.'.ADMIN_REJECT_REASON')}}">
																{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}
															</span>
															</a>
														<!-- Modal -->
														<div class="modal fade" id="viewRejectReasonModal_{{$details->ord_transaction_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	
																	<div class="modal-body" id="myBody">
																		<input type="hidden" id="modal_order_id" value=''>
																		{{$details->ord_reject_reason}}
																		
																	</div>
																	<div class="modal-footer">
																		@php 
																			$closeBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE');
																		@endphp
																		{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
																	</div>
																</div>
															</div>
														</div>
														<!-- EOF MODAL -->
													@elseif($details->ord_status >= '4')
														<a href="{{url('mer-admin-track-order/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRACK_ORDER') : trans($MER_OUR_LANGUAGE.'.ADMIN_TRACK_ORDER')}}</a>
													@endif
												@endif
											</td> */ ?>
										</tr>
										@php $i++; @endphp
										@endforeach
										
										@endif
									</tbody>
								</table>
								
								@if(count($orderdetails) > 0)
								{!! $orderdetails->appends(\Input::except('page'))->render() !!}
								@endif
								
							</div>
							{{--Manage page ends--}}
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- Modal -->
		<div class="modal fade" id="orderRejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					
					<div class="modal-body" id="myBody">
						<input type="hidden" id="modal_order_id" value=''>
						{!! Form::textarea('modal_reject_rsn','',array('placeholder'=> (Lang::has(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REASON_TO_REJECT'),'class'=>'form-control','id'=>'modal_reject_rsn')) !!}
						
					</div>
					<div class="modal-footer">
						@php 
							$rejectBtn = (Lang::has(Session::get('mer_lang_file').'.MER_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REJECT');
							$closeBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE');
						@endphp
						{!! Form::button($rejectBtn,['class' => 'btn btn-xs btn-danger' ,'onclick'=>"javascript:reject_order_submit();"])!!}
						{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
					</div>
				</div>
			</div>
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>

@section('script')

<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
<script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>
<script>
	$(document).ready(function () {
		
		$('#dataTables-example').dataTable({
		    "bPaginate": false,
			"responsive": true,
		    //"scrollX": true,
		    "bLengthChange": true,
		    "bFilter": true,
		    "bInfo": false,
		"bAutoWidth": false ,
		aoColumnDefs: [
			  {
			     bSortable: false,
			     aTargets: [-1,-2]
			  }]
	});
		
$("#startDatePicker").datepicker({
        todayBtn:  1,
        autoclose: true,
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#endDatePicker').datepicker('setStartDate', minDate);
    });

    $("#endDatePicker").datepicker()
        .on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('#startDatePicker').datepicker('setEndDate', maxDate);
        });
		
		/*$("#startDatePicker").datepicker({ 
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			minDate: new Date(),
			maxDate: '+2y',
			onSelect: function(date){
				
				var selectedDate = new Date(date);
				var msecsInADay = 86400000;
				var endDate = new Date(selectedDate.getTime() + msecsInADay);
				
				//Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
				$("#endDatePicker").datepicker( "option", "minDate", endDate );
				$("#endDatePicker").datepicker( "option", "maxDate", '+2y' );
				
			}
		});
		
		$("#endDatePicker").datepicker({ 
			dateFormat: 'yy-mm-dd',
			changeMonth: true
		});*/
	});
	
	function accept_order(order_id,status)
	{
		$.ajax({
			type: 'post',
			data: {id:order_id,status:status},
			url: '{{url("merchant-change-status/")}}',
			beforeSend: function() {
				$('#btnDiv_'+order_id).hide();
				$('#loader_'+order_id).show();
			},
			success: function(responseText){ 
				window.location.reload();
			}		
		});	
	}
	function reject_order_submit()
	{
		var reason = $('#modal_reject_rsn').val();
		var orderId = $('#modal_order_id').val();
		$.ajax({
			type: 'post',
			data: {reason:reason,orderId:orderId},
			url: '{{url("merchant-reject-status")}}',
			beforeSend: function() {
				$('#btnDiv_'+orderId).hide();
				$('#loader_'+orderId).show();
			},
			success: function(responseText){ console.log(responseText);
				window.location.reload();
			}		
		});
	}
	function change_status(status,order_id)
	{
		if(status=='1' || status=='4' || status=='8')
		{
			
			accept_order(order_id,status)
		}
		else
		{
			reject_order(order_id);
		}
	}
	function reject_order(order_id)
	{
		if(confirm('Are you want to reject?'))
		{
			$('#modal_order_id').val(order_id);
			$('#orderRejectModal').modal();
		}
	}

	$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();   
	});
</script>	

@endsection
@stop