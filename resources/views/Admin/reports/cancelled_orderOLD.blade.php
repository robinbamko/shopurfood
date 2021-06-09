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
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
										{{ $errors->first('errors') }}

									</div>
								@endif
								@if ($errors->has('upload_file'))
									<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p>
								@endif
								@if (Session::has('message'))
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
										<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
									</div>
								@endif
								<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
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
											<td><input type="text" id="cusEmail_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="orderId_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td>&nbsp;</td>
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
											<th  style="text-align:center">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}} {{-- (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_NAME')--}}
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
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_DATE')}}
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
							<div class="form-group" >
								<label class="control-label col-sm-3" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYABLE_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYABLE_AMT')}}:</label>
								<div class="col-sm-9">
									{{ Form::text('ord_cancel_paidamt','',['class' => 'form-control','required','maxlength' => '50','readonly'=>'readonly','id'=>'ord_cancel_paidamt'])}}
								</div>
							</div>
							<div class="form-group ">
								<label class="control-label col-sm-3" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID')}}:</label>
								<div class="col-sm-9">
									{{ Form::text('ord_cancelpaid_transid','',['class' => 'form-control','required','maxlength' => '50','placeholder' => 'Enter Transaction Id','id'=>'ord_cancelpaid_transid'])}}
									<div id="errorMsg" style="color:red;display:none;">Please enter transaction ID</div>
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
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>

				</div>
			</div>
			<!-- END OF MODAL-->
			<div id="stripedetail" class="modal fade" role="dialog" style="display: none">
				<form name="" id="stripe_form">
					<input name="_token" type="hidden" value="{{ csrf_token() }}"/>
					<div class="modal-dialog">
						<label id="Stripeerrors" style="display: none"><small style="color:red">Please Fill Required Fields</small></label>
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Refund</h4>
							</div>
							<div class="modal-body">
								<div class="input-field">
									<div class="form-left">
										<label>Card Number*</label>
										<input  placeholder="Enter Card Number" minlength="16" maxlength="16" id="card_no"  name="card_no" type="text" value="" class="error  stripenumberic" aria-invalid="true">
									</div>
									<div class="form-right">
										<label>Card Expiry Month</label>
										<input id="ccExpiryMonth" placeholder="Ex:4" minlength="1" maxlength="1"  name="ccExpiryMonth" type="text" value="" class="error  stripenumberic" aria-invalid="true">
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

		</div>
		<!-- END MAIN CONTENT -->
	</div>

@section('script')
	<script type="text/javascript">
        var table='';
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
        function call_pay_fun(ord_id,amount)
        {
            $('#ord_cancel_paidamt').val(amount);
            $('#ord_id').val(ord_id);
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
            table = $('#dataTables-example').DataTable({
                "processing": true,
                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,
                "ajax":{
                    "url": "{{ url('cancelled_orders_ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",'cusEmail_search': function(){return $("#cusEmail_search").val(); },orderId_search:function(){return $("#orderId_search").val(); },ordStatus_search:function(){return $("#ordStatus_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": 0,
                    "orderable": false
                } ],
                "columns": [
                    { "data": "SNo",sWidth: '5%' },
                    { "data": "custEmail", sWidth: '20%' },
                    { "data": "orderId", sWidth: '15%' },
                    { "data": "cancelAmt", sWidth: '10%' },
                    { "data": "commission", sWidth: '10%' },
                    { "data": "payableAmt", sWidth: '10%' },
                    { "data": "paidAmt", sWidth: '15%'},
                    { "data": "balanceAmt", sWidth: '15%'}
					// { "data": "ord_cancel_date", sWidth: '15%'}
                ],
                "order": [ 0, 'desc' ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });
            $('#cusEmail_search,#orderId_search').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });
            $('#ordStatus_search').change(function(){
                table.draw();
            });

        });
	</script>
	<script>
        function fnstripe(clientid,secretid,customerid)
        {
            $("#clientid").val(clientid);
            $("#secretid").val(secretid);
            $('#merchantid').val(customerid);
        }
	</script>

	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script>
        function Stripesubmit()
        {
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
                    }
                    $.ajax({
                        "url": "{{ url('stripe-cancel-paymentsubmit') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data":{stripeToken:token,customerid:$('#merchantid').val(),cardno:$("#card_no").val(),expirymonth:$("#ccExpiryMonth").val(),expiryyear:$("#ccExpiryYear").val(),
                            ccvno:$("#cvvNumber").val(),orderid:$('#order_id').val(),amttopay:$('#amt_to_pay').val()},
                        success:function(response)
                        {
                          window.location.href = "<?php echo url('/manage-cancelled-order');?>";
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
	</script>
@endsection
@stop