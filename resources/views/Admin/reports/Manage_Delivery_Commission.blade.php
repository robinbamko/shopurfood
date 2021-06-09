@extends('Admin.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
	table.dataTable
	{
		margin-top: 20px !important;
	}
	.btn-success {
		margin: 1px;
	}
	/*table#dataTables-example{width: 100%; overflow: auto;}*/
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
							@if ($errors->has('errors')) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								<i class="fa fa-check-circle"></i>
								{{ $errors->first('errors') }}
								
							</div>
							@endif
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
							</div>
							@endif
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div class="table-responsive" style="margin-top:10px;">
									<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
											<tr>
												<td>&nbsp;</td>
												<td><input type="text" id="agentEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

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
												<th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_S_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_S_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_S_NO') }}</th>
			                                   <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_DETAILS') }}</th>
											   
											    <th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_TOTAL_ORDERS')) ? trans(Session::get('admin_lang_file').'.DELMGR_TOTAL_ORDERS') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_TOTAL_ORDERS') }}</th>
												
												<th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_ORDER_AMT')) ? trans(Session::get('admin_lang_file').'.DELMGR_ORDER_AMT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_ORDER_AMT') }} <i class="fa fa-info-circle tooltip-demo" title="{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_TOTAL_COD_ORDER_AMT')) ? trans(Session::get('admin_lang_file').'.DELMGR_TOTAL_COD_ORDER_AMT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_TOTAL_COD_ORDER_AMT') }}"></i> ({{ $default_currency }})</th>
												
												<th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_RCVD_AMT')) ? trans(Session::get('admin_lang_file').'.DELMGR_RCVD_AMT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_RCVD_AMT') }} <i class="fa fa-info-circle tooltip-demo" title="{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_TOTAL_RCVD_AMT_AGENT')) ? trans(Session::get('admin_lang_file').'.DELMGR_TOTAL_RCVD_AMT_AGENT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_TOTAL_RCVD_AMT_AGENT') }}"></i> ({{ $default_currency }})</th>
												
			                                     <th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_TOTAL_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.DELMGR_TOTAL_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_TOTAL_COMMISSION_AMOUNT') }}({{ $default_currency }}) </th>
												 
												<th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_BALANCE_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.DELMGR_BALANCE_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_BALANCE_COMMISSION_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.DELMGR_BALANCE_RECEIVE_AMOUNT')) ? trans(Session::get('admin_lang_file').'.DELMGR_BALANCE_RECEIVE_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.DELMGR_BALANCE_RECEIVE_AMOUNT') }} ({{ $default_currency }}) </th>
												
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_TRANSACTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_TRANSACTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW_TRANSACTION') }}</th>
												
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIONS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACTIONS') }}
			                                    </th>
			                                </tr>
		                                </thead>
		                                <tbody>
		                               
		                                </tbody>
		                            </table>
		                        </div>
								</div>
								<!--table-->
							</div>
							{{--Manage page ends--}}
						</div>
						@if(count($commission_list) > 0)
						{!! $commission_list->render() !!}
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END MAIN CONTENT -->
	<!-- POP UP -->
	<div id="payModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>

					<h4 class="modal-title">@lang(Session::get('admin_lang_file').'.ADMIN_PAY_DEL_COMM')</h4>

				</div>
				<div class="modal-body">
					{{ Form::open(['method' => 'post','url' => 'admin_pay_to_delboy','onsubmit'=>'return valiadate_transid();'])}}
					<div class="form-group">
						<label for="email" id="ModalLabelEmail"></label>
						<div class="well" id="netBanking_details" style="background-color: #5cb85c3b">
					
						<h4>@lang(Session::get('admin_lang_file').'.ADMIN_NET_DET')</h4>
						<div class="table-responsive">
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
					</div>
						{{ Form::text('trans_id','',['class' => 'form-control','maxlength' => '50','placeholder' => 'Enter Transaction Id','id'=>'ModalTransId'])}}
						<p style="color:red;display:none" id="transidError">@lang(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING')</p>
						{{ Form::hidden('agent_id','',['id'=>'modal_agent_id'])}}
						{{ Form::hidden('agent_balance','',['id'=>'modal_agent_balance'])}}
						{{ Form::hidden('agent_curr','',['id'=>'modal_agent_curr'])}}
					</div>

					{{ Form::submit('Submit',['class' => 'btn btn-success'])}}
					{{ Form::close()}}
				</div>
				<div class="modal-footer">
				{{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
					<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('admin_lang_file').'.ADMIN_CLOSE')</button>

				</div>
			</div>

		</div>
	</div>

	<div id="stripedetail" class="modal fade" role="dialog" style="display: none">
		<form name="" id="stripe_form">
		<input name="_token" type="hidden" value="{{ csrf_token() }}"/>
		<div class="modal-dialog">
			<label id="Stripeerrors" style="display: none"><small style="color:red">Please Fill Required Fields</small></label>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pay To Merchant</h4>
				</div>
				<div class="modal-body">
					<div class="input-field">
						<div class="form-left">
							<label>Card Number*</label>
							<input  placeholder="Enter Card Number" minlength="16" maxlength="16" id="card_no"  name="card_no" type="text" value="" class="error  stripenumberic" aria-invalid="true">
						</div>
						<div class="form-right">
							<label>Card Expiry Month</label>
							<input id="ccExpiryMonth" placeholder="Ex:4" minlength="1" maxlength="2"  name="ccExpiryMonth" type="text" value="" class="error  stripenumberic" aria-invalid="true">
						</div>
					</div>

					<div class="input-field">
						<div class="form-left">
							<label>Card Expiry Year</label>
							<input  placeholder="Ex:2020" id="ccExpiryYear" name="ccExpiryYear" maxlength="4" minlength="4" type="text" value="" class="error stripenumberic" aria-invalid="true">
						</div>
						<div class="form-right">
							<label>Cvv Number</label>
							<input id="cvvNumber" maxlength="3" minlength="3"  placeholder="Enter Cvv Number"  name="cvvNumber" type="text" value="" class="error stripenumberic" aria-invalid="true">
							<input id="clientid" name="clientid" type="hidden">
							<input id="secretid" name="secretid" type="hidden">
                            <input id="merchantid" name="merchantid" type="hidden">
                            <input id="balance" name="balance" type="hidden">
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" onclick="Stripesubmit()">Submit</button>
				</div>
			</div>
		</div>
		</form>
	</div>

	<!-- END OF POP UP -->
</div>
@section('script')
<script>
	var table='';
	$(document).ready(function () {
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
						"url": "{{ url('admin-delboy-commission-lists') }}",
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
	// function payFun(mer_id,balance,currency)
    function payFun(mer_id,balance,currency,bank_details)
	{

		$('#modal_agent_id').val(mer_id);
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

	function valiadate_transid(){
	if($('#ModalTransId').val()==''){
		$('#transidError').show();
		return false;
	}else{
		$('#transidError').hide();
		return true;
	}
}
	</script>






@endsection
@stop