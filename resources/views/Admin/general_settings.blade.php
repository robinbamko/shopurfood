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
		<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_GENERAL_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_GENERAL_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GENERAL_SETTINGS')}}</h1>
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
								{{ Form::open(array('url' => 'admin-general-settings-submit','method' => 'post','id'=>'general_settings_form','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
								
								{{ Form::hidden('siteid', $details->gs_id,array('class' => 'form-control')) }}
								<div class="form-group">
									{{ Form::label('sitename', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_NAME'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::text('sitename', $details->gs_sitename,array('class' => 'form-control','maxlength'=>'100','id'=>'sitename','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_NAME'))) }}
									</div>
									<div class="col-sm-3 hidden-xs icon-help no-left-pad">
										<a href="#" id="help-name" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_NAME_HELP_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_NAME_HELP_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_NAME_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
									</div>
								</div>

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $site = 'gs_sitename_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_NAME')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::text('sitename_'.$lang->lang_code.'',$details->$site,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_NAME'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif

								<div class="form-group">
									{{ Form::label('email', (Lang::has(Session::get('admin_lang_file').'.ADMIN_CNCT_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNCT_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CNCT_EMAIL'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::email('email',$details->gs_email,array('class' => 'form-control','maxlength'=>'200','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_CNCT_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNCT_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CNCT_EMAIL'))) }}
									</div>
								</div>
								<div class="form-group">
									{{ Form::label('phone', (Lang::has(Session::get('admin_lang_file').'.ADMIN_CNCT_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNCT_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CNCT_PHONE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::text('phone',$details->gs_phone,array('class' => 'form-control','maxlength'=>'15','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_CNCT_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNCT_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CNCT_PHONE'),'onkeypress'=>"return onlyNumbers(event);")) }}
									</div>
								</div>
								<div class="form-group">
									{{ Form::label('description', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_DESCRIPTION'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::textarea('description',$details->gs_sitedescription,array('class' => 'form-control','minlength'=>'200','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_DESCRIPTION'))) }}
									</div>
								</div>

							<!-- for multilanguages start -->

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $site_desc = 'gs_sitedescription_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::textarea('description_'.$lang->lang_code.'',$details->$site_desc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_SITE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SITE_DESCRIPTION'),'class'=>'form-control','required')) !!}
												</div>
										</div>
									@endforeach
								@endif
						<!-- for multilanguages end -->
								<div class="form-group">
									{{ Form::label('metatitle', (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_TITLE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::text('metatitle', $details->gs_metatitle,array('class' => 'form-control','maxlength'=>'100','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_TITLE'))) }}
									</div>
									<div class="col-sm-3 no-left-pad hidden-xs">
										<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_METATITLE_HELP_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_METATITLE_HELP_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_METATITLE_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
									</div>
								</div>

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $metatitle = 'gs_metatitle_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_TITLE')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::text('metatitle_'.$lang->lang_code.'',$details->$metatitle,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_TITLE'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif

								<div class="form-group">
									{{ Form::label('metakeywords', (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::textarea('metakeywords',$details->gs_metakeywords,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS'))) }}
									</div>
									<div class="col-sm-3 no-left-pad hidden-xs">
										<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_METAKEYWORD_HELP_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_METAKEYWORD_HELP_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_METAKEYWORD_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
									</div>
								</div>

								<!-- for multilanguages start merta keyword-->

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $metakeywords = 'gs_metakeywords_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::textarea('metakeywords_'.$lang->lang_code.'',$details->$metakeywords,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif
							<!-- for multilanguages end -->

								<div class="form-group">
									{{ Form::label('metadescription', (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::textarea('metadescription',$details->gs_metadesc,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION'))) }}
									</div>
									<div class="col-sm-3 no-left-pad hidden-xs">
										<a href="#" id="help-description" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_METADESC_HELP_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_METADESC_HELP_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_METADESC_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
									</div>
								</div>


								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $metades = 'gs_metadesc_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::textarea('metadescription_'.$lang->lang_code.'',$details->$metades,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif
								<div class="form-group">
									{{ Form::label('footertext', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_TEXT'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::text('footertext',$details->footer_text,array('class' => 'form-control','maxlength'=>'100','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_TEXT'))) }}</div>
								</div>

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $footertext = 'footer_text_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="footertext">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_TEXT')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::text('footertext_'.$lang->lang_code.'',$details->$metatitle,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_TEXT'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif


								<div class="form-group">
									{{ Form::label('footerdescription', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_DESCRIPTION'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::textarea('footerdescription',$details->gs_footerdesc,array('class' => 'form-control','maxlength'=>'200','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_DESCRIPTION'))) }}</div>
								</div>

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $footerdesc = 'gs_footerdesc_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! 	Form::textarea('footerdescription_'.$lang->lang_code.'',$details->$footerdesc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FOOTER_DESCRIPTION'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif

								<div class="form-group">
									{{ Form::label('prefootertext', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_TITLE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::text('prefootertext',$details->prefooter_text,array('class' => 'form-control','maxlength'=>'80','minlength'=>'20','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_TITLE'))) }}</div>
								</div>



								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $prefootertext = 'prefooter_text_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_TITLE')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! Form::text('prefootertext_'.$lang->lang_code.'',$details->$prefootertext,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_TITLE'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif

								<div class="form-group">
									{{ Form::label('prefooterdesc', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_DESCRIPTION'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::textarea('prefooterdesc',$details->prefooter_desc,array('class' => 'form-control','maxlength'=>'300','minlength'=>'100','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_DESCRIPTION'))) }}</div>
								</div>

								@if(count($Admin_Active_Language) > 0)
									@foreach($Admin_Active_Language as $lang)
										@php $prefooterdesc = 'prefooter_desc_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2 starclass" for="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! Form::textarea('prefooterdesc_'.$lang->lang_code.'',$details->$prefooterdesc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_DESCRIPTION'),'class'=>'form-control','required')) !!}
											</div>
										</div>
									@endforeach
								@endif

							<div class="form-group">
									{{ Form::label('app_sec_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_IMAGE'),array('class'=>'control-label col-sm-2')) }}
								<div class="col-sm-6">
									{{ Form::file('app_sec_image',array('class' => 'form-control','accept'=>'image/*','id'=>'app_sec_image','onchange'=>'Upload(this.id,"380","640","400","670");')) }} 
									<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_FOOTER_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRE_FOOTER_IMAGE_VAL')}}</span>
									<img src="{{url('public/front/frontImages/').'/'.$details->app_sec_image}}" width="100px" height="100px">
								</div>
								<input type="hidden" name="preapp_sec_image" value="{{ $details->app_sec_image }}">	
							</div>
								<!-- ------------login image start------------- -->
							<div class="form-group">
									{{ Form::label('login_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN_IMAGE'),array('class'=>'control-label col-sm-2')) }}
								<div class="col-sm-6">
									{{ Form::file('login_image',array('class' => 'form-control','accept'=>'image/*','id'=>'login_image','onchange'=>'Upload(this.id,"465","335","475","345");')) }} 
									<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN_IMAGE_VAL')}}</span>
									<img src="{{url('public/front/frontImages/').'/'.$details->gs_login_image}}" width="80px" height="50px">
								</div>
								<input type="hidden" name="pre_login_image" value="{{ $details->gs_login_image }}">	

								<div class="radioError"></div>
							</div>
							<!-- ------------login image end------------- -->
							<!-- ------------Featured Rstaurant image start------------- -->
							<div class="form-group">
								{{ Form::label('feature_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEAT_RES_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEAT_RES_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEAT_RES_IMAGE'),array('class'=>'control-label col-sm-2')) }}
								<div class="col-sm-6">
									{{ Form::file('feature_image',array('class' => 'form-control','accept'=>'image/*','id'=>'login_image')) }}
									<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FEAT_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEAT_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEAT_IMAGE_VAL')}}</span>
									<img src="{{url('public/front/frontImages/').'/'.$details->gs_feature_res_image}}" width="80px" height="50px">
								</div>
								<input type="hidden" name="pre_feature_image" value="{{ $details->gs_feature_res_image }}">

								<div class="radioError"></div>
							</div>
							<!-- ------------featured Restaurant image end------------- -->




								<div class="form-group">
									{{ Form::label('itunes_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITUNES')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITUNES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITUNES'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::text('itunes_url',$details->gs_apple_appstore_url,array('class' => 'form-control','maxlength'=>'500','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITUNES')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITUNES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITUNES'))) }}</div>
								</div>
								<div class="form-group">
									{{ Form::label('playstore_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLAYSTORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLAYSTORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLAYSTORE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
									{{ Form::text('playstore_url',$details->gs_playstore_url,array('class' => 'form-control','maxlength'=>'500','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLAYSTORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLAYSTORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLAYSTORE'))) }}</div>
								</div>
								<div class="form-group" style="display:none">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_OTP_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_OTP_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OTP_STATUS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('otp_status','1',($details->otp_verification_status == 1) ? 'checked' : '' ,['id' => 'enable1','required']) }}
										{{ Form::label('enable1',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('otp_status','0',($details->otp_verification_status == 0) ? 'checked' : ''  ,['id' => 'disable1','required']) }}
										{{ Form::label('disable1',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div>
								<div class="" id="otpDiv" style="@if($details->otp_verification_status !='1') display:block @endif">
									<div class="form-group">
										{{ Form::label('gs_twilio_sid', (Lang::has(Session::get('admin_lang_file').'.ADMIN_TWILIO_SID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TWILIO_SID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TWILIO_SID'),array('class'=>'control-label col-sm-2 starclass')) }}
										<div class="col-sm-4">
											{{ Form::text('gs_twilio_sid',$details->gs_twilio_sid,array('class' => 'form-control','maxlength'=>'100')) }}
										</div> 
									</div>
									<div class="form-group">
										{{ Form::label('gs_twilio_token', (Lang::has(Session::get('admin_lang_file').'.ADMIN_TWILIO_TOKEN')) ? trans(Session::get('admin_lang_file').'.ADMIN_TWILIO_TOKEN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TWILIO_TOKEN'),array('class'=>'control-label col-sm-2 starclass')) }}
										<div class="col-sm-4">
											{{ Form::text('gs_twilio_token',$details->gs_twilio_token,array('class' => 'form-control','maxlength'=>'100')) }}
										</div> 
									</div>
									<div class="form-group">
										{{ Form::label('gs_twilio_from', (Lang::has(Session::get('admin_lang_file').'.ADMIN_TWILIO_FROM')) ? trans(Session::get('admin_lang_file').'.ADMIN_TWILIO_FROM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TWILIO_FROM'),array('class'=>'control-label col-sm-2 starclass')) }}
										<div class="col-sm-4">
											{{ Form::text('gs_twilio_from',$details->gs_twilio_from,array('class' => 'form-control','maxlength'=>'15','onkeypress'=>'return onlyNumbers(event);')) }}
										</div>
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAIL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAIL_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAIL_STATUS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('mail_status','1',($details->mail_verification_status == 1) ? 'checked' : '' ,['id' => 'enable2','required']) }}
										{{ Form::label('enable2',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('mail_status','0',($details->mail_verification_status == 0) ? 'checked' : ''  ,['id' => 'disable2','required']) }}
									{{ Form::label('disable2',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}</div>
								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELF_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELF_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELF_STATUS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('self_status','1',($details->self_pickup_status == 1) ? 'checked' : '' ,['id' => 'enable3','required']) }}
										{{ Form::label('enable3',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('self_status','0',($details->self_pickup_status == 0) ? 'checked' : ''  ,['id' => 'disable3','required']) }}
									{{ Form::label('disable3',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}</div>
								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SHOW_INVENTORY')) ? trans(Session::get('admin_lang_file').'.ADMIN_SHOW_INVENTORY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SHOW_INVENTORY'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_show_inventory','1',($details->gs_show_inventory == 1) ? 'checked' : '',['id' => 'yes','required']) }}
										{{ Form::label('yes',(Lang::has(Session::get('admin_lang_file').'.ADMIN_YES')) ? trans(Session::get('admin_lang_file').'.ADMIN_YES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_YES')) }}
										{{ Form::radio('gs_show_inventory','0',($details->gs_show_inventory == 0) ? 'checked' : '',['id' => 'no','required']) }}
										{{ Form::label('no',(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO')) }}
									</div>
								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERY_FEE_STATUS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_delivery_fee_status','1',($details->gs_delivery_fee_status == 1) ? 'checked' : '',['id' => 'enable4','required']) }}
										{{ Form::label('enable4',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_delivery_fee_status','0',($details->gs_delivery_fee_status == 0) ? 'checked' : '',['id' => 'disable4','required']) }}
									{{ Form::label('disable4',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}</div>
								</div>
								<div class="form-group" id="deliverFeeDiv" style="@if($details->gs_delivery_fee_status!='1') display:none @endif">
									{{ Form::label('dlivery_fee', (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERY_FEE_TYPE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::radio('del_fee_type','common_fee',($details->gs_del_fee_type == 'common_fee') ? 'checked' : '',['id' => 'common_fee'])}}
										{{ Form::label('common_fee',(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMM_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMM_FEE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMM_FEE'))}}
										{{ Form::radio('del_fee_type','km_fee',($details->gs_del_fee_type == 'km_fee') ? 'checked' : '',['id' => 'fee_km'])}}
										{{ Form::label('fee_km',(Lang::has(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEE_PER_KM'))}}
										<div class="error-block" style="color:red;"></div> 
										<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title=" Collecting delivery fee from the Customer. 
								If dont want can set as Zero"><span class="glyphicon glyphicon-question-sign"></span></a>
							
									</div>
									
								</div>
								
								<div class="form-group" id="commonFeeDiv" style="@if($details->gs_del_fee_type!='common_fee' || $details->gs_delivery_fee_status!='1') display:none @endif">
									{{ Form::label('dlivery_fee', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMM_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMM_FEE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMM_FEE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('gs_common_fee',$details->gs_delivery_fee,array('class' => 'form-control','maxlength'=>'10','onkeypress'=>"return onlyNumbersWithDot(event);",'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMM_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMM_FEE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMM_FEE'))) }}
									</div>
									<div class="col-sm-2">
										{!! Form::text('deli_curr',$default_currency,['readonly','class' => 'form-control'])!!}
									</div>
								</div>
								<div class="form-group" id="kmFeeDiv" style="@if($details->gs_del_fee_type!='km_fee' || ($details->gs_delivery_fee_status!='1')) display:none @endif">
									{{ Form::label('dlivery_fee', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEE_PER_KM'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('gs_km_fee',$details->gs_km_fee,array('class' => 'form-control','maxlength'=>'10','onkeypress'=>"return onlyNumbersWithDot(event);",'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEE_PER_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEE_PER_KM'))) }}
									</div>
									<div class="col-sm-2">
										{!! Form::text('deli_curr',$default_currency,['readonly','class' => 'form-control'])!!}
									</div>
								</div>
								<!--KILOMETER RANGE -->
								<div class="form-group">
									{{ Form::label('delivery_kmRange', (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_KMRANGE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('gs_delivery_kmrange',$details->gs_delivery_kmrange,array('class' => 'form-control','maxlength'=>'10','onkeypress'=>"return onlyNumbersWithDot(event);",'placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_KMRANGE'))) }}
									</div>
									<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.DELBOY_KMRANGE_EXPLANATION')) ? trans(Session::get('admin_lang_file').'.DELBOY_KMRANGE_EXPLANATION') : trans($ADMIN_OUR_LANGUAGE.'.DELBOY_KMRANGE_EXPLANATION')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
								</div>
								<!-- END OF KILOMETER RANGE -->
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_CART_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CART_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CART_MAIL'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_abandoned_mail','1',($details->gs_abandoned_mail == 1) ? 'checked' : '',['id' => 'enable','required']) }}
										{{ Form::label('enable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_abandoned_mail','0',($details->gs_abandoned_mail == 0) ? 'checked' : '',['id' => 'disable','required']) }}
										{{ Form::label('disable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div>
								<div class="form-group" id="mailDiv" style="@if($details->gs_abandoned_mail!='1') display:none @endif">
									{{ Form::label('gs_mail', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEND_MAIL_AFTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEND_MAIL_AFTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEND_MAIL_AFTER'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('gs_mail_after',$details->gs_mail_after,array('class' => 'form-control','maxlength'=>'3','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MAIL_AFTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAIL_AFTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAIL_AFTER'),'onkeypress'=> 'return onlyNumbers(event)')) }}
									</div>
									<div class="col-sm-2">
										{{ Form::text('mail_duration', __(Session::get('admin_lang_file').'.ADMIN_HOUR'),['class' => 'form-control','readonly'])}}
									</div>
								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVITE_FRNDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVITE_FRNDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVITE_FRNDS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_refer_friend','1',($details->gs_refer_friend == 1) ? 'checked' : '',['id' => 'enable_r','required']) }}
										{{ Form::label('enable_r',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_refer_friend','0',($details->gs_refer_friend == 0) ? 'checked' : '',['id' => 'disable_r','required']) }}
										{{ Form::label('disable_r',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div>

								<div class="form-group" id="referDiv" style="@if($details->gs_refer_friend!='1') display:none @endif">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OFFER_PERCENT'),array('class'=>'control-label col-sm-2 starclass')) }} 
									<i class="fa fa-info-circle tooltip-demo" title="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_INVITE_FRNDS_ONLY')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVITE_FRNDS_ONLY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVITE_FRNDS_ONLY')}}" style="float: left;padding:10px 0 0 0px;"></i> 
									<div class="col-sm-6">
										{{ Form::text('gs_offer_percentage',$details->gs_offer_percentage,array('class' => 'form-control','maxlength'=>'2','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OFFER_PERCENT'),'onkeypress'=> 'return onlyNumbers(event)')) }}
									</div>

									<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.REFER_FRIEND_EXPLANATION')) ? trans(Session::get('admin_lang_file').'.REFER_FRIEND_EXPLANATION') : trans($ADMIN_OUR_LANGUAGE.'.REFER_FRIEND_EXPLANATION')}}"><span class="glyphicon glyphicon-question-sign"></span></a>

								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PW_PROTECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PW_PROTECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PW_PROTECT'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_password_protect','1',($details->gs_password_protect == 1) ? 'checked' : '',['id' => 'enable_pw','required']) }}
										{{ Form::label('enable_pw',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_password_protect','0',($details->gs_password_protect == 0) ? 'checked' : '',['id' => 'disable_pw','required']) }}
										{{ Form::label('disable_pw',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}


										<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('admin_lang_file').'.PWD_PROTECT_EXPLANATION')) ? trans(Session::get('admin_lang_file').'.PWD_PROTECT_EXPLANATION') : trans($ADMIN_OUR_LANGUAGE.'.PWD_PROTECT_EXPLANATION')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
									</div>



								</div>
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SH_CAPTCHA')) ? trans(Session::get('admin_lang_file').'.ADMIN_SH_CAPTCHA') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SH_CAPTCHA'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_show_captcha','1',($details->gs_show_captcha == 1) ? 'checked' : '',['id' => 'enable_sc','required']) }}
										{{ Form::label('enable_sc',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_show_captcha','0',($details->gs_show_captcha == 0) ? 'checked' : '',['id' => 'disable_sc','required']) }}
										{{ Form::label('disable_sc',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div>
								<!--div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HIPPO_CHAT_STATUS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_hippo_chat_status','1',($details->gs_hippo_chat_status == 1) ? 'checked' : '',['id' => 'enable5','required']) }}
										{{ Form::label('enable5',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_hippo_chat_status','0',($details->gs_hippo_chat_status == 0) ? 'checked' : '',['id' => 'disable5','required']) }}
										{{ Form::label('disable5',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div-->
								<div class="form-group" id="hippochatDiv" style="@if($details->gs_hippo_chat_status!='1') display:none @endif">
									{{ Form::label('gs_hippo_secret_key', (Lang::has(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_SECRET_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HIPPO_CHAT_SECRET_KEY'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('gs_hippo_secret_key',$details->gs_hippo_secret_key,array('class' => 'form-control','maxlength'=>'100','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_HIPPO_CHAT_SECRET_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HIPPO_CHAT_SECRET_KEY'))) }}
									</div>
								</div>
								<!-- INVALID LOGIN ATTMEPTS -->
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_SUSPEND')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_SUSPEND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVALID_LOGIN_SUSPEND'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('suspend_status','1',($details->suspend_status == 1) ? 'checked' : '',['id' => 'suspend_enable','required']) }}
										{{ Form::label('suspend_enable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('suspend_status','0',($details->suspend_status == 0) ? 'checked' : '',['id' => 'suspend_disable','required']) }}
										{{ Form::label('suspend_disable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}
									</div>
								</div>
								<div id="suspendStatusDiv" style="@if($details->suspend_status!='1') display:none @endif">
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_ATTEMPTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_ATTEMPTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVALID_LOGIN_ATTEMPTS'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::text('max_attempt',$details->max_attempt,array('class' => 'form-control','maxlength'=>'2','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_ATTEMPTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_LOGIN_ATTEMPTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVALID_LOGIN_ATTEMPTS'),'onkeypress'=>'return onlyNumbers(event)')) }}
									</div>
								</div>

								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN_SUSPEND_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN_SUSPEND_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN_SUSPEND_TIME'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-4">
										{{ Form::text('suspend_time',$details->suspend_time,array('class' => 'form-control','maxlength'=>'2','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN_SUSPEND_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN_SUSPEND_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN_SUSPEND_TIME'),'onkeypress'=>'return onlyNumbers(event)')) }}
									</div>
									<div class="col-sm-2">
										{{ Form::select('suspend_duration',['minutes' => 'Minutes','hours' => 'Hours'],$details->suspend_duration,['class' => 'form-control'])}}
									</div>
								</div>
								</div>






								<!--FEATURED STORE ENABLE / DISABLE -->
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEATURED_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEATURED_STORE'),array('class'=>'control-label col-sm-2 starclass')) }}
									<div class="col-sm-6">
										{{ Form::radio('gs_featured_store','1',($details->gs_featured_store == '1') ? 'checked' : '',['id' => 'gs_featured_enable','required']) }}
										{{ Form::label('gs_featured_enable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')) }}
										{{ Form::radio('gs_featured_store','0',($details->gs_featured_store == '0') ? 'checked' : '',['id' => 'gs_featured_disable','required']) }}
										{{ Form::label('gs_featured_disable',(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')) }}

										({{(Lang::has(Session::get('admin_lang_file').'.ADMIN_12_STORES_ALLOWED')) ? trans(Session::get('admin_lang_file').'.ADMIN_12_STORES_ALLOWED') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_12_STORES_ALLOWED')}})
										<div class="error-block" style="color:red;"></div> 
									</div>
								</div>
								<!-- FEATURED PRICE -->
								<div  id="featured_priceDiv" style="@if($details->gs_featured_store!='1') display:none @endif">
									<div class="form-group">
										{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEATURED_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEATURED_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEATURED_PRICE'),array('class'=>'control-label col-sm-2 starclass')) }}
										<div class="col-sm-4">
											{{ Form::text('gs_featured_price',$details->gs_featured_price,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_FEATURED_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEATURED_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEATURED_PRICE'),'onkeypress'=>'return onlyNumbersWithDot(event)','id'=>'gs_featured_price')) }}
										</div>
									</div>
									<div class="form-group" style="display:none">
										{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_OF_FEATURED_STORE'),array('class'=>'control-label col-sm-2 starclass')) }}
										<div class="col-sm-4">


											{{ Form::text('gs_featured_numstore','12',array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_OF_FEATURED_STORE'),'onkeypress'=>'return onlyNumbers(event)','id'=>'gs_featured_numstore')) }}

											{{--{{ Form::text('gs_featured_numstore',$details->gs_featured_numstore,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_OF_FEATURED_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_OF_FEATURED_STORE'),'onkeypress'=>'return onlyNumbers(event)','id'=>'gs_featured_numstore')) }}--}}
										</div>
									</div>
								</div>
								<!-- EOF FEATURED STORE ENABLE/DISABLE-->
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION_SETTINGS'),array('class'=>'control-label col-sm-2 starclass')) }} <i class="fa fa-info-circle tooltip-demo" title="@lang(Session::get('admin_lang_file').'.ADMIN_COMM_INFO')" style="float:left;padding:10px 0 0 0px;"></i>
									<div class="col-sm-4">
										<input class="form-control" maxlength="5" placeholder="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION_PERCENTAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION_PERCENTAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION_PERCENTAGE')}}" name="commissionpercentage" type="text" value="{{$details->common_commission}}">
									</div>
								</div>
								<!-- CURRENCY CONVERTER -->
								@if(Session::get('default_currency_code')!='USD') 
									@php 
										$mandClass = 'starclass';
										$hidCurrencyVal = '1';
									@endphp 
								@else 
									@php 
										$mandClass = '';
										$hidCurrencyVal = '0';
									@endphp  
								@endif
								<input type="hidden" name="hid_currency_code" id="hid_currency_code" value="{{Session::get('default_currency_code')}}" />
								
								<div class="form-group">
									{{ Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_API_KEY_CURRENCY')) ? trans(Session::get('admin_lang_file').'.ADMIN_API_KEY_CURRENCY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_API_KEY_CURRENCY'),array('class'=>'control-label col-sm-2  '.$mandClass.'')) }} <i class="fa fa-info-circle tooltip-demo" title="@lang(Session::get('admin_lang_file').'.ADMIN_API_CURRENCY_INFO')" style="float:left;padding:10px 0 0 0px;"></i>
									<div class="col-sm-4">
										<input class="form-control" placeholder="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_API_KEY_CURRENCY')) ? trans(Session::get('admin_lang_file').'.ADMIN_API_KEY_CURRENCY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_API_KEY_CURRENCY')}}" name="gs_currency_api" type="text" value="{{$details->gs_currency_api}}">
										<span class="help-block">@lang(Session::get('admin_lang_file').'.ADMIN_API_HELP_BLOCK')</span>
									</div>
								</div>
								<!-- EOF CURRENCY CONVERTER -->
								<div class="form-group">
									<div class="col-sm-2"></div>
									<div class="col-sm-4">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>
								
								{{ Form::close() }}
								
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

	$("#general_settings_form").validate({
		//alert($('input[name=gs_delivery_fee_status]:checked').val());
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			sitename: "required",
			description: "required",
			email: {
				required: true,
				email: true
			},
			metatitle: "required",
			phone: "required",
			metakeywords: "required",
			metadescription: "required",
			footertext: "required",
			otp_status: "required",
			mail_status: "required",
			self_status: "required",
			itunes_url: {
				required: true,
				url: true
			},
			playstore_url: {
				required: true,
				url: true
			},
			gs_delivery_fee_status:"required",
            gs_delivery_kmrange:"required",
			gs_refer_friend:"required",
			gs_abandoned_mail:"required",
			gs_show_inventory:"required",
			gs_show_captcha:"required",
			gs_password_protect:"required",
			"commissionpercentage" :{
				required: true,
			    range:[0,100]
			},
			"del_fee_type": {
				required: {
					depends: function(element) {
						if($('input[name=gs_delivery_fee_status]:checked').val()==1){
							 return true;
						}else{
							  return false;
						}
						//return false;
						//if($('input[name=gs_delivery_fee_status]:checked').val()=='1'){ return true; alert('if'+$('input[name=gs_delivery_fee_status]:checked').val());} else {  alert('else'+$('input[name=gs_delivery_fee_status]:checked').val()); return false; } 
					}
				}
			},
			"gs_common_fee": {
				required: {
					depends: function(element) {
						if($('input[name=del_fee_type]:checked').val()=='common_fee'){ return true; } else { return false; } 
					}
				}
			},
			"gs_km_fee": {
				required: {
					depends: function(element) {
						if($('input[name=del_fee_type]:checked').val()=='km_fee'){ return true; } else { return false; } 
					}
				}
			},
			"gs_offer_percentage": {
				required: {
					depends: function(element) {
						if($('input[name=gs_refer_friend]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			gs_abandoned_mail:"required",
			"gs_mail_after": {
				required: {
					depends: function(element) {
						if($('input[name=gs_abandoned_mail]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"gs_hippo_secret_key": {
				required: {
					depends: function(element) {
						if($('input[name=gs_hippo_chat_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			
			"gs_twilio_sid": {
				required: {
					depends: function(element) {
						if($('input[name=otp_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"gs_twilio_token": {
				required: {
					depends: function(element) {
						if($('input[name=otp_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"gs_twilio_from": {
				required: {
					depends: function(element) {
						if($('input[name=otp_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"max_attempt": {
				required: {
					depends: function(element) {
						if($('input[name=suspend_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
			"suspend_time": {
				required: {
					depends: function(element) {
						if($('input[name=suspend_status]:checked').val()=='1'){ return true; } else { return false; } 
					}
				}
			},
            "gs_featured_price": {
                required: {
                    depends: function(element) {
                        if($('input[name=gs_featured_store]:checked').val()=='1'){ console.log('irfan'+$('input[name=gs_featured_store]:checked').val()); return true; } else { return false; }
                    }
                }
            },
            "gs_featured_numstore": {
                required: {
                    depends: function(element) {
                        if($('input[name=gs_featured_store]:checked').val()=='1'){ return true; } else { return false; }
                    }
                }
            },
			"gs_currency_api": {
                required: {
                    depends: function(element) {
                        if($('#hid_currency_code').val()=='1'){ return true; } else { return false; }
                    }
                }
            },
			
		},
		messages: {
			sitename: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SITE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SITE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_SITE_NAME')}}",
			description: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION')}}",
			email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
			metatitle: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METATITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METATITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_METATITLE')}}",
			metakeywords: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METAKEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METAKEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_METAKEYWORDS')}}",
			metadescription: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METADESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_METADESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_METADESC')}}",
			footertext: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FOOTERTEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FOOTERTEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_FOOTERTEXT')}}",
			itunes_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ITUNES_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ITUNES_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_ITUNES_URL')}}",
			playstore_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_PLAYSTORE_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_PLAYSTORE_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_PLAYSTORE_URL')}}",
			gs_delivery_fee_status: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DELIFEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DELIFEE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DELIFEE')}}",
			del_fee_type: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_DELIFEE_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_DELIFEE_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_DELIFEE_TYPE')}}",
			gs_common_fee: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DELIFEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DELIFEE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DELIFEE')}}",
			gs_km_fee: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_KM')}}",

            gs_delivery_kmrange: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_KMRANGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_KMRANGE')}}",

            gs_hippo_secret_key: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_HIPPO_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_HIPPO_SECRET_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_HIPPO_SECRET_KEY')}}",
			gs_twilio_sid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_TWILIO_ID')}}",
			gs_twilio_token: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_TWILIO_ID')}}",
			gs_twilio_from: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_FROM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_TWILIO_FROM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_TWILIO_FROM')}}",
			gs_refer_friend: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_REFER_FRND')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_REFER_FRND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_REFER_FRND')}}",
			gs_offer_percentage: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_OFFER_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_OFFER_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_OFFER_PERCENT')}}",
			gs_password_protect: "@lang(Session::get('admin_lang_file').'.ADMIN_SL_PW_ST')",
			gs_show_captcha: "@lang(Session::get('admin_lang_file').'.ADMIN_SL_CAPTCHA_ST')",
			suspend_time: "@lang(Session::get('admin_lang_file').'.ADMIN_EN_LOGIN_SUSPEND_TIME')",
			max_attempt: "@lang(Session::get('admin_lang_file').'.ADMIN_LOGIN_ATTEMPT')",
            gs_featured_price: "@lang(Session::get('admin_lang_file').'.ADMIN_ENTER_FEATURED_PRICE')",
            gs_featured_numstore: "@lang(Session::get('admin_lang_file').'.ADMIN_ENTER_NO_OF_FEATURED_STORE')",

            commissionpercentage: "@lang(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION_0_TO_100')",
			gs_currency_api:"@lang(Session::get('admin_lang_file').'.ADMIN_API_KEY_CURRENCY_ERR')",


        },
		errorPlacement: function(error, element) 
		{
			if ( element.is(":radio") ) 
			{
				//alert($(element).parents('div').next().html());
				error.appendTo($(element).next().next().next().next());
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
	
	
	function onlyNumbersWithDot(e) {           
		var charCode;
		if (e.keyCode > 0) {
			charCode = e.which || e.keyCode;
		}
		else if (typeof (e.charCode) != "undefined") {
			charCode = e.which || e.keyCode;
		}
		if (charCode == 46)
		return true
		if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
		return true;
	}
	
	function onlyNumbers(evt) {
		var e = event || evt; // for trans-browser compatibility
		var charCode = e.which || e.keyCode;
		
		if (charCode > 31 && (charCode < 42 || charCode > 57))
		return false;
		return true;
	}
	
	$('input[name=gs_delivery_fee_status]').change(function(){
		var value = $( 'input[name=gs_delivery_fee_status]:checked' ).val();
		if(value=='1')
		{
			$('#deliverFeeDiv').slideDown();
			var type_value = $( 'input[name=del_fee_type]:checked' ).val();
			if(type_value=='common_fee')
			{
				$('#commonFeeDiv').slideDown();
			}
			else if(type_value=='km_fee')
			{
				$('#kmFeeDiv').slideDown();
			}
			
		}
		else
		{
			$('#deliverFeeDiv').slideUp();
			$('#commonFeeDiv').slideUp();
			$('#kmFeeDiv').slideUp();
		}
	});
	
	$('input[name=del_fee_type]').change(function(){
		var value = $( 'input[name=del_fee_type]:checked' ).val();
		if(value=='common_fee')
		{
			$('#commonFeeDiv').slideDown();
			$('#kmFeeDiv').slideUp();
		}
		else if(value=='km_fee')
		{
			$('#kmFeeDiv').slideDown();
			$('#commonFeeDiv').slideUp();
		}
		else
		{
			$('#commonFeeDiv').slideUp();
			$('#kmFeeDiv').slideUp();
		}
	});
	$('input[name=gs_abandoned_mail]').change(function(){
		var value = $( 'input[name=gs_abandoned_mail]:checked' ).val();
		if(value=='1')
		{
			$('#mailDiv').slideDown();
		}
		else
		{
			$('#mailDiv').slideUp();
		}
	});
	
	$('input[name=gs_refer_friend]').change(function(){
		var value = $( 'input[name=gs_refer_friend]:checked' ).val();
		if(value=='1')
		{
			$('#referDiv').slideDown();
		}
		else
		{
			$('#referDiv').slideUp();
		}
	});
	
	$('input[name=gs_hippo_chat_status]').change(function(){
		var value = $( 'input[name=gs_hippo_chat_status]:checked' ).val();
		if(value=='1')
		{
			$('#hippochatDiv').slideDown();
		}
		else
		{
			$('#hippochatDiv').slideUp();
		}
	});
	
	$('input[name=suspend_status]').change(function(){
		var value = $( 'input[name=suspend_status]:checked' ).val();
		if(value=='1')
		{
			$('#suspendStatusDiv').slideDown();
		}
		else
		{
			$('#suspendStatusDiv').slideUp();
		}
	});
	
	$('input[name=otp_status]').change(function(){
		var value = $( 'input[name=otp_status]:checked' ).val();
		if(value=='1')
		{
			$('#otpDiv').slideDown();
		}
		else
		{
			$('#otpDiv').slideUp();
		}
	});


    /* ------------ FEATURED PRICE -----------*/
    $('input[name=gs_featured_store]').change(function(){
        var value = $( 'input[name=gs_featured_store]:checked' ).val();
        if(value=='1')
        {
            $('#featured_priceDiv').slideDown();
        }
        else
        {
            $('#featured_priceDiv').slideUp();
        }
    });
    /* ------------ EOF FEATURED PRICE -------*/
	
	function Upload(files,widthParam,heightParam,maxwidthparam,maxheightparam)
	{
		//alert('s'+files+'\n'+widthParam+'\n'+heightParam+'\n'+maxwidthparam+'\n'+maxheightparam);
		var fileUpload = document.getElementById(files);
		//alert(fileUpload.name);
		var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif|.jpeg)$");
		if (regex.test(fileUpload.value.toLowerCase()))
		{
			if (typeof (fileUpload.files) != "undefined")
			{
				var reader = new FileReader();
				reader.readAsDataURL(fileUpload.files[0]);
				reader.onload = function (e)
				{
					var image = new Image();
					image.src = e.target.result;

					image.onload = function ()
					{
						var height = this.height;
						var width = this.width;
						//alert(height +'<'+ heightParam +'&&'+ height +'>'+ maxheightparam+')|| ('+width+' < '+widthParam +'&& '+width+' > '+maxwidthparam+')');
						if (height < heightParam || height > maxheightparam|| width < widthParam || width > maxwidthparam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image above '+widthParam+'X'+heightParam+' and below '+maxwidthparam+'X'+maxheightparam);
							$("#"+files).val('');
							$("#"+files).focus();
							return false;
						}
						return true;
					};
				}
			}
			else
			{
				alert("This browser does not support HTML5.");
				$("#image").val('');
				return false;
			}
		}
		/*else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}*/
	}
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>



@endsection
@stop
