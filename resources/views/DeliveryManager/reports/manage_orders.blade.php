@extends('DeliveryManager.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
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
								<i class="fa fa-times-circle"></i>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT_ONE')}}
							</div>
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div style="margin: 7px 23px;">
									<div class="cal-search-filter">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('delivery-manage-orders'),'id'=>'validate_form']) !!}
											<div class="row">
												<br>
												<div class="col-sm-3 col-md-3">
													<div class="item form-group">
														<div class="col-sm-6 date-top"> @lang(Session::get('DelMgr_lang_file').'.DEL_FR_DATE')  </div>
														<div class="col-sm-6 place-size">
															<span class="icon-calendar cale-icon"></span>
															<input id="startDatePicker" class="form-control" placeholder="MM/DD/YYYY" required="required" readonly="readonly" name="from_date" type="text" value="{{$from_date}}">
														</div>
													</div>
												</div>
												<div class="col-sm-3 col-md-3">
													<div class="item form-group">
														<div class="col-sm-6 date-top"> @lang(Session::get('DelMgr_lang_file').'.DEL_TO_DATE')  </div>
														<div class="col-sm-6 place-size">
															<span class="icon-calendar cale-icon"></span>
															<input id="endDatePicker" class="form-control hasDatepicker" placeholder="MM/DD/YYYY" required="required" readonly="readonly" name="to_date" type="text" value="{{$to_date}}">
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-sm-2">
														<input type="submit" name="submit" class="btn btn-block btn-success" value=" Search  ">
													</div>
													<div class="col-sm-2">
														<a href="{{url('delivery-manage-orders')}}"><button type="button" name="reset" class="btn btn-block btn-info"> @lang(Session::get('DelMgr_lang_file').'.DEL_RESET') </button></a>
													</div>
												</div>
												
											</div>{!! Form::close() !!}</div>
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MERCHANT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MERCHANT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MERCHANT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_AGET_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_AGET_ASSIGNED') : trans($DELMGR_OUR_LANGUAGE.'.ADMIN_AGET_ASSIGNED')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_DATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PRE_ORDER_DATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMENT_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMENT_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INVOICE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INVOICE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_INVOICE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ACTIONS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ACTIONS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ACTIONS')}}
											</th>
										</tr>
									</thead>
									<tbody>
										
										@if(count($orderdetails) > 0)
										@php $i = ($orderdetails->currentpage()-1)*$orderdetails->perpage()+1;  
										
										@endphp
										@foreach($orderdetails as $details)
										<tr>
											<td width="5%">{{$i}}</td>
											<td width="15%">{{ ucfirst($details->cus_fname).' '.$details->cus_lname}}</td>
											<td width="15%">{{ ucfirst($details->mer_fname).' '.$details->mer_lname}}</td>
											<td width="10%">{{ ucfirst($details->agent_fname).' '.$details->agent_lname}}<br>{{'('.$details->agent_email.')'}}</td>
											<td width="10%">{{ $details->ord_transaction_id }}</td>
											<td  width="10%">{{ date('m/d/Y',strtotime($details->ord_date)) }}</td>
											<td  width="10%">
											@if($details->ord_pre_order_date != '')
												{{ date('m/d/Y h:i A',strtotime($details->ord_pre_order_date)) }}
											@else
											-
											@endif
											</td>
											<td width="5%">{{ $details->revenue.' '.$default_currency}}</td>
											<td width="5%">{{ $details->ord_payment_status }}</td>
											<td width="5%"><a href="{{url('delivery-invoice-order/'.base64_encode($details->ord_transaction_id).'/'.base64_encode($details->ord_merchant_id))}}" target="_blank">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW')}}</a></td>
											<td width="10%"> 
												<a href="{{url('delivery-track-order/'.base64_encode($details->ord_transaction_id).'/'.base64_encode($details->ord_merchant_id))}}" target="new">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TRACK_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TRACK_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TRACK_ORDER')}}</a>
											</td>
										</tr>
										@php $i++; @endphp
										@endforeach
										
										@endif
									</tbody>
								</table>
							</div>
							{{--Manage page ends--}}
						</div>
						@if(count($orderdetails) > 0)
						{!! $orderdetails->render() !!}
						@endif
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_DETAILS')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="myBody">
						...
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">@lang(Session::get('DelMgr_lang_file').'.DELMGR_CLOSE')</button>
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
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		"bAutoWidth": false });
		
$("#startDatePicker").datepicker({
        todayBtn:  1,
        autoclose: true,
		orientation: 'auto bottom'
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#endDatePicker').datepicker('setStartDate', minDate);
    });

    $("#endDatePicker").datepicker({orientation: 'auto bottom'})
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
	
	/*function get_track_details(id)
		{
		$.ajax( {
		type: 'get',
		data: {id:id},
		url: '{{url("admin-track-order/")}}/'+id,
		beforeSend: function() {
		$('#myBody').html('');
		},
		success: function(responseText){ 
		$('#myBody').html(responseText);
		$('#exampleModal').modal('show');
		}		
		});	
	}*/
	$(function () {
       
    });
</script>	

@endsection
@stop