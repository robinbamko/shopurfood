<!doctype html>
<html>
<head>
@include('Admin.includes.Head')
<title>{{ $SITENAME}} | @yield('PageTitle')</title> 
</head>
<body class="nav-md">
	<div id="wrapper">

<!-- Header content -->
@include('Admin.includes.Header')
<!-- sidebar content -->
@include('Admin.includes.Sidebar')

<!-- main content -->
@yield('content')
<!-- Footer content -->
<div class="clearfix"></div>

@include('Admin.includes.Footer')

</div>
@yield('script')
</body>
</html>