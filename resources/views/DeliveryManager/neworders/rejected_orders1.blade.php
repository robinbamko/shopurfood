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
							<div class="err-alrt-msg">
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
								{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT_ONE')}}
							</div>
							</div>
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div  style="margin: 7px 23px;">
									
								</div>
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}</th>
	
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_SHIPPING_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_SHIPPING_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_SHIPPING_DETAILS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_STORE_RESTAURANT_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DEL_STORE_RESTAURANT_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DEL_STORE_RESTAURANT_DETAILS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_DETAILS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGNED_DELBOY_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGNED_DELBOY_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGNED_DELBOY_DETAILS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_REJECTED_REASON')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_REJECTED_REASON') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_REJECTED_REASON')}}
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
											<td width="20%">
												{{ ucfirst($details->ord_shipping_cus_name)}} <br>
												@if($details->ord_shipping_address1!='') {{$details->ord_shipping_address1}} <br> @endif
												{{$details->ord_shipping_address}} <br>
												
												{{$details->ord_shipping_mobile}} <br>
												{{$details->ord_shipping_mobile1}} <br>
												{{$details->order_ship_mail}}
											</td>
											<td width="9%">{{ ucfirst($details->st_store_name)}}<br>{{$details->st_address}}</td>
											<td width="15%">
												<strong> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_ID')}} : </strong> {{ $details->ord_transaction_id }} <br>
												<strong> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_DATE')}} : </strong> {{ date('m/d/Y',strtotime($details->ord_date)) }} <br>
												@if($details->ord_pre_order_date != '')
												<strong> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PRE_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PRE_ORDER_DATE')}} : </strong> {{ date('m/d/Y h:i A',strtotime($details->ord_pre_order_date)) }} <br>
												@endif
												<strong> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_AMOUNT')}} :</strong>  {{ ($details->revenue).' '.$details->ord_currency }}
											</td>
											<td  width="10%">
												{{ ucfirst($details->deliver_fname).' '.$details->deliver_lname}} <br>
												{{$details->deliver_phone1}} <br>
												@if($details->deliver_phone2!='') {{$details->deliver_phone2}} <br>@endif
												{{$details->deliver_email}}
											</td>
											<td width="10%"><a href="javascript:view_rejectReason('{{$i}}');" id="view_reason_{{$i}}" data-value="{{$details->ord_delboy_rjct_reason}}">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW')}}</a></td>
											<td width="8%"><a href="{{url('delivery-invoice-order/'.base64_encode($details->ord_transaction_id).'/'.base64_encode($details->ord_merchant_id))}}" target="_blank">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW')}}</a></td>
											<td width="8%"> 
												@php $status_count = get_status_count_byTransId($details->ord_transaction_id,$details->ord_merchant_id); @endphp
												@if(empty($status_count)===false)
													@php $totalCount = $status_count->rejected+$status_count->cancelled+$status_count->assigned; @endphp
													@if($status_count->totals==$totalCount)
														<a href="javascript:individual_change_status('{{$details->ord_transaction_id}}`{{$details->ord_merchant_id}}`{{$details->ord_delivery_memid}}`{{$details->st_store_name}}');">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN_AGENT')}}</a>
													@else
														
													@endif
												@endif
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
						<h5 class="modal-title" id="exampleModalLabel">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_REJECTED_REASON')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_REJECTED_REASON') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_REJECTED_REASON')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="myBodytoShowContent">
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
			"bAutoWidth": false 
		});
		
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
	});
	
	/*function assign_deliveryBoy(trans_id,merchant_id)
	{
		$.ajax({
			type: 'get',
			data: {trans_id:trans_id,merchant_id:merchant_id},
			url: '{{url("assign-delivery-boy")}}',
			beforeSend: function() {
				//$('#myBody').html('');
			},
			success: function(responseText){ 
				window.location.reload();
			}		
		});	
	}*/
	function checkAll(ele) {
		var checkboxes = document.getElementsByTagName('input');
		if (ele.checked) {
			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].type == 'checkbox') {
					checkboxes[i].checked = true;
				}
			}
			} else {
			for (var i = 0; i < checkboxes.length; i++) {
				console.log(i)
				if (checkboxes[i].type == 'checkbox') {
					checkboxes[i].checked = false;
				}
			}
		}
	}
	$('.block_value').click(function(){
		$(".rec-select").css({"display" : "none"});
        var val = [];
		$('input[name="chk[]"]:checked').each(function(i){
			var j=i;
			val[j] = $(this).val();
		});
		
		if(val=='')
		{
			$(".rec-select").css({"display" : "block"});
			return;
		}
		change_status(val,0);
	});
	function change_status(val)
	{
		var form = $('<form action="{{url('reassign-delivery-boy1')}}" method="post">' +
					'<input type="text" name="ord_transaction_id" value="' + val + '" />' +
					'<input type="text" name="_token" value="{{csrf_token()}}" />' +
					'</form>');
					$('body').append(form);
		form.submit();
       
	}
	function individual_change_status(gotVal)
	{
		var val = [];
		val[0]=gotVal;
		change_status(val)
	}
	function view_rejectReason(id){
		$('#myBodytoShowContent').html($('#view_reason_'+id).attr('data-value'));
		$('#exampleModal').modal();
	}
</script>	

@endsection
@stop