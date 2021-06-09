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
@php extract($privileges); @endphp
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
							@if(Session::has('message'))
							<div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
							           {{ Session::get('message')}}
							        </ul>
							    </div>
							@endif
							
							{{-- Manage list starts--}}
							<div class="panel-body" id="location_table">
								{{-- 
								<div class="panel-heading p__title">
									Manage List
								</div>
								--}}
							
							<div class="err-alrt-msg">
								<div id="successMsgRole"></div>
								<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
								<button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
							</div>
							</div>
							<div id="loading-image" style="display:none">
								<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
  							</div>
								
								<div class="top-button top-btn-full" style="position:relative;">
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
									
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
									
									@if((isset($Customer) && is_array($Customer)) && in_array('3', $Customer) || $allPrev == '1')
									{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
									@endif
									
									<a href="{{url('export_customer/csv')}}">
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
									</a>
								</div>
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td><input type="text" id="cusName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td><input type="text" id="cusEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td>
												@php
													$addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['2']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
													$addedByArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_NORMAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NORMAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NORMAL');
													$addedByArray['4']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE');
													$addedByArray['3']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_FACEBOOK')) ? trans(Session::get('admin_lang_file').'.ADMIN_FACEBOOK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FACEBOOK');
												@endphp
												{{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}
											</td>
											<td>&nbsp;</td>
											<td>
												@php
													$statusArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
													$statusArray['1']='Published';
													$statusArray['0']='Unpublished';
												@endphp
												{{ Form::select('status_search',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'status_search'] ) }}
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}
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
		/*$('#dataTables-example').DataTable({
			"bPaginate": false,
			//"scrollX": true,
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		    "bAutoWidth": false
		});*/
		table = $('#dataTables-example').DataTable({
			"processing": true,
			"responsive": true,
			"serverSide": true,
			"bLengthChange": true,
			"bAutoWidth": false, 
			"searching": false,
			"ajax":{
				"url": "{{ url('ajax-customer-list') }}",
				"dataType": "json",
				"type": "POST",
				"beforeSend":function(){ $('#check_all').prop('checked',false); },
				"data":{ _token: "{{csrf_token()}}",'cusName_search': function(){return $("#cusName_search").val(); },cusEmail_search:function(){return $("#cusEmail_search").val(); },addedBy_search:function(){return $("#addedBy_search").val(); },status_search:function(){return $("#status_search").val(); }}
			},
			"columnDefs": [ {
				"targets": [0,5,7],
				"orderable": false
			} ],
			"columns": [
			{ "data": "checkBox", sWidth: '8%' },
			{ "data": "SNo", sWidth: '8%' },
			{ "data": "cusName", sWidth: '20%' },
			{ "data": "cusEmail", sWidth: '21%' },
			{ "data": "addedBy", sWidth: '12%' },
			{ "data": "Edit", sWidth: '9%' },
			{ "data": "Status", sWidth: '9%'},
			{ "data": "delete", sWidth: '9%'}
			],
			"order": [1, 'desc' ],
			"fnDrawCallback": function (oSettings) {
				$('.tooltip-demo').tooltip({placement:'left'})
			}
		});
		$('#cusName_search, #cusEmail_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#addedBy_search, #status_search').change(function(){
			table.draw();
		});
	});
</script>


<script type="text/javascript">

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
        blockUnblock(val,0);
    });

    /** multiple unblock **/
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
        blockUnblock(val,1);
    });
    /** multiple delete **/
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
        blockUnblock(val,2);
    });
    function blockUnblock(gotVal,gotStatus)
    {
        $.ajax({
            type:'get',
            url :"<?php echo url("multi_customer_block"); ?>",
            beforeSend: function() {
                $("#loading-image").show();
            },
            data:{'val':gotVal,'status':gotStatus},
            success:function(response){
                $("#loading-image").hide();
                $('#successMsgRole').show();
                $('#successMsgRole').focus();
                $('#successMsgInfo').html(response).show();
				$('#successMsgRole').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>'+response+'</div>').show();
                if(gotStatus==0)
                {
                    for(var i=0;i<gotVal.length;i++)
                    {
                        $('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i>');
                        $('.tooltip-demo').tooltip({placement:'left'})
                    }
                }
                else if(gotStatus==1)
                {
                    for(var i=0;i<gotVal.length;i++)
                    {
                        $('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',0)").html('<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i>');
                        $('.tooltip-demo').tooltip({placement:'left'})
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
        blockUnblock(val,gotStatus);
    }
</script>
		@endsection
	@stop	