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
					<div class="col-md-12">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
								@php extract($privileges); @endphp
								
								@if((isset($Category) && is_array($Category)) && in_array('1', $Category) || $allPrev == '1')
								<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form"> {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_ITEM_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_ITEM_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_ITEM_CATE')}}</a>
								@endif
							</div>
							
							{{-- Display error message--}}
							@if ($errors->any())
								<div class="alert alert-warning alert-dismissible">
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
							
							<div class="err-alrt-msg">
								<?php /*
								@if ($errors->has('errors')) 
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>								
									{{ $errors->first('errors') }}
									
								</div>
								@endif */ ?>
								@if ($errors->has('upload_file')) 
									<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p> 
								@endif
								@if (Session::has('message')) 
								<div class="alert alert-success alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{ Session::get('message') }}
								</div>
								@endif
								<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
								</div>
							<div id="successMsgRole"></div>
							</div>
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced">
								<div id="location_form" class="collapse @php  if(isset($id)){ echo 'in';} @endphp panel-body">
									@if(isset($id) && empty($cate_detail) === false)
									@php $cate_id =  $id; @endphp
									@endif
									<div class="row-fluid well">
										@if(isset($id) && empty($cate_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'update-item-category','id'=>'validate_form']) !!}
										{!! Form::hidden('category_id',$cate_id)!!}
										@else
										{!! Form::open(array('method' => 'post','class' => 'form-auth-small','url' => 'add-item-category','enctype'=>'multipart/form-data','id'=>'validate_form')) !!}
										@endif
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
											</div>
											<div class="col-md-8">
												@php $cate_name = ''; @endphp
												@if(isset($id) && empty($cate_detail) === false)
												@php
												$cate_name = $cate_detail->pro_mc_name;
												@endphp
												@endif
												{!! Form::text('cate_name',$cate_name,array('class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME'),'id' => 'category_name','autocomplete' => 'off','maxlength' => '20')) !!}
												@if($errors->has('cate_name'))
												<p style="color:red">{{$errors->first('cate_name')}}</p>
												@endif
											</div>
										</div>


										@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}&nbsp; (In {{$lang->lang_name}})*
												</span>
											</div>
											<div class="col-md-8">
												@php $cate_name = ''; @endphp
												@if(isset($id) && empty($cate_detail) === false)
												@php $lang_code = 'pro_mc_name_'.$lang->lang_code;
												$cate_name = $cate_detail->$lang_code;
												@endphp
												@endif
												{!! Form::text('cate_name_'.$lang->lang_code,$cate_name,array('class'=>'form-control','required','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME'),'id' => 'category_name_lang','autocomplete' => 'off','maxlength' => '20')) !!}

											</div>
										</div>
										@endforeach
										@endif
										
										
										
										
										<div class="panel-heading col-md-offset-3">
											
											@if(isset($id))
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											@if(isset($id))
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-item-category'); ?>'">
											@else
												{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'data-toggle'=>"collapse", 'data-target'=>"#location_form"])!!}
											@endif
										</div>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
							{{-- Add/Edit page ends--}}
							{{-- Import modal --}}
							<div class="modal fade" id="importmodal" role="dialog">
							    <div class="modal-dialog">
							    
							      <!-- Modal content-->
							      <div class="modal-content">
							        <div class="modal-header">
							          <button type="button" class="close" data-dismiss="modal">&times;</button>
							          <h4 class="modal-title">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_IMPORT_FILE')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMPORT_FILE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMPORT_FILE')}}</h4>
							        </div>
							        <div class="modal-body">
							          {!! Form::open(['method' => 'post','url' => 'import_item_category','enctype' =>'multipart/form-data','onsubmit'=>'return importValidate();'])!!}
							           <div class="row">
							          <div class="col-md-6 col-xs-12">
										{!! Form::file('upload_file',array('accept'=>'.csv','id'=>'importCsvId'))!!}
									</div>

									<div class="col-md-6 col-xs-12 text-right">
									{!! Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBMIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBMIT'),['class' => 'btn btn-success'])!!}</div></div>
									{!! Form::close()!!}
									<a href="{{url('download_item_category/csv/sample_file')}}">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_SAMPLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_SAMPLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_SAMPLE')}}</a>
									<br />
									<div id="importFileError" style="color:red;font-size: 15px;font-weight: bold;display:none">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_UPLOAD_FILE')}}</div>
									@if(Session::has('import_err'))
													<span style="color:red">{{ Session::get('import_err')}} </span>
											@endif
							        </div>
							        <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('admin_lang_file').'.ADMIN_CLOSE')</button>
									</div>
							      </div>
      						</div>
      						</div>
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
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
								
								@if((isset($Category) && is_array($Category)) && in_array('3', $Category) || $allPrev == '1')
								{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
								@endif
								
								@if((isset($Category) && is_array($Category)) && in_array('1', $Category) || $allPrev == '1')
								{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_IMPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMPORT'),['class' => 'btn btn-primary','data-toggle' => 'modal', 'data-target'=> '#importmodal'])!!}
								@endif 
								
									<a href="{{url('download_item_category/csv')}}">
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'download_value','class' => 'btn btn-info'])!!}
									</a></div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>

											<td><input type="text" id="catName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }} category"/></td>

											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												<select id="catStatus_search" class="form-control" style="width:100%">


													<option value="">@lang(Session::get('admin_lang_file').'.ADMIN_ALL')</option>
													<option value="1">@lang(Session::get('admin_lang_file').'.ADMIN_PUBLISH')</option>
													<option value="0">@lang(Session::get('admin_lang_file').'.ADMIN_UNPUB')</option>

												</select>
											</td>
											<td>&nbsp;</td>
											<td>
												<select id="addedBy_search" class="form-control" style="width:100%">

													<option value="">@lang(Session::get('admin_lang_file').'.ADMIN_ALL')</option>
													<option value="0">@lang(Session::get('admin_lang_file').'.ADMIN_ADMIN')</option>
													<option value="1">@lang(Session::get('admin_lang_file').'.ADMIN_MERCHANT')</option>

												</select>
											</td>
										</tr>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chknone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center" class="sorting_no_need">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDED_BY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
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


	<!--------model for block confirmation start -------------->
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
        <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">are you sure want to Block</h4>
            </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-btn-si">Yes</button>
        <button type="button" class="btn btn-primary" id="modal-btn-no">No</button>
        </div>
        </div>
    </div>
    </div>
