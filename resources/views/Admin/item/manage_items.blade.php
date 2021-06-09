@extends('Admin.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')
	<!-- MAIN -->
	<style>

		a.label {
			width: 100%;
			float: left;
		}
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


	</style>
	@php extract($privileges); @endphp
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
										<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
									</div>
									<div id="successMsgRole"></div>
								</div>
								
								{{--Manage page starts--}}
								<div class="panel-body" id="location_table" >
									<div id="loading-image" style="display:none">
										<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
									</div>
								<div class="top-button top-btn-full"  style="position:relative">
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
										
										@if((isset($Item) && is_array($Item)) && in_array('3', $Item) || $allPrev == '1')
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
										@endif
										<a href="{{url('download_item_list/csv')}}">
											{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
										</a>
									</div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>
											<td><input type="text" id="pdtName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											

											<td><input type="text" id="pdtCode_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
											<td><input type="text" id="pdtStore_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												@php
													$statusArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$statusArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_AVAILABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AVAILABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AVAILABLE');
													$statusArray['2']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SOLD')) ? trans(Session::get('admin_lang_file').'.ADMIN_SOLD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SOLD');
												@endphp
												{{ Form::select('pdt_status',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'pdt_status'] ) }}
											</td>
											<td>&nbsp;</td>
											<td>@php
													$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['admin']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
													$addedByArray['merchant']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
												@endphp
												{{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}</td>
										</tr>
										<tr>
											<th  style="text-align:center">{!! Form::checkbox('chknone[]','','',['id' => 'check_all','class'=>'checkboxclass','onchange' =>'checkAll(this)'])!!}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_NAME')}}
											</th>
											
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CODE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORIGINAL_PRICE')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISC_PRICE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_IMAGE')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_AVAILABLITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_AVAILABLITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AVAILABLITY')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIONS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIONS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACTIONS')}}
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

        // $('#block_value').click(function(){
        //     multi_action('0');
        // });

        // /** multiple unblock **/
        // $('#unBlock_value').click(function(){
        //     multi_action('1');
        // });
        // /** multiple delete **/
        // $('#delete_value').click(function(){
        //     multi_action('2');
        // });
        // function multi_action(status)
        // {
        //     $(".rec-select").css({"display" : "none"});
        //     var val = [];
        //     val[0]='';
        //     $('input[name="chk[]"]:checked').each(function(i){
        //         var j=i+1;
        //         //alert(j);
        //         val[j] = $(this).val();
        //     });
        //     if(val=='')
        //     {
        //         $(".rec-select").css({"display" : "block"});
        //         return;
        //     }

        //     $.ajax({
        //         type:'get',
        //         url :"<?php echo url("multi_item_block"); ?>",
        //         beforeSend: function() {
        //             $("#loading-image").show();
        //         },
        //         data:{'val':val,'status':status},
        //         success:function(response){
        //             //$("#loading-image").hide();
        //             location.reload();
        //         }
        //     });
        // }


     $('#block_value').click(function(){
		$(".rec-select").css({"display" : "none"});
		var val = [];
		$('input[name="chk[]"]:checked').each(function(i){
			val[i] = $(this).val();
		});
		if(val=='')
		{
			$(".rec-select").css({"display" : "block"});
			return;
		}
		multi_action(val,'0');
	});
	
	/** multiple unblock **/
	$('#unBlock_value').click(function(){
		$(".rec-select").css({"display" : "none"});
		var val = [];
		$('input[name="chk[]"]:checked').each(function(i){
			val[i] = $(this).val();
		});
		if(val=='')
		{
			$(".rec-select").css({"display" : "block"});
			return;
		}
		multi_action(val,'1');
	});
	/** multiple delete **/
	$('#delete_value').click(function(){
		$(".rec-select").css({"display" : "none"});
		var val = [];
		$('input[name="chk[]"]:checked').each(function(i){
			val[i] = $(this).val();
		});
		if(val=='')
		{
			$(".rec-select").css({"display" : "block"});
			return;
		}
		multi_action(val,'2');        
	});

	function individual_change_status(gotVal,gotStatus)
	{
		var val = [];
		val[0]=gotVal;
		multi_action(val,gotStatus)
	}

	function multi_action(val,status)
	{

		$.ajax({
			type:'get',
			url :"<?php echo url("multi_item_block"); ?>",
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
						//$('#statusLink_'+val[i]).attr("href", "javascript:individual_change_status('"+val[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i> Block');
						//$('.tooltip-demo').tooltip({placement:'left'})
						table.draw(false);
					}
				}
				else if(status==1)
				{
					for(var i=0;i<val.length;i++)
					{
						//$('#statusLink_'+val[i]).attr("href", "javascript:individual_change_status('"+val[i]+"',0)").html('<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i> Unblock');
						//$('.tooltip-demo').tooltip({placement:'left'})
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

        function change_pro_status(pro_id,status,type)
        {
            if(confirm("Are sure want to "+type+" ? "))
            {
                $.ajax({
                    'type'	:'POST',
                    'data'	: {'id' : pro_id,'status' : status},
                    'url'	: '{{url('item_status')}}',
                    success:function(response)
                    {	//alert(response); return false;
                        window.location.reload();
                    }
                });
            }

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
                    "url": "{{ url('ajax-item-list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "beforeSend":function(){ $('#check_all').prop('checked',false); },
                    "data":{ _token: "{{csrf_token()}}",'pdtStore_search': function(){return $("#pdtStore_search").val(); },'pdtCode_search': function(){return $("#pdtCode_search").val(); },pdtName_search:function(){return $("#pdtName_search").val(); },pdt_status:function(){return $("#pdt_status").val(); },addedBy_search:function(){return $("#addedBy_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,6,8],
                    "orderable": false
                } ],
                "columns": [
                    { "data": "check", sWidth: '10%' },
                    { "data": "pdtName", sWidth: '15%' },
                    { "data": "pdtCode", sWidth: '10%' },
                    { "data": "storeName", sWidth: '15%' },
                    { "data": "price", sWidth: '7%' },
                    { "data": "discPrice", sWidth: '7%' },
                    { "data": "itemImage", sWidth: '15%'},
                    { "data": "stock", sWidth: '6%'},
                    { "data": "action", sWidth: '5%'},
                    { "data": "addedBy", sWidth: '10%'},
                ],
                "order": [ 1, 'desc' ],
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
@endsection
@stop