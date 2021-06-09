@extends('DeliveryManager.layouts.default')
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
	.btn-sm {
		margin: 0.2px;
		width: 100%;
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
						
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								{{-- Display  message--}}
								@if(Session::has('message'))
							    <div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							   		{{Session::get('message')}}    
							    </div>
								@endif
								@if ($errors->has('errors')) 
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									
									{{ $errors->first('errors') }}
									
								</div>
								@endif
		
								<div class="" style="">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
											<tr>
												<td>&nbsp;</td>
												<td><input type="text" id="agentEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>

												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
		                                	<tr>
												<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO') }}</th>
			                                   <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGNED_DELBOY_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGNED_DELBOY_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGNED_DELBOY_DETAILS') }}</th>
											   
											    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_ORDERS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_ORDERS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TOTAL_ORDERS') }}</th>
												
												<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_AMT') }} <i class="fa fa-info-circle tooltip-demo" title="{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_COD_ORDER_AMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_COD_ORDER_AMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TOTAL_COD_ORDER_AMT') }}"></i> ({{ $default_currency }})</th>
												
												<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_RCVD_AMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_RCVD_AMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_RCVD_AMT') }} <i class="fa fa-info-circle tooltip-demo" title="{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_RCVD_AMT_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_RCVD_AMT_AGENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TOTAL_RCVD_AMT_AGENT') }}"></i> ({{ $default_currency }})</th>
												
			                                     <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_COMMISSION_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TOTAL_COMMISSION_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TOTAL_COMMISSION_AMOUNT') }}({{ $default_currency }}) </th>
												 
												<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAID_COMMISSION_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAID_COMMISSION_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAID_COMMISSION_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BALANCE_COMMISSION_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BALANCE_COMMISSION_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BALANCE_COMMISSION_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BALANCE_RECEIVE_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BALANCE_RECEIVE_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BALANCE_RECEIVE_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION') }}</th>
												
			                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ACTIONS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ACTIONS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ACTIONS') }}
			                                    </th>
			                                </tr>
		                                </thead>
		                                <tbody>
										
		                                </tbody>
		                            </table>
								</div>
								<!--table-->
							</div>
							{{--Manage page ends--}}
						</div>

					</div>
				</div>
			</div>
		</div>
		
	</div>
	<!-- END MAIN CONTENT -->
	
</div>


	<!-- POP UP -->
	<div id="payModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">@lang(Session::get('DelMgr_lang_file').'.DEL_PAY_TO_AGENT')</h4>
				</div>
				<div class="modal-body">
					{{ Form::open(['method' => 'post','url' => 'pay_to_delboy'])}}
					<div class="well" id="netBanking_details" style="background-color: #5cb85c3b">
					
						<h4>@lang(Session::get('DelMgr_lang_file').'.ADMIN_NET_DET')</h4>
						<table>
							<tbody>
							<tr>
								<td><b>@lang(Session::get('DelMgr_lang_file').'.ADMIN_ACC_NO')&nbsp;</b></td><td><span id="acc_no"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('DelMgr_lang_file').'.ADMIN_BANK_NAME')</b></td><td><span id="bank_name"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('DelMgr_lang_file').'.ADMIN_BRANCH')</b></td><td><span id="branch"></span></td>
							</tr>
							<tr>
								<td><b>@lang(Session::get('DelMgr_lang_file').'.ADMIN_IFSC')</b></td><td><span id="ifsc"></span></td>
							</tr>
							</tbody>
						</table>
					</div>
					<div class="form-group">
						<label for="email" id="ModalLabelEmail"></label>
						{{ Form::text('trans_id','',['class' => 'form-control','required','maxlength' => '50','placeholder' => 'Enter Transaction Id'])}}
						{{ Form::hidden('agent_id','',['id'=>'modal_agent_id'])}}
						{{ Form::hidden('agent_balance','',['id'=>'modal_agent_balance'])}}
						{{ Form::hidden('agent_curr','',['id'=>'modal_agent_curr'])}}
					</div>
					{{ Form::submit('Submit',['class' => 'btn btn-success'])}}
					{{ Form::close()}}
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('DelMgr_lang_file').'.DELMGR_CLOSE')</button>
				</div>
			</div>

		</div>
	</div>
	<!-- END OF POP UP -->
@section('script')
<script>
	var table='';
	$(document).ready(function () {
		$('.tooltip-demo').tooltip({placement:'left'})
		/*$('#dataTables-example').dataTable({
		    "bPaginate": false,
		    //"scrollX": true,
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false, 
		"bAutoWidth": false });*/
		table = $('#dataTables-example').DataTable({
					"processing": true,
					"responsive": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false,
					"ajax":{
						"url": "{{ url('delboy-commission-lists') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}",'delboyEmail_search': function(){return $("#agentEmail_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,7,8,9,10],
						"orderable": false
					} ],
					"columns": [
								{ "data": "SNo", sWidth: '5%' },
								{ "data": "agentName", sWidth: '14%' },
								{ "data": "totalOrders", sWidth: '9%' },
								{ "data": "totalOrderAmt", sWidth: '9%' },
								{ "data": "totalRcvdAmtCOD", sWidth: '9%' },
								{ "data": "totComisonAmt", sWidth: '9%' },
								{ "data": "paidAmt", sWidth: '9%' },
								{ "data": "balAmtToPay", sWidth: '9%'},
								{ "data": "balAmtToReceive", sWidth: '9%'},
								{ "data": "viewTransaxn", sWidth: '9%'},
								{ "data": "action", sWidth: '9%'}
								],
					"order": [ 0, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		$('#agentEmail_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
	});
	function payFun(agent_id,balance,currency,bank_details)
	{
		//alert(balance);
		$('#modal_agent_id').val(agent_id);
		$('#modal_agent_balance').val(balance);
		$('#modal_agent_curr').val(currency);
		$('#ModalLabelEmail').html('You have to Pay '+currency+'&nbsp;'+balance);
		var det = bank_details.split('`');
		//alert(det); return false;
		$('#acc_no').html(det[0]);
		$('#bank_name').html(det[1]);
		$('#branch').html(det[2]);
		$('#ifsc').html(det[3]);
		$('#payModal').modal({
			backdrop: 'static',
			keyboard: false
		})
	}
</script>
@endsection
@stop