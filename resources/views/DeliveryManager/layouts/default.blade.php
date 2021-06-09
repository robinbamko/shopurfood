<!doctype html>
<html>
<head>
@include('DeliveryManager.includes.Head')
<title>{{ $SITENAME}} | @yield('PageTitle')</title> 
</head>
<body class="nav-md">
	<div id="wrapper">

<!-- Header content -->
@include('DeliveryManager.includes.Header')
<!-- sidebar content -->
@include('DeliveryManager.includes.Sidebar')

<!-- main content -->
@yield('content')
<!-- Footer content -->
<div class="clearfix"></div>

@include('DeliveryManager.includes.Footer')
@yield('script')
</div>
</body>
</html>