<input type="hidden" name="cat_blkId" id="cat_blkId" value="">
<!--------model for block confirmation end -------------->

<!--------model for unblock confirmation start -------------->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modalDlete">
    <div class="modal-dialog modal-sm">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">are you sure want to UnBlock</h4>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-btn-yesDel">Yes</button>
        <button type="button" class="btn btn-primary" id="modal-btn-noDel">No</button>
    </div>
    </div>
    </div>
    </div>
    <input type="hidden" name="cat_unblockId" id="cat_unblockId" value="">
<!--------model for unblock confirmation end -------------->

</div>

@section('script')
<script>
	/*$('#category_name').bind('keyup blur',function(){ 
		var node = $(this);
		node.val(node.val().replace(/[^A-Z a-z 0-9 & - ,]/g,'') ); 
	});*/
function change() {
		var elem = document.getElementById("click-here");

            var add = "{{trans(Session::get('admin_lang_file').'.ADMIN_ADD_ITEM_CATE')}}";
    var manage = "{{trans(Session::get('admin_lang_file').'.ADMIN_MNGE_LIST')}}";
    //alert(add);
    if (elem.text== add)
		{ 


            elem.text = manage;
		}
    else if (elem.text==manage)
		{
            elem.text = add;
		}
		else {
        elem.text = manage;
    }
	}
	// $('#block_value').click(function(event){
	// 	bulk_function(0);
	// });
	// $('#unBlock_value').click(function(event){
	// 	bulk_function(1);
	// });
	// $('#delete_value').click(function(event){
	// 	bulk_function(2);
	// });
	// function bulk_function(gotVal)
	// {
 //         $(".rec-select").css({"display" : "none"});
 //       			 var val = [];
	// 			val[0]='';
	// 			$('input[name="chk[]"]:checked').each(function(i){
	// 				var j=i+1;
	// 				//alert(j);
	// 				val[j] = $(this).val();
	// 			}); console.log(val);


 //         if(val=='')
 //         {

 //         $(".rec-select").css({"display" : "block"});
     
 //         return;
 //         }
    
 //        $.ajax({

 //          type:'get',
 //          url :"<?php echo url("multi_pro_block"); ?>",
 //           beforeSend: function() {
 //              $("#loading-image").show();
 //           },
 //          data:{'val':val,'status':gotVal,'type' : 'item'},

 //          success:function(response){
 //          	//$("#loading-image").hide();
 //            location.reload();
 //          }
 //        }); 
	// }


$('#delete_value,#unBlock_value,#block_value').click(function(event){
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
		bulk_function(val,status);
	});

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
 /*-------block item category,subctegory and items start-----*/

 function change_all_statusblk(id){  
 	// alert(id);
            $('#cat_blkId').val(id);        
            $("#mi-modal").modal('show');       
       }

  var modalConfirm = function(callback){     
  $("#modal-btn-si").on("click", function(){
    callback(true);
    $("#mi-modal").modal('hide');
  });
  
  $("#modal-btn-no").on("click", function(){
    callback(false);
    $("#mi-modal").modal('hide');
  });
}; 

