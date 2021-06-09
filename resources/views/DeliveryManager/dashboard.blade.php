@extends('DeliveryManager.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop

@section('content')
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
			<!-- OVERVIEW -->
			<div class="panel panel-headline">
				{{-- <div class="panel-heading">
					<h3 class="panel-title">Weekly Overview</h3>
					<p class="panel-subtitle">Period: Oct 14, 2016 - Oct 21, 2016</p>
				</div> --}}
				@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					
					{{Session::get('message')}}
					
				</div>
				
				@endif
				<div class="panel-body">
					<div class="row">
						{{--<div class="col-md-3">--}}
							{{--<div class="metric">--}}
								{{--<span class="icon"><i class="fa fa-users"></i></span>--}}
								{{--<p>--}}
									{{--<span class="number">{{$agent_count}}</span>--}}
									{{--<span class="title">--}}
										{{--{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGNTS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGNTS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGNTS')}}--}}
									{{--</span>--}}
								{{--</p>--}}
							{{--</div>--}}
						{{--</div>--}}
						<div class="col-md-4">
							<div class="metric">
								<span class="icon"><i class="fa fa-users"></i></span>
								<p>
									<span class="number">{{$delivery_count}}</span>
									<span class="title">
										{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELI_MEM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELI_MEM') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELI_MEM')}}
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
										{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDS')}}
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
										{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELI_ORDS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELI_ORDS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELI_ORDS')}}
									</span>
								</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<!-- TODO LIST -->
							<div class="panel">
								
								<div class="panel-body">
									<div class="x_panel">
										<div class="x_title">
											<h2>{!! (Lang::has(Session::get('DelMgr_lang_file').'.DEL_ORD_MGMY_GRAPHP')) ? trans(Session::get('DelMgr_lang_file').'.DEL_ORD_MGMY_GRAPHP') : trans($DELMGR_OUR_LANGUAGE.'.DEL_ORD_MGMY_GRAPHP') !!}</h2>
											<ul class="nav navbar-right panel_toolbox">
												
											</ul>
											<div class="clearfix"></div>
										</div>

										<div class="x_content">
											
											<?php if( (($delivered_count)==0)  && (($failed_count)==0) && ($order_count ==0) ) {if (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS_FOUND')) { echo  trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS_FOUND'); } else { echo trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DETAILS_FOUND'); }  } else { ?>
												<canvas id="canvasDoughnut"></canvas>
											<?php } ?> 					  
										</div>
									</div>
								</div>
							</div>
							<!-- END TODO LIST -->
						</div>
						<div class="col-md-6">
							<!-- TIMELINE -->
							
							<div class="panel">
								<div class="panel-body">
									<div class="x_panel">
										<div class="x_title">
											<h2>{!! (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_GRAPH')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_GRAPH') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELMEM_GRAPH') !!}</h2>
											<ul class="nav navbar-right panel_toolbox">
												
											</ul>
											<div class="clearfix"></div>
										</div>
										
										<div class="x_content">
											
											<?php if( (($delmem_active)==0)  && (($delmem_deactive)==0)  ) {if (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS_FOUND')) { echo  trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS_FOUND'); } else { echo trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DETAILS_FOUND'); }  } else { ?>
												<canvas id="canvasDoughnut1"></canvas>
											<?php } ?> 
										</div>
									</div>
								</div>
							</div>
							<!-- END TIMELINE -->
						</div>
					</div>
					
				</div>
			</div>
			<!-- END OVERVIEW -->
			
		</div>
	</div>
	<!-- END MAIN CONTENT -->
</div>
@section('script')

<script src="{{url('')}}/public/js/canvasjs.min.js"></script>
<script src="{{url('')}}/public/js/Chart.min.js"></script>
<script src="{{url('')}}/public/js/amcharts.js" type="text/javascript"></script>
<script src="{{url('')}}/public/js/serial.js" type="text/javascript"></script>
<script>
	
	/** store graph **/
		
		// Doughnut chart
		var ctx = document.getElementById("canvasDoughnut");
		var data = {
			labels: [
			'<?php echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDS') ; ?>',
			'<?php echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELI_ORDS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELI_ORDS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELI_ORDS') ; ?>',
                '<?php echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FAILED_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FAILED_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FAILED_ORDER') ; ?>'
			],
			datasets: [{
				data: [<?php echo ($order_count); ?>, <?php echo ($delivered_count); ?>,<?php echo ($failed_count); ?>],
				backgroundColor: [
				"#26B99A",
				"#3498DB",
				"#F08080"
				],
				hoverBackgroundColor: [
				"#36CAAB",
				"#49A9EA",
                "#FA8072"
				]
				
			}]
		};
		
		var canvasDoughnut = new Chart(ctx, {
			type: 'doughnut',
			tooltipFillColor: "rgba(51, 51, 51, 0.55)",
			data: data
		});
		
		
		/** restaurant graph **/
			
			// Doughnut chart
			var ctx = document.getElementById("canvasDoughnut1");
			var data = {
				labels: [
				'<?php echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_DEACTIVE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_DEACTIVE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELMEM_DEACTIVE') ; ?>',
				'<?php echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_ACTIVE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELMEM_ACTIVE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELMEM_ACTIVE') ; ?>' 
				],
				datasets: [{
					data: [<?php echo ($delmem_deactive); ?>, <?php echo ($delmem_active); ?>],
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
			
			var canvasDoughnut1 = new Chart(ctx, {
				type: 'doughnut',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: data
			});
			
			
			
		</script>
		
		@stop
		@endsection
		