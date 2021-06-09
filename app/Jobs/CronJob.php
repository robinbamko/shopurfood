<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\SendMailable;

class CronJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        //parent::__construct();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* Send mail to users who has abandoned cart */
        $get_time = \DB::table('gr_general_setting')->select('gs_abandoned_mail','gs_mail_after')->first();
        if(empty($get_time) === false)
        {
            if($get_time->gs_abandoned_mail == '1')
            {   
                
            if($get_time->gs_abandoned_mail == '1')
            {   
                $now = date('Y-m-d H:i:s');
               // DB::connection()->enableQueryLog();
                $get_cart = \DB::table('gr_cart_save')->select('cus_email','cus_id')
                        ->Join('gr_customer','gr_customer.cus_id','=','gr_cart_save.cart_cus_id')
                        ->where('gr_customer.cus_status','=','1')
                        ->whereRaw('gr_cart_save.cart_updated_at < DATE_SUB(NOW(), INTERVAL '.$get_time->gs_mail_after.' HOUR)') 
                        ->groupBy('cus_email')                
                        ->get(); 
                //$query = DB::getQueryLog();
                //print_r($get_cart);
                if(count($get_cart) > 0)
                {
                    foreach($get_cart as $cart)
                    {
                         $get_cart = \DB::table('gr_cart_save')->select('gr_product.pro_item_name','gr_product.pro_images','cart_total_amt','cart_type','cart_currency')
                        ->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
                        ->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
                        ->where('gr_product.pro_status','=', '1')
                        ->where('gr_store.st_status' ,'=','1')
                        ->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
                        ->whereRaw('gr_cart_save.cart_updated_at < DATE_SUB(NOW(), INTERVAL '.$get_time->gs_mail_after.' HOUR)') 
                        ->where('cart_cus_id','=',$cart->cus_id)
                        //->groupBy('cart_cus_id')                
                        ->get(); 
                        Mail::to($cart->cus_email)->send(new SendMailable($get_cart));
                    }
                }
                
            }
            }
        }
    }
}
