@extends('Admin.layouts.default')
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
							<div class="err-alrt-msg">
							@if ($errors->any())
							<div class="alert alert-warning alert-dismissible">
								<a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
								<ul>
									@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
							</div>
							@endif
							
							  <!-- ADMIN SUCCESS MESSAGE -->
								@if(Session::has('success'))
									<div class="alert alert-success alert-dismissible">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										{{Session::get('success')}}
									</div>	
								@endif
							<!-- END ADMIN SUCCESS MESSAGE -->
							
							{{-- NEWSLETTER TEMPLATE STARTS--}}
							<div class="panel-body" id="location_table">
								
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									 @if(count($subscriber_details)>0)
									<thead>
										<tr>
											
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBE_UNSUBSCRIBE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBE_UNSUBSCRIBE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBSCRIBE_UNSUBSCRIBE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>
											
										</tr>
									</thead>
									<tbody>
										 @php $i = 1 @endphp
										 @foreach($subscriber_details as $news)
										<tr>
											<td>{{$i}}</td>
											<td>{{$news->news_email_id	}}</td>
											<td>
												
												@if($news->news_status == 1)  
													
												<a href="{{ url('edit_newsletter_subscriber_status/'.$news->id.'/0') }}"> <i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i> </a>
												@else
								
												<a href="{{ url('edit_newsletter_subscriber_status/'.$news->id.'/1') }}"> <i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i> </a>

												@endif
									  
											</td>
										    <td class="text-center"><a href="{!! url('delete_newsletter_subscriber').'/'.$news->id!!}" data-tooltip="{{ (Lang::has(Session::get('admin_lang_file').'.BACK_DELETE')!= '') ?  trans(Session::get('admin_lang_file').'.BACK_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.BACK_DELETE') }}"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a></td>

										</tr>
										 @php $i++ @endphp
										 @endforeach
									</tbody>
									 @else 
										{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_TEMPLATE_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_TEMPLATE_FOUND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_TEMPLATE_FOUND') }}
									 @endif
								</table>
							</div>
							{{-- NEWSLETTER TEMPLATE ENDS--}}
						</div>
						@if(count($subscriber_details) > 0)
						{!! $subscriber_details->appends(\Input::except('page'))->render() !!}
						@endif
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
	$(document).ready(function() {
		$('#dataTables-example').DataTable({
			"responsive": true,
			"bPaginate": false,
			//"scrollX": true,
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		    "bAutoWidth": false ,
			aoColumnDefs: [
			  {
			     bSortable: false,
			     aTargets: [ -1,-3]
			  }
			]
		});
	});
</script>


<script>
	
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
</script>
<script type="text/javascript">
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
			url :"<?php echo url("multi_customer_block"); ?>",
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
				url :"<?php echo url("multi_customer_block"); ?>",
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
					url :"<?php echo url("multi_customer_block"); ?>",
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
		@endsection
	@stop		