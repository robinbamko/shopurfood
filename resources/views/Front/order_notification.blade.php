@extends('Front.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<div class="main-sec">
	
	<div class="section9-inner">
		<div class="container userContainer">
			<div class="row"> 
				<div class="col-lg-12">
					<h5 class="sidebar-head">             
						{{$pagetitle}}            
					</h5>
				</div>	
				<div class="userContainer-bg row">
	
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div wow -zoomIn"> 
						<div class="row panel-heading">
							
							<div class="my-order-table col-12">
								<div class="table-responsive form-group order-notify-tble">
									<table class="table" id="dataTables-example">
										<thead>
											<tr>
												<td>
													@php
													$addedByArray[''] = (Lang::has(Session::get('front_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('front_lang_file').'.ADMIN_SELECT') : trans($FRONT_LANGUAGE.'.ADMIN_SELECT');
													$addedByArray['0']=(Lang::has(Session::get('front_lang_file').'.FRONT_UNREAD_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_UNREAD_NOTIFICATION') : trans($FRONT_LANGUAGE.'.FRONT_UNREAD_NOTIFICATION');
													$addedByArray['1']=(Lang::has(Session::get('front_lang_file').'.FRONT_READ_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_READ_NOTIFICATION') : trans($FRONT_LANGUAGE.'.FRONT_READ_NOTIFICATION');
													@endphp
													{{ Form::select('view_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'view_search'] ) }}
												</td>
												<td><input type="text" id="orderId_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
												<td><input type="text" id="message_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr >
												<th style="text-align:center" class="sorting_no_need">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SNO')) ? trans(Session::get('front_lang_file').'.ADMIN_SNO') : trans($FRONT_LANGUAGE.'.ADMIN_SNO')}}
												</th>
												<th style="text-align:center">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_ID') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_ID')}}
												</th>
												<th style="text-align:center">
													{{(Lang::has(Session::get('front_lang_file').'.FRONT_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_NOTIFICATION') : trans($FRONT_LANGUAGE.'.FRONT_NOTIFICATION')}}
												</th>
												<th style="text-align:center">
													{{(Lang::has(Session::get('front_lang_file').'.FRONT_READ_STATUS')) ? trans(Session::get('front_lang_file').'.FRONT_READ_STATUS') : trans($FRONT_LANGUAGE.'.FRONT_READ_STATUS')}}
												</th>
												
												<th style="text-align:center">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_DATE') : trans($FRONT_LANGUAGE.'.ADMIN_DATE')}}
												</th>
												<th>
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_VIEW') : trans($FRONT_LANGUAGE.'.ADMIN_VIEW')}}
												</th>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
								</div>
	
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
	</div>          
	
	
	
</div>

@section('script')
<!--  -->
<!--<link rel="stylesheet" href="http://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css">
<script src = "http://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>-->
	 <!-- DataTables CSS -->
<link rel="stylesheet" href="{{url('')}}/public/front/css/jquery.dataTables.min.css">

	<!-- DataTables Responsive CSS -->

<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>


<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">


<script src = "https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src = "https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>-->




<script>
	var table='';
	$(document).ready(function () {
		table = $('#dataTables-example').DataTable({
					"processing": true,					
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false,
					"ajax":{
						"url": "{{ url('notification-list-customer') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ },
						"data":{ _token: "{{csrf_token()}}",'view_search': function(){return $("#view_search").val(); }, 'orderId_search': function(){return $("#orderId_search").val(); },message_search:function(){return $("#message_search").val(); }}
					},
					"columnDefs": [ {
						"targets": [0,5],
						"orderable": false
					} ],
					"order": [ 0, 'asc' ],
					"columns": [
								{ "data": "SNo", sWidth: '10%'/*, className:function(data,type,row) { console.log(row); return 'info';}*/ },
								{ "data": "orderId", sWidth: '12%' },
								{ "data": "message", sWidth: '48%' },
								{ "data": "readstatus", sWidth: '12%' },
								{ "data": "date", sWidth: '8%' },
								{ "data": "view", sWidth: '8%' }
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'top'});
					},
					createdRow: function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data.readstatus=='Unread'){
							$( row ).addClass('danger');
						}
					}
				});
		$('#orderId_search, #message_search').keyup( function() {
			table.draw();
		});
		$('#view_search').change( function() {
			table.draw();
		});
	 });
	 function change_status(gotId){
		//alert(gotId);
		$.ajax({
			type:'get',
			url :"<?php echo url("notification_status_customer"); ?>",
			data:{'gotId':gotId},
			success:function(response){
				table.draw();
			}
		}); 
	 }
</script>
@endsection
@stop

