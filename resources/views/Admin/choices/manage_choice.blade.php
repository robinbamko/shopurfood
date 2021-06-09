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
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
								@php extract($privileges); @endphp
								
								@if((isset($Choices) && is_array($Choices)) && in_array('1', $Choices) || $allPrev == '1')
								<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else  @endif style="float:right" data-toggle="collapse" data-target = "#location_form"> {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_CHOICE')}}</a>
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
							<div id="successMsgRole"></div>
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
							</div>
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:5px">
								<div id="location_form" class="collapse @php  if($sid!=''){ echo 'in';} @endphp panel-body">
									@if(isset($id) && empty($cate_detail) === false)
									@php $ch_id =  $id; @endphp
									@endif
									<div class="row-fluid well">
										@if(isset($id) && empty($cate_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'update-choice','id'=>'choice_form']) !!}
										{!! Form::hidden('ch_id',$ch_id)!!}
										@else
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-choice','id'=>'choice_form']) !!}
										@endif
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
											</div>
											<div class="col-md-8">
												@php $ch_name = ''; @endphp
												@if(isset($id) && empty($cate_detail) === false)
												@php
												$ch_name = $cate_detail->ch_name;
												@endphp
												@endif
												{!! Form::text('ch_name',$ch_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME'),'id' => 'ch_name','required','autocomplete' => 'off','maxlength'=>'200']) !!}
												
											</div>
										</div>

										@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME')}}&nbsp; (In {{$lang->lang_name}})*
												</span>
											</div>
											<div class="col-md-8">
												@php $ch_name = ''; @endphp
												@if(isset($id) && empty($cate_detail) === false)
												@php $lang_code = 'ch_name_'.$lang->lang_code;
												$ch_name = $cate_detail->$lang_code;
												@endphp
												@endif
												{!! Form::text('ch_name_'.$lang->lang_code,$ch_name,['class'=>'form-control','required','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME'),'id' => 'ch_name_'.$lang->lang_code,'autocomplete' => 'off','maxlength'=>'200']) !!}
												
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
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-choices'); ?>'">
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
										{!! Form::open(['method' => 'post','url' => 'import_choices','enctype' =>'multipart/form-data','onsubmit'=>'return importValidate();'])!!}
										<div class="row">
											<div class="col-md-6 col-xs-12">
											{!! Form::file('upload_file',array('accept'=>".csv",'id'=>'importCsvId'))!!}
											</div>
											<div class="col-md-6 col-xs-12 text-right">
												{!! Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBMIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBMIT'),['class' => 'btn btn-success'])!!}
											</div>
										</div>
										{!! Form::close()!!}
										<a href="{{url('download_choices/csv/sample_file')}}">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_SAMPLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_SAMPLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_SAMPLE')}}</a>
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

								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								<div class="top-button" style="margin-top: -25px;">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}

									@if((isset($Choices) && is_array($Choices)) && in_array('3', $Choices) || $allPrev == '1')
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									@endif

									@if((isset($Choices) && is_array($Choices)) && in_array('1', $Choices) || $allPrev == '1')
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_IMPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMPORT'),['id' => 'unBlock_value','class' => 'btn btn-primary','data-toggle' => 'modal', 'data-target'=> '#importmodal'])!!}
									@endif 

									<a href="{{url('download_choices/csv')}}">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
									</a>
								</div>

								<!-- <div class="table-responsive"> -->
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td><input type="text" id="choiceNamesearch" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
												<td>&nbsp;</td>
												<td>
													@php
														$statusArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
														$statusArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH');
														$statusArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH');
													@endphp
													{{ Form::select('status_search',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'status_search'] ) }}
												</td>
												<td>&nbsp;</td>
												<td>
													@php
														$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
														$addedByArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
														$addedByArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
													@endphp
													{{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}
												</td>
											</tr>
											<tr>
												<th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
												<th style="text-align:center" class="sorting_no_need">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
												</th>
												<th style="text-align:center">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME')}}
												</th>
												
												@if((isset($Choices) && is_array($Choices)) && in_array('2', $Choices) || $allPrev == '1')
												<th style="text-align:center">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
												</th>
												@endif
												
												<th style="text-align:center">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
												</th>
												
												@if((isset($Choices) && is_array($Choices)) && in_array('3', $Choices) || $allPrev == '1')
												<th style="text-align:center">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
												</th>
												@endif
												
												<th style="text-align:center">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDED_BY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
												</th>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
								<!-- </div> -->

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
	$('#category_name').bind('keyup blur',function(){ 
		var node = $(this);
		node.val(node.val().replace(/[^A-Z a-z &]/g,'') ); 
	});
	
	$('#block_value').click(function(){
		
		$(".rec-select").css({"display" : "none"});
			var val = [];
			val[0]='';
			$('input[name="chk[]"]:checked').each(function(i){
				var j=i+1;
				//alert(j);
				val[j] = $(this).val();
			}); 
			
			if(val=='')
			{
				$(".rec-select").css({"display" : "block"});
				return;
			}
			multi_action(val,'0');
	});
	
	
	$('#unBlock_value').click(function(){
		
		$(".rec-select").css({"display" : "none"});
			var val = [];
			val[0]='';
			$('input[name="chk[]"]:checked').each(function(i){
				var j=i+1;
				//alert(j);
				val[j] = $(this).val();
			}); 
			
			if(val=='')
			{
				$(".rec-select").css({"display" : "block"});
				return;
			}
			multi_action(val,'1');
			
	});
	
	$('#delete_value').click(function(){
		
		$(".rec-select").css({"display" : "none"});
			var val = [];
			val[0]='';
			$('input[name="chk[]"]:checked').each(function(i){
				var j=i+1;
				//alert(j);
				val[j] = $(this).val();
			}); 
			
			if(val=='')
			{
				$(".rec-select").css({"display" : "block"});
				return;
			}
			multi_action(val,'2');

	});
	function multi_action(val,status)
	{

		$.ajax({
			type:'get',
			url :"<?php echo url("multi_choice_block"); ?>",
			beforeSend: function() {
				$("#loading-image").show();
			},
			data:{'val':val,'status':status},
			success:function(response){				
				$("#loading-image").hide();
				$('#successMsgRole').show();
				$('#successMsgRole').focus();
				$('#successMsgInfo').html(response).show();
				$('#successMsgRole').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>'+response+'</div>').show();
				if(status==0)
				{
					for(var i=0;i<val.length;i++)
					{
						table.draw(false);
					}
				}
				else if(status==1)
				{
					for(var i=0;i<val.length;i++)
					{
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
		multi_action(val,gotStatus)
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
<script>
        /* $(document).ready(function () {
            
             $('#dataTables-example').dataTable({
		    "bPaginate": false,
		    "responsive": true,
		    "bLengthChange": true,
		    "bFilter": true,
		    "bInfo": true,
		    "bAutoWidth": true ,
			"columnDefs": [ {
                    "targets": [0,1,3,5,6],
                    "orderable": false
                } ],
                "order": [ 1, 'asc' ],

			});
			
			var tech = getUrlParameter('addnow');
			if(tech==1)
			{
				$('#location_form').addClass('in');
			}
         });*/
    </script>	

<script>
	$("#choice_form").validate({
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			ch_name: "required",
			
			//**THIS IS FOR DYNAMIC LANGUAGE VALIDATION**//
				@if(count($Admin_Active_Language) > 0)
                     @foreach($Admin_Active_Language as $lang)
					 
					 ch_name_{{$lang->lang_code}}: "required",
					
					@endforeach
				@endif
			//**END DYNAMIC LANGUAGE VALIDATION**//
			
		},
		messages: {
			ch_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME')}}",
			
			//**THIS IS FOR DYNAMIC LANGUAGE VALIDATION**//
				@if(count($Admin_Active_Language) > 0)
                     @foreach($Admin_Active_Language as $lang)
				 
						ch_name_{{$lang->lang_code}}: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME')}}({{$lang->lang_name}})",
				
					@endforeach
				@endif
			//**END DYNAMIC LANGUAGE VALIDATION**//
			   
		}
	});
	
	
	
	
	function new_change()
	{
	   	location.href='{{url('manage-choices?addnow=1')}}';
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
                "dom": 'l<"toolbar">frtip',
                "ajax":{
                    "url": "{{ url('manage-choices') }}",
                    "dataType": "json",
                    "type": "POST",
                    "beforeSend":function(){ $('#check_all').prop('checked',false); },
                    "data":{ _token: "{{csrf_token()}}",choiceNamesearch:function(){return $("#choiceNamesearch").val(); },status_search:function(){return $("#status_search").val(); },addedBy_search:function(){return $("#addedBy_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,1,3,5],
                    "orderable": false
                } ],
                "columns": [
                    { "data": "checkBox", sWidth: '8%' },
                    { "data": "SNo", sWidth: '8%' },
                    { "data": "choiceName", sWidth: '45%' },
                    { "data": "Edit", sWidth: '8%' },
                    { "data": "Status", sWidth: '8%' },
                    { "data": "Delete", sWidth: '8%' },
                    { "data": "AddedBy", sWidth: '15%'}
                ],
                "order": [ 1, 'desc' ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });
            $('#choiceNamesearch').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });
            $('#status_search, #addedBy_search').change(function(){
                table.draw();
            });
        });
	</script>
@endsection
@stop