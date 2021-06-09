@extends('sitemerchant.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')

	<style>
		table.dataTable
		{
			margin-top: 0px !important;
		}
		.toolbar {
			float:right;
		}
		#location_table .dataTables_length label {
			margin: 0px 0 20px;
		}
		div#dataTables-example_length {
			float: left;
		}

		@media only screen and (max-width:767px)
		{
			div#dataTables-example_length{text-align:center; width:100%; }
			#dataTables-example_wrapper .toolbar{text-align:center; width:100%; }
			table.dataTable
			{
				margin-top: 0px !important;
			}
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


									<div>

										<div class="cal-search-filter" style="display:none">
											{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('manage-orders'),'id'=>'validate_form']) !!}
											<div class="row">
												<br>
												<div class="col-sm-4 col-md-4">
													<div class="item form-group">
														<div class="col-sm-6 date-top"> From Date  </div>
														<div class="col-sm-6 place-size">
															<span class="icon-calendar cale-icon"></span>
															<input id="startDatePicker" class="form-control" placeholder="MM/DD/YYYY" required="required" readonly="readonly" name="from_date" type="text" value="">
														</div>
													</div>
												</div>
												<div class="col-sm-4 col-md-4">
													<div class="item form-group">
														<div class="col-sm-6 date-top"> To Date  </div>
														<div class="col-sm-6 place-size">
															<span class="icon-calendar cale-icon"></span>
															<input id="endDatePicker" class="form-control hasDatepicker" placeholder="MM/DD/YYYY" required="required" readonly="readonly" name="to_date" type="text" value="">
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-sm-2">
														<input type="submit" name="submit" class="btn btn-block btn-success" value=" Search  ">
													</div>
													<div class="col-sm-2">
														<a href="{{url('manage-orders')}}"><button type="button" name="reset" class="btn btn-block btn-info"> Reset </button></a>
													</div>
												</div>

											</div>{!! Form::close() !!}</div>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>
											<td><input type="text" id="pdtCode_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="pdtName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><!--<input type="text" id="storename_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/>--><input type="hidden" name="storename_search" id="storename_search" /></td>
											<td><input type="text" id="merName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PDT_CODE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PDT_CODE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PDT_CODE')}}
											</th>


											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ITEMRPDT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEMRPDT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_QTY')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SOLD_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_SOLD_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_SOLD_QTY')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACTIONS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ACTIONS')}}
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


			<!-- Modal -->
			<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h5 class="modal-title" id="exampleModalLabel">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_QTY')}}</h5>

						</div>
						<div class="modal-body" id="myBody" style="padding:15px 0;">
							<input type='hidden' id="selectedPrdt_id" value="" />
							<div class="form-group">
								<label class="control-label col-sm-3" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_QTY')}} :</label>
								<div class="col-sm-6">
									{!! Form::text('updatable_qty','',array('placeholder'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_QTY'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off','id'=>'updatable_qty','onkeypress'=>"return onlyNumbers(event);",'maxlength' => '10')) !!}
								</div>
							</div>
							<br />
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" onclick="addQuantity();">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SAVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SAVE')}}</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE')}}</button>
						</div>
					</div>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- END MAIN CONTENT -->
	</div>

@section('script')
	{{--
    <link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
    <script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>--}}
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
                "scrollX": true,
                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,
                "dom": 'l<"toolbar">frtip',
                "ajax":{
                    "url": "{{ url('mer_inventory_list_ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",'pdtCode_search': function(){return $("#pdtCode_search").val(); },pdtName_search:function(){return $("#pdtName_search").val(); },storename_search:function(){return $("#storename_search").val(); },merName_search:function(){return $("#merName_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,-1],
                    "orderable": false
                } ],
                "columns": [
                    { "data": "SNo", sWidth: '5%' },
                    { "data": "pdtCode", sWidth: '15%' },
                    { "data": "pdtName", sWidth: '25%' },
                    { "data": "storeName", sWidth: '10%' },
                    { "data": "merName", sWidth: '15%' },
                    { "data": "qty", sWidth: '10%'},
                    { "data": "soldQty", sWidth: '10%'},
                    { "data": "action", sWidth: '10%'},
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });
            $("div.toolbar").html('<a href="{{url('mer_download_inventory_list/csv')}}">{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($MER_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}</a>');
            $('#pdtCode_search, #pdtName_search, #storename_search, #merName_search').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });
            /*
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
            */
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

        function show_pop(id)
        {
            $('#selectedPrdt_id').val(id);
            $('#exampleModal').modal('show');
        }
        function addQuantity()
        {
            if($('#updatable_qty').val()=='' || $('#updatable_qty').val() < 1 )
            {
                alert('Please enter quantity');
                return false;
            }
            $.ajax({
                type: 'get',
                url: "{{url('mer-update-inventory')}}",
                data: {updatable_qty : $('#updatable_qty').val(),selectedPrdt_id:$('#selectedPrdt_id').val()},
                success: function(response){
                    window.location.reload();
                }
            });
        }
        function onlyNumbers(evt) {
            var e = event || evt; // for trans-browser compatibility
            var charCode = e.which || e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
	</script>

@endsection
@stop