@extends('DeliveryManager.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')

<style>
	#location_table .dataTables_length label{margin: 20px 0 10px;}
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
							
							@if(Session::has('message'))
							<div class="alert alert-success alert-dismissible">
								<a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i>
</a>
								<ul>
									{{ Session::get('message')}}
								</ul>
							</div>
							@endif
						<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT_ONE')}}
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
									
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELETE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELETE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									<a href="{{url('export_agent/csv')}}">
										{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DWNLD_EXCEL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DWNLD_EXCEL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
									</a>
									
									
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="agentName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="agentEmail_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="agentPhone_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td>&nbsp;</td>
											<td>
												<select id="publish_search" class="form-control" style="width:100%">
													<option value="">All</option>
													<option value="1">Published</option>
													<option value="0">Unpublished</option>
												</select>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_NAME')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_EMAIL')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_PHONE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EDIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EDIT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EDIT')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_STATUS')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELETE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELETE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELETE')}}
											</th>
										</tr>
									</thead>
									<tbody>
	
									</tbody>
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
			"scrollX": true,
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
						"url": "{{ url('agent_lists_ajax') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}",'agentName_search': function(){return $("#agentName_search").val(); },agentEmail_search:function(){return $("#agentEmail_search").val(); },agentPhone_search:function(){return $("#agentPhone_search").val(); },publish_search:function(){return $("#publish_search").val(); }}
					},
					"columnDefs": [ {
						"targets": 0,
						"orderable": false
					} ],
					"order": [ 0, 'desc' ],
					"columns": [
								{ "data": "checkBox",sWidth: '5%' },
								{ "data": "SNo", sWidth: '9%' },
								{ "data": "delboyName", sWidth: '18%' },
								{ "data": "delboyEmail", sWidth: '17%' },
								{ "data": "delboyPhone", sWidth: '12%' },
								{ "data": "Edit", sWidth: '9%' },
								{ "data": "Status", sWidth: '15%'},
								{ "data": "delete", sWidth: '15%'}
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		 $('#agentName_search, #agentEmail_search, #agentPhone_search').keyup( function() {
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
		change_status(0);
	});
	/** multiple unblock **/
	$('#unBlock_value').click(function(){
		change_status(1);
	});
	/** multiple delete **/
	$('#delete_value').click(function(){
		change_status(2);
	});
	function change_status(got_status)
	{
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
		
        $.ajax({
			
			type:'get',
			url :"<?php echo url("multi_agent_block"); ?>",
			beforeSend: function() {
				$("#loading-image").show();
			},
			data:{'val':val,'status':got_status},
			success:function(response){
				location.reload();
			}
		}); 
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