@extends('sitemerchant.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')

<!-- MAIN -->
<link rel="stylesheet" type="text/css" media="all" href="{{url('')}}/public/css/daterangepicker.css"/>
<script type="text/javascript" src="{{url('')}}/public/js/moment.js"></script>
<script type="text/javascript" src="{{url('')}}/public/js/daterangepicker.js"></script>

<style type="text/css">
	.daterangepicker {
		position: absolute;
	}

	@media (max-width: 740px) {
		.daterangepicker {
			height: 320px;
			width: 280px;
		}
	}
	footer{width: 100%; float: none;}
</style>
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-6">
						<div class="location panel">
							<div class="panel-heading p__title">
								@if($featured_store=='0')
									@php $singleDay = 0; @endphp
								@else
									{{$default_currency}} {{$featured_price}} {{(Lang::has(Session::get('mer_lang_file').'.MER_PER_DAY')) ? trans(Session::get('mer_lang_file').'.MER_PER_DAY') : trans($MER_OUR_LANGUAGE.'. MER_PER_DAY')}}
									@php $singleDay = $featured_price; @endphp
								@endif
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
							
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							@endif
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced box-body-padding" >
								<div id="location_form" class="panel-body">
									<div class="row-fluid" style="margin-bottom:25px;">
										
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => '','id'=>'validate_form','enctype' => 'multipart/form-data','autocomplete'=>"off"]) !!}
											@if($featured_store=='0')
												<div class="alert alert-success alert-dismissible" role="alert">
													{{(Lang::has(Session::get('mer_lang_file').'.MER_ADMIN_DISABLED_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_ADMIN_DISABLED_FEATURED') : trans($MER_OUR_LANGUAGE.'.MER_ADMIN_DISABLED_FEATURED')}}
												</div>
											@else
												<div class="form-group">
													<label for="name" class ="control-label col-sm-3">{{(Lang::has(Session::get('mer_lang_file').'.MER_FROM_DATE')) ? trans(Session::get('mer_lang_file').'.MER_FROM_DATE') : trans($MER_OUR_LANGUAGE.'.MER_FROM_DATE')}}:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="from_date" name="from_date" placeholder="mm/dd/yyyy" autocomplete="off">
													</div>
													
													<label for="name" class ="control-label col-sm-2">{{(Lang::has(Session::get('mer_lang_file').'.MER_TO_DATE')) ? trans(Session::get('mer_lang_file').'.MER_TO_DATE') : trans($MER_OUR_LANGUAGE.'.MER_TO_DATE')}}:</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="to_date" name="to_date" placeholder="mm/dd/yyyy" autocomplete="off">
													</div>
												</div>
												<hr />

												<div class="form-group">
													<label for="email" class ="control-label col-sm-3">{{(Lang::has(Session::get('mer_lang_file').'.MER_TOTAL_PRICE')) ? trans(Session::get('mer_lang_file').'.MER_TOTAL_PRICE') : trans($MER_OUR_LANGUAGE.'.MER_TOTAL_PRICE')}} ({{$default_currency}} ):</label>
													<div class="col-sm-3">
														<input type="text" class="form-control" id="total_price" name="total_price" readonly placeholder="0.00" value="0.00">
														<input type="hidden" name="feat_num_days" id="feat_num_days" />
														<div> <span id="total_noday_span">0</span> {{(Lang::has(Session::get('mer_lang_file').'.MER_DAYS')) ? trans(Session::get('mer_lang_file').'.MER_DAYS') : trans($MER_OUR_LANGUAGE.'. MER_DAYS')}}</div>
													</div>
												</div>
												<hr />
												
												<div class="form-group">
													<label for="pwd" class ="control-label col-sm-3">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PAYMENT_METHODS')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAYMENT_METHODS') : trans($MER_OUR_LANGUAGE.'.ADMIN_PAYMENT_METHODS')}}:</label>
													<div class="col-sm-8">
														<div class="">
															@if(empty($payment_details)==true)
															@else
																@if($payment_details->netbank_status=='Publish')
																<div class="radio">
																	<label> <input type="radio" name="payment_method" id="payment_method" value="offline"> {{(Lang::has(Session::get('mer_lang_file').'.MER_NETBANKING')) ? trans(Session::get('mer_lang_file').'.MER_NETBANKING') : trans($MER_OUR_LANGUAGE.'.MER_NETBANKING')}} </label>
																</div>
																@endif
																<!-- @if($payment_details->paynamics_status=='1')
																	<input name="paynamics_client_id" id="paynamics_client_id" type="hidden" value="{{$payment_details->paynamics_client_id}}">
																	<input name="paynamics_secret_id" id="paynamics_secret_id" type="hidden" value="{{$payment_details->paynamics_secret_id}}">
																	<div class="radio">
																		<label> <input type="radio" name="payment_method" id="payment_method" value="paynamics"> {{(Lang::has(Session::get('mer_lang_file').'.MER_PAYNAMICS')) ? trans(Session::get('mer_lang_file').'.MER_PAYNAMICS') : trans($MER_OUR_LANGUAGE.'.MER_PAYNAMICS')}}</label>
																	</div>
																@endif
																@if($payment_details->paymaya_status=='1')
																	<input name="paymaya_client_id" id="paymaya_client_id" type="hidden" value="{{$payment_details->paymaya_client_id}}">
																	<input name="paymaya_secret_id" id="paymaya_secret_id" type="hidden" value="{{$payment_details->paymaya_secret_id}}">
																	<div class="radio">
																		<label> <input type="radio" name="payment_method" id="payment_method" value="paymaya"> {{(Lang::has(Session::get('mer_lang_file').'.MER_PAYMAYA')) ? trans(Session::get('mer_lang_file').'.MER_PAYMAYA') : trans($MER_OUR_LANGUAGE.'.MER_PAYMAYA')}} </label>
																	</div>
																@endif -->
															@endif
														</div>
														<div class="pre_order_label"></div>
													</div>
												</div>
												
												<hr />
												@if(empty($payment_details)==true)
													@php
													$netbank_status = '';
													$bank_name = '';
													$branch = '';
													$bank_accno = '';
													$ifsc = '';
												@endphp	
												@else
													@php
														$netbank_status = $payment_details->netbank_status;
														$bank_name 		= $payment_details->bank_name;
														$branch 		= $payment_details->branch;
														$bank_accno 	= $payment_details->bank_accno;
														$ifsc 			= $payment_details->ifsc;
													@endphp
												@endif
												<div class="well" id="netBanking_details" style="display:none">
													<div class="alert alert-success alert-dismissible" role="alert">
														<h4>{{(Lang::has(Session::get('mer_lang_file').'.MER_NETBANKING')) ? trans(Session::get('mer_lang_file').'.MER_NETBANKING') : trans($MER_OUR_LANGUAGE.'.MER_NETBANKING')}}  {{(Lang::has(Session::get('mer_lang_file').'.MER_DETAILS')) ? trans(Session::get('mer_lang_file').'.MER_DETAILS') : trans($MER_OUR_LANGUAGE.'.MER_DETAILS')}}</h4>
														<div class="form-group ">
															<div class="col-md-6"><label>{{(Lang::has(Session::get('mer_lang_file').'.MER_ACCNO')) ? trans(Session::get('mer_lang_file').'.MER_ACCNO') : trans($MER_OUR_LANGUAGE.'.MER_ACCNO')}}:</label>&nbsp; {{$bank_accno}}</div>
														</div>
														<div class="form-group ">
															<div class="col-md-6"><label>{{(Lang::has(Session::get('mer_lang_file').'.MER_BANKNAME')) ? trans(Session::get('mer_lang_file').'.MER_BANKNAME') : trans($MER_OUR_LANGUAGE.'.MER_BANKNAME')}}:</label>&nbsp; {{$bank_name}}</div>
														</div>
														<div class="form-group ">
															<div class="col-md-6"><label>{{(Lang::has(Session::get('mer_lang_file').'.MER_BRANCH')) ? trans(Session::get('mer_lang_file').'.MER_BRANCH') : trans($MER_OUR_LANGUAGE.'.MER_BRANCH')}}:</label>&nbsp; {{$branch}}</div>
														</div>
														<div class="form-group ">
															<div class="col-md-6"><label>{{(Lang::has(Session::get('mer_lang_file').'.MER_IFSC')) ? trans(Session::get('mer_lang_file').'.MER_IFSC') : trans($MER_OUR_LANGUAGE.'.MER_IFSC')}}:</label>&nbsp; {{$ifsc}}</div>
														</div>
													</div>
													<div class="form-group">
														<label for="email" class ="control-label col-sm-3">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID')}}:</label>
														<div class="col-sm-6">
															<input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID')}}">
														</div>
													</div>
												</div>
												<hr />
												
												<div class="col-sm-offset-3 col-sm-8">
													<button type="submit" class="btn btn-default" name="btn" id="submit_btn">{{(Lang::has(Session::get('mer_lang_file').'.MER_PAY')) ? trans(Session::get('mer_lang_file').'.MER_PAY') : trans($MER_OUR_LANGUAGE.'.MER_PAY')}} </button>
												</div>
												<div class="clearfix">&nbsp;</div>
											@endif
										{!! Form::close() !!}
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="location panel">
							<div class="panel-heading p__title">
								@php
									$instruction_text = (Lang::has(Session::get('mer_lang_file').'.MER_INSTRUCTION_ABOUT')) ? trans(Session::get('mer_lang_file').'.MER_INSTRUCTION_ABOUT') : trans($MER_OUR_LANGUAGE.'.MER_INSTRUCTION_ABOUT');
									echo str_replace(":business_type",$business_type,$instruction_text);
								@endphp
								
							</div>
							<div class="box-body spaced box-body-padding" >
								<div id="location_form" class="panel-body">
									<div class="row-fluid" style="margin-bottom:25px;padding:25px;">
									{!!(Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_INSTRUCTION')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_INSTRUCTION') : trans($MER_OUR_LANGUAGE.'.MER_FEATURED_INSTRUCTION')!!}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	{{-- Add/Edit page ends--}}
	
	
	@section('script')

	<script>
		$.validator.addMethod("valueNotEquals", function(value, element, arg){
			return arg !== value;
		}, "Value must not equal arg.");
		
		$("#validate_form").validate({
			rules: {
				from_date: "required",
				to_date: "required",
				payment_method: "required",
				transaction_id: {
					required: {
						depends: function(element) {
							if($('input[name="payment_method"]:checked').val()=='offline'){ return true; } else { return false; } 
						}
					}
				},
			},
			messages: {
				
				from_date: "{{(Lang::has(Session::get('mer_lang_file').'.MER_SEL_FROM_DATE')) ? trans(Session::get('mer_lang_file').'.MER_SEL_FROM_DATE') : trans($MER_OUR_LANGUAGE.'.MER_SEL_FROM_DATE')}}",
				to_date: "{{(Lang::has(Session::get('mer_lang_file').'.MER_SEL_TO_DATE')) ? trans(Session::get('mer_lang_file').'.MER_SEL_TO_DATE') : trans($MER_OUR_LANGUAGE.'.MER_SEL_TO_DATE')}}",
				payment_method: "{{(Lang::has(Session::get('mer_lang_file').'.MER_SEL_PMT_METHOD')) ? trans(Session::get('mer_lang_file').'.MER_SEL_PMT_METHOD') : trans($MER_OUR_LANGUAGE.'.MER_SEL_PMT_METHOD')}}",
				transaction_id: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_TRANXN_ID')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_TRANXN_ID') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_TRANXN_ID')}}"
			},
			errorPlacement: function(error, element) 
			{
				if ( element.is(":radio") ) 
				{
					error.appendTo( $('.pre_order_label') );
					//error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
				}
				else 
				{ // This is the default behavior 
					error.insertAfter( element );
				}
			 }
		});
		
		/* -------------------------- CHANGE FORM URL BASED ON PAYMENT METHOD SELECTION ---------- */
		$('#submit_btn').css({'display' : 'none'});
		$('input[name="payment_method"]').click(function() {
			var radioValue = $("input[name='payment_method']:checked").val();
			//alert(radioValue);
			if ($.trim(radioValue) == "offline") 
			{
				$('#netBanking_details').show();
				$('#submit_btn').css({'display' : 'block'});
				$('#validate_form').attr("action", "{{url('featured_offline_checkout')}}");
					
			} 
			else if ($.trim(radioValue) == "paymaya") 
			{
				$('#netBanking_details').hide();
				$('#submit_btn').css({'display' : 'block'});
				$('#validate_form').attr("action","{{url('featured_paymaya_checkout')}}");
			} 
			else if ($.trim(radioValue) == "paynamics") 
			{
				$('#netBanking_details').hide();
				$('#submit_btn').css({'display' : 'block'});
				$('#validate_form').attr("action","{{url('featured_paynamics_checkout')}}");
			}
		});
	</script>	
	<script>
	updateConfig();
	var BookedDates = [{!!$booked_Dates!!}];
	function updateConfig() {
		var options = {
			format: 'MM/DD/YYYY',
			singleDatePicker: true,
			autoUpdateInput: false,
			minDate: new Date(),
			isInvalidDate: function (date) {
				var formatted = date.format('MM/DD/YYYY');
				return BookedDates.indexOf(formatted) > -1;
			}
		};
		chkInDate = '';
		chkInDate1 = '';
		$('#from_date').daterangepicker(options, function (start, end, label) {
			$('#from_date').val(start.format('MM/DD/YYYY'));
			chkInDate = start.format('MM/DD/YYYY');
			//getQuote();
		});
		$('#from_date').on('apply.daterangepicker', function (ev, picker) {
			$('#to_date').val('');
			$('#feat_num_days').val('');
			$('#total_price').val('');
			$('#total_noday_span').html('0');
			chkoutFunction();
			//getQuote();
		});
		$('#to_date').on('apply.daterangepicker', function (ev, picker) {
			$(this).val(picker.startDate.format('MM/DD/YYYY'));
			getQuote();
		});

		function chkoutFunction() {
			$('#to_date').daterangepicker({
				"format": 'MM/DD/YYYY',
				"singleDatePicker": true,
				"autoUpdateInput": false,
				"minDate": chkInDate,
				isInvalidDate: function (date) {
					var formatted = date.format('MM/DD/YYYY');
					return BookedDates.indexOf(formatted) > -1;
				}
			}, function (start, end, label) {
				$('#to_date').val(start.format('MM/DD/YYYY'));
				getQuote();
			});
		}
	}
	
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}

	function daydiff(first, second) {
		return ((second-first)/(1000*60*60*24)+1)
	}
	function getQuote() {

		var checkIn = $('#from_date');
		var checkOut = $('#to_date');
		var singleDaySpan = '{{$singleDay}}';

		if (checkIn.val() == "") {
			checkIn.focus();
			return false;
		} else if (checkOut.val() == "") {
			checkOut.focus();
			return false;
		} else {
			var daysDiff = daydiff(parseDate(checkIn.val()), parseDate(checkOut.val()));
			var total_price = parseFloat(daysDiff)*parseFloat(singleDaySpan);
			$('#total_noday_span').html(daysDiff);
			$('#feat_num_days').val(daysDiff);
			$('#total_price').val(total_price);
		}
	}
	</script>
	@endsection
@stop						