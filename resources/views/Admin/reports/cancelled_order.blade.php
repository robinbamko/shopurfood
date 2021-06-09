@extends('Admin.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
	.payModal .form-group 
	{
	display: block;
	margin-bottom:15px;
	}
	.payModal .form-group input.form-control 
	{
	width:100%;
	}
	/*a.btn.btn-success.btn-sm {
		padding: 2px 5px;
	}*/
	
	table.dataTable
	{
		margin-top: 20px !important;
	}
	.btn-success {
		margin: 1px;
	}
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
								{{ $errors->first('errors') }}
								
							</div>
							@endif
							@if ($errors->has('upload_file')) 
							<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p> 
							@endif
							 
							<div class="alert alert-success alert-dismissible" role="alert" id="success_div" style="@if (Session::has('message')) display:block @else display:none @endif">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<span id="success_msg">{{ Session::get('message') }}</span>
							</div>
							
							<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<i class="fa fa-times-circle"></i>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
							</div>
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
							
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td><input type="text" id="cusEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="orderId_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="pdtName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
											{{--@php $ordStatusArray = array(''=>'Select All','Paid'=>'Paid','Unpaid'=>'Payable');@endphp
											{{ Form::select('ord_status',$ordStatusArray,$ord_status,['class' => 'form-control' , 'style' => 'width:100%','id'=>'ordStatus_search'] ) }}--}}
											</td>
										</tr>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}} 
											</th>
											<th style="text-align:center">
												{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_NAME') }}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCELLATION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCELLATION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCELLATION_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYABLE_AMT')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BALANCE_COMMISSION_AMOUNT')}}
											</th>

										</tr>
									</thead>
									<tbody>
									
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
							{{--Manage page ends--}}
						</div>

					</div>
				</div>
			</div>
		</div>
		
		<!--MODAL -->
		<div id="payCustModal" class="modal payModal" role="dialog" style="color:#000;" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAY_CUSTOMER')}}</h4>
					</div>
					<div class="modal-body">
						{{ Form::open(['method' => 'post','url' => 'pay_to_customer','class'=>'form-horizontal','id'=>'custPayForm'])}}
						<div class="well" id="netBanking_details" style="background-color: #5cb85c3b">
					
						<h4>@lang(Session::get('admin_lang_file').'.ADMIN_NET_DET')</h4>
						<table>
							<tbody>
							<tr>
								<td><b>@lang(Session::get('admin_lang_file').'.ADMIN_ACC_NO')&nbsp;</b></td><td><span id="acc_no"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('admin_lang_file').'.ADMIN_BANK_NAME')</b></td><td><span id="bank_name"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('admin_lang_file').'.ADMIN_BRANCH')</b></td><td><span id="branch"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('admin_lang_file').'.ADMIN_IFSC')</b></td><td><span id="ifsc"></span></td>
							</tr>
							</tbody>
						</table>
					</div>
						<div class="form-group" >
							<label class="control-label col-sm-3" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYABLE_AMT')}}:</label>
							<div class="col-sm-9">
								{{ Form::text('ord_cancel_paidamt','',['class' => 'form-control','required','maxlength' => '50','readonly'=>'readonly','id'=>'ord_cancel_paidamt'])}}
								{{ Form::hidden('ord_cancel_paytype','NetBanking',['class' => 'form-control'])}}
							</div>
						</div>
						<div class="form-group ">
							<label class="control-label col-sm-3" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID')}}:</label>
							<div class="col-sm-9">
								{{ Form::text('ord_cancelpaid_transid','',['class' => 'form-control','required','maxlength' => '50','placeholder' => 'Enter Transaction Id','id'=>'ord_cancelpaid_transid'])}}
								<div id="errorMsg" style="color:red;display:none;">@lang(Session::get('admin_lang_file').'.ADMIN_ENTR_TRANS_ID')</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-10">
								{{ Form::submit('Submit',['class' => 'btn btn-success','type'=>'button','onclick'=>'submit_payment()'])}}
							</div>
						</div>
						{{ Form::hidden('ord_id','',array('id' => 'ord_id'))}}
						
						{{ Form::close()}}
						</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('admin_lang_file').'.ADMIN_CLOSE')</button>
					</div>
				</div>
				
			</div>
		</div>
		<!-- END OF MODAL-->
		
	</div>
	<!-- END MAIN CONTENT -->
</div>

@section('script')

<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
<script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>
<script>
	var table='';
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});	
	function call_pay_fun(ord_id,amount,bank_details)
	{
		$('#ord_cancel_paidamt').val(amount);
		$('#ord_id').val(ord_id);
		var det = bank_details.split('`');
		//alert(det); return false;
		$('#acc_no').html(det[0]);
		$('#bank_name').html(det[1]);
		$('#branch').html(det[2]);
		$('#ifsc').html(det[3]);
		$('#payCustModal').modal({
			backdrop: 'static',
			keyboard: false
		})
	}
	
	function submit_payment()
	{
		if($('#ord_cancelpaid_transid').val()=='')
		{
			$('#errorMsg').show();
		}
		else
		{
			$('#errorMsg').hide();
			$('#custPayForm').submit();
		}
	}
	$(document).ready(function () {
		var table;
		/*table = $('#dataTables-example').dataTable({
					"bPaginate": false,
					//"scrollX": true,
					"bLengthChange": false,
					"bFilter": true,
					"bInfo": false,
					"bAutoWidth": false 
				});*/
		table = $('#dataTables-example').DataTable({
					"processing": true,
					"responsive": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					
					"searching": false,
					"ajax":{
						"url": "{{ url('cancelled_orders_ajax') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}",'cusEmail_search': function(){return $("#cusEmail_search").val(); },orderId_search:function(){return $("#orderId_search").val(); },pdtName_search:function(){return $("#pdtName_search").val(); },ordStatus_search:function(){return $("#ordStatus_search").val(); }} 
					},
					"columnDefs": [ {
						"targets": 0,
						"orderable": false
					} ],
					"columns": [
								{ "data": "SNo",sWidth: '8%' },
								{ "data": "custEmail", sWidth: '17%' },
								{ "data": "orderId", sWidth: '10%' },
								{ "data": "pdtName", sWidth: '10%' },
								{ "data": "cancelAmt", sWidth: '10%' },
								{ "data": "commission", sWidth: '10%' },
								{ "data": "payableAmt", sWidth: '10%' },
								{ "data": "paidAmt", sWidth: '10%'},
								{ "data": "balanceAmt", sWidth: '15%'},
								],
					"order": [ 0, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		$('#cusEmail_search,#orderId_search,#pdtName_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#ordStatus_search').change(function(){
			table.draw();
		});
		/*$("#startDatePicker").datepicker({
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
		});*/
		
	});
	
	function call_wallet_fun(ord_id,cus_id,amount,trans_id)
	{
		$.ajax({
			type:'get',
			url:"{{url('refund-to-wallet')}}",
			data:{'ord_id' : ord_id,'cus_id':cus_id,'amount':amount,'trans_id' : trans_id},
			beforeSend : function(){
				$('#loading-image').show();
			},
			success:function(resposne){
				//table.draw();
				$('#loading-image').hide();
				$('#success_div').show();
				$('#success_msg').text(resposne);
				location.reload();
			}
		});
	}
	
	
</script>	

@endsection
@stop