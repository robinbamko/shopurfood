@extends('Admin.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<!-- MAIN -->
	<style type="text/css">
		.input-group {
			width: 110px;
			margin-bottom: 10px;
		}
	</style>
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
								
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="panel-body">
										<div class="">
									
										@if(!empty($subadmin_details ) && $s_id != '')
										{{ Form::open(array('url' => 'admin-editsubmit-subadmin','method' => 'post','enctype'=>'multipart/form-data','id'=>'subadmin_form','class'=>'form-horizontal form-auth-small')) }}
										@else
										{{ Form::open(array('autocomplete' => 'off','url' => 'admin-submit-subadmin','method' => 'post','enctype'=>'multipart/form-data','id'=>'subadmin_form','class'=>'form-horizontal form-auth-small')) }}
										@endif
										
										{!! Form::hidden('_token',csrf_token())!!}
							
										<!---EDIT VALUES---->
							
										@php $sub_name = $sub_email = $sub_phone = $sub_password = ''; $sub_dec_password = '';
										 $privArr = array(); @endphp
										
										@if(!empty($subadmin_details ) && $s_id != '')
												
												@php $sub_name = $subadmin_details->adm_fname;
												$sub_email = $subadmin_details->adm_email;
												$sub_phone = $subadmin_details->adm_phone1;
												$sub_password = $subadmin_details->adm_password;
												$sub_dec_password = $subadmin_details->adm_decrypt_password;
												$privArr = unserialize($subadmin_details->sub_privileges);
												@endphp
												{!! Form::hidden('subadmin_edit_id',$s_id,array()) !!}
										@endif
										
										
										<!---END EDIT VALUES---->
							
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_name">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NAME')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('subadmin_name',$sub_name,['autocomplete'=>'nope','class'=>'form-control','maxlength'=>'50','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NAME'),'id' => 'subadmin_name','required','maxlength' => '100']) !!}
												@if ($errors->has('subadmin_name') )
													<p class="error-block" style="color:red;">{{ $errors->first('subadmin_name') }}</p>
												@endif
											</div>
										</div>
										
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_email_id_label">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												@if(empty($subadmin_details ) && $s_id == '')
													{!! Form::text('subadmin_email_id',$sub_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),'id' => 'subadmin_email_id','required','maxlength' => '100','autocomplete'=>'nope','onchange'=>'validate_email_exists();']) !!}
												@else
													{!! Form::text('subadmin_email_id',$sub_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),'id' => 'subadmin_email_id','required','maxlength' => '100','autocomplete'=>'nope']) !!}
													{{ Form::hidden('old_email',$sub_email)}}
												@endif	
												
												@if ($errors->has('subadmin_email_id') )
													<p class="error-block" style="color:red;">{{ $errors->first('subadmin_email_id') }}</p>
												@endif
											</div>
											
											<div id="error_email_exists" style="color:red;display:none;">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL_ALREADY_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL_ALREADY_EXISTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL_ALREADY_EXISTS') !!}</div>
										</div>
										
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('subadmin_phone',$sub_phone,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO'),'id' => 'subadmin_phone','required','maxlength' => '16','onkeypress'=>"return onlyNumbers(event);"]) !!}
												@if ($errors->has('subadmin_phone') )
													<p class="error-block" style="color:red;">{{ $errors->first('subadmin_phone') }}</p>
												@endif
											</div>
										</div>
										
										@if(empty($subadmin_details ) && $s_id == '')
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_password">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('subadmin_password',$sub_password,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD'),'id' => 'subadmin_password','required','maxlength'=>'50']) !!}
												@if ($errors->has('subadmin_password') )
													<p class="error-block" style="color:red;">{{ $errors->first('subadmin_password') }}</p>
												@endif
											</div>
										</div>
										@elseif($s_id != '')
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_password">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('subadmin_password',$sub_dec_password,['class'=>'form-control','required']) !!}												{{ Form::hidden('old_password',$sub_dec_password)}}
											</div>
										</div>
										@endif	
										
										@if(empty($subadmin_details ) && $s_id == '')
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CONFIRM_PASSWORD')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('password_confirmation',$sub_password,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CONFIRM_PASSWORD'),'id' => 'password_confirmation','required','maxlength' => '50']) !!}
												@if ($errors->has('password_confirmation') )
													<p class="error-block" style="color:red;">{{ $errors->first('password_confirmation') }}</p>
												@endif
											</div>
										</div>
										@endif
										
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_privilege_label">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRIVILEGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRIVILEGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRIVILEGE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-4 checkbox-div">
												<label class="multi-checkbox"><input type="checkbox" onchange="checkAll(this);" name="subadmin_privilege_all" id="subadmin_privilege_all" class="control-label">
												<span class="checkmark"></span>
												</label>
												{!! Form::label('subadmin_privilege_all',(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ALL')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ALL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ALL'),array()) !!} 
												<span id="checkErr"></span>
											</div>
										</div>

										<div class="form-group">
											<div class="col-sm-2">	</div>
												<div class="col-sm-6">
												
												<table border="0" cellspacing="0" cellpadding="0" width="400" class="table-responsive">
													<tr>
														<td align="center" width="15%">{!! Form::label('subadmin_privilege_l',(Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW'),array('class'=>'control-label col-sm-2 center-label')) !!}</td>
														<td align="center" width="15%">{!! Form::label('subadmin_privilege_l',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD'),array('class'=>'control-label col-sm-2 center-label')) !!}</td>
														<td align="center" width="15%">{!! Form::label('subadmin_privilege_l',(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT'),array('class'=>'control-label col-sm-2 center-label')) !!}</td>
														<td align="center" width="15%">{!! Form::label('subadmin_privilege_l',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),array('class'=>'control-label col-sm-2 center-label')) !!}</td>
													</tr>
												</table>
												
											</div>
										</div>
										
										<!-- MANAGE PREVILEGE -->
										
										@for($i=0;$i<sizeof(Config('subadmin_privilege'));$i++)
										
										@php $subadmin = Config('subadmin_privilege')[$i]; $count = 0; @endphp
										
										<!---EDIT-->
										@php $priv = array(); $disabled="";  @endphp
										@if (isset($privArr[$subadmin])) 
											@php $priv = $privArr[$subadmin]; @endphp
										@endif
										<!--END EDIT-->
										
										<div class="form-group">
										{!! Form::label('subadmin_privilege',str_replace('_',' ',ucfirst($subadmin)),array('class'=>'control-label col-sm-2')) !!}
										<div class="col-sm-6 checkbox-div">
											<table border="0" cellspacing="0" cellpadding="0" width="400" class="table-responsive">
												<tr>
												@for($j=0;$j<4;$j++)
													<td align="center" width="15%">
														@php $chk = ""; @endphp
														@if (in_array($j, $priv))
															@php $chk = 'checked'; @endphp
														@endif
													<label class="multi-checkbox">
													@if(($subadmin == 'Order' || $subadmin == 'Commission' || $subadmin == 'Inventory' || $subadmin == 'Cancellation' || $subadmin == 'Refer_Friend' || $subadmin == 'Dashboard' || $subadmin == 'Newsletter' || $subadmin == 'Featured_Resturant' || $subadmin == 'Failed_orders' || $subadmin == 'Reports' || $subadmin == 'Delivery_Commission' || $subadmin == 'Promotional_And_Offers')&&($j==1 || $j==2 || $j==3))
														{!! Form::checkbox($subadmin.'[]','','', array('id'=>'','class'=>'selectcase col-sm-7','false','disabled')) !!}
													@else	
														{!! Form::checkbox($subadmin.'[]',$j,$chk, array('id'=>'check_id','class'=>'selectcase')) !!}
													@endif		
													<span class="checkmark"></span>
													</label>
													
													</td>
												@endfor
												</tr>
											</table>
											
										</div>
									</div>
									
									@endfor	

									<div class="form-group">
										<div class="col-sm-2">
										</div>
										<div class="col-sm-6">
											{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
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
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
	</div>
@section('script')
	<script>
		function checkAll(ele) {               
               
                 var checkboxes = document.getElementsByTagName('input');
                 if (ele.checked) {
                     for (var i = 0; i < checkboxes.length; i++) {
                         if (checkboxes[i].type == 'checkbox' && checkboxes[i].id == 'check_id' ) {
                             checkboxes[i].checked = true;
                         }
                     }
                 } else {
                     for (var i = 0; i < checkboxes.length; i++) {
                         // console.log(i)
                         if (checkboxes[i].type == 'checkbox') {
                             checkboxes[i].checked = false;
                         }
                     }
                 }
               }
	</script>
	
	
	<script>
	function validate_email_exists(){
	
	var subadmin_email_id = $('#subadmin_email_id').val();
	 $.ajax({
          headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			  },
          url: "{{ url('ajax_subadmin_check') }}",
          type: 'POST',
          data: {'subadmin_email_id' : subadmin_email_id},
          success:function(response) {
              if(response == 2){
				  $('#error_email_exists').show();
				  $('#subadmin_email_id').val('');
				  return false;
			  }else{
			  	 $('#error_email_exists').hide();
			  }
          }
     });
	 
	}
	
	</script>
	
	
	 <script>
	    function onlyNumbers(evt) {
            var e = event || evt; // for trans-browser compatibility
            var charCode = e.which || e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
   </script>
   
   <script>
	 /*$.validator.addClassRules({
        selectcase:{
		
       // required: true
    }
    });*/
	/*$.validator.addMethod("roles", function(value, elem, param) {
	   return $(".selectcase:checkbox:checked").length > 0;
	},"You must select at least one!");*/
	$('#subadmin_form').validate({ 
        rules: {
			
			subadmin_email_id: {
			required: true,
			},
			
			subadmin_name: {
			required: true,
			},
			
			subadmin_phone: {
			required: true,
			},
		
			subadmin_password: {
			required: true,
			},
			
			password_confirmation: {
			required: true,
			},
			
			subadmin_privilege: {
			required: true,
			},
		 },
		 
		 messages: {
			subadmin_email_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL')}}",
			subadmin_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_ENTR_NAME')}}",
			subadmin_phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_ENTR_PHONE')}}",
			subadmin_password: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_ENTR_PASS')}}",
			password_confirmation: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_MATCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_MATCH') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_PASSWORD_MUST_MATCH')}}",
			subadmin_privilege: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_PRIVILEGES')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_PRIVILEGES') : trans($L_ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_PRIVILEGES')}}",
			 
		 },
		errorPlacement: function(error, element) 
		{
			if ( element.is(":checkbox") ) 
			{
				error.insertAfter($('#checkErr'));
			}
			else 
			{ 
				error.insertAfter( element );
			}
			//error.insertAfter( element );
		}
    });
	$(".selectcase").rules("add", { 
		required:true,  
		minlength:1
	});

  </script>
	
@endsection
@stop