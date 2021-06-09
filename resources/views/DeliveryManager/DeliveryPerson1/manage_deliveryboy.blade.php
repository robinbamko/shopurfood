@extends('DeliveryManager.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')
<style type="text/css">
	#location_table .dataTables_length label {
    margin: 20px 0 10px;
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

						
							@if(Session::has('message'))
							<div class="alert alert-success alert-dismissible">
								<a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
								<ul>
									{{ Session::get('message')}}
								</ul>
							</div>
							@endif
							<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT_ONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT_ONE')}}
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
									
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
									{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELETE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELETE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									<a href="{{url('export_deliveryboy1/csv')}}">
										{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DWNLD_EXCEL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DWNLD_EXCEL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
									</a>
									
									
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="delboyName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
											<td><input type="text" id="delboyEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
											<td><input type="text" id="delboyPhone_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
											<td>&nbsp;</td>
											<td>
												<select id="publish_search" class="form-control" style="width:100%">
													<option value="">@lang(Session::get('DelMgr_lang_file').'.DEL_ALL')</option>
													<option value="1">@lang(Session::get('DelMgr_lang_file').'.DEL_PUBLISH')</option>
													<option value="0">@lang(Session::get('DelMgr_lang_file').'.DEL_UNPUBLISH')</option>
												</select>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NAME')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_EMAIL')}}
											</th>
											<th>
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_PHONE')}}
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
		table = $('#dataTables-example').DataTable({
					"processing": true,
					"responsive": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false,
					"ajax":{
						"url": "{{ url('deliveryboy_lists_ajax1') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}",'delboyName_search': function(){return $("#delboyName_search").val(); },delboyEmail_search:function(){return $("#delboyEmail_search").val(); },delboyPhone_search:function(){return $("#delboyPhone_search").val(); },publish_search:function(){return $("#publish_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,1,5,7],
						"orderable": false
					} ],
					"order": [ 0, 'desc' ],
					"columns": [
								{ "data": "checkBox",sWidth: '5%' },
								{ "data": "SNo", sWidth: '9%' },
								{ "data": "delboyName", sWidth: '23%' },
								{ "data": "delboyEmail", sWidth: '22%' },
								{ "data": "delboyPhone", sWidth: '17%' },
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
		/*$('#dataTables-example').DataTable({
			"bPaginate": false,
			"scrollX": true,
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		    "bAutoWidth": false
		});*/
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
			url :"<?php echo url("multi_deliveryboy_block1"); ?>",
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