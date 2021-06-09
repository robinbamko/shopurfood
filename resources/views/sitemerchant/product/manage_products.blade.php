@extends('sitemerchant.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')
	<!-- MAIN -->
	<style>


		#myImg {
			border-radius: 5px;
			cursor: pointer;
			transition: 0.3s;
		}

		#myImg:hover {opacity: 0.7;}

		/* The Modal (background) */
		.modal {
			display: none; /* Hidden by default */
			position: fixed; /* Stay in place */
			z-index: 1; /* Sit on top */
			padding-top: 100px; /* Location of the box */
			left: 0;
			top: 0;
			width: 100%; /* Full width */
			height: 100%; /* Full height */
			overflow: auto; /* Enable scroll if needed */
			background-color: rgb(0,0,0); /* Fallback color */
			background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
		}

		/* Modal Content (image) */
		.modal-content {
			margin: auto;
			display: block;
			width: 80%;
			max-width: 700px;
		}

		/* Caption of Modal Image */
		#caption {
			margin: auto;
			display: block;
			width: 80%;
			max-width: 700px;
			text-align: center;
			color: #ccc;
			padding: 10px 0;
			height: 150px;
		}

		/* Add Animation */
		.modal-content, #caption {
			-webkit-animation-name: zoom;
			-webkit-animation-duration: 0.6s;
			animation-name: zoom;
			animation-duration: 0.6s;
		}

		@-webkit-keyframes zoom {
			from {-webkit-transform:scale(0)}
			to {-webkit-transform:scale(1)}
		}

		@keyframes zoom {
			from {transform:scale(0)}
			to {transform:scale(1)}
		}

		/* The Close Button
        .close {
        position: absolute;
        float: left;
        top: 83px;
        right: 408px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        }

        .close:hover,
        .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
        }*/

		/* 100% Image Width on Smaller Screens */
		@media only screen and (max-width: 700px){
			.modal-content {
				width: 100%;
			}
		}


		table.dataTable
		{
			margin-top: 20px !important;
		}

		a.label {
			width: 100%;
			float: left;
		}
		/*	div#dataTables-example_length {
            margin-top: 20px;
        }*/

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

								{{-- Display error message--}}
								<div class="err-alrt-msg">
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
										{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_ONE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
									</div>
								</div>

								{{--Manage page starts--}}
								<div class="panel-body" id="location_table" >
									<div id="loading-image" style="display:none">
										<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
									</div>
									<div class="top-button top-btn-full" style="position: relative;">
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('mer_lang_file').'.ADMIN_BLOCK') : trans($MER_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('mer_lang_file').'.ADMIN_UNBLOCK') : trans($MER_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
										{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}

										<a href="{{url('mer_download_product_list/csv')}}">
											{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('mer_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($MER_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
										</a>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>
											<td><input type="text" id="pdtStore_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="pdtCode_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td><input type="text" id="pdtName_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												@php
													$statusArray[''] = (Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT');
													$statusArray['1']=(Lang::has(Session::get('mer_lang_file').'.ADMIN_AVAILABLE')) ? trans(Session::get('mer_lang_file').'.ADMIN_AVAILABLE') : trans($MER_OUR_LANGUAGE.'.ADMIN_AVAILABLE');
													$statusArray['2']=(Lang::has(Session::get('mer_lang_file').'.ADMIN_SOLD')) ? trans(Session::get('mer_lang_file').'.ADMIN_SOLD') : trans($MER_OUR_LANGUAGE.'.ADMIN_SOLD');
												@endphp
												{{ Form::select('pdt_status',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'pdt_status'] ) }}
											</td>
											<td>&nbsp;</td>
											<td>@php
													$addedByArray[''] = (Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['admin']=(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
													$addedByArray['merchant']=(Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
												@endphp
												{{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}</td>
										</tr>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{!! Form::checkbox('chk_none[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ST_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ST_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PDT_CODE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PDT_CODE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PDT_CODE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_PDT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_PDT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORIGINAL_PRICE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORIGINAL_PRICE') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORIGINAL_PRICE')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DISC_PRICE')) ? trans(Session::get('mer_lang_file').'.ADMIN_DISC_PRICE') : trans($MER_OUR_LANGUAGE.'.ADMIN_DISC_PRICE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PDT_IMAGE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PDT_IMAGE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PDT_IMAGE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_AVAILABLITY')) ? trans(Session::get('mer_lang_file').'.ADMIN_AVAILABLITY') : trans($MER_OUR_LANGUAGE.'.ADMIN_AVAILABLITY')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACTIONS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ACTIONS')}}
											</th>
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
	<div id="myModal" class="modal">
		<span class="close">&times;</span>
		<img class="modal-content" id="img01">
		<div id="caption"></div>
	</div>
@section('script')
	<script>
        $('#category_name').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^A-Z a-z &]/g,'') ); }
        );
        /*function change() {
            var elem = document.getElementById("click-here");
            if (elem.text=="Add Page")
            {
            elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_LIST')}}";
		}
		else if (elem.text=="Manage List")
		{
		elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_PAGE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_PAGE') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_PAGE')}}";
		}
		else {
		elem.text = "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_LIST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_LIST')}}";
		}
	}*/

        $('#block_value').click(function(){
            multi_action('0');
        });

        /** multiple unblock **/
        $('#unBlock_value').click(function(){
            multi_action('1');
        });
        /** multiple delete **/
        $('#delete_value').click(function(){
            multi_action('2');
        });
        function multi_action(status)
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
                url :"<?php echo url("mer_multi_item_block"); ?>",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data:{'val':val,'status':status},
                success:function(response){
                    //$("#loading-image").hide();
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
	<script>
        var table='';
        $(document).ready(function () {

            /*$('#dataTables-example').dataTable({
                "bPaginate": false,
                "bLengthChange": false,
                //"scrollX": true,
                "bFilter": true,
                "bInfo": false,
            "bAutoWidth": false });*/
            table = $('#dataTables-example').DataTable({
                "processing": true,

                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,
                "ajax":{
                    "url": "{{ url('mer-ajax-product') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",'pdtStore_search': function(){return $("#pdtStore_search").val(); },'pdtCode_search': function(){return $("#pdtCode_search").val(); },pdtName_search:function(){return $("#pdtName_search").val(); },pdt_status:function(){return $("#pdt_status").val(); },addedBy_search:function(){return $("#addedBy_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,6,8],
                    "orderable": false
                } ],
                "order": [ 0, 'desc' ],
                "columns": [
                    { "data": "check", sWidth: '5%' },
                    { "data": "storeName", sWidth: '12%' },
                    { "data": "pdtCode", sWidth: '9%' },
                    { "data": "pdtName", sWidth: '16%' },
                    { "data": "price", sWidth: '7%' },
                    { "data": "discPrice", sWidth: '7%' },
                    { "data": "itemImage", sWidth: '15%'},
                    { "data": "stock", sWidth: '6%'},
                    { "data": "action", sWidth: '8%'},
                    { "data": "addedBy", sWidth: '15%'},
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });
            $('#pdtStore_search, #pdtCode_search, #pdtName_search').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });
            $('#pdt_status, #addedBy_search').change(function(){
                table.draw();
            });
        });
	</script>
	<script>
        $("#validate_form").validate({
            rules: {
                cate_name: "required"
            },
            messages: {
                cate_name: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME')}}"
            }
        });


	</script>
	<script>
        /*$(document).ready(function(){
            $('.item_image').click(function(){
            var modal = document.getElementById('myModal');
            var img = document.getElementById(this.id);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
            });
            $('.close').click(function(){
            var modal = document.getElementById('myModal');
            modal.style.display = "none";
            });
        });*/

	</script>
@endsection
@stop