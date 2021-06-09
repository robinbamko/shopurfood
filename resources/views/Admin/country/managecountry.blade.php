@extends('Admin.layouts.default')
@section('PageTitle')
  @if(isset($pagetitle))
  {{$pagetitle}}
  @endif
  @stop
@section('content')
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
								<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if($id!='')  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form"> {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOC_ADD')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOC_ADD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOC_ADD')}}</a>
							</div>
							
							{{-- Display error message--}}
							<div class="err-alrt-msg">
							@if ($errors->has('errors')) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ $errors->first('errors') }}
							</div>
							@endif
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							</div>
							@endif
							
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced box-body-padding">
								<div id="location_form" class="collapse @php  if($id!=''){ echo 'in';} @endphp panel-body">
									{{--Edit page values--}}
									@php $cnty_code = $curr_code = $curr_sym = $tele_code =  $co_id = ''; 
									@endphp
									@if($id != '' && empty($country_detail) === false)
									@php 
									$cnty_code = $country_detail->co_code;
									$curr_code = $country_detail->co_curcode;
									$curr_sym = $country_detail->co_cursymbol;
									$tele_code = $country_detail->co_dialcode;
									$def_cnty = $country_detail->default_counrty;
									$status = $country_detail->co_status;
									$co_id = $country_detail->id;
									@endphp
									@endif
									{{--Edit page values--}}
									<div class="row-fluid well">
										@if($id != '' && empty($country_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'update-country']) !!}
										{!! Form::hidden('cnty_id',$co_id)!!}
										@else
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-country']) !!}
										@endif
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_NAME')}}&nbsp; (In {{$default_lang}})*
												</span>
											</div>
											<div class="col-md-5">
												@php $cnty_name = ''; @endphp
												@if($id != '' && empty($country_detail) === false)
												@php 
												$cnty_name = $country_detail->co_name;
												@endphp
												@endif
												{!! Form::text('co_name',$cnty_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_CO_NAME'),'id' => 'cnty_name','required']) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_NAME')}}&nbsp; (In {{$lang->lang_name}})
												</span>
											</div>
											<div class="col-md-5">
												@php $cnty_name = ''; @endphp
												@if($id != '' && empty($country_detail) === false)
												@php $lang_code = 'co_name_'.$lang->lang_code;
												$cnty_name = $country_detail->$lang_code;
												@endphp
												@endif
												{!! Form::text('co_name_'.$lang->lang_code,$cnty_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_CO_NAME'),'id' => 'cnty_name']) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										@endforeach
										@endif
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_CODE')}}
												</span>
											</div>
											<div class="col-md-5">
												{!! Form::text('co_code',$cnty_code,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'cnty_code','readonly','required']) !!}
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_SYM')}}
												</span>
											</div>
											<div class="col-md-5">
												{!! Form::text('cur_sym',$curr_sym,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'curr_sym','readonly','required']) !!}
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}
												</span>
											</div>
											<div class="col-md-5">
												{!! Form::text('cur_code',$curr_code,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'curr_code','readonly','required']) !!}
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-3">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIAL_CODE')}}
												</span>
											</div>
											<div class="col-md-5">
												{!! Form::text('tel_code',$tele_code,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'tel_code','readonly','required']) !!}
											</div>
										</div>
										<div class="panel-heading col-md-offset-3">
											
											@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											@if($id!='')
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-country'); ?>'">
											@else
												{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'data-toggle'=>"collapse", 'data-target'=>"#location_form"])!!}
											@endif
										</div>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
							{{-- Add page ends--}}
							{{-- Manage list starts--}}
							<div class="panel-body location-scroll" id="location_table" >
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
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_NAME')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_CODE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_SYM')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIAL_CODE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DEFAULT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEFAULT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEFAULT')}}
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
										@if(count($all_details) > 0)
										@php
										 $i = ($all_details->currentpage()-1)*$all_details->perpage()+1;  
										$co_name = 'co_name'; 
										@endphp
										@foreach($all_details as $details)
										<tr>
											<td>{{$i}}</td>
											<td>{{ ucfirst($details->$co_name)}}</td>
											<td>{{$details->co_code}}</td>
											<td>{{$details->co_curcode}}</td>
											<td>{{$details->co_cursymbol}}</td>
											<td>{{$details->co_dialcode}}</td>
											<td>
												@if($details->co_status == 0) 
												<a href="" class="tooltip-demo" title="Unblock country to make as default">
													{!! Form::radio('',$details->id,($details->default_counrty == 1) ? true : '',['onClick' => 'make_default(this.value)','disabled'])!!} 
													@else
													<a href= "" title="Make default country" class="tooltip-demo">
														{!! Form::radio('',$details->id,($details->default_counrty == 1) ? true : '',['onClick' => 'make_default(this.value)'])!!} 
														@endif
													</a>	
												</td>
												<td>
													<a href="{!! url('edit_country').'/'.base64_encode($details->id) !!}"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>
												</td>
												<td>
													@if($details->co_status == 1 && $details->default_counrty == 1)
													<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Default Country Can't Be Block" ></i>
													@elseif($details->co_status == 1)  {{--0-block, 1- active --}}
													<a href="{!! url('country_status').'/'.$details->id.'/0' !!}"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>

													@else
													<a href="{!! url('country_status').'/'.$details->id.'/1' !!}"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" onClick="change_status($details->id)"></i></a>
													@endif
												</td>
												<td>
													@if($details->default_counrty == 1)  {{--0-none, 1- default --}}
													
													<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Default Country Can't Be Delete"></i> 
													@else
													<a href= "{!! url('country_status').'/'.$details->id.'/2' !!}" title="delete" class="tooltip-demo">
														<i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i>
														@endif
													</a>
												</td>
											</tr>
											@php $i++; @endphp
											@endforeach
											
											@endif
										</tbody>
									</table>
								</div>
								{{-- Manage list ends--}}
							</div>
							@if(count($all_details) > 0)
							{!! $all_details->render() !!}
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
		$('#d-table').DataTable({
			responsive: true
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
		var add = "{{trans(Session::get('admin_lang_file').'.ADMIN_LOC_ADD')}}";
		var manage = "{{trans(Session::get('admin_lang_file').'.ADMIN_MNGE_LIST')}}";
		//alert(add);
		if (elem.text== add)
		{ 
			elem.text = manage;
		}
		else if (elem.text==manage)
		{
			elem.text = add;
		}
		else {
			elem.text = manage;
		}
	}
	function new_change()
	{
	   	location.href='{{url('manage-country?addnow=1')}}';
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
@endsection
@stop