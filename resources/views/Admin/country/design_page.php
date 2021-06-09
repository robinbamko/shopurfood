@extends('Admin.layouts.default')

@section('content')



<head>

	 <!-- DataTables CSS -->
	 <link href="{{url('')}}/public/admin/assets/css/dataTables.bootstrap.css" rel="stylesheet">

	<!-- DataTables Responsive CSS -->
	<link href="{{url('')}}/public/admin/assets/css/dataTables.responsive.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="{{url('')}}/public/admin/assets/css/sb-admin-2.css" rel="stylesheet">

</head>

<!-- MAIN -->
		<div class="main">
			<!-- MAIN CONTENT -->
			<div class="main-content">
			 <h1 class="page-header">{{$pagetitle}}</h1>
				<div class="container-fluid add-country">
					
					<div class="row">
						<div class="container right-container">
						<div class="r-btn">
							<a id="click-here" class="btn btn-default fa fa-bars" href="#" role="button" onclick="change()">Add Page</a>
						</div>
							<div class="col-md-12">
								{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'admin_check_login']) !!}

								<div class="location panel">
									{{-- <div class="panel-heading">
										<h3 class="panel-title">Inputs</h3>
									</div> --}}


									<div class="panel-body" id="location_form">
									<div class="panel-heading p__title">
										App Page
									</div>
									@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
									<div class="row panel-heading">
									<div class="col-md-4">
										<span class="panel-title">
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_NAME')}}&nbsp; (In {{$lang->lang_name}})*
										</span>
									</div>
									<div class="col-md-8">
										{!! Form::text('co_name','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_CO_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_CO_NAME'),'id' => 'cnty_name','required']) !!}

									</div>
									</div>
										@endforeach
									@endif
									<div class="row panel-heading">
									<div class="col-md-4">
										<span class="panel-title">
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_CODE')}}
										</span>
									</div>
									<div class="col-md-8">
										{!! Form::text('co_code','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'cnty_code','readonly']) !!}

									</div>
									</div>
									<div class="row panel-heading">
									<div class="col-md-4">
										<span class="panel-title">
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY_SYM')}}
										</span>
									</div>
									<div class="col-md-8">
										{!! Form::text('co_code','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'cnty_code','readonly']) !!}

									</div>
									</div>
									<div class="row panel-heading">
									<div class="col-md-4">
										<span class="panel-title">
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}
										</span>
									</div>
									<div class="col-md-8">
										{!! Form::text('co_code','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'cnty_code','readonly']) !!}

									</div>
									</div>
									<div class="row panel-heading">
									<div class="col-md-4">
										<span class="panel-title">
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIAL_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIAL_CODE')}}
										</span>
									</div>
									<div class="col-md-8">
										{!! Form::text('co_code','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AUTOCOMPLETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AUTOCOMPLETE'),'id' => 'cnty_code','readonly']) !!}

									</div>
									</div>
									<div class="panel-heading">
									{!! Form::submit('Create',['class' => 'btn btn-success'])!!}

									{!! Form::reset('Reset',['class' => 'btn btn-warning'])!!}
									</div>
								</div>
								<div class="panel-body" id="location_table" >
								<div class="panel-heading p__title">
										Manage List
									</div>
									<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" >
									<thead>
										<tr>
											<th>Rendering engine</th>
											<th>Browser</th>
											<th>Platform(s)</th>
											<th>Engine version</th>
											<th>CSS grade</th>
										</tr>
									</thead>
									<tbody>
										<tr class="odd gradeX">
											<td>Trident</td>
											<td>Internet Explorer 4.0</td>
											<td>Win 95+</td>
											<td class="center">4</td>
											<td class="center">X</td>
										</tr>
										<tr class="even gradeC">
											<td>Trident</td>
											<td>Internet Explorer 5.0</td>
											<td>Win 95+</td>
											<td class="center">5</td>
											<td class="center">C</td>
										</tr>
										<tr class="odd gradeA">
											<td>Trident</td>
											<td>Internet Explorer 5.5</td>
											<td>Win 95+</td>
											<td class="center">5.5</td>
											<td class="center">A</td>
										</tr>
										<tr class="even gradeA">
											<td>Trident</td>
											<td>Internet Explorer 6</td>
											<td>Win 98+</td>
											<td class="center">6</td>
											<td class="center">A</td>
										</tr>
										<tr class="odd gradeA">
											<td>Trident</td>
											<td>Internet Explorer 7</td>
											<td>Win XP SP2+</td>
											<td class="center">7</td>
											<td class="center">A</td>
										</tr>
										<tr class="even gradeA">
											<td>Trident</td>
											<td>AOL browser (AOL desktop)</td>
											<td>Win XP</td>
											<td class="center">6</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Firefox 1.0</td>
											<td>Win 98+ / OSX.2+</td>
											<td class="center">1.7</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Firefox 1.5</td>
											<td>Win 98+ / OSX.2+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Firefox 2.0</td>
											<td>Win 98+ / OSX.2+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Firefox 3.0</td>
											<td>Win 2k+ / OSX.3+</td>
											<td class="center">1.9</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Camino 1.0</td>
											<td>OSX.2+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Camino 1.5</td>
											<td>OSX.3+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Netscape 7.2</td>
											<td>Win 95+ / Mac OS 8.6-9.2</td>
											<td class="center">1.7</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Netscape Browser 8</td>
											<td>Win 98SE+</td>
											<td class="center">1.7</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Netscape Navigator 9</td>
											<td>Win 98+ / OSX.2+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.0</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.1</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.1</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.2</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.2</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.3</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.3</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.4</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.4</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.5</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.5</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.6</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">1.6</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.7</td>
											<td>Win 98+ / OSX.1+</td>
											<td class="center">1.7</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Mozilla 1.8</td>
											<td>Win 98+ / OSX.1+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Seamonkey 1.1</td>
											<td>Win 98+ / OSX.2+</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Gecko</td>
											<td>Epiphany 2.20</td>
											<td>Gnome</td>
											<td class="center">1.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>Safari 1.2</td>
											<td>OSX.3</td>
											<td class="center">125.5</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>Safari 1.3</td>
											<td>OSX.3</td>
											<td class="center">312.8</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>Safari 2.0</td>
											<td>OSX.4+</td>
											<td class="center">419.3</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>Safari 3.0</td>
											<td>OSX.4+</td>
											<td class="center">522.1</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>OmniWeb 5.5</td>
											<td>OSX.4+</td>
											<td class="center">420</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>iPod Touch / iPhone</td>
											<td>iPod</td>
											<td class="center">420.1</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Webkit</td>
											<td>S60</td>
											<td>S60</td>
											<td class="center">413</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 7.0</td>
											<td>Win 95+ / OSX.1+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 7.5</td>
											<td>Win 95+ / OSX.2+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 8.0</td>
											<td>Win 95+ / OSX.2+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 8.5</td>
											<td>Win 95+ / OSX.2+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 9.0</td>
											<td>Win 95+ / OSX.3+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 9.2</td>
											<td>Win 88+ / OSX.3+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera 9.5</td>
											<td>Win 88+ / OSX.3+</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Opera for Wii</td>
											<td>Wii</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Nokia N800</td>
											<td>N800</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>Presto</td>
											<td>Nintendo DS browser</td>
											<td>Nintendo DS</td>
											<td class="center">8.5</td>
											<td class="center">C/A<sup>1</sup>
											</td>
										</tr>
										<tr class="gradeC">
											<td>KHTML</td>
											<td>Konqureror 3.1</td>
											<td>KDE 3.1</td>
											<td class="center">3.1</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeA">
											<td>KHTML</td>
											<td>Konqureror 3.3</td>
											<td>KDE 3.3</td>
											<td class="center">3.3</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeA">
											<td>KHTML</td>
											<td>Konqureror 3.5</td>
											<td>KDE 3.5</td>
											<td class="center">3.5</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeX">
											<td>Tasman</td>
											<td>Internet Explorer 4.5</td>
											<td>Mac OS 8-9</td>
											<td class="center">-</td>
											<td class="center">X</td>
										</tr>
										<tr class="gradeC">
											<td>Tasman</td>
											<td>Internet Explorer 5.1</td>
											<td>Mac OS 7.6-9</td>
											<td class="center">1</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeC">
											<td>Tasman</td>
											<td>Internet Explorer 5.2</td>
											<td>Mac OS 8-X</td>
											<td class="center">1</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeA">
											<td>Misc</td>
											<td>NetFront 3.1</td>
											<td>Embedded devices</td>
											<td class="center">-</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeA">
											<td>Misc</td>
											<td>NetFront 3.4</td>
											<td>Embedded devices</td>
											<td class="center">-</td>
											<td class="center">A</td>
										</tr>
										<tr class="gradeX">
											<td>Misc</td>
											<td>Dillo 0.8</td>
											<td>Embedded devices</td>
											<td class="center">-</td>
											<td class="center">X</td>
										</tr>
										<tr class="gradeX">
											<td>Misc</td>
											<td>Links</td>
											<td>Text only</td>
											<td class="center">-</td>
											<td class="center">X</td>
										</tr>
										<tr class="gradeX">
											<td>Misc</td>
											<td>Lynx</td>
											<td>Text only</td>
											<td class="center">-</td>
											<td class="center">X</td>
										</tr>
										<tr class="gradeC">
											<td>Misc</td>
											<td>IE Mobile</td>
											<td>Windows Mobile 6</td>
											<td class="center">-</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeC">
											<td>Misc</td>
											<td>PSP browser</td>
											<td>PSP</td>
											<td class="center">-</td>
											<td class="center">C</td>
										</tr>
										<tr class="gradeU">
											<td>Other browsers</td>
											<td>All others</td>
											<td>-</td>
											<td class="center">-</td>
											<td class="center">U</td>
										</tr>
									</tbody>
								</table>
									</div>
							</div>							
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
			<!-- END MAIN CONTENT -->
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

		<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/dataTables.bootstrap.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/metisMenu.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/dataTables.responsive.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/sb-admin-2.js"></script>
		<script>
    $(document).ready(function() {
        $('#d-table').DataTable({
            responsive: true
        });
    });
	</script>
	<script>
		$(document).ready(function(){
			$("#location_form").hide();
			$("#click-here").click(function(){	
				$("#location_form").toggle();			
				$("#location_table").toggle();
			});
		});

		// function change() {
		// document.getElementById("click-here").text="Manage List";
		// }

		 function change() {
                 var elem = document.getElementById("click-here");
                if (elem.text=="Add Page")
                    { 
                        elem.text = "Manage List";
                    }
                else if (elem.text=="Manage List")
                 {
                     elem.text = "Add Page";
				 }
				 else {
					elem.text = "Manage List";
				 }
            }
	</script>

@stop
