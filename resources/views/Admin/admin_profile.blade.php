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
		<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MYPROFILE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MYPROFILE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MYPROFILE')}}</h1>
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
					{{ Form::open(array('url' => 'admin-profile-settings-submit','id'=>'admin_profile','method' => 'post','class'=>'form-horizontal')) }}

					{{ Form::hidden('adminid', $set_val->id,array('class' => 'form-control')) }}
				<div class="form-group">
					{{ Form::label('adminfirstname', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::text('adminfirstname', $set_val->adm_fname,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('adminlastname', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'),array('class'=>'control-label col-sm-2 withoutstarclass')) }}
					<div class="col-sm-6">
					{{ Form::text('adminlastname', $set_val->adm_lname,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('adminemail', (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::email('adminemail',$set_val->adm_email,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'))) }}</div>

				</div>
				<div class="form-group">
					{{ Form::label('admin_phone_one', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_ONE'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::text('admin_phone_one', $set_val->adm_phone1,array('class' => 'form-control','onkeyup'=>'validate_phone(\'admin_phone_one\');','maxlength'=>'15','id'=>'admin_phone_one','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_ONE'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('admin_phone_two', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_TWO'),array('class'=>'control-label col-sm-2 withoutstarclass')) }}
					<div class="col-sm-6">
					{{ Form::text('admin_phone_two', $set_val->adm_phone2,array('class' => 'form-control','onkeyup'=>'validate_phone(\'admin_phone_two\');','maxlength'=>'15','id'=>'admin_phone_two','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_TWO'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('admin_address', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDRESS'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::textarea('admin_address',$set_val->adm_address,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDRESS'))) }}</div>
				</div>
		
				<div class="form-group" style="display:none">
					{{ Form::label('admin_country', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::select('admin_country',$country_list,$set_val->adm_country,array('class'=>'form-control')) }}</div>
				</div> 
				<div class="form-group">
					
				</div>
				<div class="form-group">
					<div class="col-sm-2"> 
					</div>
					<div class="col-sm-6"> 
						{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
					</div>
				</div>
				
					{{ Form::close() }}
					
					
					
				</div>
				@else
					<div class="panel-body">
					{{ Form::open(array('url' => 'admin-profile-settings-submit','id'=>'admin_profile','method' => 'post','class'=>'form-horizontal')) }}

					{{ Form::hidden('adminid', '',array('class' => 'form-control')) }}
				<div class="form-group">
					{{ Form::label('adminfirstname', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::text('adminfirstname', '',array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('adminlastname', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('adminlastname', '',array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('adminemail', (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::email('adminemail','',array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'))) }}</div>s
				</div>
				<div class="form-group">

					{{ Form::label('admin_phone_one', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_ONE'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::text('admin_phone_one', '',array('class' => 'form-control','onkeyup'=>'validate_phone(\'admin_phone_one\');','maxlength'=>'15','id'=>'admin_phone_one','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_ONE'))) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('admin_phone_two', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_TWO'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('admin_phone_two', '',array('class' => 'form-control','maxlength'=>'15','id'=>'admin_phone_two','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE_TWO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE_TWO'),'onkeyup'=>'validate_phone(\'admin_phone_two\');')) }}</div>
				</div>
				<div class="form-group">
					{{ Form::label('admin_address', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDRESS'),array('class'=>'control-label col-sm-2 starclass')) }}
					<div class="col-sm-6">
					{{ Form::textarea('admin_address','',array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDRESS'))) }}</div>
				</div>
				<div class="form-group"  style="display:none">
					{{ Form::label('admin_country', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY'),array('class'=>'control-label col-sm-2 starclass')) }}

					<div class="col-sm-6">
					{{ Form::select('admin_country',$country_list,null,array('class'=>'form-control' )) }}
					</div>
				</div>
				<div class="form-group">
					
				</div>
				<div class="form-group">
					<div class="col-sm-2"> 
					</div>
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
		<script type="text/javascript">
			
		</script>

@section('script')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
<script>
	$(document).ready(function() {
		$('#d-table').DataTable({
			responsive: true
		});
		var element = document.getElementById('admin_phone_one');
		if(element.value=='')
		{
			$('#admin_phone_one').val('{{$default_dial}}');
		}
		
		var element = document.getElementById('admin_phone_two');
		if(element.value=='')
		{
			$('#admin_phone_two').val('{{$default_dial}}');
		}
	});
</script>
<script type="text/javascript">
	$.validator.addMethod("jsPhoneValidation", function(value, element) { 
		var defaultDial = '{{Config::get('config_default_dial')}}';
		return value.substr(0, (defaultDial.trim().length)) != value.trim()
	}, "No space please and don't leave it empty");
	
	$("#admin_profile").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			adminfirstname: "required",
			//adminlastname: "required",
			adminemail: {
				required: true,
				email: true
			},
			admin_phone_one : { jsPhoneValidation : true  },
			//admin_phone_two : { jsPhoneValidation : true  },
			admin_address: "required",
			admin_country: "required",
				
		},
		messages: {
			adminfirstname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME_REQUIRED')}}",
			//adminlastname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME_REQUIRED')}}",
			adminemail: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			admin_phone_one: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE1_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE1_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE1_REQUIRED')}}",
			//admin_phone_two: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE2_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE2_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE2_REQUIRED')}}",
			admin_address: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDR_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDR_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDR_REQUIRED')}}",
			admin_country: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_REQUIRED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_REQUIRED')}}",
				
		}
	});
</script>
 <script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
   <script type="text/javascript">
    $("#admin_phone_one").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
    $("#admin_phone_two").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

   </script>
@endsection

@stop
