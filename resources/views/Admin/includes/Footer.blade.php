                    
		<footer>
			<div class="container-fluid">
				<p class="copyright"> &copy; {{$FOOTERNAME}} . {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALL_RIGHTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALL_RIGHTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALL_RIGHTS')}}</p>
			</div>
		</footer>
		         

</div>
<!-- Javascript -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
	
	<script src="{{url('')}}/public/admin/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="{{url('')}}/public/admin/assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
	<script src="{{url('')}}/public/admin/assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
	<!--<script src="{{url('')}}/public/admin/assets/vendor/chartist/js/chartist.min.js"></script>-->
	<script src="{{url('')}}/public/admin/assets/scripts/klorofil-common.js"></script>
	

		<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/dataTables.bootstrap.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/dataTables.responsive.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/metisMenu.min.js"></script>		
		<script src="{{url('')}}/public/admin/assets/scripts/sb-admin-2.js"></script>
		<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
		<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
		<script src="{{url('')}}/public/admin/assets/select2/select2.min.js"></script>
		<script>
		$(function () {
		  $('.tooltip-demo').tooltip({placement:'left'})
		  $('.select2').select2({ placeholder: "Select",allowClear: true});
		})
		 $.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		function validate_phone(gotId) {
			var element = document.getElementById(gotId);
			//alert();
			var defaultDial = '{{Config::get('config_default_dial')}}';
			if(element.value=='' || element.value.length < defaultDial.trim().length)
			{

			$('#'+gotId).val('{{$default_dial}}');

			}
			element.value = element.value.replace(/[^0-9 +]+/, '');
		}
		$(function(){
		    $("[data-hide]").on("click", function(){
		        $(this).closest("." + $(this).attr("data-hide")).hide();
		    });
		});
		</script>
