<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Request;
use Validator;
use Config;
use App\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		
        Validator::extend('only_cnty_code', function($attribute, $value, $parameters) {
			return substr($value, 0, strlen(Config::get('config_default_dial'))) != $value;
        });

        /*TUTORIAL SECTION*/
        $video_status = 0;
        $doc_status = 0;
        $video = "";
        $document = "";
        $heading = "";
		$vidTutoStatus = '';
        $tutorial_show_status = Settings::get_tutorial_status(); /*Checking status*/
        $allowed_urls = array("admin-general-settings","admin-commission-tracking"); /*allowed page's url*/
        $current_url = Request::segments(); /*current page url*/ 

        if(empty($current_url)){
            $path_url = "";
        }else{
            $dir = 'public/tutorials/'.$current_url[0];
            $path_url = $current_url[0];
            $heading = str_replace("-"," ",$path_url);

			if(empty($tutorial_show_status)===false) {
				if($tutorial_show_status->video_tutorial_status == '1' && in_array($current_url[0], $allowed_urls)){
					$vidTutoStatus = $tutorial_show_status->video_tutorial_status;
					clearstatcache(); /*to clear the cache of is_dir()*/
					if (is_dir($dir)) {
						foreach(glob($dir.'/*.*') as $file) {
							$name = explode('/', $file);
							$extension = explode(".",$name[3]);

							if(strtolower($extension[1]) == "mp4"){
								$video = $name[3];
								$video_status = 1;
							}

							if(strtolower($extension[1]) == "pdf"){
								$document = $name[3];
								$doc_status = 1;
							}
						}
					}
				}
			}
        }

        View::share('path_url', $path_url); 
        View::share('allowed_urls', $allowed_urls);
        View::share('heading', $heading);
        View::share("tutorial_status", $vidTutoStatus);

        View::share('video_status', $video_status);
        View::share('video_name', $video);
        View::share('doc_status', $doc_status);
        View::share('doc_name', $document);
        /***************/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
