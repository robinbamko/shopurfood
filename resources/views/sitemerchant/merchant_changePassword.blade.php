@extends('sitemerchant.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<style>
		.invalid {
			color: red;
		}
		.valid {
			color: green;
		}
		@media only screen and (max-width:767px)
		{
			.box-body{ padding:5px!important; }
		}

	</style>
	<div class="main">
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<h1 class="page-header">{{$pagetitle}}</h1>
			<div class="container-fluid add-country">
				<div class="row">
					<div class="container right-container">
						<div class="col-md-12">
							<div class="location panel">
								
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
										<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
									</div>
								@endif
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">

									<div class="">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'merchant_change_password','id'=>'profile_form']) !!}
                                        <?php //print_r($getvendor); exit; ?>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_OLD_PASS')) ? trans(Session::get('mer_lang_file').'.MER_OLD_PASS') : trans($MER_OUR_LANGUAGE.'.MER_OLD_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','old_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_OLD_PASS')) ? trans(Session::get('mer_lang_file').'.MER_OLD_PASS') : trans($MER_OUR_LANGUAGE.'.MER_OLD_PASS'),'id' => 'old_pwd','maxlength' => 15]) !!}
												@if ($errors->has('old_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('old_pwd') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_NEW_PASS')) ? trans(Session::get('mer_lang_file').'.MER_NEW_PASS') : trans($MER_OUR_LANGUAGE.'.MER_NEW_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','new_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_NEW_PASS')) ? trans(Session::get('mer_lang_file').'.MER_NEW_PASS') : trans($MER_OUR_LANGUAGE.'.MER_NEW_PASS'),'id' => 'new_pwd','maxlength' => 15]) !!}
												@if ($errors->has('new_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('new_pwd') }}</p>
												@endif
												@if($PW_PROTECT == 1)
													<ul style="list-style-type:disc;" id="newpwd_require">
														<li id="letter" class="invalid">A <b>lowercase</b> letter</li>
														<li id="capital" class="invalid">A <b>capital (uppercase)</b> letter</li>
														<li id="number" class="invalid">A <b>number and special characters</b></li>
														<li id="length" class="invalid">Minimum <b>6 characters</b></li>
													</ul>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CONFIRM_PASS')) ? trans(Session::get('mer_lang_file').'.MER_CONFIRM_PASS') : trans($MER_OUR_LANGUAGE.'.MER_CONFIRM_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','conf_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_CONFIRM_PASS')) ? trans(Session::get('mer_lang_file').'.MER_CONFIRM_PASS') : trans($MER_OUR_LANGUAGE.'.MER_CONFIRM_PASS'),'id' => 'conf_pwd','maxlength' => 15]) !!}
												@if ($errors->has('conf_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('conf_pwd') }}</p>
												@endif
												@if($PW_PROTECT == 1)
													<ul style="list-style-type:disc;" id="confpwd_require">
														<li id="letterconf_pwd" class="invalid">A <b>lowercase</b> letter</li>
														<li id="capitalconf_pwd" class="invalid">A <b>capital (uppercase)</b> letter</li>
														<li id="numberconf_pwd" class="invalid">A <b>number and special characters</b></li>
														<li id="lengthconf_pwd" class="invalid">Minimum <b>6 characters</b></li>
													</ul>
												@endif
											</div>
										</div>

										<div class="form-group">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
												@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.MER_UPDATE')) ? trans(Session::get('mer_lang_file').'.MER_UPDATE') : trans($MER_OUR_LANGUAGE.'.MER_UPDATE') @endphp
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											</div>
										</div>

										{!! Form::close() !!}
									</div>
								</div>

								{{-- Add/Edit page ends--}}
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
	<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
	<script>

        <?php if($PW_PROTECT == 1)
        { ?>
        jQuery.validator.addMethod("newPwdValidate", function(value, element) {
            return value.trim().length != 0 && $('#newpwd_require').find('.invalid').length == 0;
        }, "No space please and don't leave it empty");
        $("#profile_form").validate({
            rules: {
                old_pwd: "required",
                new_pwd : { newPwdValidate: true  },
                conf_pwd: {
                    required: true,
                    equalTo: "#new_pwd"
                }
            },
            messages: {
                old_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_OLDPWD')}}",
                new_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_INVALID_PASS')) ? trans(Session::get('mer_lang_file').'.MER_INVALID_PASS') : trans($MER_OUR_LANGUAGE.'.MER_INVALID_PASS')}}",
                conf_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CONPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CONPWD') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_CONPWD')}}"
            }
        });
        <?php } else { ?>
        $("#profile_form").validate({
            rules: {
                old_pwd: "required",
                new_pwd: "required",
                conf_pwd: {
                    required: true,
                    equalTo: "#new_pwd"
                }
            },
            messages: {
                old_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_OLDPWD')}}",
                new_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_NEWPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_NEWPWD') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_NEWPWD')}}",
                conf_pwd: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CONPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CONPWD') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_CONPWD')}}"
            }
        });
        <?php } ?>
        $(document).ready(function(){
                <?php if($PW_PROTECT == 1)
                { ?>
            var pw = document.getElementById('new_pwd');
            var letter = document.getElementById("letter");
            var capital = document.getElementById("capital");
            var number = document.getElementById("number");
            var length = document.getElementById("length");


            pw.onkeyup = function() {

                // Validate lowercase letters
                var lowerCaseLetters = /[a-z]/g;
                if(pw.value.match(lowerCaseLetters)) {
                    letter.classList.remove("invalid");
                    letter.classList.add("valid");
                } else {
                    letter.classList.remove("valid");
                    letter.classList.add("invalid");
                }

                // Validate capital letters
                var upperCaseLetters = /[A-Z]/g;
                if(pw.value.match(upperCaseLetters)) {
                    capital.classList.remove("invalid");
                    capital.classList.add("valid");
                } else {
                    capital.classList.remove("valid");
                    capital.classList.add("invalid");
                }

                // Validate numbers
                var numbers = /[0-9]/g;
                var spl_chr = /[!@$%^&*()]/g;
                if(pw.value.match(numbers) && pw.value.match(spl_chr)) {
                    // if(pw.value.match(numbers)) {
                    number.classList.remove("invalid");
                    number.classList.add("valid");
                } else {
                    number.classList.remove("valid");
                    number.classList.add("invalid");
                }

                // Validate length
                if(pw.value.length >= 6) {
                    length.classList.remove("invalid");
                    length.classList.add("valid");
                } else {
                    length.classList.remove("valid");
                    length.classList.add("invalid");
                }
            }
            //CONFIRM PASSWORD
            var pwconf_pwd = document.getElementById('conf_pwd');
            var letterconf_pwd = document.getElementById("letterconf_pwd");
            var capitalconf_pwd = document.getElementById("capitalconf_pwd");
            var numberconf_pwd = document.getElementById("numberconf_pwd");
            var lengthconf_pwd = document.getElementById("lengthconf_pwd");


            pwconf_pwd.onkeyup = function() {

                // Validate lowercase letters
                var lowerCaseLetters = /[a-z]/g;
                if(pwconf_pwd.value.match(lowerCaseLetters)) {
                    letterconf_pwd.classList.remove("invalid");
                    letterconf_pwd.classList.add("valid");
                } else {
                    letterconf_pwd.classList.remove("valid");
                    letterconf_pwd.classList.add("invalid");
                }

                // Validate capital letters
                var upperCaseLetters = /[A-Z]/g;
                if(pwconf_pwd.value.match(upperCaseLetters)) {
                    capitalconf_pwd.classList.remove("invalid");
                    capitalconf_pwd.classList.add("valid");
                } else {
                    capitalconf_pwd.classList.remove("valid");
                    capitalconf_pwd.classList.add("invalid");
                }

                // Validate numbers
                var numbers = /[0-9]/g;
                var spl_chr = /[!@$%^&*()]/g;
                if(pwconf_pwd.value.match(numbers) && pwconf_pwd.value.match(spl_chr)) {
                    //if(pwconf_pwd.value.match(numbers)) {
                    numberconf_pwd.classList.remove("invalid");
                    numberconf_pwd.classList.add("valid");
                } else {
                    numberconf_pwd.classList.remove("valid");
                    numberconf_pwd.classList.add("invalid");
                }

                // Validate length
                if(pwconf_pwd.value.length >= 6) {
                    lengthconf_pwd.classList.remove("invalid");
                    lengthconf_pwd.classList.add("valid");
                } else {
                    lengthconf_pwd.classList.remove("valid");
                    lengthconf_pwd.classList.add("invalid");
                }
            }
            <?php } ?>
        });
	</script>
@endsection
@stop