<?php

    namespace App\Http\Middleware;

    use Closure;
    use JWTAuth;
    use Exception;
    use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
    use Config;
    use Response;
    //use Tymon\JWTAuth\Facades\JWTAuth
    class JwtMiddleware extends BaseMiddleware
    {

        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            try {
                if (! $user = JWTAuth::parseToken()->authenticate()) {
                    $encode = ['code' => 404,'message'  => 'user_not_found','data' => []];
                    return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
                    //return response()->json(['user_not_found'], 404);
                }
                //$user = JWTAuth::parseToken()->authenticate();
                // $user = JWTAuth::parseToken($request);
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    $encode = ['code' => 400,'message'  => 'Token is Invalid','data' => []];
                    return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
                    //return response()->json(['status' => 'Token is Invalid']);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    $encode = ['code' => 400,'message'  => 'Token is Expired','data' => []];
                    return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
                    //return response()->json(['status' => 'Token is Expired']);
                }else{
                    $encode = ['code' => 400,'message'  => 'Authorization Token not found','data' => []];
                    return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
                    //return response()->json(['status' => 'Authorization Token not found']);
                }
            }

            return $next($request);
        }
    }
