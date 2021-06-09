<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('open', 'DataController@open');
/* ----------------------------------check language------------------------------------*/

Route::group(['middleware' => ['chk_mob_lang']], function() {
	Route::any('registration','MobileApp\CustomerAppController@user_registration');
	Route::any('customer_otp','MobileApp\CustomerAppController@check_otp');
	Route::any('country_list','MobileApp\CustomerAppController@country_list');
	Route::post('customer_home_page', 'MobileApp\CustomerAppController@home');
	Route::post('user_login', 'MobileApp\ApiController@userLogin');
	Route::post('facebook_login', 'MobileApp\ApiController@facebook_login');
	Route::post('google_login', 'MobileApp\ApiController@google_login');
	Route::post('customer_forgot_password', 'MobileApp\CustomerAppController@forgot_password');	//NAGOOR
	Route::any('help', 'MobileApp\ApiController@help');
	Route::any('terms', 'MobileApp\ApiController@terms');
	
	/*=================== AGENT SECTION ARE BELOW ================*/
	Route::any('agent_registration','MobileApp\AgentAppController@agent_registration');
	Route::post('agent_login', 'MobileApp\ApiController@agent_login');
	Route::post('agent_forgot_password', 'MobileApp\AgentAppController@agent_forgot_password');
	Route::any('agent_my_account', 'MobileApp\AgentAppController@agent_my_account');
	 Route::any('agent-home-page', 'MobileApp\AgentAppController@home');
	Route::group(['prefix'=>'agent','middleware' => ['jwt.verify']], function() {
	   
	    Route::any('agent_reset_password', 'MobileApp\AgentAppController@agent_reset_password');
		Route::any('agent_my_account', 'MobileApp\AgentAppController@agent_my_account');
	    Route::any('agent_update_account', 'MobileApp\AgentAppController@agent_update_account');
	    Route::any('agent_update_account_otp', 'MobileApp\AgentAppController@agent_update_account_otp');
		Route::any('agent_get_payment_settings', 'MobileApp\AgentAppController@agent_get_payment_settings');
	    Route::any('agent_update_payment_setting', 'MobileApp\AgentAppController@agent_update_payment_setting');	    
	    Route::any('agent_add_deliveryboy', 'MobileApp\AgentAppController@agent_add_deliveryboy');
	    Route::any('agent_add_deliveryboy', 'MobileApp\AgentAppController@agent_add_deliveryboy');
	    Route::any('agent_manage_deliveryboy', 'MobileApp\AgentAppController@agent_manage_deliveryboy');
	    Route::any('agent_edit_deliveryboy', 'MobileApp\AgentAppController@agent_edit_deliveryboy');
	    Route::any('block_unblock_delete', 'MobileApp\AgentAppController@block_unblock_delete');
	    Route::any('new_order_agent', 'MobileApp\AgentAppController@new_order_agent');
	    Route::any('new_order_action', 'MobileApp\AgentAppController@new_order_action');
	    Route::any('agent_order_managment', 'MobileApp\AgentAppController@order_management');
	    Route::any('agent_delivered_orders', 'MobileApp\AgentAppController@order_management');
	    Route::any('agent_get_deliveryboy_list', 'MobileApp\AgentAppController@agent_get_deliveryboy_list');
	    Route::any('assign_order_delBoy', 'MobileApp\AgentAppController@assign_order');
	    Route::any('rejected_order_bydelBoy', 'MobileApp\AgentAppController@rejected_order_bydelBoy');
		Route::any('agent_get_deliveryboy_reassign', 'MobileApp\AgentAppController@agent_get_deliveryboy_reassign');
		Route::any('agent_dashboard','MobileApp\AgentAppController@dashboard');
		Route::any('agent_earning_report','MobileApp\AgentAppController@earning_report');
		Route::any('agent_commission_tracking','MobileApp\AgentAppController@commission_tracking');
		Route::any('agent_commission_transaction','MobileApp\AgentAppController@commission_transaxn');
		Route::any('agent_pay_request','MobileApp\AgentAppController@agent_pay_request');
		Route::any('agent_commission_payment','MobileApp\AgentAppController@agent_commission_payment');
		Route::any('agent_logout', 'MobileApp\AgentAppController@logout');
	});
	Route::group(['prefix'=>'customer','middleware' => ['jwt.verify']], function() {
		/* ====================== CUSTOMER SECTION ARE BELOW ==================== */
		
		Route::post('landing_page', 'MobileApp\CustomerAppController@landing_page');
		Route::any('grocery_home_page', 'MobileApp\CustomerAppController@grocery_home_page'); 
		Route::any('restaurant_home_page', 'MobileApp\CustomerAppController@restaurant_home_page');
		Route::any('all_grocery_list', 'MobileApp\CustomerAppController@all_grocery_lists');  
		Route::any('category_based_grocery', 'MobileApp\CustomerAppController@all_grocery_lists');  
		Route::any('all_restaurant_list', 'MobileApp\CustomerAppController@all_restaurant_lists'); 
		Route::any('category_based_restaurant', 'MobileApp\CustomerAppController@all_restaurant_lists'); 
		Route::any('grocery_details', 'MobileApp\CustomerAppController@grocery_details');
		Route::any('restaurant_details', 'MobileApp\CustomerAppController@restaurant_details');
		Route::any('category_based_products', 'MobileApp\CustomerAppController@category_based_products');
		Route::any('category_based_items', 'MobileApp\CustomerAppController@category_based_items');
		Route::any('choice_list', 'MobileApp\CustomerAppController@choice_list');
    	Route::any('product_details', 'MobileApp\CustomerAppController@product_details');
		Route::any('item_details', 'MobileApp\CustomerAppController@item_details');
		Route::any('payment_methods', 'MobileApp\CustomerAppController@payment_methods');
		Route::any('my_cart', 'MobileApp\CustomerAppController@my_cart');
		Route::any('cod_checkout', 'MobileApp\CustomerAppController@checkout');
		Route::any('paypal_checkout', 'MobileApp\CustomerAppController@checkout');
		Route::any('stripe_checkout', 'MobileApp\CustomerAppController@checkout');
		Route::any('wallet_checkout', 'MobileApp\CustomerAppController@checkout');
		Route::any('customer_reset_password', 'MobileApp\CustomerAppController@customer_reset_password');
		Route::any('customer_my_account', 'MobileApp\CustomerAppController@customer_my_account');
		Route::any('customer_update_account', 'MobileApp\CustomerAppController@customer_update_account');
		Route::any('customer_update_account_with_otp', 'MobileApp\CustomerAppController@customer_update_account_with_otp');
		Route::any('customer_ship_address', 'MobileApp\CustomerAppController@customer_ship_address');
		Route::any('customer_update_shipadd', 'MobileApp\CustomerAppController@customer_update_shipadd');
		Route::any('customer_wishlist', 'MobileApp\CustomerAppController@customer_wishlist');
		Route::any('customer_product_review', 'MobileApp\CustomerAppController@customer_product_review');
		Route::any('customer_item_review', 'MobileApp\CustomerAppController@customer_item_review');
		Route::any('customer_store_review', 'MobileApp\CustomerAppController@customer_store_review');
		Route::any('customer_rest_review', 'MobileApp\CustomerAppController@customer_rest_review');
		Route::any('add_to_wishlist', 'MobileApp\CustomerAppController@add_to_wishlist');
		Route::any('customer_refer_friend', 'MobileApp\CustomerAppController@refer_friend');
		Route::any('refer_friend_send_mail', 'MobileApp\CustomerAppController@refer_friend_send_mail');
		Route::any('customer_payment_settings', 'MobileApp\CustomerAppController@customer_payment_settings');
		Route::any('customer_update_payment_settings', 'MobileApp\CustomerAppController@customer_update_pmtSettings');
		Route::any('product_write_review', 'MobileApp\CustomerAppController@product_write_review');
		Route::any('store_write_review', 'MobileApp\CustomerAppController@store_write_review');
		Route::any('my_orders', 'MobileApp\CustomerAppController@my_orders');
		Route::any('my_order_details', 'MobileApp\CustomerAppController@my_order_details');
		Route::any('customer_invoice', 'MobileApp\CustomerAppController@customer_invoice');
		Route::any('add_to_cart', 'MobileApp\CustomerAppController@add_to_cart');
		Route::any('repeat_order', 'MobileApp\CustomerAppController@repeat_order');
		Route::any('qty_update_cart', 'MobileApp\CustomerAppController@qty_update_cart');
		Route::any('remove_from_cart', 'MobileApp\CustomerAppController@remove_from_cart');
		Route::any('add_choice_toCart', 'MobileApp\CustomerAppController@add_choice_toCart');
		Route::any('remove_choice', 'MobileApp\CustomerAppController@remove_choice');
		Route::any('cart_restaurant_wise', 'MobileApp\CustomerAppController@cart_restaurant_wise');
		Route::any('cart_detail_byrestID', 'MobileApp\CustomerAppController@cart_detail_byrestID');
		Route::any('cancel_order', 'MobileApp\CustomerAppController@cancel_order');
		Route::any('customer_order_review', 'MobileApp\CustomerAppController@customer_order_review');
		Route::any('order_write_review', 'MobileApp\CustomerAppController@order_write_review');
		Route::any('order_tracking', 'MobileApp\CustomerAppController@order_tracking');
		Route::any('add_pre_order_date', 'MobileApp\CustomerAppController@add_pre_order_date');
		Route::any('remove_pre_order', 'MobileApp\CustomerAppController@remove_pre_order');
		Route::any('use_wallet', 'MobileApp\CustomerAppController@use_wallet_selfOrder');
		Route::any('my_wallet', 'MobileApp\CustomerAppController@my_wallet');
		Route::any('my_reviews', 'MobileApp\CustomerAppController@my_reviews');
		Route::any('refund_details', 'MobileApp\CustomerAppController@refund_details');
		Route::any('get_deliver_location', 'MobileApp\CustomerAppController@get_deliver_location');
		Route::any('check_qty_payment', 'MobileApp\CustomerAppController@check_avail_qty');
		Route::any('save_shipping_address', 'MobileApp\CustomerAppController@save_shipping_address');
		
		
	    Route::any('customer_logout', 'MobileApp\CustomerAppController@logout');
	    Route::get('closed', 'DataController@closed');
	});
	
	/* delivery boy api starts */
	Route::any('delivery_person_login','MobileApp\ApiController@delivery_login');
	Route::post('delivery_forgot_password', 'MobileApp\DeliveryAppController@delivery_forgot_password');
	Route::any('delivery_home_page', 'MobileApp\DeliveryAppController@home');
	Route::group(['prefix' => 'delivery','middleware' => ['jwt.verify']],function(){
		
		Route::any('delivery_profile','MobileApp\DeliveryAppController@delivery_profile');
		Route::any('update_profile','MobileApp\DeliveryAppController@update_profile');
		Route::any('update_profile_withOtp','MobileApp\DeliveryAppController@update_profile_withOtp');
		Route::any('get_payment_settings', 'MobileApp\DeliveryAppController@get_payment_settings');
	    Route::any('update_payment_setting', 'MobileApp\DeliveryAppController@update_payment_setting');		
		Route::any('add_update_working_hours','MobileApp\DeliveryAppController@update_wking_hrs');
		Route::any('view_working_hours','MobileApp\DeliveryAppController@view_wk_hrs');
		Route::any('change_password','MobileApp\DeliveryAppController@change_password');
		Route::any('new_orders','MobileApp\DeliveryAppController@new_orders');
		Route::any('delivered_orders','MobileApp\DeliveryAppController@new_orders');
		Route::any('assigned_orders','MobileApp\DeliveryAppController@new_orders');
		Route::any('accept_reject_order','MobileApp\DeliveryAppController@accept_order');
		Route::any('order_management','MobileApp\DeliveryAppController@order_management');
		Route::any('change_order_status','MobileApp\DeliveryAppController@change_order_status');
		Route::any('send_otp','MobileApp\DeliveryAppController@send_otp');
		Route::any('dashboard','MobileApp\DeliveryAppController@dashboard');
		Route::any('invoice_detail','MobileApp\DeliveryAppController@invoice_detail');
		Route::any('earning_report','MobileApp\DeliveryAppController@earning_report');
		Route::any('order_tracking','MobileApp\DeliveryAppController@order_tracking');
		Route::any('commission_tracking','MobileApp\DeliveryAppController@commission_tracking');
		Route::any('commission_transaction','MobileApp\DeliveryAppController@commission_transaxn');
		Route::any('pay_request','MobileApp\DeliveryAppController@pay_request');
		Route::any('commission_payment','MobileApp\DeliveryAppController@commission_payment');
		Route::any('update_location','MobileApp\DeliveryAppController@update_location');
		
		Route::any('delivery_logout','MobileApp\DeliveryAppController@logout');
		
	});


	/*  Restaurant app */
	Route::any('merchant-login','MobileApp\ApiController@merchant_login');
	Route::post('merchant_forgot_password', 'MobileApp\MerchantAppController@merchant_forgot_password');
	Route::any('merchant-home-page','MobileApp\MerchantAppController@home');
	Route::group(['prefix' => 'merchant','middleware' => ['jwt.verify']],function(){
		Route::any('dashboard','MobileApp\MerchantAppController@dashboard');
		Route::any('new-orders','MobileApp\MerchantAppController@order_management');
		Route::any('preparing-orders','MobileApp\MerchantAppController@order_management');
		Route::any('processing-orders','MobileApp\MerchantAppController@order_management');
		Route::any('delivered-orders','MobileApp\MerchantAppController@order_management');
		Route::any('invoice-detail','MobileApp\MerchantAppController@invoice_detail');
		Route::any('accept-reject-item','MobileApp\MerchantAppController@accept_item');
		Route::any('change-status','MobileApp\MerchantAppController@accept_item');
		Route::any('commission-tracking','MobileApp\MerchantAppController@commission_tracking');
		Route::any('transaction-history','MobileApp\MerchantAppController@transaction_history');
		Route::any('pay_request','MobileApp\MerchantAppController@pay_request');
		Route::any('my_profile','MobileApp\MerchantAppController@my_profile');
		Route::any('update_profile','MobileApp\MerchantAppController@update_profile');
		Route::any('view_payment_setting','MobileApp\MerchantAppController@view_payment_setting');
		Route::any('update_payment_setting','MobileApp\MerchantAppController@update_payment_setting');
		Route::any('change_password','MobileApp\MerchantAppController@change_password');
		Route::any('stock_management','MobileApp\MerchantAppController@stock_management');
		Route::any('update_status','MobileApp\MerchantAppController@update_quantity_andStatus');
		Route::any('increase_quantity','MobileApp\MerchantAppController@update_quantity_andStatus');
		Route::any('decrease_quantity','MobileApp\MerchantAppController@update_quantity_andStatus');
		Route::any('notification_list','MobileApp\MerchantAppController@notification_list');
		Route::any('read_notification','MobileApp\MerchantAppController@read_notification');
		Route::any('cancelled-orders','MobileApp\MerchantAppController@order_management');
		Route::any('logout','MobileApp\MerchantAppController@logout');
		
	});
}); 
Route::any('convert_currency','MobileApp\ApiController@convert_currency');
//Route::post('userlogin', 'ApiController@userLogin');
//Route::post('agentlogin', 'ApiController@adminLogin');
//Route::post('test','MobileApp\DeliveryAppController@test');

/* AGENT */
//Route::post('agent_registration', 'UserController@agent_register');
/* EOF AGENT  */

?>