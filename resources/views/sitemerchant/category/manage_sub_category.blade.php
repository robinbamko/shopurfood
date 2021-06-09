@extends('sitemerchant.layouts.default')

@section('content')

	<style>
		table.dataTable
		{
			margin-top: 20px !important;
		}
		@media only screen and (max-width:767px)
		{
			.panel-heading .col-xs-12{ text-align:center!important;  }
			.panel-heading .col-xs-12 a{ float:none!important; margin:0 0 5px; }
			.panel-title-btn{float:none!important;}
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
									<div class="col-md-4 col-sm-4 col-xs-12">{{$pagetitle}}</div>
									
									<!-- <div class="col-md-4 col-sm-4 col-xs-12"><a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE') : trans($MER_OUR_LANGUAGE .'.ADMIN_ADD_SUB_CATE')}}</a></div> -->

									<!-- <div class="col-md-4 col-sm-4 col-xs-12"><a href="{{url('mer_manage-product-category')}}">{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_BACK')) ? trans(Session::get('mer_lang_file').'.ADMIN_BACK') : trans($MER_OUR_LANGUAGE .'.ADMIN_BACK'),['class' => 'btn panel-title-btn','style' => 'float:right;margin-right:10px'])!!}</a></div>
 -->
									<div class="pull-right">
									<a href="{{url('mer_manage-product-category')}}" >{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_BACK')) ? trans(Session::get('mer_lang_file').'.ADMIN_BACK') : trans($MER_OUR_LANGUAGE .'.ADMIN_BACK'),['class' => 'btn btn-default','style' => 'margin-right:10px'])!!}</a>
								<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE') : trans($MER_OUR_LANGUAGE .'.ADMIN_ADD_SUB_CATE')}}</a>

							</div>
								</div>

								{{-- Display error message--}}
								<div class="err-alrt-msg">
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
										{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE') : trans($MER_OUR_LANGUAGE .'.ADMIN_SELECT_ONE')}}
									</div>
								</div>

								{{-- Add/Edit page starts--}}
								<div class="box-body spaced">
									<div id="location_form" class="collapse @php  if(isset($id)){ echo 'in';} @endphp panel-body">
										@if(isset($id) && empty($cate_detail) === false)
											@php $cate_id =  $id; @endphp
										@endif

										<div class="row-fluid well">
											@if(isset($id) && empty($cate_detail) === false)
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_update-sub-category','id'=>'validate_form']) !!}
												{!! Form::hidden('category_id',$cate_id)!!}
											@else
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_add-sub-category','id'=>'validate_form']) !!}
											@endif
											{!! Form::hidden('main_id',$main_id)!!}
											@php $segment = request()->segment(1);
										$main = request()->segment(3);
											@endphp
											@if($segment == "mer_manage-subproduct")
												{!! Form::hidden('cate_type','2')!!}
											@elseif($segment == "mer_manage-subitem")
												{!! Form::hidden('cate_type','1')!!}
											@endif
											<div class="row panel-heading">
												<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_CATE_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
												</div>
												<div class="col-md-8">
													@php $cate_name = ''; @endphp
													@if(isset($id) && empty($cate_detail) === false)
														@php
															$cate_name = $cate_detail->pro_sc_name;
														@endphp
													@endif
													{!! Form::text('cate_name',$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_ENTR_CATE_NAME'),'id' => 'category_name','required','autocomplete' => 'off','maxlength' => '100']) !!}
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
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_CATE_NAME')}}&nbsp; (In {{$lang->lang_name}})
												</span>
														</div>
														<div class="col-md-8">
															@php $cate_name = ''; @endphp
															@if(isset($id) && empty($cate_detail) === false)
																@php $lang_code = 'pro_sc_name_'.$lang->lang_code;
												$cate_name = $cate_detail->$lang_code;
																@endphp
															@endif
															{!! Form::text('cate_name_'.$lang->lang_code,$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_ENTR_CATE_NAME'),'id' => 'category_name','autocomplete' => 'off','maxlength' => '100']) !!}

														</div>
													</div>
												@endforeach
											@endif




											<div class="panel-heading col-md-offset-3">

												@if(isset($id))
													@php $saveBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE') : trans($MER_OUR_LANGUAGE .'.ADMIN_UPDATE') @endphp
												@else
													@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SAVE') : trans($MER_OUR_LANGUAGE .'.ADMIN_SAVE') @endphp
												@endif

												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												@if(isset($id))
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer_manage-subproduct').'/'.$main; ?>'">
												@else
													{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'data-toggle'=>"collapse", 'data-target'=>"#location_form"])!!}
												@endif
											</div>
											{!! Form::close() !!}
										</div>
									</div>
								</div>
								{{-- Add/Edit page ends--}}


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


									@php
										$segment = request()->segment(1);
                                        $mainid = request()->segment(2);
									@endphp

									<div class="top-button top-btn-full" style="position:relative">
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT') : trans($MER_OUR_LANGUAGE.'.ADMIN_IMPORT'),['class' => 'btn btn-primary','data-toggle' => 'modal', 'data-target'=> '#importmodal'])!!}
										<a href="{{url('mer_download_sub_category/csv/2/'.$mainid)}}">
											{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($MER_OUR_LANGUAGE .'.ADMIN_DWNLD_EXCEL'),['class' => 'btn btn-info'])!!}
										</a>
									</div>
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
													{!! Form::open(['method' => 'post','url' => 'mer_import_sub_category','enctype' =>'multipart/form-data','onsubmit'=>'return importValidate();'])!!}
													<div class="row">
														<div class="col-md-6 col-xs-12">
															{!! Form::file('upload_file',array('accept'=>'.csv','id'=>'importCsvId'))!!}
															{!! Form::hidden('main_id',base64_decode($mainid))!!}
															@if($segment == "mer_manage-subproduct")
																{!! Form::hidden('cate_type','2')!!}
															@elseif($segment == "mer_manage-subitem")
																{!! Form::hidden('cate_type','1')!!}
															@endif
														</div>

														<div class="col-md-6 col-xs-12 text-right">
															{!! Form::submit((Lang::has(Session::get('mer_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUBMIT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SUBMIT'),['class' => 'btn btn-success'])!!}</div>
													</div>
													{!! Form::close()!!}
													<a href="{{url('mer_download_sub_category/csv/2/'.$mainid.'/sample_file')}}">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_SAMPLE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_SAMPLE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DWNLD_SAMPLE')}}</a>
													<br />
													<div id="importFileError" style="color:red;font-size: 15px;font-weight: bold;display:none">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_UPLOAD_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_UPLOAD_FILE')}}</div>
													@if(Session::has('import_err'))
														<span style="color:red">{{ Session::get('import_err')}} </span>
													@endif
												</div>
												<div class="modal-footer">

													<button type="button" class="btn btn-default" data-dismiss="modal">@lang(Session::get('mer_lang_file').'.ADMIN_CLOSE')</button>

												</div>
											</div>
										</div>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>

											<td><input type="text" id="catName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('mer_lang_file').'.MER_ENTR_TXT_SERCH') }}"/></td>

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
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE .'.ADMIN_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_CATE_NAME')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_EDIT') : trans($MER_OUR_LANGUAGE .'.ADMIN_EDIT')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADDED_BY') : trans($MER_OUR_LANGUAGE .'.ADMIN_ADDED_BY')}}
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
            node.val(node.val().replace(/[^A-Z a-z & - ,]/g,'') );
        });
        function change() {
            var elem = document.getElementById("click-here");
            if (elem.text=="Add Page")
            {
                elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE .'.ADMIN_MNGE_LIST')}}";
            }
            else if (elem.text=="Manage List")
            {
                elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_SUB_CATE') : trans($MER_OUR_LANGUAGE .'.ADMIN_ADD_SUB_CATE')}}";
            }
            else {
                elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE .'.ADMIN_MNGE_LIST')}}";
            }
        }


        /** multiple delete ,block,unblock**/
        $('#delete_value,#unBlock_value,#block_value').click(function(event){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });  console.log(val);

            //alert();
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

            $.ajax({

                type:'get',
                url :"<?php echo url("mer_multi_sub_block"); ?>",
                beforeSend: function() {
                    $("#loading-image").show();

                },
                data:{'val':val,'status':status},

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
                //"scrollX": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
            "bAutoWidth": false });*/
            table =    $('#dataTables-example').DataTable({
                "processing": true,
                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching":false,
                "ajax":{
                    "url": "{{ route('sub_product_ajax',['id' => base64_encode($main_id)]) }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",catName_search : function(){ return $('#catName_search').val(); },addedBy_search : function() { return $('#addedBy_search').val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,2],
                    "orderable": false
                } ],
                "order": [ 0, 'desc' ],
                "columns": [
                    { "data": "SNo", sWidth: '9%' },
                    { "data": "CategoryName", sWidth: '65%' },
                    { "data": "Edit", sWidth: '11%' },
                    { "data": "AddedBy", sWidth: '15%' }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });

            $('#catName_search').keyup( function(){
                table.draw();
            });

            $('#addedBy_search').change( function(){
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
                cate_name: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE .'.ADMIN_ENTR_CATE_NAME')}}"
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