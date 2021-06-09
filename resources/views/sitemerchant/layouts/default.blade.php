<!doctype html>
<html>
<head>
@include('sitemerchant.includes.Head')
<title>{{ucfirst($SITENAME)}}| @yield('PageTitle')</title>
</head>
<body class="nav-md">
	<div id="wrapper">

<!-- Header content -->
@include('sitemerchant.includes.Header')
<!-- sidebar content -->
@include('sitemerchant.includes.Sidebar')

<!-- main content -->
@yield('content')
<!-- Footer content -->
<div class="clearfix"></div>

@include('sitemerchant.includes.Footer')
</div>
@yield('script')
</body>
</html>