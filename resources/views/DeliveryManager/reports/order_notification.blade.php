@extends('DeliveryManager.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')

<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid">
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
							</div>
							
				
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" style="margin-top: 10px;">
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>
												@php
													$addedByArray[''] = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT');
													$addedByArray['0']=(Lang::has(Session::get('DelMgr_lang_file').'.DEL_UNREAD_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_UNREAD_NOTIFICATION') : trans($DELMGR_OUR_LANGUAGE.'.DEL_UNREAD_NOTIFICATION');
													$addedByArray['1']=(Lang::has(Session::get('DelMgr_lang_file').'.DEL_READ_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_READ_NOTIFICATION') : trans($DELMGR_OUR_LANGUAGE.'.DEL_READ_NOTIFICATION');
												@endphp
												{{ Form::select('view_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'view_search'] ) }}
											</td>
											<td><input type="text" id="orderId_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
											<td><input type="text" id="message_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr style="z-index: 0;">
											<th style="text-align:center" class="sorting_no_need">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_NOTIFICATION') : trans($DELMGR_OUR_LANGUAGE.'.DEL_NOTIFICATION')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_READ_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DEL_READ_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DEL_READ_STATUS')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DATE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW')}}
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
						"url": "{{ url('dmgr-ajax-notification-list') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ },
						"data":{ _token: "{{csrf_token()}}",'view_search': function(){return $("#view_search").val(); }, 'orderId_search': function(){return $("#orderId_search").val(); },message_search:function(){return $("#message_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,5],
						"orderable": false
					} ],
					"order": [ 0, 'asc' ],
					"columns": [
								{ "data": "SNo", sWidth: '10%'/*, className:function(data,type,row) { console.log(row); return 'info';}*/ },
								{ "data": "orderId", sWidth: '12%' },
								{ "data": "message", sWidth: '48%' },
								{ "data": "readstatus", sWidth: '12%' },
								{ "data": "date", sWidth: '8%' },
								{ "data": "view", sWidth: '8%' }
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
		$('#orderId_search, #message_search').keyup( function() {
			table.draw();
		});
		$('#view_search').change( function() {
			table.draw();
		});
	 });
	 function change_status(gotId){
		//alert(gotId);
		$.ajax({
			type:'get',
			url :"<?php echo url("dmgr_notification_change_status"); ?>",
			data:{'gotId':gotId},
			success:function(response){
				table.draw();
			}
		}); 
	 }
    </script>	

@endsection
@stop