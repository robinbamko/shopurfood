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
								
								{!! Form::open(['method' => 'any','url'=>'send_newsletter_submit','id'=>'demo-form2','class'=>'form-horizontal form-label-left','data-parsley-validate']) !!}
								{{-- SEND NEWSLETTER STARTS --}}
								<div class="box-body spaced" style="padding:10px">
									<div id="location_form" class="panel-body">
										<div class="">
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_name">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL_TO')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL_TO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL_TO')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												
												{{ Form::radio('email_to',0,false, ['required','class'=>'field','id'=>'all_users','onclick' => 'check_user_type(0)']) }}
												{{ Form::label('all_users',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALL_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALL_USER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALL_USER')) }}
												{{ Form::radio('email_to',1,false, ['required','class'=>'field','id'=>'particular_user','onclick' => 'check_user_type(1)']) }}
												{{ Form::label('particular_user',(Lang::has(Session::get('admin_lang_file').'.ADMIN_PARTICULAR_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PARTICULAR_USER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PARTICULAR_USER')) }}
												{{ Form::radio('email_to',2,false, ['required','class'=>'field','id'=>'subscriber_id','onclick' => 'check_user_type(2)']) }}
												{{ Form::label('subscriber_id',(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBSCRIBER')) }}
								
												@if ($errors->has('email_to') )
													<p class="error-block" style="color:red;">{{ $errors->first('email_to') }}</p>
												@endif
												<div></div>
											</div>
										</div>
										
										<div class="form-group" id="users_list" style="display:none;">
											<label class="control-label col-sm-2 starclass">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER')}} </label>
											<div class="col-sm-6">
												@if(count($customer_details)>0)
													<select name="user_id[]" id="multi_user_select1" class="select2 form-control" multiple ="multiple" style="width:100%">
														@foreach($customer_details as $cus)
															<option value="{{ $cus->cus_id }}">{{$cus->cus_fname}} - {{$cus->cus_email}}</option>
														@endforeach
													</select>
												@else 
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_USER_NOT_FOUND')}}
												@endif
											</div>
										</div>

										<div class="form-group" id="subscriber_list" style="display:none;">
											<label class="control-label col-sm-2 starclass">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBSCRIBER')}} </label>
											<div class="col-sm-6">
												@if(count($subscriber_details)>0)
													<select name="subscriber_id[]" id="multi_user_select2" class="select2 form-control" placeholder="test" multiple ="multiple"  style="width:100%">
														@foreach($subscriber_details as $cus)
															<option value="{{ $cus->id }}">{{$cus->news_email_id}}</option>
														@endforeach
													</select>
												@else 
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_SUBSCRIBER_AVAILABEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_SUBSCRIBER_AVAILABEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_SUBSCRIBER_AVAILABEL')}}
												@endif
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="newsletter_subject">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBJECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBJECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBJECT')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('newsletter_subject','',['autocomplete'=>'nope','class'=>'form-control','maxlength'=>'50','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBJECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBJECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBJECT'),'id' => 'newsletter_subject','required','maxlength' => '100']) !!}
												@if ($errors->has('newsletter_subject') )
													<p class="error-block" style="color:red;">{{ $errors->first('newsletter_subject') }}</p>
												@endif
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="newsletter_message">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MESSAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MESSAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MESSAGE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::textarea('newsletter_message',null,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MESSAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MESSAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MESSAGE'),'class'=>'form-control col-md-7 col-xs-12')) !!}
												@if ($errors->has('newsletter_message') )
													<p class="error-block" style="color:red;">{{ $errors->first('newsletter_message') }}</p>
												@endif
											</div>
										</div>
										
										
									<div class="form-group">
											<label class="control-label col-sm-2"></label>
										
										<div class="col-sm-6">
											{{ Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBMIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBMIT'),array('class'=>'btn btn-success')) }}
											{{ Form::Reset((Lang::has(Session::get('admin_lang_file').'.ADMIN_RESET')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESET'),array('class'=>'btn btn-info')) }}
										</div>
									</div>
												
										{!! Form::close() !!}
										</div>
									</div>
								</div>
								{{-- SEND NEWSLETTER END --}}

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
	// check user type ,all user or particular user
	$("#demo-form2").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			email_to: "required",
			"user_id[]": {
				required: {
					depends: function(element) {
						if($('input[name=email_to]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"subscriber_id[]": {
				required: {
					depends: function(element) {
						if($('input[name=email_to]:checked').val()=='2'){  return true; } else { return false; } 
					}
				}
			},
			"newsletter_subject":"required",
			"newsletter_message":"required",
		},
		messages: {
			"email_to": "{{(Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_SELECTMAILTO')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_SELECTMAILTO') : trans($ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_SELECTMAILTO')}}",
			"user_id[]": "{{(Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_CUSTOMER_MAIL')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_CUSTOMER_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_CUSTOMER_MAIL')}}",
			"subscriber_id[]": "{{(Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_SUBSCRIBER_MAIL')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_SUBSCRIBER_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_SUBSCRIBER_MAIL')}}",
			"newsletter_subject": "{{(Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_SUBJECT')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_SUBJECT') : trans($ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_ENTER_SUBJECT')}}",
			"newsletter_message": "{{(Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_MESSAGE')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_MESSAGE') : trans($ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_ENTER_MESSAGE')}}",
		},
		errorPlacement: function(error, element) 
		{
			if ( element.is(":radio") ) 
			{
				//alert($(element).next().next().next().next().next().next().html());
				error.appendTo($(element).next().next().next().next().next().next());
				//<div class="form-group"><label class="control-label col-sm-2"></label><div class="col-sm-6 pre_order_label">test</div></div>
				//error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
			}
			else 
			{ // This is the default behavior 
				error.insertAfter( element );
			}
			//error.insertAfter( element );
		}
	});
function check_user_type(val)
{
	/*if(val==1){
		$("#users_list").show(); // show particular users list
		$('#multi_user_select1').attr('required', 'required');
		$('#multi_user_select2').removeAttr('required');
		$('#subscriber_list').hide();
	}else if(val ==0){
		$("#users_list").hide();	// hide users list
		$('#multi_user_select1').removeAttr('required');
		$('#multi_user_select2').removeAttr('required');
		$('#subscriber_list').hide();

	}
	else if(val ==2){
		$("#users_list").hide();	// hide users list
		$('#multi_user_select1').removeAttr('required');
		$('#multi_user_select2').attr('required', 'required');
		$('#subscriber_list').show();  // show subscirber list

	}*/
	if(val==1){
		$("#users_list").show(); // show particular users list
		$('#subscriber_list').hide();
		$('.select2').select2({ placeholder: "Select",allowClear: true});
	}else if(val ==0){
		$("#users_list").hide();	// hide users list
		$('#subscriber_list').hide();
		
	}
	else if(val ==2){
		$("#users_list").hide();	// hide users list
		$('#subscriber_list').show();  // show subscirber list
		$('.select2').select2({ placeholder: "Select",allowClear: true});
	}
}
</script>
@endsection
@stop