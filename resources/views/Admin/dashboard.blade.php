@extends('Admin.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop

@section('content')

	<style>
	footer{width: 100%; float: none;}
		.switch {
			position: relative;
			display: inline-block;
			width: 60px;
			height: 34px;
		}

		.switch input {
			opacity: 0;
			width: 0;
			height: 0;
		}

		.slider {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #ccc;
			-webkit-transition: .4s;
			transition: .4s;
		}

		.slider:before {
			position: absolute;
			content: "";
			height: 26px;
			width: 26px;
			left: 4px;
			bottom: 4px;
			background-color: white;
			-webkit-transition: .4s;
			transition: .4s;
		}

		input:checked + .slider {
			background-color: #373756;
		}

		input:focus + .slider {
			box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
			-webkit-transform: translateX(26px);
			-ms-transform: translateX(26px);
			transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round {
			border-radius: 34px;
		}

		.slider.round:before {
			border-radius: 50%;
		}
	</style>

	<div class="main">
		<style>
			.blue {
				color: #3498DB;
			}
			.green {
				color: #1ABB9C;
			}
			.purple {
				color: #9B59B6;
			}
			.red {
				color: rgb(231, 76, 60);
			}
			table.tile_info td {
				text-align: left;
				padding: 1px;
				font-size: 15px;
			}
			body #chartdiv {
				width:100%;
				height: 236px;
			}
			.x_content table tr td {
				vertical-align: middle;
			}
			table.tile_info {
				padding: 10px 15px;
			}
			table.tile_info {
				width: 100%;
			}
			table.tile_info td i {
				margin-right: 8px;
				font-size: 17px;
				float: left;
				width: 18px;
				line-height: 28px;
			}
			.custom-grap div
			{
				padding-left:10px;
				padding-right:10px;
			}

			.custom-grap p
			{
				font-size:14px;
			}
		</style>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						@if(Session::has('message'))
							<div class="alert alert-success alert-dismissible">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								{{Session::get('message')}}
							</div>
						@endif
						<!-- REALTIME CHART -->
						<div class="panel">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<div class="metric">
											<span class="icon"><i class="fa fa-users"></i></span>
											<p>
												<span class="number">{{$customer_count}}</span>
												<span class="title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER')}}
												</span>
											</p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="metric">
											<span class="icon"><i class="fa fa-users"></i></span>
											<p>
												<span class="number">{{$merchant_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT')}}s
									</span>
											</p>
										</div>
									</div>

									<div class="col-md-3">
										<div class="metric">
											<span class="icon"><i class="fa fa-cutlery"></i></span>
											<p>
												<span class="number">{{$restaurant_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTS')}}
									</span>
											</p>
										</div>
									</div>

									<div class="col-md-3">
										<div class="metric">
											<span class="icon"><i class="fa fa-cutlery"></i></span>
											<p>
												<span class="number">{{$item_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_ITEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_ITEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_ITEM')}}
									</span>
											</p>
										</div>
									</div>
								</div>
								<div class="row">

									<div class="col-md-4">
										<div class="metric">
											<span class="icon"><i class="fa fa-users"></i></span>
											<p>
												<span class="number">{{$delivery_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_MEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_MEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELI_MEM')}}
									</span>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="metric">
											<span class="icon"><i class="fa fa-shopping-cart"></i></span>
											<p>
												<span class="number">{{$order_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDS')}}
									</span>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="metric">
											<span class="icon"><i class="fa fa-shopping-cart"></i></span>
											<p>
												<span class="number">{{$delivered_count}}</span>
												<span class="title">
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_ORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_ORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELI_ORDS')}}
									</span>
											</p>
										</div>
									</div>
								</div>

							</div>

						</div>
					</div>
					<!-- END MAIN CONTENT -->
				</div>
				<!-- OVERVIEW -->
				<div class="panel panel-headline">
					{{-- <div class="panel-heading">
                        <h3 class="panel-title">Weekly Overview</h3>
                        <p class="panel-subtitle">Period: Oct 14, 2016 - Oct 21, 2016</p>
                    </div> --}}
					
					<div class="panel-body">
						<div class="x_title">
							<h2>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_ORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDS') !!}</h2>

							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="x_content">
								<div id="cartdiv"></div>
							</div>
						</div>
					</div>
				</div>
				<!-- END OVERVIEW -->
				
				<div class="row">
					<div class="col-md-6">
						<!-- RECENT PURCHASES -->
						<div class="panel">
							<div class="panel-heading">
								<h3 class="panel-title">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER') !!} <label style="display: none" id="titlechangecustomer">-{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_LATEST')) ? trans(Session::get('admin_lang_file').'.ADMIN_LATEST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LATEST') !!}</label></h3>
							</div>
							<div id="last5" class="panel-body no-padding">
								<div class="table-responsive">
								<table class="table table-striped">
									<thead>
									<tr>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PHONE') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS') !!}</th>
									</tr>
									</thead>
									<tbody>
									@foreach($customers as  $key => $customer)
										<tr>
											<td>{{++$key}}</td>
											<td>{{$customer->cus_fname}}</td>
											<td>{{$customer->cus_email}}</td>
											<td>{{$customer->cus_phone1}}</td>
											<td><span class="label label-success">{{'Active'}}</span></td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							</div>
							<div id="last24" style="display:none" class="panel-body no-padding">
								<div class="table-responsive">
								<table class="table table-striped">
									<thead>
									<tr>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PHONE') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS') !!}</th>
									</tr>
									</thead>
									<tbody>
									@foreach($recentcustomers as  $key => $customer)
										<tr>
											<td>{{++$key}}</td>
											<td>{{$customer->cus_fname}}</td>
											<td>{{$customer->cus_email}}</td>
											<td>{{$customer->cus_phone1}}</td>
											<td>
												@if($customer->cus_status == '1')
												<span class="label label-success">{{'Active'}}</span>
												@elseif($customer->cus_status == '0')
												<span class="label label-danger">{{'Deactive'}}</span>
												@endif
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							</div>
							<div class="panel-footer">
								<div class="row">
									<div class="col-md-6"><label class="switch">
											<input id="customerchange" type="checkbox" checked>
											<span class="slider round"></span>
										</label>
									</div>
									<div class="col-md-6 text-right"><a href="{{url('manage-customer')}}" class="btn btn-primary">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW_CUSTOMER') !!}</a></div>
								</div>
							</div>
						</div>
						<!-- END RECENT PURCHASES -->
					</div>
					<div class="col-md-6">
						<!-- RECENT PURCHASES -->
						<div class="panel">
							<div class="panel-heading">
								<h3 class="panel-title">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT') !!}
									<label style="display: none" id="titlechangemerchant">-{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_LATEST')) ? trans(Session::get('admin_lang_file').'.ADMIN_LATEST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LATEST') !!}</label>
								</h3>
							</div>
							<div id="mer_last5"  class="panel-body no-padding">
								<div class="table-responsive">
								<table class="table table-striped">
									<thead>
									<tr>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.MER_EMAIL')) ? trans(Session::get('admin_lang_file').'.MER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.MER_EMAIL') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS') !!}</th>
									</tr>
									</thead>
									<tbody>
									@foreach($merchants as $key =>   $merchant)
										<tr>
											<td>{{++$key}}</td>
											<td>{{$merchant->mer_fname}}</td>
											<td>{{$merchant->mer_email}}</td>
											<td>{{$merchant->mer_phone}}</td>
											<td>
												@if($merchant->mer_status == '1')
												<span class="label label-success">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_ACTIVE') !!}</span>
												@elseif($merchant->mer_status == '0')
												<span class="label label-danger">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEACTIVE') !!}</span>
												@endif
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							</div>
							<div id="mer_last24" style="display: none;" class="panel-body no-padding">
								<div class="table-responsive">
								<table class="table table-striped">
									<thead>
									<tr>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.MER_EMAIL')) ? trans(Session::get('admin_lang_file').'.MER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.MER_EMAIL') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE') !!}</th>
										<th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS') !!}</th>
									</tr>
									</thead>
									<tbody>
									@foreach($recentmerchants as $key =>   $merchant)
										<tr>
											<td>{{++$key}}</td>
											<td>{{$merchant->mer_fname}}</td>
											<td>{{$merchant->mer_email}}</td>
											<td>{{$merchant->mer_phone}}</td>
											<td><span class="label label-success">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_ACTIVE') !!}</span></td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							</div>
							<div class="panel-footer">
								<div class="row">
									<div class="col-md-6">
										<label class="switch">
											<input id="merchantchange" type="checkbox" checked>
											<span class="slider round"></span>
										</label>
									</div>
									<div class="col-md-6 text-right"><a href="{{url('manage-merchant')}}" class="btn btn-primary">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_ALL_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_ALL_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VIEW_ALL_MERCHANT') !!}</a></div>
								</div>
							</div>
						</div>
						<!-- END RECENT PURCHASES -->
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="panel grp-height">
							<!-- RECENT PURCHASES -->
							<div class="panel-body">
								<div class="x_panel fixed_height_320">
									<div class="x_title">
										<h2>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_GRAPH')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_GRAPH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_GRAPH') !!}</h2>

										<div class="clearfix"></div>
									</div>
									<div class="x_content">

										<div class="row">
											<div class="col-md-4 col-xs-12 col-sm-6">
												<div class="cus-grp"><canvas id="canvas1" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas></div>
											</div>
											<div class="col-md-8 col-xs-12 col-sm-6">
												<div class="table-responsive">
													<table class="tile_info">
														<tr><th><p class="">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_REGISTRATION_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_REGISTRATION_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REGISTRATION_TYPE') !!}</p></th>
															<th>
																<p class="">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_PROGRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PROGRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PROGRESS') !!} (%)</p>
															</th></tr>
														<tr>
															<td>
																<p><i class="fa fa-square red"></i>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_NORMAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NORMAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NORMAL') !!}</p>
															</td>
															<td>
                                                                <?php //echo count($Usertype_Customer_normal);

                                                                $user_normal = (($Customer_normal)*4)/100; if(($Customer_normal)==0){  if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_REGISTERED'); } } else{echo $user_normal.'%';}?>
															</td>
														</tr>
														<tr>
															<td>
																<p><i class="fa fa-square blue"></i>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN') !!} </p>
															</td>
															<td>
                                                                <?php $user_admin = (($Customer_admin)*4)/100; if(($Customer_admin)==0){if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_REGISTERED'); } } else{echo $user_admin.'%';}?>
															</td>
														</tr>
														<tr>
															<td>
																<p><i class="fa fa-square green"></i>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_FACEBOOK')) ? trans(Session::get('admin_lang_file').'.ADMIN_FACEBOOK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FACEBOOK') !!}</p>
															</td>
															<td>
                                                                <?php $user_fb = ($Customer_facebook)*4/100; if(($Customer_facebook)==0){if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_REGISTERED'); }} else{echo $user_fb.'%';}?>
															</td>
														</tr>
														<tr>
															<td>
																<p><i class="fa fa-square purple"></i>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE') !!}</p>
															</td>
															<td>
                                                                <?php $user_fb = ($Customer_google)*4/100; if(($Customer_google)==0){if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NOT_REGISTERED'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_REGISTERED'); }} else{echo $user_fb.'%';}?>
															</td>
														</tr>


													</table></div>
											</div>

										</div>

									</div>
								</div>
							</div>
						</div>
						<!-- END RECENT PURCHASES -->
					</div>
					<div class="col-md-6">
						<!-- MULTI CHARTS -->
						<div class="panel">

							<div class="panel-body">
								<div class="x_panel">
									<div class="x_title">
										<h2>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_VENDOR_GRAPH')) ? trans(Session::get('admin_lang_file').'.ADMIN_VENDOR_GRAPH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VENDOR_GRAPH') !!}</h2>
										<ul class="nav navbar-right panel_toolbox">

										</ul>
										<div class="clearfix"></div>
									</div>
									<div class="x_content">

                                        <?php if( (($merchant_deactive)==0)  && (($merchant_active)==0)  ) {if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS_FOUND'); }  } else { ?>
										<canvas id="canvasDoughnut2"></canvas>
                                        <?php } ?>
									</div>
								</div>
							</div>
						</div>
						<!-- END MULTI CHARTS -->
					</div>
				</div>
				<div class="row">

					<div class="col-md-6">
						<!-- TIMELINE -->

						<div class="panel">
							<div class="panel-body">
								<div class="x_panel">
									<div class="x_title">
										<h2>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_GR')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_GR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_GR') !!}</h2>
										<ul class="nav navbar-right panel_toolbox">

										</ul>
										<div class="clearfix"></div>
									</div>
									<div class="x_content">

                                        <?php if( (($restaurant_deactive)==0)  && (($restaurant_active)==0)  ) {if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS_FOUND'); }  } else { ?>
										<canvas id="canvasDoughnut1"></canvas>
                                        <?php } ?>
									</div>
								</div>
							</div>
						</div>
						<!-- END TIMELINE -->
					</div>

					<div class="col-md-6">
						<!-- TODO LIST -->
						<div class="panel">

							<div class="panel-body">
								<div class="x_panel">
									<div class="x_title">
										<h2>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERYBOY_GR')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERYBOY_GR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERYBOY_GR') !!}</h2>
										<ul class="nav navbar-right panel_toolbox">

										</ul>
										<div class="clearfix"></div>
									</div>
									<div class="x_content">

                                        <?php if( (($deliveryboy_deactive)==0)  && (($deliveryboy_active)==0)  ) {if (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND')) { echo  trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS_FOUND'); } else { echo trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS_FOUND'); }  } else { ?>
										<canvas id="canvasDoughnut"></canvas>
                                        <?php } ?>
									</div>
								</div>
							</div>
						</div>
						<!-- END TODO LIST -->
					</div>
				</div>
				
				<script>
                    $('#customerchange').change(function() {
                        if($(this).is(":checked")) {
                            $("#last5").show();
                            $("#last24").hide();
                            $('#titlechangecustomer').hide();
                        }
                        else{
                            $("#last24").show();
                            $("#last5").hide();
                            $('#titlechangecustomer').show();
                        }
                    });

                    $('#merchantchange').change(function() {
                        if($(this).is(":checked")) {
                            $("#mer_last5").show();
                            $("#mer_last24").hide();
                            $('#titlechangemerchant').hide();
                        }
                        else{
                            $("#mer_last24").show();
                            $("#mer_last5").hide();
                            $('#titlechangemerchant').show();
                        }
                    });



				</script>
				@section('script')

					<script src="{{url('')}}/public/js/canvasjs.min.js"></script>
					<script src="{{url('')}}/public/js/Chart.min.js"></script>
					<script src="{{url('')}}/public/js/amcharts.js" type="text/javascript"></script>
					<script src="{{url('')}}/public/js/serial.js" type="text/javascript"></script>


					<script>
                        Chart.defaults.global.legend = {
                            enabled: false
                        };

                        $(document).ready(function() {
                            var options = {
                                legend: false,
                                responsive: false
                            };

                            new Chart(document.getElementById("canvas1"), {
                                type: 'doughnut',
                                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                                data: {
                                    labels: [
                                        '<?php echo  (Lang::has(Session::get('admin_lang_file').'.ADMIN_NORMAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NORMAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NORMAL') ?>',
                                        '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN') ?>',
                                        '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_FACEBOOK')) ? trans(Session::get('admin_lang_file').'.ADMIN_FACEBOOK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FACEBOOK') ?>',
                                        '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE') ?>',
                                    ],
                                    datasets: [{
                                        data: ['<?php echo ($Customer_normal);?>', '<?php echo ($Customer_admin);?>', '<?php echo ($Customer_facebook);?>','<?php echo $Customer_google; ?>'],
                                        backgroundColor: [
                                            "#E74C3C",
                                            "#3498DB",
                                            "#1ABB9C",
                                            "#9B59B6"
                                        ],
                                        hoverBackgroundColor: [
                                            "#E74C3C",
                                            "#3498DB",
                                            "#1ABB9C",
                                            "#9B59B6"
                                        ]
                                    }]
                                },
                                options: options
                            });
                        });

                        /** merchant graph **/

					</script>
					<script>

                        /** store graph **/

                            // Doughnut chart
                        var ctx = document.getElementById("canvasDoughnut");
                        var data = {
                            labels: [
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_DEACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_DEACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_DEACTIVE') ; ?>',
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_ACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_ACTIVE') ; ?>'
                            ],
                            datasets: [{
                                data: [<?php echo ($deliveryboy_deactive); ?>, <?php echo ($deliveryboy_active); ?>],
                                backgroundColor: [
                                    "#455C73",
                                    "#BDC3C7",
                                    "#26B99A",
                                    "#3498DB"
                                ],
                                hoverBackgroundColor: [
                                    "#34495E",
                                    "#CFD4D8",
                                    "#36CAAB",
                                    "#49A9EA"
                                ]

                            }]
                        };
                            <?php if($deliveryboy_deactive == 0 && $deliveryboy_active == 0 ) { } else { ?>
                        var canvasDoughnut = new Chart(ctx, {
                                type: 'doughnut',
                                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                                data: data
                            });
                        <?php } ?>

                        /** restaurant graph **/

                            // Doughnut chart
                        var ctx = document.getElementById("canvasDoughnut1");
                        var data = {
                            labels: [
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_RE_DEACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_RE_DEACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RE_DEACTIVE') ; ?>',
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_RE_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_RE_ACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RE_ACTIVE') ; ?>'
                            ],
                            datasets: [{
                                data: [<?php echo ($restaurant_deactive); ?>, <?php echo ($restaurant_active); ?>],
                                //data: [120, 50, 140, 180, 100],
                                backgroundColor: [
                                    "#455C73",
                                    "#BDC3C7",
                                    "#26B99A",
                                    "#3498DB"
                                ],
                                hoverBackgroundColor: [
                                    "#34495E",
                                    "#CFD4D8",
                                    "#36CAAB",
                                    "#49A9EA"
                                ]
                            }]
                        };
                            <?php if($restaurant_deactive == 0 && $restaurant_active == 0 ) { } else { ?>
                        var canvasDoughnut1 = new Chart(ctx, {
                                type: 'doughnut',
                                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                                data: data
                            });
                        <?php } ?>
                        /* Doughnut chart */
                        var ctx = document.getElementById("canvasDoughnut2");
                        var data = {
                            labels: [
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_VENDOR_DEACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_VENDOR_DEACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VENDOR_DEACTIVE') ; ?>',
                                '<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_VENDOR_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_VENDOR_ACTIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VENDOR_ACTIVE') ; ?>'
                            ],
                            datasets: [{
                                data: [<?php echo ($merchant_deactive); ?>, <?php echo ($merchant_active); ?>],
                                backgroundColor: [
                                    "#455C73",
                                    "#BDC3C7",
                                    "#26B99A",
                                    "#3498DB"
                                ],
                                hoverBackgroundColor: [
                                    "#34495E",
                                    "#CFD4D8",
                                    "#36CAAB",
                                    "#49A9EA"
                                ]
                            }]
                        };
                            <?php if($merchant_deactive == 0 && $merchant_active == 0 ) { } else { ?>
                        var canvasDoughnut2 = new Chart(ctx, {
                                type: 'doughnut',
                                startAngle: 60,
                                indexLabelFontSize: 17,
                                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                                data: data
                            });
                        <?php } ?>
					</script>



					<script>
                        var chart;
                        var graph;
                        var categoryAxis;

                            <?php
                            $month = array('January'=>'','February'=>'','March'=>'','April'=>'','May'=>'','June'=>'','July'=>'','August'=>'','September'=>'','October'=>'','November'=>'','December'=>'');
                            for($i=01;$i<=date('m');$i++){
                                $order_count = DB::table('gr_order')->whereMonth('ord_date',$i)->whereYear('ord_date',date('Y'))->count();

                                if($i == 1){
                                    $k = 'January';
                                }
								elseif($i == 2){
                                    $k = 'February';
                                }
								elseif($i == 3){
                                    $k = 'March';
                                }
								elseif($i == 4){
                                    $k = 'April';
                                }
								elseif($i == 5){
                                    $k = 'May';
                                }
								elseif($i == 6){
                                    $k = 'June';
                                }
								elseif($i == 7){
                                    $k = 'July';
                                }
								elseif($i == 8){
                                    $k = 'August';
                                }
								elseif($i == 9){
                                    $k = 'September';
                                }
								elseif($i == 10){
                                    $k = 'October';
                                }
								elseif($i == 11){
                                    $k = 'November';
                                }
								elseif($i == 12){
                                    $k = 'December';
                                }
                                $month[$k] = $order_count;
                            }

                            ?>
                        var chartData =
                                [

                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JANUARY')) ? trans(Session::get('admin_lang_file').'.ADMIN_JANUARY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JANUARY') !!}",
                                        "visits": Number(<?php echo $month['January'];?>),
                                        "color": "#660F57"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEBRUARY')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEBRUARY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEBRUARY') !!}",
                                        "visits": Number(<?php echo $month['February'];?>),
                                        "color": "#04D215"

                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MARCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_MARCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MARCH') !!}",
                                        "visits": Number(<?php echo $month['March'];?>),
                                        "color": "#097054"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_APRIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_APRIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_APRIL') !!}",
                                        "visits": Number(<?php echo $month['April'];?>),
                                        "color": "#0D52D1"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAY')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAY') !!}",
                                        "visits": Number(<?php echo $month['May'];?>),
                                        "color": "#2A0CD0"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JUNE')) ? trans(Session::get('admin_lang_file').'.ADMIN_JUNE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JUNE') !!}",
                                        "visits": Number(<?php echo $month['June'];?>),
                                        "color": "#8A0CCF"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JULY')) ? trans(Session::get('admin_lang_file').'.ADMIN_JULY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JULY') !!}",
                                        "visits": Number(<?php echo $month['July'];?>),
                                        "color": "#CD0D74"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUGUST')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUGUST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUGUST') !!}",
                                        "visits": Number(<?php echo $month['August'];?>),
                                        "color": "#754DEB"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEPTEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEPTEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEPTEMBER') !!}",
                                        "visits": Number(<?php echo $month['September'];?>),
                                        "color": "#097054"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_OCTOBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_OCTOBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OCTOBER') !!}",
                                        "visits": Number(<?php echo $month['October'];?>),
                                        "color": "#999999"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOVEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOVEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOVEMBER') !!}",
                                        "visits": Number(<?php echo $month['November'];?>),
                                        "color": "#FF9900"
                                    },
                                    {
                                        "month": "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_DECEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_DECEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DECEMBER') !!}",
                                        "visits": Number(<?php echo $month['December'];?>),
                                        "color": "#6599FF"
                                    }
                                ];


                        AmCharts.ready(function () {
                            chart = new AmCharts.AmSerialChart();
                            chart.dataProvider = chartData;
                            chart.categoryField = "month";
                            chart.position = "left";
                            chart.angle = 30;
                            chart.depth3D = 15;
                            chart.startDuration = 1;

                            categoryAxis = chart.categoryAxis;
                            categoryAxis.labelRotation = 45;
                            categoryAxis.dashLength = 5; //
                            categoryAxis.gridPosition = "start";
                            categoryAxis.autoGridCount = false;
                            categoryAxis.gridCount = chartData.length;


                            graph = new AmCharts.AmGraph();
                            graph.valueField = "visits";
                            graph.type = "column";
                            graph.colorField = "color";
                            graph.lineAlpha = 0;
                            graph.fillAlphas = 0.8;
                            graph.balloonText = "[[category]]: <b>[[value]]</b>";

                            chart.addGraph(graph);

                            chart.write('chartdiv');
                        });
					</script>
					<script src="{{url('')}}/public/js/vendor/chartist/js/chartist.min.js" type="text/javascript"></script>
					<script>
                        var data = {
                            labels: ["{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JANUARY')) ? trans(Session::get('admin_lang_file').'.ADMIN_JANUARY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JANUARY') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_FEBRUARY')) ? trans(Session::get('admin_lang_file').'.ADMIN_FEBRUARY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FEBRUARY') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MARCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_MARCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MARCH') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_APRIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_APRIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_APRIL') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAY')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAY') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JUNE')) ? trans(Session::get('admin_lang_file').'.ADMIN_JUNE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JUNE') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_JULY')) ? trans(Session::get('admin_lang_file').'.ADMIN_JULY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_JULY') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUGUST')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUGUST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUGUST') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEPTEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEPTEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEPTEMBER') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_OCTOBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_OCTOBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OCTOBER') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOVEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOVEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOVEMBER') !!}",
                                "{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_DECEMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_DECEMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DECEMBER') !!}"
                            ],
                            series: [
                                [Number(<?php echo $month['January'];?>),Number(<?php echo $month['February'];?>),Number(<?php echo $month['March'];?>),Number(<?php echo $month['April'];?>),Number(<?php echo $month['May'];?>),Number(<?php echo $month['June'];?>),Number(<?php echo $month['July'];?>),Number(<?php echo $month['August'];?>),Number(<?php echo $month['September'];?>),Number(<?php echo $month['October'];?>),Number(<?php echo $month['November'];?>),Number(<?php echo $month['December'];?>)],
                            ]
                        };
                        //Area chart
                        options = {
                            height: "270px",
                            showArea: true,
                            showLine: true,
                            showPoint: true,
							showLabel: true,
                            axisX: {
                                showGrid: true
                            },
                            lineSmooth: true,
                        };
                        new Chartist.Line('#cartdiv', data, options);
					</script>
@endsection
@stop