@extends('Admin.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')
<style>
	
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
							<div class="err-alrt-msg">
								@if ($errors->has('errors'))
								<div class="alert alert-danger alert-dismissible" role="alert" >
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{ $errors->first('errors') }}

								</div>
								@endif

								@if ($errors->has('upload_file'))
									<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p>
								@endif
								<div id="successMsgRole"></div>
									<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
								</div>

							</div>


							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" style="margin-top: 10px;">
								<div class="loading-image" style="display:none;">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								<div class="top-button top-btn-full" style="position:relative;">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_APPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_APPROVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_APPROVE'),['id' => 'approve_status','class' => 'btn btn-success'])!!}

									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISAPPROVE'),['id' => 'disapprove_status','class' => 'btn btn-danger'])!!}
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											
											<td><input type="text" id="storeName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="merchantName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												@php
													$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_APPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_APPROVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_APPROVE');
													$addedByArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISAPPROVE');
												@endphp
												{{ Form::select('view_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'view_search'] ) }}
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr style="z-index: 0;">
											<th  style="text-align:center"  class="checkboxclass sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center" class="sorting_no_need">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME')}}
											</th>
											<th style="text-align:center" >
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.MER_FROM_DATE')) ? trans(Session::get('admin_lang_file').'.MER_FROM_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_FROM_DATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.MER_TO_DATE')) ? trans(Session::get('admin_lang_file').'.MER_TO_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_TO_DATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_AMOUNT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_APPROVED_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_APPROVED_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_APPROVED_STATUS')}}
											</th>

											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW_DETAILS')}}
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
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>

@section('script')

<script>
	var table='';
	$(document).ready(function () {

		table = $('#dataTables-example').DataTable({
					"processing": true,
					"responsive": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false,
					"searching": false,
					"ajax":{
						"url": "{{ url('ajax-featuredStore-list') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ },
						"data":{ _token: "{{csrf_token()}}",'view_search': function(){return $("#view_search").val(); }, 'merchantName_search': function(){return $("#merchantName_search").val(); },storeName_search:function(){return $("#storeName_search").val(); }}
					},
					"columnDefs": [ 
						// "targets": [0,1,8],
						// "orderable": true
						{ responsivePriority: 1, targets: 1 },
        				{ responsivePriority: 2, targets: 2 }
					 ],
					"order": [ 0, 'desc' ],
					"columns": [{ "data": "checkBox", sWidth: '8%' },
								{ "data": "SNo", sWidth: '8%'/*, className:function(data,type,row) { console.log(row); return 'info';}*/ },
								{ "data": "storeName", sWidth: '10%' },
								{ "data": "merName", sWidth: '19%' },
								{ "data": "fromDate", sWidth: '10%' },
								{ "data": "toDate", sWidth: '10%' },
								{ "data": "paidAmount", sWidth: '10%' },
								{ "data": "approvestatus", sWidth: '15%' },
								{ "data": "view", sWidth: '10%' }
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'top'});
					},
					createdRow: function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data.readstatus=='Unread'){
							$( row ).addClass('danger');
						}
					}
				});
		$('#merchantName_search, #storeName_search').keyup( function() {
			table.draw();
		});
		$('#view_search').change( function() {
			table.draw();
		});
	 });
	 $('#approve_status,#disapprove_status').click(function(event){
		$(".rec-select").css({"display" : "none"});
        var val = [];
		$('input[name="chk[]"]:checked').each(function(i){
			val[i] = $(this).val();
		});  console.log(val);


		if(val=='')
		{

			$(".rec-select").css({"display" : "block"});

			return;
		}
		//alert(val); return false;
		if($(event.target).attr('id')=='approve_status'){
      		var status = '1';
		}
		else if($(event.target).attr('id')=='disapprove_status'){
        	var status = '0';
		}

		bulk_change_status(val,status);

	});
	function bulk_change_status(gotVal,gotStatus)
	{
		$.ajax({
			type:'get',
			url :"<?php echo url("featStore_approve_status"); ?>",
				beforeSend: function() {
				$(".loading-image").show();
				$('#successMsgRole').html('').hide();
			},
			data:{'val':gotVal,'status':gotStatus},
			success:function(response){
			    // alert(response);
			    // return false;
				$(".loading-image").hide();
				$('#successMsgRole').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">'+response+'</strong></div>').show();
				table.row( this ).remove().draw( false );
				$('.checkboxclass').prop('checked', false);
			}
		});
	}
	function checkAll(ele) {
		var checkboxes = document.getElementsByName('chk[]');
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
	 function individual_change_status(gotId,gotVal){
		var val = [];
		val[0]=gotId;
		bulk_change_status(val,gotVal);
	 }
    </script>

@endsection
@stop