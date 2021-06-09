@extends('sitemerchant.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
<style>
	input[type=file] {
		display: inline-block;
	}
	.banner {
		display: inline-block;
		float: left;
		width: 40%;
		margin-right: 5px;
	}
</style>

<!-- MAIN -->
<script src="<?php echo URL::to('/'); ?>/public/js/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/public/css/jquery.timepicker.css" />
<script src="<?php echo URL::to('/'); ?>/public/js/moment.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/public/js/datepair.js"></script>
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
								<span style="float:right;color:#373756;font-size:15px">
									@php 
									if( $getrestaurant->st_status == '1'){
									echo "Restaurant Status : Active";
									}else{
									echo "Restaurant Status : block";
									}
									@endphp
								</span>
							</div>
							
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced box-body-padding" >
								<div id="location_form" class="panel-body">
									<div class="row-fluid">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Add/Edit page ends--}}


@section('script')
<!--SCRIPT SECTION -->
@endsection
@stop