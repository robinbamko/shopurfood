@extends('sitemerchant.layouts.default')
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

		table.dataTable
		{
			margin-top: 20px !important;
		}
		#location_table .dataTables_length label{
			margin: 0px 0 0;
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
											<td>
												{{--@php $ordStatusArray = array(''=>'Select All','Paid'=>'Paid','Unpaid'=>'Payable');@endphp
                                                {{ Form::select('ord_status',$ordStatusArray,$ord_status,['class' => 'form-control' , 'style' => 'width:100%','id'=>'ordStatus_search'] ) }}--}}
											</td>
										</tr>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_ID') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_CANCELLATION_AMOUNT')) ? trans(Session::get('mer_lang_file').'.MER_CANCELLATION_AMOUNT') : trans($MER_OUR_LANGUAGE.'.MER_CANCELLATION_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_COMMISSION')) ? trans(Session::get('mer_lang_file').'.MER_COMMISSION') : trans($MER_OUR_LANGUAGE.'.MER_COMMISSION')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_PAYABLE_AMT')) ? trans(Session::get('mer_lang_file').'.MER_PAYABLE_AMT') : trans($MER_OUR_LANGUAGE.'.MER_PAYABLE_AMT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_PAID_AMOUNT')) ? trans(Session::get('mer_lang_file').'.MER_PAID_AMOUNT') : trans($MER_OUR_LANGUAGE.'.MER_PAID_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_BALANCE_AMOUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_BALANCE_AMOUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_BALANCE_AMOUNT')}}
											</th>

										</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
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

@section('script')

	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
	<script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>
	<script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
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
                    "url": "{{ url('mer_cancelled_orders_ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",'cusEmail_search': function(){return $("#cusEmail_search").val(); },orderId_search:function(){return $("#orderId_search").val(); },ordStatus_search:function(){return $("#ordStatus_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": 0,
                    "orderable": false
                } ],
                "columns": [
                    { "data": "SNo",sWidth: '8%' },
                    { "data": "custEmail", sWidth: '17%' },
                    { "data": "orderId", sWidth: '15%' },
                    { "data": "cancelAmt", sWidth: '10%' },
                    { "data": "commission", sWidth: '10%' },
                    { "data": "payableAmt", sWidth: '10%' },
                    { "data": "paidAmt", sWidth: '15%'},
                    { "data": "balanceAmt", sWidth: '15%'},
                ],
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

        });



	</script>

@endsection
@stop