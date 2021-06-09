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
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:20px">
								<div id="location_form" class="collapse in panel-body">
									{{--Edit page values--}}
									@php $coupon_name = $coupon_code = $coupon_percent = $coupon_start_date = $coupon_end_date = $coupon_desc_en = $coupon_id = $readonly = $disabled = ''; 
									$coupon_customer = null;
									@endphp
									@if($id != '' && empty($coupon_detail) === false)
									@php 
									$coupon_name = $coupon_detail->coupon_name;
									$coupon_code = $coupon_detail->coupon_code;
									$coupon_percent = $coupon_detail->coupon_percent;
									$coupon_start_date = $coupon_detail->coupon_start_date;
									$coupon_end_date = $coupon_detail->coupon_end_date;
									$coupon_customer = explode(',',$coupon_detail->coupon_customer);
									$coupon_desc_en = $coupon_detail->coupon_desc_en;
									$coupon_id = $coupon_detail->id;
									$readonly = 'readonly';
									$disabled = 'disabled';
									@endphp
									@endif
									{{--Edit page values--}}
									<div class="row-fluid well">
										@if($id != '' && empty($coupon_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-coupon','enctype'=>'multipart/form-data','id'=>'coupon_form']) !!}
										{!! Form::hidden('coupon_id',$coupon_id)!!}
										@else
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-coupon','enctype'=>'multipart/form-data','id'=>'coupon_form']) !!}
										{!! Form::hidden('coupon_id',$coupon_id)!!}
										@endif
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_NAME')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::text('coupon_name',$coupon_name,['class'=>'form-control','required',$readonly]) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_CODE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::text('coupon_code',$coupon_code,['class'=>'form-control','required',$readonly]) !!}
												
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_PERCENTAGE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::text('coupon_percent',$coupon_percent,['class'=>'form-control','required','onkeypress'=>'return isNumberKey(event)','onchange'=>'handleChange(this)',$readonly]) !!}
												
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_START_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_START_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_START_DATE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::text('coupon_start_date',$coupon_start_date,['class'=>'form-control','required','id'=>'date_foo']) !!}
												
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_END_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_END_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_END_DATE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::text('coupon_end_date',$coupon_end_date,['class'=>'form-control','required','id'=>'date_end']) !!}
												
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_CUSTOMER')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::select('coupon_customer[]',$user_list,$coupon_customer,['class'=>'form-control','required','multiple',$disabled]) !!}
												<p>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_Notification')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_Notification') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_CUSTOMER_Notification') }}</p>
											</div>
										</div>
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_DESC')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												{!! Form::textarea('coupon_desc_en',$coupon_desc_en,['class'=>'form-control','required']) !!}
											</div>
										</div>

										<div class="panel-heading">
											
											@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											@if($id!='')
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-coupon'); ?>'">
											@else
												<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-coupon'); ?>'">
											@endif
										</div>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
							{{-- Add page ends--}}
							
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


<script type="text/javascript">
 	$("#coupon_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			coupon_name: "required",
			coupon_code: "required",
			coupon_percent: {
				required: true,
				number: true
			},
			coupon_start_date:"required",
			coupon_end_date:"required",
			coupon_customer: "required",
			coupon_desc_en: "required",

			
		},
		messages: {
			coupon_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_NAME_VAL')}}",
			coupon_code: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_CODE_VAL')}}",
			coupon_percent: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_PERCENTAGE_VAL')}}",
			coupon_start_date: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_SDATE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_SDATE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_SDATE_VAL')}}",
			coupon_end_date: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_EDATE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_EDATE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_EDATE_VAL')}}",
			coupon_customer: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_CUSTOMER_VAL')}}",
			coupon_desc_en: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUPON_DESC_VAL')}}",
		}
	});
 </script>

		<script type="text/javascript">
		function isNumberKey(evt)
		{
		    var charCode = (evt.which) ? evt.which : event.keyCode;
		    //alert(charCode);  
		    if (charCode > 31 && (charCode < 48 || charCode > 57 ) )
		    {
		       /*  if(charCode!=46)*/
		            return false; 
		    }
		    
		 return true;
		  
		}
		function handleChange(input) {
	        if (input.value < 0) input.value = 0;
	        if (input.value > 99) input.value = 99;
      	}
		</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		 <script src="{{url('')}}/public/admin/assets/scripts/jquery.simple-dtpicker.js"></script>
		 <script type="text/javascript">
		 	 $('#date_foo').appendDtpicker({
	            "autodateOnStart": true,
	            "futureOnly": true
		        });

	        $('#date_end').appendDtpicker({
	            "autodateOnStart": true,
	            "futureOnly": true
	        });
		 </script>
@endsection
@stop