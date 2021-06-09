@extends('sitemerchant.layouts.default')
@section('content')
	<style>
		input[type=file] {
			display: inline-block;
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
									<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if(isset($id))  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form">{{(Lang::has(Session::get('mer_lang_file').'.MER_ADD_PAGE')) ? trans(Session::get('mer_lang_file').'.MER_ADD_PAGE') : trans($MER_OUR_LANGUAGE.'.MER_ADD_PAGE')}}</a>
								</div>
								{{-- Display error message--}}

								@if ($errors->has('errors'))
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{ $errors->first('errors') }}
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
									{{(Lang::has(Session::get('mer_lang_file').'.MER_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.MER_SELECT_ONE') : trans($MER_OUR_LANGUAGE.'.MER_SELECT_ONE')}}
								</div>
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="collapse @php  if(isset($id)){ echo 'in';} @endphp panel-body">
										@if(isset($id) && empty($cate_detail) === false)
											@php $cate_id =  $id; @endphp
										@endif
										<div class="row-fluid well">
											@if(isset($id) && empty($cate_detail) === false)
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'update-merchant-store-category']) !!}
												{!! Form::hidden('category_id',$cate_id)!!}
											@else
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-merchant-store-category']) !!}
											@endif
											<div class="row panel-heading">
												<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.MER_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.MER_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.MER_CATE_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
												</div>
												<div class="col-md-8">
													@php $cate_name = ''; @endphp
													@if(isset($id) && empty($cate_detail) === false)
														@php
															$cate_name = $cate_detail->cate_name;
														@endphp
													@endif
													{!! Form::text('cate_name',$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.MER_ENTR_CATE_NAME'),'id' => 'category_name','required','autocomplete' => 'off']) !!}

												</div>
											</div>

											@if(count($Mer_Active_Language) > 0)
												@foreach($Mer_Active_Language as $lang)
													<div class="row panel-heading">
														<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.MER_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.MER_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.MER_CATE_NAME')}}&nbsp; (In {{$lang->lang_name}})
												</span>
														</div>
														<div class="col-md-8">
															@php $cate_name = ''; @endphp
															@if(isset($id) && empty($cate_detail) === false)
																@php $lang_code = 'cate_name_'.$lang->lang_code;
												$cate_name = $cate_detail->$lang_code;
																@endphp
															@endif
															{!! Form::text('cate_name_'.$lang->lang_code,$cate_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.MER_ENTR_CATE_NAME'),'id' => 'category_name','autocomplete' => 'off']) !!}

														</div>
													</div>
												@endforeach
											@endif




											<div class="panel-heading">

												@if(isset($id))
													@php $saveBtn = (Lang::has(Session::get('mer_lang_file').'.MER_UPDATE')) ? trans(Session::get('mer_lang_file').'.MER_UPDATE') : trans($MER_OUR_LANGUAGE.'.MER_UPDATE') @endphp
												@else
													@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.MER_SAVE')) ? trans(Session::get('mer_lang_file').'.MER_SAVE') : trans($MER_OUR_LANGUAGE.'.MER_SAVE') @endphp
												@endif

												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												@if(isset($id))
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-store-category'); ?>'">
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
												<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.MER_IMPORT_FILE')) ? trans(Session::get('mer_lang_file').'.MER_IMPORT_FILE') : trans($MER_OUR_LANGUAGE.'.MER_IMPORT_FILE')}}</h4>
											</div>
											<div class="modal-body">
												{!! Form::open(['method' => 'post','class'=>'form-inline','url' => 'import_store_category','enctype' =>'multipart/form-data'])!!}
												{!! Form::file('upload_file')!!}
												{!! Form::submit((Lang::has(Session::get('mer_lang_file').'.MER_SUBMIT')) ? trans(Session::get('mer_lang_file').'.MER_SUBMIT') : trans($MER_OUR_LANGUAGE.'.MER_SUBMIT'),['class' => 'btn btn-success'])!!}
												{!! Form::close()!!}
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
									<div class="top-button" >
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.MER_BLOCK')) ? trans(Session::get('mer_lang_file').'.MER_BLOCK') : trans($MER_OUR_LANGUAGE.'.MER_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.MER_UNBLOCK')) ? trans(Session::get('mer_lang_file').'.MER_UNBLOCK') : trans($MER_OUR_LANGUAGE.'.MER_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.MER_DELETE')) ? trans(Session::get('mer_lang_file').'.MER_DELETE') : trans($MER_OUR_LANGUAGE.'.MER_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.MER_IMPORT')) ? trans(Session::get('mer_lang_file').'.MER_IMPORT') : trans($MER_OUR_LANGUAGE.'.MER_IMPORT'),['id' => 'unBlock_value','class' => 'btn btn-primary','data-toggle' => 'modal', 'data-target'=> '#importmodal'])!!}

										<a href="{{url('download_store_catedory/csv')}}">
											{!! Form::button((Lang::has(Session::get('mer_lang_file').'.MER_DWNLD_EXCEL')) ? trans(Session::get('mer_lang_file').'.MER_DWNLD_EXCEL') : trans($MER_OUR_LANGUAGE.'.MER_DWNLD_EXCEL'),['id' => 'download_value','class' => 'btn btn-info'])!!}
										</a>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chk[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_SNO')) ? trans(Session::get('mer_lang_file').'.MER_SNO') : trans($MER_OUR_LANGUAGE.'.MER_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.MER_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.MER_CATE_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_EDIT')) ? trans(Session::get('mer_lang_file').'.MER_EDIT') : trans($MER_OUR_LANGUAGE.'.MER_EDIT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_DELETE')) ? trans(Session::get('mer_lang_file').'.MER_DELETE') : trans($MER_OUR_LANGUAGE.'.MER_DELETE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.MER_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.MER_ADDED_BY') : trans($MER_OUR_LANGUAGE.'.MER_ADDED_BY')}}
											</th>
										</tr>
										</thead>
										<tbody>
										@if(count($all_details) > 0)
											@php $i = 1;
										$cate_name = 'cate_name';
											@endphp
											@foreach($all_details as $details)
												<tr>
													<td>
														{!! Form::checkbox('chk[]',$details->cate_id)!!}
													</td>
													<td>{{$i}}</td>
													<td>{{ ucfirst($details->$cate_name)}}</td>
													<td>
														<a href="{!! url('edit_store_category').'/'.base64_encode($details->cate_id) !!}"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>
													</td>
													<td>
														@if($details->cate_status == 1)  {{--0-block, 1- active --}}
														<a href="{!! url('store_cate_status').'/'.$details->cate_id.'/0' !!}"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>
														@else
															<a href="{!! url('store_cate_status').'/'.$details->cate_id.'/1' !!}"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" onClick="change_status($details->cate_id)"></i></a>
														@endif
													</td>
													<td>
														<a href="{!! url('store_cate_status').'/'.$details->cate_id.'/2' !!}"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>
													</td>
													<td>
														@if($details->cate_added_by == 0)
															{{(Lang::has(Session::get('mer_lang_file').'.MER_ADMIN')) ? trans(Session::get('mer_lang_file').'.MER_ADMIN') : trans($MER_OUR_LANGUAGE.'.MER_ADMIN')}}
														@else
															{{(Lang::has(Session::get('mer_lang_file').'.MER_MERCHANT')) ? trans(Session::get('mer_lang_file').'.MER_MERCHANT') : trans($MER_OUR_LANGUAGE.'.MER_MERCHANT')}}
														@endif
													</td>
												</tr>
												@php $i++; @endphp
											@endforeach
										@else
											{{(Lang::has(Session::get('mer_lang_file').'.MER_NO_DETAILS')) ? trans(Session::get('mer_lang_file').'.MER_NO_DETAILS') : trans($MER_OUR_LANGUAGE.'.MER_NO_DETAILS')}}
										@endif
										</tbody>
									</table>
								</div>
								{{--Manage page ends--}}
							</div>
							@if(count($all_details) > 0)
								{!! $all_details->render() !!}
							@endif
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
            node.val(node.val().replace(/[^A-Z a-z &]/g,'') ); }
        );
        function change() {
            var elem = document.getElementById("click-here");
            if (elem.text=="Add Page")
            {
                elem.text = "Manage List";
            }
            else if (elem.text=="Manage List")
            {
                elem.text = "Add Page";
            }
            else {
                elem.text = "Manage List";
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
                url :"<?php echo url("multi_store_block"); ?>",
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
                url :"<?php echo url("multi_store_block"); ?>",
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
                url :"<?php echo url("multi_store_block"); ?>",
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
        $(document).ready(function () {

            $('#dataTables-example').dataTable({
                "bPaginate": false,
                "scrollX": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false });
        });
	</script>
@endsection
@stop