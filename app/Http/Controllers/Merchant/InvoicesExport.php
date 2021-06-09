<?php 
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	
	use Maatwebsite\Excel\Concerns\FromQuery;
	use Maatwebsite\Excel\Concerns\Exportable;
	use DB;
	
	class InvoicesExport implements FromQuery
	{
		use Exportable;
		
		public function __construct()
		{
			parent::__construct();
			// $this->year = $year;
		}
		
		public function query()
		{
			return DB::table('gr_category')->select('cate_name as StoreCategoryName')->where('cate_status','=','1')->get();
		}
	}	