<title>{{ $SITENAME}}</title> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
    <link rel="stylesheet" href="{{url('')}}/public/front/css/bootstrap.min.css">	
    <?php
	if(Session::get('front_lang_code') == 'ar'){
	?>
    	<link rel="stylesheet" href="{{url('')}}/public/front/css/style-arabic.css" />
    	<link href="{{url('')}}/public/front/css/simple-sidebar-arabic.css" rel="stylesheet">
    <?php
	}else{
	?>
    	<link rel="stylesheet" href="{{url('')}}/public/front/css/style.css" />
    	<link href="{{url('')}}/public/front/css/simple-sidebar.css" rel="stylesheet">
    <?php
	}
    ?>
	<link rel="stylesheet" href="{{url('')}}/public/front/css/animate.css">    
    <link rel="stylesheet" href="{{url('')}}/public/front/css/font-awesome.min.css">    
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">    
    <link rel="stylesheet" type="text/css" href="{{url('')}}/public/front/css/slick.css">
	<link rel="stylesheet" type="text/css" href="{{url('')}}/public/front/css/slick-theme.css">
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
    @if(count($logo_settings_details) > 0)
		@php
			foreach($logo_settings_details as $logo_set_val){ }
		@endphp
		<link rel="icon" type="image/png" sizes="96x96" href="{{url('public/images/logo/'.$logo_set_val->favicon)}}">
					
	@else
	<link rel="icon" type="image/png" sizes="96x96" href="{{url('')}}/public/admin/assets/img/favicon.png">
	@endif
    <style>
        
    </style>