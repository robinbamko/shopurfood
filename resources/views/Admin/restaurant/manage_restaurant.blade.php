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
									@if(Session::has('message')){{Session::get('message')}} @endif
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
								<div class="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								<div class="top-button top-btn-full" style="position:relative;">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
									
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="storeName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="merName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="cat_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td>
												@php
													$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['admin']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
													$addedByArray['merchant']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
												@endphp
												{{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}
											</td>
											<td>&nbsp;</td>
											<td>
												@php
													$statusArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$statusArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH');
													$statusArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUB')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUB') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUB');
												@endphp
												{{ Form::select('status_search',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'status_search'] ) }}
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th class="sorting_no_need">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME')}}
											</th>
											
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDED_BY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
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
									
									</table>
									<div class="loading-image" style="display:none">
										<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
									</div>
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

 <script type="text/javascript">
	var table='';
   $('#block_value,#unBlock_value,#delete_value').click(function(event){
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
         //\\\alert(val); return false;
          if($(event.target).attr('id')=='block_value'){
      		var status = '0';
   		 } 
   		 else if($(event.target).attr('id')=='unBlock_value'){
        	var status = '1';
    	} 
    	else if($(event.target).attr('id')=='delete_value'){
       		var status = '2';
    	}

        change_status(val,status);
      });

	function change_status(gotVal,gotStatus)
	{
		$.ajax({
			type:'get',
			url :"<?php echo url("multi_restaurant_status"); ?>",
				beforeSend: function() {
			$(".loading-image").show();
			},
			data:{'val':gotVal,'status':gotStatus},
			success:function(response){
				$(".loading-image").hide();
				$('#successMsgRole').show();
				$('#successMsgRole').focus();
				$('#successMsgInfo').html(response).show();

				if(gotStatus==0)
				{
					for(var i=0;i<gotVal.length;i++)
					{
						//$('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i>');
						//$('.tooltip-demo').tooltip({placement:'top'})
						table.draw(false);
					}
				}
				else if(gotStatus==1)
				{
					for(var i=0;i<gotVal.length;i++)
					{
						//$('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',0)").html('<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i>');
						//$('.tooltip-demo').tooltip({placement:'top'})
						table.draw(false);
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
		change_status(val,gotStatus);
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

	
	$(document).ready(function () {
		$('[data-toggle="popover"]').popover({placement:'top'});   
		table = $('#dataTables-example').DataTable({
			"processing": true,
			"responsive": true,
			"serverSide": true,
			"bLengthChange": true,
			"bAutoWidth": false, 
			"searching": false,			
			"ajax":{
				"url": "{{ url('ajax-restaurant-list') }}",
				"dataType": "json",
				"type": "POST",
				"beforeSend":function(){ $('#check_all').prop('checked',false); },
				"data":{ _token: "{{csrf_token()}}",'storeName_search': function(){return $("#storeName_search").val(); },merName_search:function(){return $("#merName_search").val(); },cat_search:function(){return $("#cat_search").val(); },addedBy_search:function(){return $("#addedBy_search").val(); },status_search:function(){return $("#status_search").val(); }}
			},
			"columnDefs": [ {
				"targets": [0,1,6,8],
				"orderable": false
			} ],
			"columns": [ 
			{ "data": "checkBox", sWidth: '7%' },
			{ "data": "SNo", sWidth: '7%' },
			{ "data": "storeName", sWidth: '22%' },
			{ "data": "merName", sWidth: '21%' },
			{ "data": "category", sWidth: '12%' },
			{ "data": "addedBy", sWidth: '10%' },
			{ "data": "Edit", sWidth: '7%' },
			{ "data": "Status", sWidth: '7%'},
			{ "data": "delete", sWidth: '7%'}
			],
			"order": [ 1, 'desc' ],
			"fnDrawCallback": function (oSettings) {
				$('.tooltip-demo').tooltip({placement:'top'});	
				//$('[data-toggle="tooltip"]').tooltip();
				$('[data-toggle="popover"]').popover({placement:'top'});  
			}
		});
		$('#storeName_search, #merName_search, #cat_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#addedBy_search, #status_search').change(function(){
			table.draw();
		});
	});
 </script>
@endsection
@stop