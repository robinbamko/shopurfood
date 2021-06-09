@extends('Admin.layouts.default')

@section('content')

<div class="main">
			<!-- MAIN CONTENT -->
			<div class="main-content">
				<div class="container-fluid">
					<h3 class="panel-heading page-title">{{$pagetitle}}</h3>
					<div class="row">
						<div class="panel">
				
				<div class="panel-body no-padding">
					<table class="table">
						<thead>
							<tr>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_NAME')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_CODE')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_SYM')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIAL_CODE')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DEFAULT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEFAULT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEFAULT')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
								</th>
								<th>
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@if(count($all_details) > 0)
							@php $i = 1; 
							$co_name = 'co_name_'.Session::get('admin_lang_code'); 
							@endphp
							@foreach($all_details as $details)
							<tr>
								<td>{{$i}}</td>
								<td>{{ ucfirst($details->$co_name)}}</td>
								<td>{{$details->co_code}}</td>
								<td>{{$details->co_curcode}}</td>
								<td>{{$details->co_cursymbol}}</td>
								<td>{{$details->co_dialcode}}</td>
								<td>
									{!! Form::radio('','',($details->default_counrty == 1) ? true : '')!!}	
								</td>
								<td><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></td>
								<td>
									@if($details->co_status == 1)  {{--0-block, 1- active --}}
										<a href="{!! url('country_status').'/'.$details->id.'/0' !!}"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>
									@else
										<a href="{!! url('country_status').'/'.$details->id.'/1' !!}"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" onClick="change_status($details->id)"></i></a>
									@endif
								</td>
							</tr>
							@php $i++; @endphp
							@endforeach
							@else
								{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS')}}
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<!-- END TABLE NO PADDING -->

		</div>
	</div>
							</div>
						</div>
					

@stop