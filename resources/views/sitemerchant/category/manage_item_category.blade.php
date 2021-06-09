@extends('sitemerchant.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')

	<style>
		table.dataTable
		{
			margin-top: 20px 0 20px!important;
		}
		#location_table .dataTables_length label
		{
			margin-bottom:20px;
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
									<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form"> {{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_ITEM_CATE')}}</a>
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
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
								</div>
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="collapse @php  if(isset($id)){ echo 'in';} @endphp panel-body">
										@if(isset($id) && empty($cate_detail) === false)
											@php $cate_id =  $id; @endphp
										@endif
										<div class="row-fluid well">
											@if(isset($id) && empty($cate_detail) === false)
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer-update-item-category','id'=>'validate_form']) !!}
												{!! Form::hidden('category_id',$cate_id)!!}
											@else
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_add-item-category','id'=>'validate_form']) !!}
											@endif
											<div class="row panel-heading">
												<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
												</div>
												<div class="col-md-5">
													@php $cate_name = ''; @endphp
													@if(isset($id) && empty($cate_detail) === false)
														@php $cate_name = $cate_detail->pro_mc_name;  @endphp
													@endif
													{!! Form::text('cate_name',$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME'),'id' => 'category_name','required','autocomplete' => 'off','maxlength' => '25']) !!}
													@if($errors->has('cate_name'))
														<p style="color:red">{{$errors->first('cate_name')}}</p>
													@endif
												</div>
											</div>

											@if(count($Mer_Active_Language) > 0)
												@foreach($Mer_Active_Language as $lang)
													<div class="row panel-heading">
														<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}&nbsp; (In {{$lang->lang_name}})
												</span>
														</div>
														<div class="col-md-5">
															@php $cate_name = ''; @endphp
															@if(isset($id) && empty($cate_detail) === false)
																@php
																	$lang_code = 'pro_mc_name_'.$lang->lang_code;
                                                                    $cate_name = $cate_detail->$lang_code;
																@endphp
															@endif
															{!! Form::text('cate_name_'.$lang->lang_code,$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME'),'required','id' => 'category_name_lang','autocomplete' => 'off','maxlength' => '25']) !!}

														</div>
													</div>
												@endforeach
											@endif




											<div class="panel-heading col-md-offset-3">

												@if(isset($id))
													@php $saveBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
													@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SAVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif

												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												@if(isset($id))
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer_manage-item-category'); ?>'">
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
												<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_IMPORT_FILE')}}</h4>
											</div>
											<div class="modal-body">
												{!! Form::open(['method' => 'post','url' => 'mer_import_item_category','enctype' =>'multipart/form-data','onsubmit'=>'return importValidate();'])!!}
												<div class="row">
													<div class="col-md-6 col-xs-12">
														{!! Form::file('upload_file',array('accept'=>'.csv','id'=>'importCsvId'))!!}
													</div>

													<div class="col-md-6 col-xs-12 text-right">
														{!! Form::submit((Lang::has(Session::get('mer_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUBMIT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SUBMIT'),['class' => 'btn btn-success'])!!}</div></div>
												{!! Form::close()!!}
												<a href="{{url('mer_download_item_category/csv/sample_file')}}">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_SAMPLE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_SAMPLE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DWNLD_SAMPLE')}}</a>
												<br />
												<div id="importFileError" style="color:red;font-size: 15px;font-weight: bold;display:none">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_UPLOAD_FILE')}}</div>
												@if(Session::has('import_err'))
													<span style="color:red">{{ Session::get('import_err')}} </span>
												@endif
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT') : trans($MER_OUR_LANGUAGE.'.ADMIN_IMPORT'),['class' => 'btn btn-primary','data-toggle' => 'modal', 'data-target'=> '#importmodal'])!!}
										<a href="{{url('mer_download_item_category/csv')}}">
											{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($MER_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'download_value','class' => 'btn btn-info'])!!}
										</a>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>


                                            <td><input type="text" id="catName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('mer_lang_file').'.MER_ENTR_TXT_SERCH') }}"/></td>

                                            <td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												<select id="addedBy_search" class="form-control" style="width:100%">

                                                    <option value="">@lang(Session::get('mer_lang_file').'.MER_ALL')</option>
                                                    <option value="0">@lang(Session::get('mer_lang_file').'.ADMIN_ADMIN')</option>
                                                    <option value="1">@lang(Session::get('mer_lang_file').'.ADMIN_MERCHANT')</option>

                                                </select>
											</td>
										</tr>
										<tr>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE')}}
											</th>
											<th style="text-align:center">
											{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_EDIT') : trans($MER_OUR_LANGUAGE.'.ADMIN_EDIT')}}

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADDED_BY') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
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
        $('#category_name').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^A-Z a-z &]/g,'') );
        });

        function change() {
            var elem = document.getElementById("click-here");
            if (elem.text=="Add Item")
            {
                elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_LIST')}}";
            }
            else if (elem.text==" Manage List")
            {
                elem.text = " {{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_ITEM_CATE')}}";
            }
            else {
                elem.text = " {{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_LIST')}}";
            }
        }

        $('#block_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });  console.log(val);

            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            //alert(val); return false;

            $.ajax({
                type:'get',
                url :"<?php echo url("mer_multi_pro_block"); ?>",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data:{'val':val,'status':0},
                success:function(response){
                    //$("#loading-image").hide();
                    location.reload();
                }
            });
        });

        /** multiple unblock **/
        $('#unBlock_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });  console.log(val);


            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            //alert(val); return false;

            $.ajax({
                type:'get',
                url :"<?php echo url("mer_multi_pro_block"); ?>",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data:{'val':val,'status':1},
                success:function(response){
                    //$("#loading-image").hide();
                    location.reload();
                }
            });
        });
        /** multiple delete **/
        $('#delete_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });  console.log(val);

            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            //alert(val); return false;

            $.ajax({
                type:'get',
                url :"<?php echo url("mer_multi_pro_block"); ?>",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data:{'val':val,'status':2},
                success:function(response){
                    //$("#loading-image").hide();
                    location.reload();
                }
            });
        });
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
        var table = '';
        $(document).ready(function () {

            /*$('#dataTables-example').dataTable({
                "bPaginate": false,
                "scrollX": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });*/
            table =  $('#dataTables-example').DataTable({
                "processing": true,
				"responsive": true,
                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,
                "ajax":{
                    "url": "{{ url('mer_item_category_list_ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",catName_search : function(){ return $('#catName_search').val(); },addedBy_search : function() { return $('#addedBy_search').val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,2,3],
                    "orderable": false
                } ],
                "order": [ 0, 'desc' ],
                "columns": [
                    { "data": "SNo", sWidth: '9%' },
                    { "data": "CategoryName", sWidth: '40%' },
                    { "data": "ManageSubCategory", sWidth: '27%' },
                    { "data": "Edit", sWidth: '9%' },
                    { "data": "AddedBy", sWidth: '15%' }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
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
                cate_name : { noSpace : true }
            },
            messages: {
                cate_name: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME')}}"
            }
        });

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