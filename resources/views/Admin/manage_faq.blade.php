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
							
							
							{{-- Manage list starts--}}
							<div class="panel-body" id="location_table">
								{{-- 
								<div class="panel-heading p__title">
									Manage List
								</div>
								--}}
								
								<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
									<thead>
										<tr>
											
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_QUESTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_QUESTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_QUESTION')}}
											</th>
											@if((isset($FAQ) && is_array($FAQ)) && in_array('2', $FAQ) || $allPrev == '1')
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
											</th>
											@endif
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
											</th>
											@if((isset($FAQ) && is_array($FAQ)) && in_array('3', $FAQ) || $allPrev == '1')
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>
											@endif
										</tr>
									</thead>
									<tbody>
										@if(count($all_details) > 0)
										@php $i = ($all_details->currentpage()-1)*$all_details->perpage()+1;  
										$co_name = 'co_name'; 
										@endphp
										@foreach($all_details as $details)
										<tr>
											
											<td>{{$i}}</td>
											<td>{{ ucfirst($details->faq_name_en) }}</td>
											
											@if((isset($FAQ) && is_array($FAQ)) && in_array('2', $FAQ) || $allPrev == '1')
										    <td>
												<a href="{!! url('edit-faq').'/'.base64_encode($details->id) !!}"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>
											</td>
											@endif
											
											<td>
												@if($details->faq_status == 1)  {{--0-block, 1- active --}}
												<a href="{!! url('faq_status').'/'.$details->id.'/0' !!}"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>
												@else
												<a href="{!! url('faq_status').'/'.$details->id.'/1' !!}"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" onClick="change_status($details->id)"></i></a>
												@endif
											</td>
											
											@if((isset($FAQ) && is_array($FAQ)) && in_array('3', $FAQ) || $allPrev == '1')
											<td>
												
												<a href= "{!! url('faq_status').'/'.$details->id.'/2' !!}" title="delete" class="tooltip-demo">
													<i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i>
													
												</a>
											</td>
											@endif
											
										</tr>
										@php $i++; @endphp
										@endforeach
										
										@endif
									</tbody>
								</table>
								@if(count($all_details) > 0)
									{!! $all_details->appends(\Input::except('page'))->render() !!}
								@endif
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
	$(document).ready(function() {
		$('#dataTables-example').DataTable({
			
			"bPaginate": false,
			"responsive": true,
			//"scrollX": true,
		    "bLengthChange": true,
		    "bFilter": true,
		    "bInfo": true,
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
	$(document).ready(function(){
	   	//$("#location_form").hide();
	   	/*$("#click-here").click(function(){	
	   		$("#location_form").toggle();			
	   		//$("#location_table").toggle();
		});*/
	   	var tech = getUrlParameter('addnow');
	   	if(tech==1)
	   	{
	   		$('#location_form').addClass('in');
		}
	   	
	});
	
	// function change() {
	// document.getElementById("click-here").text="Manage List";
	// }
	
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
	function new_change()
	{
	   	location.href='{{url('admin-banner-settings?addnow=1')}}';
	   	
	}
</script>
<script type="text/javascript">
	$('#cnty_name').keyup(function(){
	   	var key = $('#cnty_name').val();
	   	$('#cnty_code').val('');
		$('#curr_sym').val('');
		$('#curr_code').val('');
		$('#tel_code').val('');
		if(key == '')
		{
			return;
		}
		
		$.ajax({
			type: 'get',
			url: '{{url('array_search_country')}}',
			data: {searched_country : key},
			success: function(response){
				myContent=response;
				
				result=$(myContent).text();
				
				if($.trim(result)==''){
					
					$("#suggesstion-box").show();
					$("#suggesstion-box").text("No country found!");
					return false;
				}
				
	            // alert(response);
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(response);
				$("#cnty_name").css("background","#FFF");
				
			}
		});
	});
	
	function selectCountry(val) {
		
		$("#cnty_name").val(val);
		$("#suggesstion-box").hide();
		var searched_country_name=val;
		
		$.ajax({
			type: 'get',
			url: '{{url('add_searched_country')}}',
			data: {'searched_country_name': searched_country_name},
			
			success: function(response){
	            //alert(response); return false;
				id_numbers = response.split('||');
	            var Country_code=id_numbers[0];
	            var Country_name=id_numbers[1];
	            var currency_symbol=id_numbers[2];
	            var currency_code=id_numbers[3];
	            var dial_code=id_numbers[4];
				
	            $('#cnty_code').val(Country_code);
				$('#curr_sym').val(currency_symbol);
				$('#curr_code').val(currency_code);
				$('#tel_code').val('+'+dial_code);
				
			}
		}); 
	}
	
	
	$('#cnty_name').bind('keyup blur',function(){ 
		var node = $(this);
	node.val(node.val().replace(/[^a-z]/g,'') ); }
	);
</script>
<script>
	function make_default(val)
	{	
	   	$.ajax({
			type: 'get',
			url: 'country_default',
			data: {'co_id': val},
			success: function(response){
				location.reload();
				
			}
		});
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