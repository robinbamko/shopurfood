@extends('sitemerchant.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<!-- MAIN -->
	<style>
		.checked {
			color: orange;
		}

		table.dataTable
		{
			margin-top: 20px !important;
		}
		#location_table .dataTables_length label{  margin: 55px 0 0; }
		@media only screen and (max-width:767px)
		{
			#location_table .dataTables_length label{  margin: 0px 0 0; }
		}
		@media only screen and (min-width:768px) and (max-width:1023px)
		{
			#location_table .dataTables_length label{  margin: 0px 0 0; }
		}

	</style>
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
								</div>

								<div class="err-alrt-msg">
									@if(Session::has('message'))
										<div class="alert alert-success alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
											{{ Session::get('message') }}
										</div>
									@endif
									<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
									</div>
								</div>

								<div class="box-body spaced">

								</div>
								{{--Manage page starts--}}
								<div class="panel-body" id="location_table" >
									{{--
                                    <div class="panel-heading p__title">
                                        Manage List
                                    </div>
                                    --}}

									<div class="top-button" >
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DISAPPROVE'),['id' => 'block_value','class' => 'btn btn-success'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_APPROVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_APPROVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_APPROVE'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{!! Form::checkbox('chk_none[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ST_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ST_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_STATUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_STATUS')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>

										</tr>
										</thead>
										<tbody>
										@if(count($all_details) > 0)
											@php $i = 1;

											@endphp
											@foreach($all_details as $details)
												<tr>
													<td>
														{!! Form::checkbox('chk[]',$details->comment_id)!!}
													</td>
													<td>{{$i}}</td>
													<td>{{ ucfirst($details->st_store_name)}}</td>
													<td>{{ ucfirst($details->mer_fname).' '.$details->mer_lname}}</td>
													<td>{{ ucfirst($details->cus_fname)}}<br>{{$details->cus_email}}</td>

													<td>
														<a href="{!! url('mer_view_review').'/'.base64_encode($details->comment_id) !!}"><i class="fa fa-eye tooltip-demo" aria-hidden="true" title="View"></i></a>
													</td>
													<td>
														@if($details->review_status == 1)  {{--0-block, 1- active --}}
														<a href="{!! url('mer_review_status').'/'.base64_encode($details->comment_id).'/0/'.$details->review_type !!}" class="btn  btn-success btn-sm tooltip-demo"  title="Click to Disapprove"><i class="lnr lnr-thumbs-up " aria-hidden="true"></i>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_APPROVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_APPROVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_APPROVE')}}</a>
														@else
															<a href="{!! url('mer_review_status').'/'.base64_encode($details->comment_id).'/1/'.$details->review_type !!}" class="btn  btn-danger btn-sm tooltip-demo" title="Click to Approve"><i class="lnr lnr-thumbs-down" aria-hidden="true"></i>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DISAPPROVE')}}</a>
														@endif
													</td>
													<td>
														<a href="{!! url('mer_review_status').'/'.base64_encode($details->comment_id).'/2/'.$details->review_type !!}"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>
													</td>

												</tr>
												@php $i++; @endphp
											@endforeach

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

        /** multiple delete ,block,unblock**/
        $('#delete_value,#unBlock_value,#block_value').click(function(event){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            val[0]='';
            $('input[name="chk[]"]:checked').each(function(i){
                var j=i+1;
                val[j] = $(this).val();
            });

            //alert();
            if(val=='')
            {

                $(".rec-select").css({"display" : "block"});

                return;
            }
            //alert(val); return false;
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
                url :"<?php echo url("mer_multi_review_block"); ?>",
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
        $(document).ready(function () {

            $('#dataTables-example').dataTable({
                "bPaginate": false,
                //"scrollX": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false,
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: [-1,-3]
                    }]
            });
        });
	</script>

@endsection
@stop