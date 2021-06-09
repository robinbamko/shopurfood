@extends('Admin.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')

<style>
	table.dataTable
	{
		margin-top: 20px !important;
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
					<div class="r-btn">
					</div>
					<div class="col-md-12">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
								
							</div>
							
							
						{{-- Display error message--}}
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
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									<a href="{{url('export_deliveryboy_admin1/csv')}}">
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
									</a>
									
									
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="delboyName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="delboyEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="delboyPhone_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
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
											<th style="text-align:center" class="sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_NAME')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_EMAIL')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_PHONE')}}
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
	$(document).ready(function() {
		/*$('#dataTables-example').DataTable({
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
						"url": "{{ url('deliveryboy_list_ajax1') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ $('#check_all').prop('checked',false); },
						"data":{ _token: "{{csrf_token()}}",'delboyName_search': function(){return $("#delboyName_search").val(); },delboyEmail_search:function(){return $("#delboyEmail_search").val(); },delboyPhone_search:function(){return $("#delboyPhone_search").val(); },publish_search:function(){return $("#publish_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,1,5,6,7],
						"orderable": false
					} ],
					"order": [ 0, 'desc' ],
					"columns": [
								{ "data": "checkBox",sWidth: '8%' },
								{ "data": "SNo", sWidth: '8%' },
								{ "data": "delboyName", sWidth: '22%' },
								{ "data": "delboyEmail", sWidth: '22%' },
								{ "data": "delboyPhone", sWidth: '16%' },
								{ "data": "Edit", sWidth: '8%' },
								{ "data": "Status", sWidth: '8%'},
								{ "data": "delete", sWidth: '8%'}
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
								
				});
		 $('#delboyName_search, #delboyEmail_search, #delboyPhone_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#publish_search').change(function(){
			table.draw();
		});
	});
</script>

<script type="text/javascript">
	$('#block_value').click(function(){
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
		change_status(val,0);
	});
	/** multiple unblock **/
	$('#unBlock_value').click(function(){
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
		change_status(val,1);
	});
	/** multiple delete **/
	$('#delete_value').click(function(){
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
		change_status(val,2);
	});
	function change_status(val,got_status)
	{
		
        $.ajax({
			
			type:'get',
			url :"<?php echo url("multi_deliveryboy_block_admin1"); ?>",
			beforeSend: function() {
				$("#loading-image").show();
			},
			data:{'val':val,'status':got_status},
			success:function(response){
				//location.reload();
				$("#loading-image").hide();
				$('#successMsgRole').show();
				$('#successMsgRole').focus();
				$('#successMsgInfo').html(response).show();

				if(got_status==0)
				{
					for(var i=0;i<val.length;i++)
					{
						$('#statusLink_'+val[i]).attr("href", "javascript:individual_change_status('"+val[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i>');
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				}
				else if(got_status==1)
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
		change_status(val,gotStatus)
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
</script>
		@endsection
	@stop		