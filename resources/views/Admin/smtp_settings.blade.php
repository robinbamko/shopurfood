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
		<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Settings')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Settings') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Settings')}}</h1>
				<div class="container-fluid">
	

	<div class="row">
		<div class="container right-container">
					<div class="r-btn">
					</div>
		<div class="col-md-12">
			

			<!-- INPUTS -->
			<div class="panel">
				
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

				@if(count($settings_details) > 0)
				@php
					foreach($settings_details as $set_val){ }
					
				@endphp
				<div class="panel-body">
					{{ Form::open(array('url' => 'admin-smtp-settings-submit','id'=>'smtp_settings','method' => 'post','class'=>'form-horizontal')) }}

					{{ Form::hidden('siteid',$set_val->gs_id,array('class' => 'form-control')) }}
					<div class="form-group">
					{{ Form::label('smtp_host', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Host')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Host') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Host'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('smtp_host', $set_val->gs_smtp_host,array('class' => 'form-control','maxlength'=>100,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Host')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Host') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Host'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_port', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Port')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Port') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Port'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('smtp_port', $set_val->gs_smtp_port,array('class' => 'form-control','onkeypress'=>'return isNumberKey(event)','maxlength'=>6,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Port')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Port') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Port'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_email', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Email')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Email') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Email'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::email('smtp_email', $set_val->gs_smtp_email,array('class' => 'form-control','maxlength'=>100,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Email')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Email') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Email'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_password', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Password')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Password') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Password'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::input('password','smtp_password',$set_val->gs_smtp_password,array('class'=>'form-control','maxlength'=>15,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Password')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Password') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Password'))) }} </div>
					</div>
					
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-6">
						{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
						</div>
					</div>				
					
					{{ Form::close() }}
				</div>
				@else
					<div class="panel-body">
					{{ Form::open(array('url' => 'admin-smtp-settings-submit','id'=>'smtp_settings','method' => 'post','class'=>'form-horizontal')) }}

					{{ Form::hidden('siteid','',array('class' => 'form-control')) }}
					<div class="form-group">
					{{ Form::label('smtp_host', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Host')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Host') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Host'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('smtp_host','',array('class' => 'form-control','onkeypress'=>'return isNumberKey(event)','maxlength'=>100,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Host')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Host') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Host'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_port', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Port')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Port') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Port'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('smtp_port','',array('class' => 'form-control','maxlength'=>6,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Port')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Port') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Port'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_email', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Email')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Email') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Email'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::email('smtp_email','',array('class' => 'form-control','maxlength'=>100,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Email')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Email') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Email'))) }} </div>
					</div>
					<div class="form-group">
					{{ Form::label('smtp_password', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Password')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Password') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Password'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::input('password','smtp_password','',array('class'=>'form-control','maxlength'=>15,'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Password')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Password') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Password'))) }} </div>
					</div>
					
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-6">
						{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
						</div>
					</div>										
					{{ Form::close() }}
					</div>
				@endif
			</div>
			<!-- END INPUTS -->

			
		</div>
		</div>
	</div>
	

				</div>
			</div>
			<!-- END MAIN CONTENT -->
		</div>
		<!-- END MAIN -->

@section('script')
<script type="text/javascript">
	$("#smtp_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			smtp_host: "required",
			smtp_port: "required",
			smtp_email: {
				required: true,
				email: true
			},
			smtp_password: "required",
				
		},
		messages: {
			smtp_host: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_HOST')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_HOST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_SMTP_HOST')}}",
			smtp_port: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_PORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_PORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_SMTP_PORT')}}",
			smtp_email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			smtp_password: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SMTP_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_SMTP_PASSWORD')}}",
			
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
</script>
@endsection
@stop
