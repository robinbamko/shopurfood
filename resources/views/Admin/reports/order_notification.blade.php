@extends('Admin.layouts.default')
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
									<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
								</div>
							
							</div>
							
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" style="margin-top: 10px;">
								<div class="loading-image" style="display:none;">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								<div class="top-button top-btn-full" style="position:relative;">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_MARKAS_READ')) ? trans(Session::get('admin_lang_file').'.ADMIN_MARKAS_READ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MARKAS_READ'),['id' => 'read_status','class' => 'btn btn-success'])!!}
									
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_MARKAS_UNREAD')) ? trans(Session::get('admin_lang_file').'.ADMIN_MARKAS_UNREAD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MARKAS_UNREAD'),['id' => 'unread_status','class' => 'btn btn-danger'])!!}
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>
												@php
													$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNREAD_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNREAD_NOTIFICATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNREAD_NOTIFICATION');
													$addedByArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_READ_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_READ_NOTIFICATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_READ_NOTIFICATION');
												@endphp
												{{ Form::select('view_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'view_search'] ) }}
											</td>
											<td><input type="text" id="orderId_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="message_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr style="z-index: 0;">
											<th  style="text-align:center"  class="checkboxclass sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center" class="sorting_no_need">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOTIFICATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOTIFICATION')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_READ_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_READ_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_READ_STATUS')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DATE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW')}}
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
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false,
					"ajax":{
						"url": "{{ url('ajax-notification-list') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ },
						"data":{ _token: "{{csrf_token()}}",'view_search': function(){return $("#view_search").val(); }, 'orderId_search': function(){return $("#orderId_search").val(); },message_search:function(){return $("#message_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,1,5],
						"orderable": false
					} ],
					"order": [ 1, 'asc' ],
					"columns": [{ "data": "checkBox", sWidth: '7%' },
								{ "data": "SNo", sWidth: '10%'/*, className:function(data,type,row) { console.log(row); return 'info';}*/ },
								{ "data": "orderId", sWidth: '12%' },
								{ "data": "message", sWidth: '41%' },
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
	 $('#read_status,#unread_status').click(function(event){
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
		if($(event.target).attr('id')=='read_status'){
      		var status = '1';
		} 
		else if($(event.target).attr('id')=='unread_status'){
        	var status = '0';
		} 

		bulk_change_status(val,status);
       
	});
	function bulk_change_status(gotVal,gotStatus)
	{
		$.ajax({
			type:'get',
			url :"<?php echo url("notification_change_status"); ?>",
				beforeSend: function() {
				$(".loading-image").show();
				$('#successMsgRole').html('').hide();
			},
			data:{'val':gotVal,'status':gotStatus},
			success:function(response){
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
	 function change_status(gotId){
		var val = [];
		val[0]=gotId;
		bulk_change_status(val,1);
	 }
    </script>	

@endsection
@stop