@extends('Front.layouts.default')
@section('content')
<style type="text/css">
	.row .panel-heading{
	margin-bottom: 10px;
    }
	
	.message-box-conact{
    margin-top: 18px;
	}
	#contact_form_map ul.store_info{
    list-style: none;
    font-style: normal;
    color: #696969;
	}
	#contact_form_map ul{
    line-height: 28px;
    margin: auto;
    padding-left:2px;
	}
	.page-subheading{
    padding-left: 0px;
    border: none;
    margin: 14px 0 15px;
    text-transform: capitalize;
    font-size: 15px;
    color: #333;
	}
</style> 
<div class="main-sec">    
<div class="section9-inner">
	<div class="container userContainer">
		<div class="row">
			<!--<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 contact-title">
				<h2>CONTACT US</h2>
			</div>-->
			<div class="message-box-conact"></div>
			
			<div class="row contact-page-inner">
				
				<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12 contact-left"  id="contact_form_map">
					
					<h3>{{(Lang::has(Session::get('front_lang_file').'.FRONT_CONT_US')) ? trans(Session::get('front_lang_file').'.FRONT_CONT_US') : trans($FRONT_LANGUAGE.'.FRONT_CONT_US')}}</h3>
					<!--<div class="contact-email">
						<h5>Email</h5>
						<p><i class="fa fa-envelope"></i>{{$SITEEMAIL}}</p>
						</div>
						<div class="contact-phone">
						<h5>Phone</h5>
						<p><i class="fa fa-phone"></i>{{$SITEPHONE}}</p>
						</div>
						<div class="contact-web">
						<h5>Web</h5>
						<a target="_blank" href="{{ url('/') }}"><p><i class="fa fa-globe"></i>{{ url('/') }}</p></a>
					</div>-->
					
					<ul class="store_info">
						<li><i class="fa fa-envelope"></i>{{$SITEEMAIL}}</li>
						
						<li><i class="fa fa-phone"></i><span>{{$SITEPHONE}}</span></li>
						
						<li><a target="_blank" href="{{ url('/') }}"><i class="fa fa-globe"></i><span>{{ url('/') }}</span></a></li>
					</ul>
					
					<!-- Sidebar -->
					<!-- @include('Front.includes.profile_sidebar') -->
					<!-- Sidebar -->
				</div>
				
				<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12 contact-right">
					<h3>{{(Lang::has(Session::get('front_lang_file').'.FRONT_SEND_US_MSG')) ? trans(Session::get('front_lang_file').'.FRONT_SEND_US_MSG') : trans($FRONT_LANGUAGE.'.FRONT_SEND_US_MSG')}}</h3>
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
					<div class="box-body spaced" >
						<div id="location_form">
							
							<div class="row-fluid well">
								
								
								{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'contact_us_message','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
								
								
								<div class="row panel-heading">
									
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 contact-form-label">
										<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_REG_NAME')}}&nbsp;*
											</span>
										</div>
									</div>
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12">
										<div class="form-group">
											

						{!! Form::text('name','',['class'=>'form-control', 'placeholder'=> __(Session::get('front_lang_file').'.ADMIN_REG_NAME'),'id' => 'cus_name','required','maxlength'=>50]) !!}

											<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span>
											</div></div>
									</div>
									<!-- </div>
										
									<div class="row panel-heading"> -->
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 contact-form-label">
										<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_MAIL') : trans($FRONT_LANGUAGE.'.ADMIN_REG_MAIL')}}&nbsp;*
											</span>
										</div>
									</div>
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12">
										<div class="form-group">											
								{!! Form::email('email','',['class'=>'form-control','placeholder'=>__(Session::get('front_lang_file').'.ADMIN_EMAIL'),'required']) !!}

										</div>
									</div>
									<!-- </div>
										
									<div class="row panel-heading"> -->
									<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 contact-form-label">
										<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_PHONE')}}&nbsp;*
											</span>
										</div>
									</div>
									<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
										<div class="form-group">
											{!! Form::text('phone','',['class'=>'form-control','required','id'=>'con_cus_phone','maxlength'=>15,'onkeyup'=>'validate_phone(\'con_cus_phone\');']) !!}
										</div>
									</div>
									
									<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 contact-form-label">
										<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.FRONT_MESSAGE')) ? trans(Session::get('front_lang_file').'.FRONT_MESSAGE') : trans($FRONT_LANGUAGE.'.FRONT_MESSAGE')}}&nbsp;*
											</span>
										</div>
									</div>
									<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
										<div class="form-group">											
			{!! Form::textarea('message','',['class'=>'form-control','placeholder'=> __(Session::get('front_lang_file').'.FRONT_MESSAGE'),'required','maxlength' => '600']) !!}

										</div>
									</div>
									
									<!-- </div>

									<div class="panel-heading"> -->
									<div class=" col-lg-12 col-sm-12 col-md-12 col-xs-12">
										<div class="form-group formBtn">
											
											@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.FRONT_SND')) ? trans(Session::get('front_lang_file').'.FRONT_SND') : trans($FRONT_LANGUAGE.'.FRONT_SND') @endphp
											
											
											{!! Form::submit($saveBtn,['class' => ''])!!}
										</div>
									</div>
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

@section('script')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
<script>
    $(document).ready(function() {
		
		var element = document.getElementById('con_cus_phone');
		if(element.value=='')
		{
			$('#con_cus_phone').val('+');
		}
	});
    function validate_phone(gotId) {
		
		var element = document.getElementById(gotId);
		//alert();
		if(element.value=='' || element.value.length < 3)
		{
			//$('#'+gotId).val('{{$default_dial}}');
		}
		element.value = element.value.replace(/[^0-9 +]+/, '');
	}
</script>


<script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>

<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
<script type="text/javascript">
	$("#con_cus_phone").intlTelInput();//{onlyCountries: ["{{$default_country_code}}"]}
	$("#cus_alt_phone").intlTelInput();//{onlyCountries: ["{{$default_country_code}}"]}
	
</script>
@endsection
@stop

