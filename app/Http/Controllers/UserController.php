<?php
    namespace App\Http\Controllers;
    use App\User;
    use App\Agent;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
	use DB;
    use Tymon\JWTAuth\Exceptions\JWTException;
	
	//https://blog.pusher.com/laravel-jwt/
    class UserController extends Controller
    {
        public function authenticate(Request $request)
        {
            $credentials = $request->only('cus_email', 'cus_password');
			$credentials['cus_status'] = '1';
			//print_r($credentials); exit;
            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
				}
				else{
					$user = JWTAuth::user();
					//echo $user->cus_id;
					//exit;
					return response()->json(compact('token', 'user'));
				}
				} catch (JWTException $e) {
				
                return response()->json(['error' => 'could_not_create_token'], 500);
			}
			
            return response()->json(compact('token'));
		}
		public function agent_login(Request $request)
        {
            $credentials = $request->only('agent_email', 'agent_password');
			$credentials['agent_status'] = '1';
			//print_r($credentials); exit;
            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
				}
				else{
					$user = JWTAuth::agent();
					//echo $user->cus_id;
					//exit;
					return response()->json(compact('token', 'user'));
				}
				} catch (JWTException $e) {
				
                return response()->json(['error' => 'could_not_create_token'], 500);
			}
			
            return response()->json(compact('token'));
		}
        public function register(Request $request)
        {
			//print_r($request->all()); exit;
			$validator = Validator::make($request->all(), [
			'cus_fname' => 'required|string|max:255',
			'cus_email' => 'required|string|email|max:255|unique:gr_customer',
			'cus_password' => 'required|string|min:6',
            ]);
			
            if($validator->fails()){
				return response()->json($validator->errors()->toJson(), 400);
			}
			
            $user = User::create([
			'cus_fname' => $request->get('cus_fname'),
			'cus_email' => $request->get('cus_email'),
			'cus_password' => md5($request->get('cus_password')),
			'cus_phone1'=>$request->get('cus_phone1'),
			'cus_login_type' => '1',
			'cus_status' => '1',
			'cus_paynamics_status'=>'Unpublish',
			'cus_paymaya_status'=>'Unpublish',
			'cus_netbank_status'=>'Unpublish',
			'cus_created_date' => date('Y-m-d')
            ]);
			$lastinsertid = DB::getPdo()->lastInsertId();
			echo $lastinsertid; exit;
			//$user = DB::table('gr_customer')->first();
			//print_r($user); exit;
            $token = JWTAuth::fromUser($user);
			
            return response()->json(compact('user','token'),201);
		}
		public function agent_register(Request $request)
        {
			//print_r($request->all()); exit;
			$validator = Validator::make($request->all(), [
			'agent_fname' => 'required|string|max:255',
			'agent_lname' => 'required|string|max:255',
			'agent_email' => 'required|string|email|max:255|unique:gr_agent',
			'agent_password' => 'required|string|min:6',
            ]);
			
            if($validator->fails()){
				return response()->json($validator->errors()->toJson(), 400);
			}
			
            $user = Agent::create([
			'agent_fname' => $request->get('agent_fname'),
			'agent_lname' => $request->get('agent_lname'),
			'agent_email' => $request->get('agent_email'),
			'agent_password' => md5($request->get('agent_password')),
			'agent_phone1'=>$request->get('agent_phone1'),
			'agent_status' => '1',
			'mer_paynamics_status'=>'Unpublish',
			'mer_paymaya_status'=>'Unpublish',
			'mer_netbank_status'=>'Unpublish',
			'agent_created_at' => date('Y-m-d')
            ]);
			$lastinsertid = DB::getPdo()->lastInsertId();
			//echo $lastinsertid; exit;
			//$user = DB::table('gr_customer')->first();
			//print_r($user); exit;
            $token = JWTAuth::fromUser($user);
			
            return response()->json(compact('user','token'),201);
		}
		public function logout(Request $request)
		{
			$this->validate($request, [
			'token' => 'required'
			]);
			/*if (! $token = $this->parseAuthHeader($header, $method)) { // all your get method not passed this step
				if (! $token = $this->request->query($query, false)) { // all your post method stucked here 
				throw new JWTException('The token could not be parsed from the request', 400);
				}
			}*/
			
			try {
				JWTAuth::invalidate($request->token);
				
				return response()->json([
				'success' => true,
				'message' => 'User logged out successfully'
				]);
				} catch (JWTException $exception) {
				return response()->json([
				'success' => false,
				'message' => 'Sorry, the user cannot be logged out'
				], 500);
			}
		}
        public function getAuthenticatedUser()
		{
			try {
				
				if (! $user = JWTAuth::parseToken()->authenticate()) {
					return response()->json(['user_not_found'], 404);
				}
				
				} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				
				return response()->json(['token_expired'], $e->getStatusCode());
				
				} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
				
				return response()->json(['token_invalid'], $e->getStatusCode());
				
				} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
				
				return response()->json(['token_absent'], $e->getStatusCode());
				
			}
			
			return response()->json(compact('user'));
		}
	}		