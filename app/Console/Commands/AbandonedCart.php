<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\SendMailable;
use Illuminate\Support\Facades\Mail;
use DB;
class AbandonedCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AbandonedCart:send_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to users having cart without purchase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        /* Send mail to users who has abandoned cart */
        $get_time = \DB::table('gr_general_setting')->select('gs_abandoned_mail','gs_mail_after')->first();
        if(empty($get_time) === false)
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
                $logo_settings = \DB::table('gr_logo_settings')->select('admin_logo')->first();
                $footer_settings = \DB::table('gr_general_setting')->select('footer_text')->first();
                if(count($get_cart) > 0)
                {
                    foreach($get_cart as $cart)
                    {   
                        DB::connection()->enableQueryLog();
                         $get_customer_cart = \DB::table('gr_cart_save')->select('gr_product.pro_item_name','gr_product.pro_images','cart_total_amt','cart_type','cart_currency','cart_id')
                        ->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
                        ->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
                        ->where('gr_product.pro_status','=', '1')
                        ->where('gr_store.st_status' ,'=','1')
                        ->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
                        ->whereRaw('gr_cart_save.cart_updated_at < DATE_SUB(NOW(), INTERVAL '.$get_time->gs_mail_after.' HOUR)') 
                        ->where('cart_cus_id','=',$cart->cus_id)
                        //->groupBy('cart_cus_id')                
                        ->get(); 
                        $query = DB::getQueryLog();
                //print_r($get_cart);
                        if(count($get_customer_cart) > 0)
                        {
                           Mail::to($cart->cus_email)->send(new SendMailable($get_customer_cart,$logo_settings,$footer_settings));
                            foreach($get_customer_cart as $details)
                            {
                            /** update cart date in cart_save **/
                               $update = \DB::table('gr_cart_save')
                                        ->where('cart_cus_id','=',$cart->cus_id)
                                        ->where('cart_id','=',$details->cart_id)
                                        ->update(['cart_updated_at' => date('Y-m-d H:i:s')]);
                            }
                            
                        }
                }
                
                }
            }
        }
    }
}
