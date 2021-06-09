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
		<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHANGE_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHANGE_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHANGE_PASSWORD')}}</h1>
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
				
			
					<div class="panel-body">
					{{ Form::open(array('url' => 'admin-password-submit','class'=>'form-horizontal','id'=>'change_pass','method' => 'post')) }}

					{{ Form::hidden('adminid', $admin_details->id,array('class' => 'form-control')) }}
					<div class="form-group">
						{{ Form::label('currentpassword', (Lang::has(Session::get('admin_lang_file').'.ADMIN_CURRENT_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURRENT_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURRENT_PASSWORD'),array('class'=>'control-label col-sm-2 starclass')) }}
						<div class="col-sm-6">
							{{ Form::input('password','currentpassword','',array('class'=>'form-control') ) }}
						</div>
					</div>
					<div class="form-group">
						{{ Form::label('newpassword', (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_PASSWORD'),array('class'=>'control-label col-sm-2 starclass')) }}
						<div class="col-sm-6">
							{{ Form::input('password','newpassword','',array('class'=>'form-control') ) }}
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('confirmpassword', (Lang::has(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CONFIRM_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CONFIRM_PASSWORD'),array('class'=>'control-label col-sm-2 starclass')) }}
						<div class="col-sm-6">
							{{ Form::input('password','confirmpassword','',array('class'=>'form-control') ) }}
						</div>
					</div>
				<br>
					
					<div class="form-group">
						<div class="col-sm-2"> 
						</div>
						<div class="col-sm-6"> 
							{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
							{{ Form::close() }}
						</div>
					</div>
					
				
				
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
	$("#change_pass").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			currentpassword: "required",
			newpassword: "required",
			confirmpassword: "required",
		},
		messages: {
			currentpassword: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_CURRENT_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_CURRENT_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_CURRENT_PASSWORD')}}",
			newpassword: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_NEW_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_NEW_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_NEW_PASSWORD')}}",
			confirmpassword: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_CONFIRM_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_CONFIRM_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_CONFIRM_PASSWORD')}}",
			
			
		}
	});
</script>
@endsection
		
@stop
