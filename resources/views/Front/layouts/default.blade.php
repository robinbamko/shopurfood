<!doctype html>
<html>
<head>
@include('Front.includes.Head')
<title>{{ $SITENAME}} | @yield('PageTitle')</title> 
</head>
<body>
	<div class="pre-header">  </div>
 @include('Partials.errors')
 @include('Partials.success')
<!-- Header content -->
@include('Front.includes.Header')
<!-- sidebar content -->


<!-- main content -->
@yield('content')
<!-- Footer content -->
<div class="clearfix"></div>

@include('Front.includes.Footer')
@yield('script')
</div>
</body>
</html>