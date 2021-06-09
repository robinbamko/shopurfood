<?php

namespace App\Http\Middleware;

use Closure;
use DB , Session ;
use App;
use View;
use App\Settings;
use Config;
use Response;
class MobileLanguage
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
           if($request->lang != '')
            {   
                $lang= $request->lang;

                if(file_exists('resources/lang/'.$lang.'/'.$lang.'_mob_lang.php'))
                {
                    /* check language file is active */
                    $check = \DB::table('gr_language')->where(['status' => '1','lang_code' => $lang])->count();
                    if($check == 0)
                    {
                        $encode = [ 'code' => 400,'message'  => 'Invalid language!','data' => []];
                        return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
                        
                    }
                }
                else
                {   
                    $encode = [ 'code' => 400,'message'  => 'Language file does not exist!','data' => []];
                    return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
                   
                }
                
            }
            elseif($request->lang == '')
            {   
                $encode = [ 'code' => 400,'message'  => 'Language parameter missing!','data' => []];
                return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
                
            }
            /* This will add the "accept" => 'application/json' in header ,to get exception error*/
            $request->headers->set('Accept', 'application/json');
            
         return $next($request);
    }
}
