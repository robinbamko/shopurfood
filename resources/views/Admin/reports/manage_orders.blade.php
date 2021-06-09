@extends('Admin.layouts.default')
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
							<div class="err-alrt-msg">
							@if ($errors->has('errors')) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>								
								{{ $errors->first('errors') }}
								
							</div>
							@endif
							@if ($errors->has('upload_file')) 
							<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p> 
							@endif
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							@endif
							<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
							</div>
							</div>
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								<div class="well">
									<div class="cal-search-filter">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('manage-orders'),'id'=>'validate_form']) !!}
											<div class="modal-header">
												<h4 class="modal-title" id="myModalLabel" style="cursor:pointer" onclick="toggle_fun();">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEARCH_FILTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEARCH_FILTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEARCH_FILTER')}}
													<span class="pull-right" id="mySymbol"><i class="fa fa-arrows-h"></i></span>
												</h4>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}} :</label>
													<div class="col-md-4">
														{!! Form::text('order_id','',['class'=>'form-control','placeholder' => 'Order ID','id' => 'order_id']) !!}
													</div>
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_STATUS')}} :</label>
													<div class="col-md-4">
														@php
															$ordStatusArray = order_status_array('admin_lang_file',$ADMIN_OUR_LANGUAGE);
															$ordStatusArray['9']='Cancelled';
														@endphp
														{{ Form::select('ord_status',$ordStatusArray,$ord_status,['class' => 'form-control' , 'style' => 'width:100%','id'=>'ord_status'] ) }}
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.MER_FROM_DATE')) ? trans(Session::get('admin_lang_file').'.MER_FROM_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_FROM_DATE')}} :</label>
													<div class="col-md-4">
														{!! Form::text('from_date',$from_date,['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'startDatePicker']) !!}
													</div>
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.MER_TO_DATE')) ? trans(Session::get('admin_lang_file').'.MER_TO_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_TO_DATE')}} :</label>
													<div class="col-md-4">
														{!! Form::text('to_date',$to_date,['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'endDatePicker']) !!}
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-offset-2">
													<button type="submit" class="btn btn-success" id="newConsigneeReset" name="submit" >@lang(Session::get('admin_lang_file').'.MER_SEARCH')</button>
													<a href="{{url('manage-orders')}}"><button type="button" name="reset" class="btn btn-default"> @lang(Session::get('admin_lang_file').'.ADMIN_RESET') </button></a>
													
												</div>
											</div>
											<!-- END -->
										{!! Form::close() !!}
									</div>
								</div>
								
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}
											</th>
											
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_DATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMENT_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_INVOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVOICE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIONS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACTIONS')}}
											</th>
										</tr>
									</thead>
									<tbody>
										@if(count($orderdetails) > 0)
										@php 
											$i = ($orderdetails->currentpage()-1)*$orderdetails->perpage()+1;  
										@endphp
										@foreach($orderdetails as $details)
										<tr>
											<td width="5%">{{$i}}</td>
											<td width="22%">{{ ucfirst($details->cus_fname).' '.$details->cus_lname}} @if($details->ord_admin_viewed==0) <span class="badge bg-danger">New</span> @endif</td>
											<td width="16%">{{ $details->ord_transaction_id }}</td>
											<td  width="10%">{{ date('m/d/Y',strtotime($details->ord_date)) }}</td>
											<td width="15%">{{ number_format(($details->revenue+$details->ord_delivery_fee),2).' '.$details->ord_currency }}</td>
											<td width="12%">{{ $details->ord_payment_status }}</td>
											<td width="6%"><a href="{{url('admin-invoice-order/'.base64_encode($details->ord_transaction_id))}}" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW')}}</a></td>
											<td width="14%"> 
											{{--@if($details->ord_self_pickup=='1')
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
											@else--}}
													<a href="{{url('admin-track-order/'.base64_encode($details->ord_transaction_id))}}" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRACK_ORDER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TRACK_ORDER')}}</a>
														{{--@endif--}}
											</td>
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
						{!! Form::textarea('modal_reject_rsn','',array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('admin_lang_file').'.MER_REASON_TO_REJECT') : trans($ADMIN_OUR_LANGUAGE.'.MER_REASON_TO_REJECT'),'class'=>'form-control','id'=>'modal_reject_rsn')) !!}
						
					</div>
					<div class="modal-footer">
						@php 
							$rejectBtn = (Lang::has(Session::get('admin_lang_file').'.MER_REJECT')) ? trans(Session::get('admin_lang_file').'.MER_REJECT') : trans($ADMIN_OUR_LANGUAGE.'.MER_REJECT');
							$closeBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLOSE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CLOSE');
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
		    "bLengthChange": true,
		    "bFilter": true,
		    "bInfo": true,
			"bAutoWidth": false ,
			aoColumnDefs: [
			  {
			     bSortable: false,
			     aTargets: [-1,-2]
			  }
			]
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
$(function(){	
	$("#startDatePicker").datepicker({
		todayBtn:  1,
		autoclose: true,
	}).on('changeDate', function (selected) {
		var minDate = new Date(selected.date.valueOf());
		$('#endDatePicker').datepicker('setStartDate', minDate);
	});

	$("#endDatePicker").datepicker().on('changeDate', function (selected) {
		var maxDate = new Date(selected.date.valueOf());
		$('#startDatePicker').datepicker('setEndDate', maxDate);
	});
});		
	function accept_order(order_id,status)
	{
		$.ajax({
			type: 'post',
			data: {id:order_id,status:status},
			url: '{{url("admin-change-status/")}}',
			beforeSend: function() {
				$('#btnDiv_'+order_id).hide();
				$('#loader_'+order_id).show();
			},
			success: function(responseText){ 
				window.location.reload();
			}		
		});	
	}
	function reject_order(order_id)
	{
		if(confirm('Are you want to reject?'))
		{
			$('#modal_order_id').val(order_id);
			$('#orderRejectModal').modal();
		}
	}
	function reject_order_submit()
	{
		var reason = $('#modal_reject_rsn').val();
		var orderId = $('#modal_order_id').val();
		$.ajax({
			type: 'post',
			data: {reason:reason,orderId:orderId},
			url: '{{url("admin-reject-status/")}}',
			beforeSend: function() {
				$('#btnDiv_'+orderId).hide();
				$('#loader_'+orderId).show();
			},
			success: function(responseText){ 
				window.location.reload();
			}		
		});
	}
	function change_status(status,order_id)
	{
		if(status=='1' || status=='4')
		{
			accept_order(order_id,status)
		}
		else
		{
			reject_order(order_id);
		}
	}
	
	function toggle_fun(){
		
		if($('.modal-body:visible').length){
			$('.modal-body').slideUp(500);
			$('#mySymbol').html('<i class="fa fa-arrows-v"></i>');
		}else{
			$('.modal-body').slideDown(500); 
			$('#mySymbol').html('<i class="fa fa-arrows-h"></i>');
		}
	}
</script>	

@endsection
@stop