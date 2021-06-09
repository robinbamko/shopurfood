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
</style>
@php extract($privileges); @endphp
<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
	            <div class="container right-container">
					<div class="r-btn">
					</div>
					<div class="col-md-12">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
								
							</div>
							
							{{-- Display  message--}}
							<div class="err-alrt-msg">
								<div class="alert alert-success alert-dismissible" id="successMsgRole" style="@if (Session::has('message')) display:block @else display:none @endif">
									<a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
									<span id="successMsgInfo"></span>
									@if (Session::has('message'))  {{Session::get('message')}}    @endif
								</div>
								<div class="alert alert-danger alert-dismissible" id="errorMsgRole"  role="alert" style="@if (Session::has('errors')) display:block @else display:none @endif">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									<span id="errorMsgInfo"></span>
								</div>
								<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
									
								</div>
							</div>
							{{-- Manage list starts--}}
							<div class="panel-body" id="location_table">
								{{-- 
								<div class="panel-heading p__title">
									Manage List
								</div>
								--}}
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								<div class="top-button top-btn-full" style="position:relative">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
									
									@if((isset($Delivery_Manager) && is_array($Delivery_Manager)) && in_array('3', $Delivery_Manager) || $allPrev == '1')
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									@endif
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="name_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td><input type="text" id="email_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td>&nbsp;</td>
											
											<td>
												<select id="publish_search" class="form-control" style="width:100%">
													<option value="">@lang(Session::get('admin_lang_file').'.ADMIN_ALL')</option>
													<option value="1">@lang(Session::get('admin_lang_file').'.ADMIN_PUBLISH')</option>
													<option value="0">@lang(Session::get('admin_lang_file').'.ADMIN_UNPUB')</option>

												</select>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th class="sorting_no_need">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_NAME')}}
											</th>
											
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_MAIL')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
							{{-- Manage list ends--}}
						</div>
						@if(count($all_details) > 0)
						{!! $all_details->render() !!}
						@endif
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
	$(document).ready(function() {
		$('#d-table').DataTable({
			responsive: true
		});
	});
</script>

<script type="text/javascript">
	$('#block_value,#unBlock_value,#delete_value').click(function(){
		$(".rec-select").css({"display" : "none"});
        var val = [];
		val[0]='';
		$('input[name="chk[]"]:checked').each(function(i){
			var j=i+1;
			val[j] = $(this).val();
		}); 
		
		if(val=='')
		{
			$(".rec-select").css({"display" : "block"});
			return;
		}

		if($(event.target).attr('id')=='block_value'){
      		var status = '0';
		} 
		else if($(event.target).attr('id')=='unBlock_value'){
        	var status = '1';
		} 
    	else if($(event.target).attr('id')=='delete_value'){
       		var status = '2';
		}
		changeStatus(val,status);
		
	});
	function changeStatus(val,status)
	{
		$.ajax({
			type:'get',
			url :"<?php echo url("multi_manager_block"); ?>",
			beforeSend: function() {
				$("#loading-image").show();
			},
			data:{'val':val,'status':status},
			success:function(response){
				$("#loading-image").hide();
				//location.reload();
				gotResponse = response.split('~');
				if(gotResponse[0]==1)
				{
					$('#successMsgRole').show();
					$('#successMsgRole').focus();
					$('#successMsgInfo').html(gotResponse[1]).show();
				}
				else
				{
					$('#errorMsgRole').show();
					$('#errorMsgRole').focus();
					$('#errorMsgInfo').html(gotResponse[1]).show();
				}
				if(status==0)
				{
					for(var i=0;i<val.length;i++)
					{
						$('#statusLink_'+val[i]).attr("href", "javascript:individual_change_status('"+val[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i>');
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				}
				else if(status==1)
				{
					for(var i=0;i<val.length;i++)
					{
						$('#statusLink_'+val[i]).attr("href", "javascript:individual_change_status('"+val[i]+"',0)").html('<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i>');
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				}
				else
				{
					table.row( this ).remove().draw( false );
				}
				$('.checkboxclass').prop('checked', false);
			}
		}); 
	}
	function individual_change_status(gotVal,gotStatus)
	{
		var val = [];
		val[0]=gotVal;
		changeStatus(val,gotStatus)
	}
	function checkAll(ele) {
		var checkboxes = document.getElementsByTagName('input');
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
	var table='';
	$(document).ready(function () {
		
		/*$('#dataTables-example').dataTable({
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
						"url": "{{ url('ajax-delivery-manager') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ $('#check_all').prop('checked',false); },
						"data":{ _token: "{{csrf_token()}}",'name_search': function(){return $("#name_search").val(); },email_search:function(){return $("#email_search").val(); },publish_search:function(){return $("#publish_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,4,6],
						"orderable": false
					} ],
					"columns": [
								{ "data": "checkBox",sWidth: '8%' },
								{ "data": "SNo", sWidth: '8%' },
								{ "data": "name", sWidth: '40%' },
								{ "data": "email", sWidth: '20%' },
								{ "data": "Edit", sWidth: '8%' },
								{ "data": "Status", sWidth: '8%'},
								{ "data": "delete", sWidth: '8%'}
								],
					"order": [ 1, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		 $('#name_search, #email_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#publish_search').change(function(){
			table.draw();
		});
	});
</script>
@endsection
@stop