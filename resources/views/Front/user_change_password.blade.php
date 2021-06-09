@extends('Front.layouts.default')
@section('content')


<style type="text/css">
    .row .panel-heading{
	margin-bottom: 10px;
    }
	
    .userinvalid {
    color: red;
	}
	.uservalid {
    color: green;
	}
</style> 


	<div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
	</div>
 
	
	<div class="section9-inner">
		<div class="container userContainer">
			<div class="row">   
				<!--<div class="col-lg-12">
					<h5 class="sidebar-head">             
						@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')              
					</h5>
				</div>-->
				<div class="userContainer-bg row">					
					
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div"> 
						
						{{-- Add/Edit page starts--}}
						<div class="box-body spaced" >
							<div id="location_form">
								<div class="row-fluid well">
									{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'user-change-password-submit','enctype'=>'multipart/form-data','id'=>'customer_form','onsubmit'=>'return chk_pw();']) !!}
									<div class="row panel-heading payment-text-div">
									
										<div class="col-lg-12 col-md-12 form-group">
											<div class="profile-left-label">												
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CURRENT_PASSWORD')) ? trans(Session::get('front_lang_file').'.ADMIN_CURRENT_PASSWORD') : trans($FRONT_LANGUAGE.'.ADMIN_CURRENT_PASSWORD')}}&nbsp;*
													</label>												
											</div>
											<div class="profile-right-input">												
													{{ Form::input('password','currentpassword','',['class'=>'form-control','required','id'=>'old_pwd','maxlength'=>15]) }}
											</div>
										</div>
										
										
										<div class="col-lg-12 col-md-12 form-group">
											<div class="profile-left-label">												
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_PASSWORD')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_PASSWORD') : trans($FRONT_LANGUAGE.'.ADMIN_NEW_PASSWORD')}}&nbsp;*
													</label>												
											</div>
											<div class="profile-right-input">												
													{{ Form::input('password','newpassword','',['class'=>'form-control','required','id'=>'newpwd','maxlength'=>15]) }}
													@if($PW_PROTECT == 1)
													<ul style="list-style-type:disc;" id="newpwd_require">

														<li id="letteruser" class="userinvalid">{!! trans(Session::get('front_lang_file').'.FRONT_LW_CASE')!!}</li>


														<li id="capitaluser" class="userinvalid">{!! trans(Session::get('front_lang_file').'.FRONT_CA_CASE')!!}</li>
														letter</li>

														<li id="numberuser" class="userinvalid">{!! trans(Session::get('front_lang_file').'.FRONT_SP_CASE')!!}</li>

														<li id="lengthuser" class="userinvalid">{!! trans(Session::get('front_lang_file').'.FRONT_MIN6_CASE')!!}</li>

													</ul>
													<div id="cus_newpwd_err"></div>
													@endif												
											</div>
										</div>
										
										<div class="col-lg-12 col-md-12 form-group">
											<div class="profile-left-label">												
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CONFIRM_PASSWORD')) ? trans(Session::get('front_lang_file').'.ADMIN_CONFIRM_PASSWORD') : trans($FRONT_LANGUAGE.'.ADMIN_CONFIRM_PASSWORD')}}&nbsp;*
													</label>												
											</div>
											<div class="profile-right-input">												
													{{ Form::input('password','confirmpassword','',['class'=>'form-control','required','id'=>'confirm_pass','maxlength'=>15]) }}
											</div>
										</div>									
										
										
										
										<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12"> 
											<div class="form-group formBtn">
												
												@php $saveBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE') : trans($FRONT_LANGUAGE.'.ADMIN_UPDATE') @endphp
												
												
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
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
	


@section('script')
<script>
	<?php if($PW_PROTECT == 1 )
		{ ?>
		var pw = document.getElementById('newpwd');
		var letter = document.getElementById("letteruser");
		var capital = document.getElementById("capitaluser");
		var number = document.getElementById("numberuser");
		var length = document.getElementById("lengthuser");
		
		
		pw.onkeyup = function() {
			
			// Validate lowercase letters
			var lowerCaseLetters = /[a-z]/g;
			if(pw.value.match(lowerCaseLetters)) {  
				letter.classList.remove("userinvalid");
				letter.classList.add("uservalid");
				} else {
				letter.classList.remove("uservalid");
				letter.classList.add("userinvalid");
			}
			
			// Validate capital letters
			var upperCaseLetters = /[A-Z]/g;
			if(pw.value.match(upperCaseLetters)) {  
				capital.classList.remove("userinvalid");
				capital.classList.add("uservalid");
				} else {
				capital.classList.remove("uservalid");
				capital.classList.add("userinvalid");
			}
			
			// Validate numbers
			var numbers = /[0-9]/g;
			var spl_chr = /[!@$%^&*()]/g;
			if(pw.value.match(numbers) && pw.value.match(spl_chr)) {  
				number.classList.remove("userinvalid");
				number.classList.add("uservalid");
				} else {
				number.classList.remove("uservalid");
				number.classList.add("userinvalid");
			}
			
			// Validate length
			if(pw.value.length >= 6) {
				length.classList.remove("userinvalid");
				length.classList.add("uservalid");
				} else {
				length.classList.remove("uservalid");
				length.classList.add("userinvalid");
			}
		}
	<?php } ?>
	
	function chk_pw()
	{ 
		<?php if($PW_PROTECT == 1) {  ?>
			if($('#newpwd_require .userinvalid').length > 0)
			{
				$('#cus_newpwd_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_PASS')");
				return false;
			}
			
			<?php } else { ?>
			return true;
		<?php } ?>
		if($("#newpwd").val()!=$("#confirm_pass").val())
		{
			$('#cus_newpwd_err').html("@lang(Session::get('front_lang_file').'.ADMIN_PASS_CONFIRMATION')");
				return false;
		}
	}
	
</script>

@endsection
@stop

