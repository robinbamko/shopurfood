@extends('sitemerchant.layouts.default')
@section('content')
	<!-- MAIN -->
	<style>
		.checked {
			color: orange;
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
								<div class="panel-heading p__title">
									{{$pagetitle}}
								</div>
								@if(Session::has('message'))
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{ Session::get('message') }}
									</div>
								@endif
								<div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
									<i class="fa fa-times-circle"></i>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
								</div>


								{{-- Add/Edit page ends--}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="collapse @php  if(isset($id)){ echo 'in';} @endphp panel-body">
										<div class="row-fluid well">
											@if(isset($id) && empty($detail) === false)
												<div class="row panel-heading">
													<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}
												</span>
													</div>
													<div class="col-md-8">
														{!! $detail->sr_comments !!}
													</div>
												</div>
												<div class="row panel-heading">
													<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RATING_REVIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_RATING_REVIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RATING_REVIEW')}}
												</span>
													</div>
													<div class="col-md-8">
														@if($detail->sr_rating != '')
															@for($i= 0;$i<$detail->sr_rating; $i++)
																<i class="fa fa-star checked"></i>
															@endfor
														@else
															-
														@endif
													</div>
												</div>
												<div class="panel-heading">
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:history.go(-1)">

												</div>
											@endif
										</div>
									</div>
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


@stop