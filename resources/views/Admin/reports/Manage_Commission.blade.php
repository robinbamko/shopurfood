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
												<td><input type="text" id="merEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

												<td><input type="text" id="merName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

												<td><input type="text" id="storename_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

												<td style="text-align:center;font-size: 12px;"><span class="help-block">({{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_MER_AMT_COMMI')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_MER_AMT_COMMI') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TOTAL_MER_AMT_COMMI') }})</span></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
		                                	<tr>

			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') }}</th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_EMAIL') }}</th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME') }}</th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME') }}</th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_MER_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_MER_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TOTAL_MER_AMT') }}
			                                        ({{ $default_currency }}) 
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TOTAL_COMMISSION_AMOUNT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_WALLET_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_WALLET_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TOTAL_WALLET_AMT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_CANCEL_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_CANCEL_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TOTAL_CANCEL_AMT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_COMMISSION_AMOUNT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BALANCE_COMMISSION_AMOUNT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMENT_STATUS') }}</th>
			                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_TRANSACTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_TRANSACTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW_TRANSACTION') }}</th>
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

					<h4 class="modal-title">@lang(Session::get('admin_lang_file').'.ADMIN_PAY_TO_MER')</h4>

				</div>
				<div class="modal-body">
					{{ Form::open(['method' => 'post','url' => 'pay_to_merchant','onsubmit'=>'return valiadate_transid();'])}}
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
						{{ Form::hidden('mer_id','',['id'=>'modal_mer_id'])}}
						{{ Form::hidden('mer_balance','',['id'=>'modal_mer_balance'])}}
						{{ Form::hidden('mer_curr','',['id'=>'modal_mer_curr'])}}
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
					 // "scrollX": true,
					 // "scrollY": true,
					  
					 // "bScrollCollapse": true,
					 //  "show": true,
					"ajax":{
						"url": "{{ url('ajax-commission-lists') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}",'merEmail_search': function(){return $("#merEmail_search").val(); },merName_search:function(){return $("#merName_search").val(); },storename_search:function(){return $("#storename_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0],
						"orderable": false
					} ],
					"columns": [
								{ "data": "SNo", sWidth: '8%' },
								{ "data": "merEmail", sWidth: '10%' },
								{ "data": "merName", sWidth: '8%' },
								{ "data": "storeName", sWidth: '10%' },
								{ "data": "totMerAmt", sWidth: '8%' },
								{ "data": "adminComnAmt", sWidth: '8%'},
								{ "data": "walletAmt", sWidth: '8%'},
								{ "data": "cancelAmt", sWidth: '8%'},
								{ "data": "paidAmt", sWidth: '8%'},
								{ "data": "balAmt", sWidth: '8%'},
								{ "data": "pmtStatus", sWidth: '8%'},
								{ "data": "action", sWidth: '8%'}
								],
					"order": [ 0, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		 $('#merEmail_search, #merName_search, #storename_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
	});
	// function payFun(mer_id,balance,currency)
    function payFun(mer_id,balance,currency,bank_details)
	{

		$('#modal_mer_id').val(mer_id);
		$('#modal_mer_balance').val(balance);
		$('#modal_mer_curr').val(currency);
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


<script>

function fnstripe(clientid,secretid,merchantid) {


    $("#clientid").val(clientid);
    $("#secretid").val(secretid);
    $('#merchantid').val(merchantid);
}

</script>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
function Stripesubmit(){

    var errormsg="";
	 if($("#card_no").val()=="")
	  errormsg="Please Fill Cardno";
	if($("#ccExpiryMonth").val()=="")
	  errormsg="Please Expiry Month";
	if($("#ccExpiryYear").val()=="")
	  errormsg="Please Fill Expiry Year";
	if($("#cvvNumber").val()=="")
	  errormsg="Please Fill cvvnumber";


if(errormsg=="") {

	var $form = $('#stripe_form');
	sk=$("#clientid").val();
	Stripe.setPublishableKey(sk);
	Stripe.createToken({
		number: $("#card_no").val(),
		cvc: $("#cvvNumber").val(),
		exp_month: $("#ccExpiryMonth").val(),
		exp_year: $("#ccExpiryYear").val(),
	}, stripeResponseHandler);

	function stripeResponseHandler(status, response) {
		var $form = $('#stripe_form');
		if (response.error) {
          alert(response.error.message);
		} else {
		    var token = response.id;
		    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            $('#stripedetail').css({'display' : 'none'});

		}
        $.ajax({
            "url": "{{ url('stripesubmit') }}",
            "dataType": "json",
            "type": "POST",
            "data":{stripeToken:token,mechantid:$('#merchantid').val(),cardno:$("#card_no").val(),expirymonth:$("#ccExpiryMonth").val(),expiryyear:$("#ccExpiryYear").val(),
                ccvno:$("#cvvNumber").val(),amounttopay:$('#amt_to_pay').val()},
            success:function(response)
            {
                if(response==0) {
                    alert("No merchant found");
                }
                if(response==2) {
                    alert("Commission Paid Success");
                }
                if(response==3) {
                    alert("Commission Paid Failure");
                }
                window.location.href = "<?php echo url('/admin-commission-tracking');?>";
            }
        });
	}
}
	else {
		$('#stripedetail').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#Stripeerrors').css({'display' : 'block'});
        return false;
	}
}
$(".stripenumberic").keypress(function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
        return false;
    }
});
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