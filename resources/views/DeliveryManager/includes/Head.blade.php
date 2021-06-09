<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<!-- VENDOR CSS -->

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/linearicons/style.css">
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/chartist/css/chartist-custom.css">
<link rel="stylesheet" href="{{url('')}}/public/front/css/animate.css">
<!-- MAIN CSS -->
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/main.css">
<!-- GOOGLE FONTS -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
<!-- ICONS -->
<link rel="apple-touch-icon" sizes="76x76" href="{{url('')}}/public/admin/assets/img/apple-icon.png">

<!--<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">-->
<link href="{{url('')}}/public/admin/assets/css/summernote.css" rel="stylesheet">

@if(count($logo_settings_details) > 0)
	@php
		foreach($logo_settings_details as $logo_set_val){ }
	@endphp
	<link rel="icon" type="image/png" sizes="96x96" href="{{url('public/images/logo/'.$logo_set_val->favicon)}}">

@else
	<link rel="icon" type="image/png" sizes="96x96" href="{{url('')}}/public/admin/assets/img/favicon.png">
@endif


<!-- DataTables CSS -->
<link href="{{url('')}}/public/admin/assets/css/dataTables.bootstrap.css" rel="stylesheet">

<!-- DataTables Responsive CSS -->
<link href="{{url('')}}/public/admin/assets/css/dataTables.responsive.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="{{url('')}}/public/admin/assets/css/sb-admin-2.css" rel="stylesheet">


<style>
	input[type=file] {
		display: inline-block;
	}
	.require:after{
		content:'* :';
	}

	.btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
		/* border-radius: 0; */
		display: none;
	}
</style>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="{{url('')}}/public/admin/assets/vendor/jquery/jquery.min.js"></script>