modalConfirm(function(confirm){
  if(confirm){ 
  	var main_cat_id = $('#cat_blkId').val(); 

    $.ajax({
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          url: "{{ url('ajax_change_itemCat_status') }}",
          type: 'POST',
          data: {'main_cat_id' : main_cat_id,'status':'0'},
          success:function(response) { 
            if(response == 1){    
            alert("Blocked Succesfully");
            window.location.reload();
            }else{
            alert("Block unSuccessfull");
            window.location.reload();
            }           
        }
     });
  	}else{
  		// alert('not confirm');
  	}
  });
 /*-------block item category,subctegory and items end-----*/

 /*-------Unblock item category,subctegory and items end-----*/
function change_all_unblock(id){
       $('#cat_unblockId').val(id);        
       $("#mi-modalDlete").modal('show');       
        }

 var modalConfirmDel = function(callback){     
  $("#modal-btn-yesDel").on("click", function(){
    callback(true);
    $("#mi-modalDlete").modal('hide');
  });
  
  $("#modal-btn-noDel").on("click", function(){
    callback(false);
    $("#mi-modalDlete").modal('hide');
  });
};

modalConfirmDel(function(confirm){
  if(confirm){ 

  	var main_cat_id = $('#cat_unblockId').val(); 
  	 
    $.ajax({
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          url: "{{ url('ajax_change_itemCat_status') }}",
          type: 'POST',
          data: {'main_cat_id' : main_cat_id,'status':'1'},
          success:function(response) { 
            if(response == 1){    
            alert("UnBlocked Succesfully");
            window.location.reload();
            }else{
            alert("UnBlock unSuccessfull");
            window.location.reload();
            }           
        }
     });

  }else{
// alert('not confirm');
  }
});
 /*-------Unblock item category,subctegory and items end-----*/

 function individual_change_status(gotVal,gotStatus)
	{
		var val = [];
		val[0]=gotVal;
		bulk_function(val,gotStatus);
	}

	function bulk_function(gotVal,gotStatus){
		$.ajax({
			type:'get',
			url :"<?php echo url("multi_pro_block"); ?>",
			beforeSend: function() {
				$(".loading-image").show();
			},
			data:{'val':gotVal,'status': gotStatus,'type' : 'item'},
			success:function(response){
				$(".loading-image").hide();
				$('#successMsgRole').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>'+response+'</div>').show();
				
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

</script>
<script>
		var table = '';
         $(document).ready(function () {
            
           table =  $('#dataTables-example').DataTable({
					"processing": true,
					"responsive": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false, 
					"ajax":{
						"url": "{{ url('item-category_list_ajax') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ $('#check_all').prop('checked',false); },
						"data":{ _token: "{{csrf_token()}}",catName_search : function(){ return $('#catName_search').val(); },addedBy_search : function() { return $('#addedBy_search').val(); },catStatus_search : function (){ return $('#catStatus_search').val(); }}
					},
					"columnDefs": [ {
						"targets": [0,1,3,4,6],
						"orderable": false
					} ],
					"order": [ 1, 'desc' ],
					"columns": [
								{ "data": "checkBox",sWidth: '8%' },
								{ "data": "SNo", sWidth: '8%' },
								{ "data": "CategoryName", sWidth: '22%' },
								{ "data": "ManageSubCategory", sWidth: '20%' },
								{ "data": "Edit", sWidth: '9%' },
								{ "data": "Status", sWidth: '9%' },
								{ "data": "Delete", sWidth: '9%' },
								{ "data": "AddedBy", sWidth: '15%' }
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'});
						$('[data-toggle="popover"]').popover({placement:'left'});   
					}
				});

           	$('#catName_search').keyup( function(){
           		table.draw();
           	});

           	$('#catStatus_search,#addedBy_search').change( function(){
           		table.draw();
           	});
            jQuery.validator.addMethod("noSpace", function(value, element) { 
			  return value.trim().length != 0; 
			}, "No space please and don't leave it empty");
         });
    </script>
    <script>
	$("#validate_form").validate({
		rules: {
			cate_name: "required",
			cate_name : {
							noSpace : true
						}
		},
		messages: {
			cate_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME')}}"
		}
	});
	
	function new_change()
	{
	   	location.href='{{url('manage-item-category?addnow=1')}}';
	}

	var getUrlParameter = function getUrlParameter(sParam) {
	   	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;
		
	   	for (i = 0; i < sURLVariables.length; i++) {
	   		sParameterName = sURLVariables[i].split('=');
			
	   		if (sParameterName[0] === sParam) {
	   			return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
	
	
	var tech = getUrlParameter('addnow');
	if(tech==1)
	{
		$('#location_form').addClass('in');
	}
	function importValidate()
	{
		var imgVal = $('#importCsvId').val(); 
		if(imgVal=='') 
		{ 
			$('#importFileError').show();
			return false; 
		} 
	}

	/* show popup */
	var popup = '<?php echo Session::has("popup"); ?>';
	if(popup)
	{
		<?php if(Session::get('popup') == 'open') { ?> 
			$('#importmodal').modal('show');
		<?php } ?>
	}
</script>	
@endsection
@stop