@extends('Front.layouts.default')
@section('content')
	<!-- MAIN -->
	<style>
		.checked {
			color: orange;
		}
	</style>
	<div class="main">
		<!-- MAIN CONTENT -->
		<div class="main-content">

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



								{{-- Add/Edit page ends--}}
								<div class="box-body spaced" style="padding:20px">

									<div id="location_form" class="">

										<div class="row-fluid well">

											@if(isset($id) && empty($detail) === false)

												<div class="row panel-heading">
													<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}
												</span>
													</div>
													<div class="col-md-8">
														{!! $detail->review_comments !!}
													</div>
												</div>
												<div class="row panel-heading">
													<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_RATING_REVIEW')}}
												</span>
													</div>
													<div class="col-md-8">
														@if($detail->review_rating != '')
															@for($i= 0;$i<$detail->review_rating; $i++)
																<i class="fa fa-star checked"></i>
															@endfor
														@else
															-
														@endif
													</div>
												</div>
												<div class="panel-heading">
													<input type="button" value="Back" class="btn btn-warning" onclick="javascript:history.go(-1)">

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