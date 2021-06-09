<?php
	
	namespace App\Http\Controllers;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Validator;
	
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	class HomeController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->setLanguageFront();
		}
		
		public function index()
		{
			return view('Front.index');
		}
		
		public function change_language(Request $request){
			
			echo 'hi';
		}
	}
