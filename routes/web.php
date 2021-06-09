<?php
	/*
		|--------------------------------------------------------------------------
		| Web Routes
		|--------------------------------------------------------------------------
		|
		| Here is where you can register web routes for your application. These
		| routes are loaded by the RouteServiceProvider within a group which
		| contains the "web" middleware group. Now create something great!
		|
	*/
	use App\Http\Controllers\Controller;
//use App\Http\Controllers;
	//use Config;
	Route::get('/', function () {
		return view('welcome');
	});
	
	
	
	
	/** -------------------------------------ADMIN ROUTES STARTS---------------------------------- **/
	Route::any('admin-login' , function () {
		$set_admin_lang = new Controller;
		$set_admin_session = $set_admin_lang->setAdminLanguage();
		return view('Admin.login');
	});

    Route::post('change_language', 'Controller@change_language');
	Route::any('admin_check_login','Admin\AdminController@check_login');
	Route::get('forgot_password','Admin\AdminController@forgot_password');
	Route::post('admin_forgot_password','Admin\AdminController@admin_forgot_password');
	//	Route::any('change_language','HomeController@change_language');
	Route::any('get-pro-count','Front\FrontRestaurantController@get_pro_count');
	/*-------------------- check admin session ---------------*/

        Route::get('/clear-cache', function() {
            $exitCode = Artisan::call('config:cache');
            return 'DONE'; //Return anything
        });

	Route::group(['middleware' =>['chk_admin_session','subadmin_prev']],function()
	{
		Route::any('admin-dashboard','Admin\AdminController@admin_dashboard');
		/** manage location **/
		Route::any('add-country','Admin\AdminController@add_country');
		Route::any('update-country','Admin\AdminController@update_country');
		Route::any('manage-country','Admin\AdminController@manage_country');
		Route::get('array_search_country', 'Admin\AdminController@array_search_country');
		Route::get('add_searched_country', 'Admin\AdminController@add_searched_country');
		Route::any('country_status/{id}/{status}', 'Admin\AdminController@change_status');
		Route::any('country_default', 'Admin\AdminController@country_default');
		Route::any('edit_country/{id}', 'Admin\AdminController@edit_country');
		/** settings **/
		Route::get('admin-general-settings','Admin\SettingsController@general_settings');
		Route::post('admin-general-settings-submit','Admin\SettingsController@general_settings_submit');
		Route::get('admin-smtp-settings','Admin\SettingsController@smtp_settings');
		Route::post('admin-smtp-settings-submit','Admin\SettingsController@smtp_settings_submit');
		Route::get('admin-social-settings','Admin\SettingsController@social_settings');
		Route::post('admin-social-settings-submit','Admin\SettingsController@social_settings_submit');
		Route::get('admin-logo-settings','Admin\SettingsController@logo_settings');
		Route::post('admin-logo-settings-submit','Admin\SettingsController@logo_settings_submit');
		Route::get('admin-noimage-settings','Admin\SettingsController@noimage_settings');
		Route::post('admin-noimage-settings-submit','Admin\SettingsController@noimage_settings_submit');
		Route::get('admin-payment-settings','Admin\SettingsController@payment_settings');
		Route::post('admin-paynamics-payment-settings-submit','Admin\SettingsController@paynamics_payment_settings_submit');
		Route::post('admin-paymaya-payment-settings-submit','Admin\SettingsController@paymaya_payment_settings_submit');
        Route::post('admin_netbanking_submit','Admin\SettingsController@admin_netbanking_submit');
		Route::post('admin-cod-payment-settings-submit','Admin\SettingsController@cod_payment_settings_submit');
		Route::get('admin-banner-settings','Admin\SettingsController@banner_settings');
		Route::get('admin-banner-settings/{id}','Admin\SettingsController@banner_settings');
		Route::get('admin-profile','Admin\AdminController@admin_profile');
		Route::post('ajax-get-city','Admin\AdminController@ajax_get_city');
		Route::post('admin-profile-settings-submit','Admin\AdminController@profile_submit');
		Route::get('admin-change-password','Admin\AdminController@change_password');
		Route::post('admin-password-submit','Admin\AdminController@change_password_submit');
		Route::get('admin-logout','Admin\AdminController@admin_logout');
		Route::any('add-update-banner','Admin\SettingsController@add_update_banner');
		Route::any('edit_banner/{id}', 'Admin\SettingsController@edit_banner');
		Route::any('banner_status/{id}/{status}', 'Admin\SettingsController@change_banner_status');
		Route::any('manage-advertisement', 'Admin\SettingsController@manage_advertisement');
		Route::any('admin-advertisement-submit','Admin\SettingsController@advertisement_submit');

		
		/**Subadmin**/
		Route::any('add-subadmin','Admin\SubadminController@add_subadmin');
		Route::any('manage-subadmin','Admin\SubadminController@manage_subadmin');
		Route::any('subadmin_list','Admin\SubadminController@subadmin_list');
		Route::any('edit-subadmin/{id}','Admin\SubadminController@edit_subadmin');
		Route::post('admin-submit-subadmin', 'Admin\SubadminController@submit_subadmin');
		Route::post('admin-editsubmit-subadmin', 'Admin\SubadminController@edit_submit_subadmin');
		Route::any('admin-subadmin-status/{id}/{status}', 'Admin\SubadminController@admin_subadmin_status');
		Route::any('change_multi_subadmin_status', 'Admin\SubadminController@change_multi_subadmin_status');
		Route::post('ajax_subadmin_check', 'Admin\SubadminController@ajax_checksubadmin');
		
		
		
		/** Category management **/
		//store category
		Route::any('manage-store-category','Admin\CategoryController@store_category');
		Route::any('store-category_list_ajax','Admin\CategoryController@store_category_list')->name('store-category_list_ajax');

		Route::any('add-store-category','Admin\CategoryController@add_store_category');

		Route::any('ajax_change_all_status','Admin\CategoryController@ajax_change_all_status');

		Route::any('ajax_change_restaurant_status','Admin\CategoryController@ajax_change_restaurant_status');


		Route::any('update-store-category','Admin\CategoryController@update_store_category');
		Route::any('store_cate_status/{id}/{status}','Admin\CategoryController@store_cate_status');
		Route::any('edit_store_category/{id}','Admin\CategoryController@edit_store_category');
		Route::any('multi_store_block','Admin\CategoryController@multi_store_block');
		Route::any('download_store_category/{type}','Admin\CategoryController@download_store_category');
		Route::any('import_store_category','Admin\CategoryController@import_store_category');
		//restaurant category
		Route::any('manage-restaurant-category','Admin\CategoryController@manage_restaurant_category');
		Route::any('res-category_list_ajax','Admin\CategoryController@restaurant_category_list');
		Route::any('add-restaurant-category','Admin\CategoryController@add_restaurant_category');
		Route::any('update-restaurant-category','Admin\CategoryController@update_restaurant_category');
		Route::any('restaurant_cate_status/{id}/{status}','Admin\CategoryController@restaurant_cate_status');
		Route::any('edit_restaurant_category/{id}','Admin\CategoryController@edit_restaurant_category');
		Route::any('multi_restaurant_block','Admin\CategoryController@multi_restaurant_block');
		Route::any('download_restaurant_category/{type}/{sample?}','Admin\CategoryController@download_restaurant_category');
		Route::any('import_restaurant_category','Admin\CategoryController@import_restaurant_category');
		//product category
		Route::any('manage-product-category','Admin\CategoryController@manage_product_category');
		Route::any('product-category_list_ajax','Admin\CategoryController@product_category_list_ajax');
		Route::any('ajax_change_productCat_status','Admin\CategoryController@ajax_change_productCat_status');
		
		Route::any('add-product-category','Admin\CategoryController@add_product_category');
		Route::any('update-product-category','Admin\CategoryController@update_product_category');
		Route::any('edit_product_category/{id}','Admin\CategoryController@edit_product_category');
		Route::any('pro_cate_status/{id}/{status}/{type}','Admin\CategoryController@pro_cate_status'); //common for all pro ,item category
		Route::any('multi_pro_block','Admin\CategoryController@multi_pro_block'); //common for all pro ,item category
		Route::any('download_product_category/{type}','Admin\CategoryController@download_product_category'); //common for all pro ,item category
		Route::any('download_item_category/{type}/{sample?}','Admin\CategoryController@download_item_category');
		Route::any('import_product_category','Admin\CategoryController@import_product_category'); //common for all pro ,item category
		Route::any('import_item_category','Admin\CategoryController@import_item_category'); //common for all pro ,item category
		//sub product category
		Route::any('manage-subproduct/{id}','Admin\CategoryController@manage_subproduct')->name('sub_products_ajax');

		Route::any('ajax_change_subcat_status','Admin\CategoryController@ajax_change_subcat_status');

		
		Route::any('edit_sub_category/{id}/{main_id}','Admin\CategoryController@edit_sub_category');
		Route::any('update-sub-category','Admin\CategoryController@update_sub_category');
		Route::any('sub_cate_status/{id}/{status}/{type}','Admin\CategoryController@sub_cate_status'); //common for all pro ,item category
		Route::any('multi_sub_block','Admin\CategoryController@multi_sub_block'); //common for all sub  pro ,item category
		Route::any('add-sub-category','Admin\CategoryController@add_sub_category'); 
		Route::any('download_sub_category/{type}/{title}/{main}/{sample?}','Admin\CategoryController@download_sub_category'); //common for all sub  pro ,item category
		Route::any('import_sub_category','Admin\CategoryController@import_sub_category'); //common for all sub  pro ,item category
		//item category
		Route::any('manage-item-category','Admin\CategoryController@manage_item_category');
		Route::any('item-category_list_ajax','Admin\CategoryController@item_category_list_ajax');

		Route::any('ajax_change_itemCat_status','Admin\CategoryController@ajax_change_itemCat_status');

		Route::any('add-item-category','Admin\CategoryController@add_item_category');
		Route::any('update-item-category','Admin\CategoryController@update_item_category');
		Route::any('edit_item_category/{id}','Admin\CategoryController@edit_item_category');
		//item sub category
		Route::any('add-subitem-category','Admin\CategoryController@add_subitem_category');
		Route::any('manage-subitem/{id}','Admin\CategoryController@manage_subitem')->name('sub_item_ajax');
		Route::any('edit_subitem_category/{id}/{main_id}','Admin\CategoryController@edit_subtiem_category');
		Route::any('update-subitem-category','Admin\CategoryController@update_subitem_category');
		//choices
		Route::any('manage-choices','Admin\ChoiceController@choice_management');
		Route::any('add-choice','Admin\ChoiceController@add_choice');
		Route::any('edit-choice/{id}','Admin\ChoiceController@edit_choice');
		Route::any('update-choice','Admin\ChoiceController@update_choice');
		Route::any('choice_status/{id}/{status}','Admin\ChoiceController@choice_status');
		Route::any('multi_choice_block','Admin\ChoiceController@multi_choice_block');
		Route::any('download_choices/{type}/{sample?}','Admin\ChoiceController@download_choices');
		Route::any('import_choices','Admin\ChoiceController@import_choices');
		//restaurant
		Route::any('manage-restaurant','Admin\RestaurantController@restaurant_management');
		Route::any('ajax-restaurant-list','Admin\RestaurantController@ajax_restaurant_list');
		Route::any('add-restaurant','Admin\RestaurantController@add_restaurant');

		Route::any('ajax_change_res_status','Admin\RestaurantController@ajax_change_res_status');
		
		
		Route::any('add-restaurant-submit','Admin\RestaurantController@add_restaurant_submit');
		Route::any('edit-restaurant/{id}','Admin\RestaurantController@edit_restaurant');
		Route::any('update-restaurant','Admin\RestaurantController@update_restaurant');
		Route::any('remove_restaurant_banner','Admin\RestaurantController@remove_restaurant_banner');
		Route::any('restaurant_status','Admin\RestaurantController@restaurant_status');
		Route::any('multi_restaurant_status','Admin\RestaurantController@multi_restaurant_block');
		//store
		Route::any('manage-store','Admin\StoreController@manage_store');
		Route::any('ajax-store-list','Admin\StoreController@ajax_store_list');
		Route::any('add-store','Admin\StoreController@add_store');
		Route::any('edit-store/{id}','Admin\StoreController@edit_store');
		Route::any('add-store-submit','Admin\StoreController@add_store_submit');
		Route::any('ajax_change_store_status','Admin\StoreController@ajax_change_store_status');

		Route::any('edit-store-submit','Admin\StoreController@update_store_submit');
		Route::any('remove_store_banner','Admin\StoreController@remove_store_banner');
		Route::any('store_status','Admin\StoreController@store_status');
		Route::any('multi_store_status','Admin\StoreController@multi_store_block');

			Route::any('manage-order','Admin\OrderController@manage_order');
	
	
		/** customer management **/
		Route::any('manage-customer','Admin\CustomerController@manage_customer');
		Route::any('manage-customer/{id}','Admin\CustomerController@manage_customer');
		Route::any('ajax-customer-list','Admin\CustomerController@ajax_customer_list');
		Route::any('add-customer','Admin\CustomerController@add_customer');
		Route::any('edit-customer/{id}','Admin\CustomerController@edit_customer');
		Route::any('add-update-customer','Admin\CustomerController@add_update_customer');
		Route::any('customer_status/{id}/{status}', 'Admin\CustomerController@change_customer_status');
		Route::any('multi_customer_block','Admin\CustomerController@multi_customer_block');
		Route::any('export_customer/{type}','Admin\CustomerController@export_customer');

		Route::any('manage-cms','Admin\CmsController@manage_cms');
		Route::any('add-cms','Admin\CmsController@add_cms');
		Route::any('edit-cms/{id}','Admin\CmsController@edit_cms');
		Route::any('add-update-cms','Admin\CmsController@add_update_cms');
		Route::any('cms_status/{id}/{status}', 'Admin\CmsController@change_cms_status');

		Route::any('manage-faq','Admin\FaqController@manage_faq');
		Route::any('add-faq','Admin\FaqController@add_faq');
		Route::any('edit-faq/{id}','Admin\FaqController@edit_faq');
		Route::any('add-update-faq','Admin\FaqController@add_update_faq');
		Route::any('faq_status/{id}/{status}', 'Admin\FaqController@change_faq_status');
		/** coupon management **/

		/*Route::any('manage-coupon','Admin\CouponController@manage_coupon');
		Route::any('add-coupon','Admin\CouponController@add_coupon');
		Route::any('edit-coupon/{id}','Admin\CouponController@edit_coupon');
		Route::any('add-update-coupon','Admin\CouponController@add_update_coupon');

		Route::any('coupon_status/{id}/{status}', 'Admin\CouponController@change_coupon_status');*/
		Route::any('manage-order','Admin\OrderController@manage_order');
		/**merchant **/
		Route::any('manage-merchant','Admin\MerchantController@merchants_list');
		Route::any('ajax_change_mer_all_status','Admin\MerchantController@ajax_change_mer_all_status');

		Route::any('manage-merchant/{id}','Admin\MerchantController@merchants_list');
		Route::any('ajax-merchant-list','Admin\MerchantController@ajax_merchants_list');
		Route::any('add-merchant','Admin\MerchantController@add_merchant');
		Route::any('save-merchant','Admin\MerchantController@save_merchant');
		Route::any('edit-merchant/{id}','Admin\MerchantController@edit_merchant');
		Route::any('update-merchant','Admin\MerchantController@update_merchant');
		Route::any('merchant_status/{id}/{status}','Admin\MerchantController@change_status');
		Route::any('multi_merchant_block','Admin\MerchantController@multi_changeStatus');
		Route::any('download_merchant_list/{type}','Admin\MerchantController@download_merchants_list');
		//ITEM MANAGEMENT
		Route::any('manage-item','Admin\ItemController@items_list');
		Route::any('manage-item/{id}','Admin\ItemController@items_list');
		Route::any('ajax-item-list','Admin\ItemController@ajax_item_list');
		Route::any('add-item','Admin\ItemController@add_item');
		Route::any('save-item','Admin\ItemController@save_item');
		Route::any('edit-item/{id}','Admin\ItemController@edit_item');
		Route::any('update-item','Admin\ItemController@update_item');
		Route::any('item_status','Admin\ItemController@change_status');
		Route::any('multi_item_block','Admin\ItemController@multi_changeStatus');
		Route::any('get_sub_category','Admin\ItemController@get_sub_category');
		Route::any('download_item_list/{type}','Admin\ItemController@download_items_list');
		//PRODUCT MANAGEMENT
		Route::any('manage-product','Admin\ProductController@products_list');
		Route::any('manage-product/{id}','Admin\ProductController@products_list');
		Route::any('ajax-product-list','Admin\ProductController@ajax_products_list');
		Route::any('add-product','Admin\ProductController@add_product');
		Route::any('save-product','Admin\ProductController@save_product');
		Route::any('edit-product/{id}','Admin\ProductController@edit_product');
		Route::any('update-product','Admin\ProductController@update_product');
		Route::any('product_status','Admin\ProductController@change_status');
		Route::any('multi_product_block','Admin\ProductController@multi_changeStatus');
		Route::any('get_subpdt_category','Admin\ProductController@get_sub_category');
		Route::any('download_product_list/{type}','Admin\ProductController@download_products_list');
		//AGENT
		Route::any('manage-agent-admin','Admin\AgentController@manage_agent');
		Route::any('agent_list_ajax','Admin\AgentController@agent_list_ajax');
		Route::any('add-agent-admin','Admin\AgentController@add_agent');
		Route::any('save-agent-admin','Admin\AgentController@save_agent');
		Route::any('edit-agent-admin/{id}','Admin\AgentController@edit_agent');
		Route::any('update-agent-admin','Admin\AgentController@update_agent');
		Route::any('agent_status_admin/{id}/{status}', 'Admin\AgentController@change_agent_status');
		Route::any('multi_agent_block_admin','Admin\AgentController@multi_agent_block');
		Route::any('export_agent_admin/{type}','Admin\AgentController@export_agent');
		//DELIVERY BOY
		Route::any('manage-deliveryboy-admin','Admin\DeliveryBoyController@manage_deliveryboy');
		Route::any('deliveryboy_list_ajax','Admin\DeliveryBoyController@deliveryboy_list_ajax');
		Route::any('add-deliveryboy-admin','Admin\DeliveryBoyController@add_deliveryboy');
		Route::any('save-deliveryboy-admin','Admin\DeliveryBoyController@save_deliveryboy');
		Route::any('edit-deliveryboy-admin/{id}','Admin\DeliveryBoyController@edit_deliveryboy');
		Route::any('update-deliveryboy-admin','Admin\DeliveryBoyController@update_deliveryboy');
		Route::any('deliveryboy_status_admin/{id}/{status}', 'Admin\DeliveryBoyController@change_deliveryboy_status');
		Route::any('multi_deliveryboy_block_admin','Admin\DeliveryBoyController@multi_deliveryboy_block');
		Route::any('export_deliveryboy_admin/{type}','Admin\DeliveryBoyController@export_deliveryboy');
		//DELIVERY BOY (AGENT MODULE DISABLED)
		Route::any('manage-deliveryboy-admin1/{id?}','Admin\DeliveryBoyController1@manage_deliveryboy');
		
		Route::any('deliveryboy_list_ajax1','Admin\DeliveryBoyController1@deliveryboy_list_ajax');
		Route::any('add-deliveryboy-admin1','Admin\DeliveryBoyController1@add_deliveryboy');
		Route::any('save-deliveryboy-admin1','Admin\DeliveryBoyController1@save_deliveryboy');
		Route::any('edit-deliveryboy-admin1/{id}','Admin\DeliveryBoyController1@edit_deliveryboy');
		Route::any('update-deliveryboy-admin1','Admin\DeliveryBoyController1@update_deliveryboy');
		Route::any('deliveryboy_status_admin1/{id}/{status}', 'Admin\DeliveryBoyController1@change_deliveryboy_status');
		Route::any('multi_deliveryboy_block_admin1','Admin\DeliveryBoyController1@multi_deliveryboy_block');
		Route::any('export_deliveryboy_admin1/{type}','Admin\DeliveryBoyController1@export_deliveryboy');
		
		//ORDER MANAGEMENT
		Route::any('manage-orders','Admin\OrderMgmtController@deals_all_orders');
		Route::any('order-tracking-design','Admin\OrderMgmtController@order_tracking_design');
		Route::any('admin-change-status','Admin\OrderMgmtController@changeStatus');
		Route::any('admin-reject-status','Admin\OrderMgmtController@rejectStatus');
		Route::any('admin-invoice-order/{id}','Admin\OrderMgmtController@InvoiceOrder');
		Route::any('admin-track-order/{id}','Admin\OrderMgmtController@TrackOrder');
		//COMMISSION TRAKCING
		Route::any('admin-commission-tracking','Admin\CmsnTrackController@commision_list');
		Route::any('ajax-commission-lists','Admin\CmsnTrackController@ajax_commision_list');
		Route::any('commission_view_transaction/{id}','Admin\CmsnTrackController@commission_view_transaction');
		Route::any('pay_to_merchant','Admin\CmsnTrackController@pay_to_merchant');
		
		
		//INVENTORY MANAGEMENT
		Route::any('manage-inventory','Admin\InventoryController@inventory_list');
		Route::any('inventory_list_ajax','Admin\InventoryController@ajax_inventory_list');
		Route::any('update-inventory','Admin\InventoryController@updat_quantity');
		Route::any('download_inventory_list/{type}','Admin\InventoryController@download_inventory_list');
		
		Route::any('manage-cancelled-order','Admin\CancelledOrderController@cancelled_order_list');
		Route::any('cancelled_orders_ajax','Admin\CancelledOrderController@cancelled_orders_ajax');
		Route::any('refer-friend-report','Admin\ReferFriendReportController@refer_friend_list')->name('refer_friend_ajax');
		Route::any('pay_to_customer','Admin\CancelledOrderController@pay_to_customer');
		Route::any('refund-to-wallet','Admin\CancelledOrderController@refund_to_wallet');
		
		Route::any('product_bulk_upload','Admin\ProductController@product_bulk_upload');
		Route::any('product_image_bulk_upload_submit','Admin\ProductController@product_image_bulk_upload_submit');
		Route::post('product_bulk_upload_submit', 'Admin\ProductController@product_bulk_upload_submit');
		Route::any('delete_zip/{folder_name}', 'Admin\ProductController@delete_zip_folder');
		
		Route::any('item_bulk_upload','Admin\ItemController@item_bulk_upload');
		Route::any('item_image_bulk_upload_submit','Admin\ItemController@item_image_bulk_upload_submit');
		Route::post('item_bulk_upload_submit', 'Admin\ItemController@item_bulk_upload_submit');
		Route::any('item_delete_zip/{folder_name}', 'Admin\ItemController@item_delete_zip_folder');
		
		//NEWSLETTER
		Route::any('manage-news-letter','Admin\NewsletterController@manage_news_template');
		Route::any('send-newsletter','Admin\NewsletterController@send_newsletter');
		Route::any('send_newsletter_submit','Admin\NewsletterController@send_newsletter_submit');
		Route::get('edit_newsletter_subscriber_status/{id}/{status}', 'Admin\NewsletterController@edit_newsletter_subscriber_status');
		Route::get('delete_newsletter_subscriber/{id}', 'Admin\NewsletterController@delete_newsletter_subscriber');
		
		
		/** review management **/
		Route::any('manage-product-review','Admin\ReviewController@manage_product_review');
		Route::any('manage-item-review','Admin\ReviewController@manage_item_review');
		Route::any('review_status/{id}/{status}/{type}','Admin\ReviewController@pro_review_status'); //common for all type of reviews
		Route::any('multi_review_block','Admin\ReviewController@multi_proreview_block');
		Route::any('view_review/{id}','Admin\ReviewController@view_pro_review');
		Route::any('manage-store-review','Admin\ReviewController@manage_store_review');
		Route::any('manage-restaurant-review','Admin\ReviewController@manage_restaurant_review');
		Route::any('manage-order-review','Admin\ReviewController@manage_order_review');
		/** delivery manager **/
		Route::any('add-delivery-manager','Admin\DeliverymanagerController@add_delivery_manager');
		Route::any('add-manager-submit','Admin\DeliverymanagerController@add_manager_submit');
		Route::any('manage-delivery-manager','Admin\DeliverymanagerController@manage_delivery_manager');
		Route::any('ajax-delivery-manager','Admin\DeliverymanagerController@ajax_delivery_manager');
		Route::any('edit-manager/{id}','Admin\DeliverymanagerController@edit_delivery_manager');
		Route::any('manager_status/{id}/{status}','Admin\DeliverymanagerController@manager_status');
		Route::any('multi_manager_block','Admin\DeliverymanagerController@multi_manager_block');
		Route::any('update-manager','Admin\DeliverymanagerController@update_delivery_manager');
		
		Route::any('paymaya-commission_payment','Admin\PaymayaController@commission_payment');
		Route::any('paymaya_commision_failure', 'Admin\PaymayaController@checkout_failure')->name('paymaya_commision_failure');
		Route::any('paymaya_commision_success', 'Admin\PaymayaController@checkout_success')->name('paymaya_commision_success');

		Route::any('paymaya-cancel-payment','Admin\PaymayaController@cancel_payment');
		Route::any('paypal-details-refund','Admin\PaymayaController@paypal_details_refund');


		Route::any('paymaya_cancel_failure', 'Admin\PaymayaController@cancel_failure')->name('paymaya_cancel_failure');
		Route::any('paymaya_cancel_success', 'Admin\PaymayaController@cancel_success')->name('paymaya_cancel_success');
        Route::any('paymaya-failed-payment','Admin\PaymayaController@failed_payment');
        Route::any('paymaya_failed_failure', 'Admin\PaymayaController@failed_failure');
        Route::any('paymaya_failed_success', 'Admin\PaymayaController@failed_success');

		Route::any('read-notification/{status}', 'Admin\AdminController@read_notification');


		Route::any('admin-order-notification', 'Admin\AdminController@order_notification');
		Route::any('ajax-notification-list', 'Admin\AdminController@order_notification_ajax');
		Route::any('notification_change_status','Admin\AdminController@status_change_notification');
        Route::any('manage_featured_store','Admin\AdminController@manage_featured_store');
        Route::any('manage_failed_orders','Admin\AdminController@manage_failed_orders');
        Route::any('failed_orders_ajax','Admin\AdminController@failed_orders_ajax');
        Route::any('ajax-featuredStore-list','Admin\AdminController@ajax_featuredStore_list');
        Route::any('featStore_approve_status','Admin\AdminController@featStore_approve_status');

        Route::any('failAmt_to_customer','Admin\AdminController@pay_customer');

        //Add by karthik for payment gateway  on 30112018
        Route::any('paypal','Admin\PaymentController@paywithpaypal');
        Route::any('status','Admin\paymentController@getPaymentStatus')->name('status');
        Route::any('stripesubmit','Admin\StripeController@stripepay');
        Route::any('stripe-cancel-paymentsubmit','Admin\StripeController@cancelstripepay');
		
		/*Delivery Boy Map */
		Route::any('admin-delivery-boy-map','Admin\AdminController@delivery_boy_map');

		/* Delivery person commission tracking */
		Route::any('admin-delivery-commission-tracking','Admin\DeliveryCmsnController@delivery_commission_tracking');
		Route::any('admin-delboy-commission-lists','Admin\DeliveryCmsnController@ajax_delBoy_commision_list');
		Route::any('delboy_view_transaction/{id}','Admin\DeliveryCmsnController@commission_view_transaction1');
		Route::any('admin_pay_request/{id}/{delboy_id}','Admin\DeliveryCmsnController@pay_request1');
		Route::any('admin_pay_to_delboy','Admin\DeliveryCmsnController@pay_to_deliveryboy');
		Route::any('admin-paypal-commission-delboy','Admin\DeliveryCmsnController@commission_paypal_delboy');
		Route::any('admin-paypal-commission-success-delboy','Admin\DeliveryCmsnController@paypal_cmsn_suxes');
        Route::any('admin-paypal-commission-failure-delboy','Admin\DeliveryCmsnController@paypal_cmsn_fail');

		//REPORT SECTION 
		Route::any('manage-order-report', 'Admin\ReportController@order_report');
		Route::any('download_order_rpt', 'Admin\ReportController@download_order_report');
		Route::any('manage-delboy-report', 'Admin\ReportController@delboy_report');
		Route::any('earning_report', 'Admin\ReportController@earning_report');
		Route::any('earning-report', 'Admin\ReportController@earning_report');
		Route::any('download_earning_rpt', 'Admin\ReportController@download_earning_rpt');
		Route::any('merchant-transaction-report', 'Admin\ReportController@merchant_transaction_report');
		Route::any('download_mer_transaction', 'Admin\ReportController@download_mer_transaction');
		Route::any('delboy-transaction-report', 'Admin\ReportController@delboy_transaction_report');
		Route::any('download_delboy_transaction', 'Admin\ReportController@download_delboy_transaction');
		Route::any('delboy_earning_report', 'Admin\ReportController@delboy_earning_report');
		Route::any('download_delboy_earning_rpt', 'Admin\ReportController@download_delboy_earning_rpt');
		Route::any('consolidate-report', 'Admin\ReportController@consolidate_report');
		Route::any('download_consolidate_rpt', 'Admin\ReportController@download_consolidate_rpt');
        /*For Testing purpose only
         * Route::get('stripeget', function () {
            return view('Admin.Stripe');
        });*/
	});

	/** ------------------------------ ADMIN ROUTES ENDS --------------------------------**/
	
	/*CHECKOUT  test */
	Route::any('cart-checkout','Admin\CheckoutController@checkout');
	Route::any('checkout-success','Admin\CheckoutController@checkoutSuccess');
	Route::any('checkout-failure','Admin\CheckoutController@checkoutFailure');
	Route::any('checkout-cancel','Admin\CheckoutController@checkoutCancel');
	/* EOF CHECKOUT */
	
	/**-----------------------------MERCHANT LOGIN ROUTES START------------------------------**/
	Route::any('merchant-login' , function () {
		$set_admin_lang = new Controller;
		$set_admin_session = $set_admin_lang->setLanguageLocaleMerchant();
		return view('sitemerchant.merchant_login');
	});
	Route::any('mer_login_check', 'Merchant\MerchantLoginController@merchant_login_check');
	Route::get('mer_forgot_password', 'Merchant\MerchantLoginController@mer_forgot_password');
	Route::post('mer_forgot_password_submit','Merchant\MerchantLoginController@mer_forgot_password_submit');

	Route::group(['middleware' => ['chk_mer_session']],function(){
	
		Route::get('merchant-logout', 'Merchant\MerchantLoginController@merchant_logout');
		Route::get('merchant_dashboard', 'Merchant\DashboardController@merchant_dashboard');
		Route::any('merchant_profile', 'Merchant\DashboardController@merchant_profile');
		Route::any('merchant_change_password', 'Merchant\DashboardController@change_password');
		/*product category*/
		Route::any('mer_manage-product-category','Merchant\CategoryController@manage_product_category');
		Route::any('mer_ajax-product-category','Merchant\CategoryController@ajax_product_category');
		Route::any('mer_multi_pro_block','Merchant\CategoryController@multi_pro_block'); //common for all pro ,item category
		Route::any('mer_edit_product_category/{id}','Merchant\CategoryController@edit_product_category');
		Route::any('mer_download_product_category/{type}','Merchant\CategoryController@download_product_category'); 
		Route::any('mer_download_item_category/{type}/{sample?}','Merchant\CategoryController@download_item_category'); 
		Route::any('mer_update-product-category','Merchant\CategoryController@update_product_category');
		Route::any('mer_add-product-category','Merchant\CategoryController@add_product_category');
		Route::any('mer_manage-subproduct/{id}','Merchant\CategoryController@manage_subproduct')->name('sub_product_ajax');
		Route::any('mer_add-sub-category','Merchant\CategoryController@add_sub_category'); 
		Route::any('mer_edit_sub_category/{id}/{main_id}','Merchant\CategoryController@edit_sub_category');
		Route::any('mer_update-sub-category','Merchant\CategoryController@update_sub_category');
		Route::any('mer_multi_sub_block','Merchant\CategoryController@multi_sub_block'); //common for all sub  pro ,item category
		Route::any('mer_download_sub_category/{type}/{title}/{main}','Merchant\CategoryController@download_sub_category');
		Route::any('mer_manage-item-category','Merchant\CategoryController@manage_item_category');
		Route::any('mer_item_category_list_ajax','Merchant\CategoryController@item_category_list_ajax');
		Route::any('mer_multi_pro_block','Merchant\CategoryController@multi_pro_block'); //common for all pro ,item category
		Route::any('mer_add-item-category','Merchant\CategoryController@add_item_category');
		Route::any('mer-update-item-category','Merchant\CategoryController@update_item_category');
		Route::any('mer_edit_item_category/{id}','Merchant\CategoryController@edit_item_category');
		//mer_manage-subitem
		Route::any('mer_manage-subitem/{id}','Merchant\CategoryController@manage_subitem')->name('mer_sub_item_ajax');
		Route::any('mer_add-subitem-category','Merchant\CategoryController@add_subitem_category'); 
		Route::any('mer_edit_subitem_category/{id}/{main_id}','Merchant\CategoryController@edit_subtiem_category');
		Route::any('mer_update-subitem-category','Merchant\CategoryController@update_subitem_category');
		Route::any('mer_import_product_category','Merchant\CategoryController@import_product_category');
		Route::any('mer_import_item_category','Merchant\CategoryController@import_item_category');
		Route::any('mer_import_sub_category','Merchant\CategoryController@import_sub_category');//common for all sub  pro ,item categoryr
			

		/** delete notification **/
		
		
		//MERCHANT SECTION
		//restaurant
		Route::any('mer-manage-restaurant','Merchant\MerRestaurantController@manage_restaurant');
		Route::any('mer-add-restaurant','Merchant\MerRestaurantController@add_restaurant');
		Route::any('mer-update-restaurant','Merchant\MerRestaurantController@update_restaurant');
		Route::any('remove_mer_restaurant_banner','Merchant\MerRestaurantController@remove_restaurant_banner');
		//store
		Route::any('mer-manage-store','Merchant\MerStoreController@manage_store');
		Route::any('mer-add-store','Merchant\MerStoreController@add_store');
		Route::any('mer-update-store','Merchant\MerStoreController@update_store');
		Route::any('remove_mer_store_banner','Merchant\MerStoreController@remove_store_banner');
		/*Merchant choices*/
		Route::any('mer-manage-choices','Merchant\ChoiceController@choice_management');
		Route::any('mer-add-choice','Merchant\ChoiceController@add_choice');
		Route::any('mer-edit-choice/{id}','Merchant\ChoiceController@edit_choice');
		Route::any('mer-update-choice','Merchant\ChoiceController@update_choice');
		Route::any('mer-choice_status/{id}/{status}','Merchant\ChoiceController@choice_status');
		Route::any('mer-multi_choice_block','Merchant\ChoiceController@multi_choice_block');
		Route::any('mer-download_choices/{type}/{sample?}','Merchant\ChoiceController@download_choices');
		Route::any('mer-import_choices','Merchant\ChoiceController@import_choices');
		/*End Merchant choices*/
		
		//MERCHANT ORDER MANAGEMENT
		Route::any('mer-manage-orders','Merchant\OrderMgmtController@deals_all_orders');
		Route::any('mer-order-details/{id}','Merchant\OrderMgmtController@order_detail');
		Route::any('mer-admin-invoice-order/{id}','Merchant\OrderMgmtController@InvoiceOrder');
		Route::any('mer-admin-track-order/{id}','Merchant\OrderMgmtController@TrackOrder');
		Route::any('merchant-change-status','Merchant\OrderMgmtController@changeStatus');
		Route::any('merchant-reject-status','Merchant\OrderMgmtController@rejectStatus');
		
		//END MERCHANT ORDER MANAGEMENT
		
		//MERCHANT COMMISSION TRAKCING
		Route::any('merchant-commission-tracking','Merchant\CmsnTrackController@commision_list');
		Route::any('mer_commission_view_transaction/{id}','Merchant\CmsnTrackController@commission_view_transaction');
		Route::any('send_pay_request/{id}','Merchant\CmsnTrackController@pay_request');
		//MERCHANT COMMISSION TRAKCING
		
		//MERCHANT INVENTORY MANAGEMENT
		Route::any('mer-manage-inventory','Merchant\InventoryController@inventory_list');
		Route::any('mer_inventory_list_ajax','Merchant\InventoryController@ajax_inventory_list');
		Route::any('mer-update-inventory','Merchant\InventoryController@updat_quantity');
		Route::any('mer_download_inventory_list/{type}','Merchant\InventoryController@download_inventory_list');
		
		/* MERCHANT REVIEW MANAGEMENT */
		/** review management **/
		Route::any('mer-manage-product-review','Merchant\ReviewController@manage_product_review');
		Route::any('mer-manage-item-review','Merchant\ReviewController@manage_item_review');
		Route::any('mer_review_status/{id}/{status}/{type}','Merchant\ReviewController@pro_review_status'); //common for all type of reviews
		Route::any('mer_multi_review_block','Merchant\ReviewController@multi_proreview_block');
		Route::any('mer_view_review/{id}','Merchant\ReviewController@view_pro_review');
		Route::any('mer-manage-store-review','Merchant\ReviewController@manage_store_review');
		Route::any('mer-manage-restaurant-review','Merchant\ReviewController@manage_restaurant_review');
		Route::any('mer-manage-order-review','Merchant\ReviewController@manage_order_review');
		/* MERCHANT REVIEW MANAGEMENT */
		
		//MERCHANT ITEM MANAGEMENT
        Route::any('mer-manage-item','Merchant\ItemController@items_list');
		Route::any('mer-manage-item/{id}','Merchant\ItemController@items_list');
		Route::any('ajax-mer-item-list','Merchant\ItemController@ajax_item_list');
		Route::any('mer-add-item','Merchant\ItemController@add_item');
		Route::any('mer-save-item','Merchant\ItemController@save_item');
		Route::any('mer-edit-item/{id}','Merchant\ItemController@edit_item');
		Route::any('mer-update-item','Merchant\ItemController@update_item');
		Route::any('mer_item_status/{id}/{status}','Merchant\ItemController@change_status');
		Route::any('mer_multi_item_block','Merchant\ItemController@multi_changeStatus');
		Route::any('mer_get_sub_category','Merchant\ItemController@get_sub_category');
		Route::any('mer_download_item_list/{type}','Merchant\ItemController@download_items_list');
		Route::any('mer_item_bulk_upload','Merchant\ItemController@item_bulk_upload');
		Route::any('mer_item_image_bulk_upload_submit','Merchant\ItemController@item_image_bulk_upload_submit');
		Route::post('mer_item_bulk_upload_submit', 'Merchant\ItemController@item_bulk_upload_submit');
		Route::any('mer_item_delete_zip/{folder_name}', 'Merchant\ItemController@item_delete_zip_folder');
		//MERCHANT PRODUCT MANAGEMENT
		Route::any('mer-manage-product','Merchant\ProductController@products_list');
		Route::any('mer-manage-product/{id}','Merchant\ProductController@products_list');
		Route::any('mer-ajax-product','Merchant\ProductController@ajax_products_list');
		Route::any('mer-add-product','Merchant\ProductController@add_product');
		Route::any('mer-save-product','Merchant\ProductController@save_product');
		Route::any('mer-edit-product/{id}','Merchant\ProductController@edit_product');
		Route::any('mer-update-product','Merchant\ProductController@update_product');
		Route::any('mer_product_status/{id}/{status}','Merchant\ProductController@change_status');
		Route::any('mer_multi_product_block','Merchant\ProductController@multi_changeStatus');
		Route::any('mer_get_subpdt_category','Merchant\ProductController@get_sub_category');
		
		Route::any('mer_download_product_list/{type}','Merchant\ProductController@download_products_list');
		Route::any('mer_product_bulk_upload','Merchant\ProductController@product_bulk_upload');
		Route::any('mer_product_image_bulk_upload_submit','Merchant\ProductController@product_image_bulk_upload_submit');
		Route::post('mer_product_bulk_upload_submit', 'Merchant\ProductController@product_bulk_upload_submit');
		Route::any('mer_delete_zip/{folder_name}', 'Merchant\ProductController@delete_zip_folder');
		Route::any('mer-manage-cancelled-order','Merchant\CancelledOrderController@cancelled_order_list');
		Route::any('mer_cancelled_orders_ajax','Merchant\CancelledOrderController@cancelled_orders_ajax');


		Route::any('notification-manager', 'Merchant\MerchantController@order_notification');
		Route::any('notification-list-merchant', 'Merchant\MerchantController@order_notification_ajax');
		Route::any('notification_status_change','Merchant\MerchantController@status_change_notification');

        Route::any('make-featured','Merchant\FeaturedController@make_featured');
        Route::any('featured_offline_checkout','Merchant\FeaturedController@featured_offline_checkout');
        Route::any('featured_paymaya_checkout','Merchant\FeaturedController@featured_paymaya_checkout');
        Route::any('featured_paynamics_checkout','Merchant\FeaturedController@featured_paynamics_checkout');

        Route::any('paymaya_featured_success', 'Merchant\FeaturedController@checkout_failure');
        Route::any('paymaya_featured_failure', 'Merchant\FeaturedController@checkout_success');
        
        Route::any('refresh_mer_notification', 'Merchant\DashboardController@refresh_mer_notification');



    });
	/**------------------------------ MERCHANT ROUTE ENDS ----------------------------------------------**/
	
	/**----------------------------------- FRONT END ROUTE STARTS --------------------------------------**/
	// To get Session language define routes in following group
	Route::group(['middleware' => ['language']], function () 
	{	
		Route::get('/','Front\FrontController@index');
		Route::get('refer-login/{id}','Front\FrontController@index');
		Route::get('faq','Front\FrontController@faq');
		Route::get('testcron','Front\FrontController@testcron');
		Route::get('search-restaurant','Front\FrontController@search_restaurant');
		Route::get('refreshcaptcha','Front\FrontController@refreshcaptcha');
		Route::any('add_searched_restaurant','Front\FrontController@add_searched_restaurant');
		Route::any('restaurant_redirect','Front\FrontController@restaurant_redirect');
		Route::any('subscription_submit','Front\FrontController@subscription_submit');
		if(Config::get('suspend_status')==1)
		{
			Route::group(['middleware' => 'login_throttle:'.Config::get('max_attempt').','.Config::get('suspend_duration').''],function(){
				Route::any('check_login','Front\LoginController@check_login');
			});
		}
		else
		{
		  Route::any('check_login','Front\LoginController@check_login');
		}
		
		Route::any('user_forgot_password','Front\LoginController@forgot_password');
		Route::any('restaurant-listings','Front\FrontController@restaurant_listings');
		Route::any('save-location-insession','Front\FrontController@session_location');
		Route::any('save-location-insession-clearcart','Front\FrontController@session_location_clearcart');
		Route::any('clearcart','Front\FrontController@clearcart');
		Route::any('clearSavecart','Front\FrontCartController@clearSavecart');
		Route::any('all-categories','Front\FrontController@all_categories');
		Route::any('all-categories/{id}','Front\FrontController@all_categories');
		Route::any('grocery-listings','Front\FrontController@grocery_listings');
		Route::any('all-grocery-categories','Front\FrontController@all_grocery_categories');
		Route::any('all-grocery-categories/{id}','Front\FrontController@all_grocery_categories');
		Route::any('restaurant/{name}/{id?}','Front\FrontRestaurantController@restaurant_detail');
		Route::any('getItemName','Front\FrontRestaurantController@getItemName');
		Route::any('item-list','Front\FrontRestaurantController@item_list');
		Route::any('clear_user_session','Front\FrontRestaurantController@clear_session');
		Route::any('item-details/{id}','Front\FrontRestaurantController@item_details');
		Route::any('{rest_name}/item-details/{item_slug}','Front\FrontRestaurantController@item_details2');
		Route::any('store/{name}/{id}','Front\FrontStoreController@store_detail');
		Route::get('getpdtName','Front\FrontStoreController@getItemName');
		Route::any('product-details/{id}','Front\FrontStoreController@product_details');
		Route::any('test_sql','Front\FooterController@test');
        Route::get('cms/{id}', 'Front\FooterController@cms');
        Route::get('cus_logout','Front\LoginController@cus_logout');
        Route::get('fb_logout','Front\FacebookController@logout');
		Route::post('signup_with_otp','Front\LoginController@cus_signup_with_otp');
		Route::post('signup','Front\LoginController@cus_signup');
		Route::post('check_otp','Front\LoginController@check_otp');
		Route::post('mobile_check_otp','Front\LoginController@mobile_check_otp');
		Route::post('mer_check_otp','Front\LoginController@mer_check_otp');
		Route::get('auth/facebook', 'Front\FacebookController@redirectToFacebook');
		Route::get('auth/facebook/callback', 'Front\FacebookController@handleFacebookCallback');
		Route::get('auth/google','Front\GoogleLoginController@redirectToGoole');
		Route::get('auth/google/callback', 'Front\GoogleLoginController@handleGoogleCallback');
		Route::get('merchant_signup','Front\LoginController@merchant_signup');
		Route::post('merchant_signup_submit','Front\LoginController@merchant_signup_submit');
		Route::post('mer_signup_with_otp','Front\LoginController@mer_signup_with_otp');
		Route::any('mer_signup_enter_otp','Front\LoginController@mer_signup_enter_otp');
		Route::get('delivery-person-signup','Front\LoginController@delivery_signup');
		Route::post('delivery_signup_submit','Front\LoginController@delivery_signup_submit');
		Route::post('del_signup_with_otp','Front\LoginController@del_signup_with_otp');
		Route::any('del_signup_enter_otp','Front\LoginController@del_signup_enter_otp');
		Route::post('del_check_otp','Front\LoginController@del_check_otp');
		//Route::view('order-invoice','Front.order_invoice');
		Route::get('contact-us','Front\FooterController@contact_us');
		Route::post('contact_us_message','Front\FooterController@contact_us_message');
		Route::any('insert_newsletter','Front\FrontController@insert_newsletter_subscription');
		
		/*------------------Check User Session -------------------------------*/
		Route::group(['middleware' => ['checkSession']], function ()
		{
			Route::any('add_cart_item','Front\FrontRestaurantController@add_cart_item');
			Route::any('add_cart_product','Front\FrontStoreController@add_cart_product');
			Route::any('cart','Front\FrontCartController@cart_details');
			Route::any('remove_cart','Front\FrontCartController@remove_from_cart');
			Route::any('remove_all_item','Front\FrontCartController@remove_all_item');
			Route::any('update-cart-choice','Front\FrontCartController@update_cart_choice');
			Route::any('update-cart-items','Front\FrontCartController@update_cart_items');
			Route::any('update_spl_req','Front\FrontCartController@update_spl_request');
			Route::any('cart_update_chk_qty','Front\FrontCartController@cart_update_chk_qty');
			Route::any('save-pre-order','Front\FrontCartController@save_pre_order');
			Route::any('save-check-pre-order','Front\FrontCartController@save_check_pre_order');
			Route::any('remove-pre-order','Front\FrontCartController@remove_pre_order');
			Route::any('checkout','Front\FrontCartController@checkout');
			Route::any('cod_checkout','Front\FrontCodController@cod_checkout');
			Route::any('paynamics_checkout','Front\FrontCodController@paynamics_checkout');

			Route::any('paymaya_checkout','Front\PaymayaController@paymaya_checkout');
			Route::any('paymaya_failure', 'Front\PaymayaController@checkout_failure');
			Route::any('paymaya_success', 'Front\PaymayaController@checkout_success');

            Route::any('paypalstatus','Front\PaymayaController@getPaymentStatus')->name('paypalstatus');
			Route::any('paypal_pmt_failure', 'Front\PaymayaController@checkout_failure');
            Route::any('frontpaypal','Admin\PaymentController@paywithpaypal');

			Route::any('wallet_checkout','Front\FrontCodController@wallet_checkout');
			Route::any('refer_friend','Front\FrontController@refer_friend');
			Route::any('user-payment-settings','Front\FrontController@payment_settings');
			Route::get('customer_profile','Front\LoginController@customer_profile');
			Route::post('customer_profile_update','Front\LoginController@customer_profile_update');
			Route::get('shipping_address','Front\LoginController@shipping_address');
			Route::post('customer_shipping_update','Front\LoginController@customer_shipping_update');
			Route::get('addtowish','Front\FrontController@addtowish');
			//remove wishlist 
//			Route::any('remove_wish_product','Front\FrontController@remove_wish_product');
			 Route::any('remove_wish_product/{id}','Front\FrontController@remove_wish_product');
			Route::any('user-wishlist', 'Front\FrontController@user_wishlist');
	 		Route::any('my-orders','Front\FrontController@my_orders');
			//Route::view('user-change-password','Front.user_change_password');
			Route::any('user-change-password','Front\FrontController@user_change_password');
			Route::post('user-change-password-submit','Front\FrontController@user_change_password_submit');
			Route::any('order-invoice/{id}','Front\FrontController@InvoiceOrder');
			Route::any('order-test','Front\FrontController@TestOrder');
			Route::any('checkout-test','Front\FrontCodController@TestOrder');

			Route::any('order-details/{id}','Front\FrontController@OrderDetailToCancel');
            Route::any('reorder/{id}','Front\FrontController@Reorder');

			Route::any('customer-cancel-status','Front\FrontController@cancelOrder');
			Route::any('track-order/{id}','Front\FrontController@TrackOrder');
			Route::any('user-wallet','Front\CustomerController@my_wallet');
            Route::any('wallet-used','Front\CustomerController@used_wallet');
			Route::any('user-review','Front\CustomerController@my_review');
			Route::post('restaurant_review_submit','Front\CustomerController@restaurant_review_submit');
			Route::post('store_review_submit','Front\CustomerController@store_review_submit');
			Route::post('product_review_submit','Front\CustomerController@product_review_submit');
			Route::post('item_review_submit','Front\CustomerController@item_review_submit');
			Route::post('order_review_submit','Front\CustomerController@order_review_submit');
			Route::any('view_order_review/{id}','Front\CustomerController@view_order_review');
			Route::get('check_mobile_num_is_new','Front\CustomerController@check_mobile_num_is_new');
			Route::any('send_verification_mail','Front\FrontController@send_verification_mail');
			Route::any('send_verification_msg','Front\FrontController@send_verification_msg');
			Route::any('save_verify_status','Front\FrontController@save_verify_status');
			Route::any('order-notification', 'Front\FrontController@order_notification');
			Route::any('notification-list-customer', 'Front\FrontController@order_notification_ajax');
			Route::any('notification_status_customer','Front\FrontController@status_change_notification');
			Route::any('set-redirect-url','Front\FrontController@set_redirect_url');
		

		Route::any('order-summary/{id}','Front\FrontController@order_summary');
		Route::any('view-refund/{id}','Front\FrontController@refund_details');
			
		});
		/*------------------------Check user session ends ----------------------------*/

	});
	
	/**-------------------------------FRONT END ROUTE ENDS ---------------------------------**/
	/*DELIVERY MANAGEMENT PANEL STARTS HERE */
	Route::get('delivery-manager-login', 'DeliveryManager\DelMgrController@dmgr_login');
	Route::any('delivery-manager-authentication', 'DeliveryManager\DelMgrController@dmgr_login_check');
	Route::get('delivery-manager-forgot-password', 'DeliveryManager\DelMgrController@forgot_password');
	Route::post('delmgr_forgot_password_submit','DeliveryManager\DelMgrController@forgot_password_submit');
	
	Route::group(['middleware' => ['DeliveryManagerAuth']], function () {
		Route::get('delivery-manager-logout', 'DeliveryManager\DelMgrController@dmgr_logout');
		
		Route::get('delivery-manager-dashboard', 'DeliveryManager\DelMgrController@dmgr_dashboard');
		Route::any('delivery-managerprofile', 'DeliveryManager\DelMgrController@dmgr_profile');
		Route::any('delivery-manager-change-password', 'DeliveryManager\DelMgrController@change_password');
		
		Route::any('assign_agent_ajax','DeliveryManager\OrderMgmtController@agent_list_ajax');
		Route::any('assign_delboy_ajax','DeliveryManager\OrderMgmtController@delboy_list_ajax');
		Route::any('assign_order_to_agent','DeliveryManager\OrderMgmtController@assign_order_to_agent');
		Route::any('assign_order_to_delboy','DeliveryManager\OrderMgmtController@assign_order_to_delboy');
		
		Route::any('delivery-manager-settings','DeliveryManager\DelMgrController@general_settings');
		
		Route::any('manage-agent','DeliveryManager\AgentController@manage_agent');
		Route::any('agent_lists_ajax','DeliveryManager\AgentController@agent_list_ajax');
		Route::any('add-agent','DeliveryManager\AgentController@add_agent');
		Route::any('save-agent','DeliveryManager\AgentController@save_agent');
		Route::any('edit-agent/{id}','DeliveryManager\AgentController@edit_agent');
		Route::any('update-agent','DeliveryManager\AgentController@update_agent');
		Route::any('agent_status/{id}/{status}', 'DeliveryManager\AgentController@change_agent_status');
		Route::any('multi_agent_block','DeliveryManager\AgentController@multi_agent_block');
		Route::any('export_agent/{type}','DeliveryManager\AgentController@export_agent');
		
		/* IF AGENT IS ENABLED */
		Route::any('manage-deliveryboy','DeliveryManager\DeliveryBoyController@manage_deliveryboy');
		Route::any('deliveryboy_lists_ajax','DeliveryManager\DeliveryBoyController@deliveryboy_list_ajax');
		Route::any('add-deliveryboy','DeliveryManager\DeliveryBoyController@add_deliveryboy');
		Route::any('save-deliveryboy','DeliveryManager\DeliveryBoyController@save_deliveryboy');
		Route::any('edit-deliveryboy/{id}','DeliveryManager\DeliveryBoyController@edit_deliveryboy');
		Route::any('update-deliveryboy','DeliveryManager\DeliveryBoyController@update_deliveryboy');
		Route::any('deliveryboy_status/{id}/{status}', 'DeliveryManager\DeliveryBoyController@change_deliveryboy_status');
		Route::any('multi_deliveryboy_block','DeliveryManager\DeliveryBoyController@multi_deliveryboy_block');
		Route::any('export_deliveryboy/{type}','DeliveryManager\DeliveryBoyController@export_deliveryboy');
		
		/*IF AGENT IS DISABLED */ 
		Route::any('manage-deliveryboy1','DeliveryManager\DeliveryBoyController1@manage_deliveryboy');
		Route::any('deliveryboy_lists_ajax1','DeliveryManager\DeliveryBoyController1@deliveryboy_list_ajax');
		Route::any('add-deliveryboy1','DeliveryManager\DeliveryBoyController1@add_deliveryboy');
		Route::any('save-deliveryboy1','DeliveryManager\DeliveryBoyController1@save_deliveryboy');
		Route::any('edit-deliveryboy1/{id}','DeliveryManager\DeliveryBoyController1@edit_deliveryboy');
		Route::any('update-deliveryboy1','DeliveryManager\DeliveryBoyController1@update_deliveryboy');
		Route::any('deliveryboy_status1/{id}/{status}', 'DeliveryManager\DeliveryBoyController1@change_deliveryboy_status');
		Route::any('multi_deliveryboy_block1','DeliveryManager\DeliveryBoyController1@multi_deliveryboy_block');
		Route::any('export_deliveryboy1/{type}','DeliveryManager\DeliveryBoyController1@export_deliveryboy');
		
		
		Route::any('delivery-manage-orders', 'DeliveryManager\OrderMgmtController@deals_all_orders');
		Route::any('delivery-manage-orders1', 'DeliveryManager\OrderMgmtController@manage_orders');
		Route::any('delivery-invoice-order/{id}/{merchant_id}', 'DeliveryManager\OrderMgmtController@InvoiceOrder');
		Route::any('delivery-track-order/{id}/{merchant_id}', 'DeliveryManager\OrderMgmtController@TrackOrder');
		Route::any('assign-delivery-boy', 'DeliveryManager\OrderMgmtController@AssignDeliveryBoy');
		Route::any('assign-delivery-boy1', 'DeliveryManager\OrderMgmtController@AssignDeliveryBoy1');
		
		Route::any('new-delivery-manage-orders', 'DeliveryManager\NewOrderMgmtController@newdeals_all_orders');

		Route::any('rejected-order-agent-dmgr', 'DeliveryManager\NewOrderMgmtController@rejected_order_by_agent');
		Route::any('reassign-delivery-boy', 'DeliveryManager\NewOrderMgmtController@ReassignDeliveryBoy');
		Route::any('reassign_agent_ajax','DeliveryManager\NewOrderMgmtController@agent_withoutRejected_ajax');
		Route::any('reassign_order_to_agent','DeliveryManager\NewOrderMgmtController@reassign_order_to_agent');
		
		Route::any('rejected-order-delboy-dmgr', 'DeliveryManager\NewOrderMgmtController@rejected_order_by_delboy');
		Route::any('reassign-delivery-boy1', 'DeliveryManager\NewOrderMgmtController@ReassignDeliveryBoy1'); 
		Route::any('reassign_delboy_ajax','DeliveryManager\NewOrderMgmtController@delboy_withoutRejected_ajax'); 
		Route::any('reassign_order_to_delboy','DeliveryManager\NewOrderMgmtController@reassign_order_to_delboy');
		
		Route::any('delivery-commission-tracking','DeliveryManager\CmsnTrackController@commision_list');
		Route::any('deliveryboy-commission-tracking','DeliveryManager\CmsnTrackController@delBoy_commision_list');
		Route::any('agent-commission-lists','DeliveryManager\CmsnTrackController@ajax_commision_list');
		Route::any('delboy-commission-lists','DeliveryManager\CmsnTrackController@ajax_delBoy_commision_list');
		Route::any('delmgr_view_transaction/{id}','DeliveryManager\CmsnTrackController@commission_view_transaction');
		Route::any('delmgr_view_transaction1/{id}','DeliveryManager\CmsnTrackController@commission_view_transaction1');
		
		Route::any('agent_pay_request/{id}/{agent_id}','DeliveryManager\CmsnTrackController@pay_request');
		Route::any('delboy_pay_request/{id}/{delboy_id}','DeliveryManager\CmsnTrackController@pay_request1');
		Route::any('pay_to_delmgr','DeliveryManager\CmsnTrackController@pay_to_deliverymanager');
		Route::any('pay_to_delboy','DeliveryManager\CmsnTrackController@pay_to_deliveryboy');
		Route::any('paypal-commission-delboy','DeliveryManager\CmsnTrackController@commission_paypal_delboy');
		Route::any('paypal-commission-success-delboy','DeliveryManager\CmsnTrackController@paypal_cmsn_suxes');
        Route::any('paypal-commission-failure-delboy','DeliveryManager\CmsnTrackController@paypal_cmsn_fail');
			
		Route::any('paymaya-commission_agent','DeliveryManager\CmsnTrackController@commission_payment');
		Route::any('agent_commision_failure', 'DeliveryManager\CmsnTrackController@checkout_failure');
		Route::any('agent_commision_success', 'DeliveryManager\CmsnTrackController@checkout_success');
		Route::any('dmgr-order-notification', 'DeliveryManager\DelMgrController@order_notification');
		Route::any('dmgr-ajax-notification-list', 'DeliveryManager\DelMgrController@order_notification_ajax');
		Route::any('dmgr_notification_change_status','DeliveryManager\DelMgrController@status_change_notification');


		/*Delivery Boy Map*/
		Route::any('delivery-boy-map','DeliveryManager\DelMgrController@delivery_boy_map');
		
		Route::any('refresh_delboy_notification','DeliveryManager\DelMgrController@refresh_delboy_notification');
		
	});
	
	// Route::get('/admin', function () { /* action */ })->middleware("requirerole:admin");	
	Route::get('payment-status',array('as'=>'payment.status','uses'=>'PaymayaController@paymentInfo'));

	Route::any('orders-auto-allocation', 'DeliveryManager\OrderMgmtController@auto_allocation'); // RUN EVERY 15 MINS
	Route::any('orders-auto-reject', 'DeliveryManager\OrderMgmtController@auto_reject'); 		 // RUN EVERY 5 MINS
	
	/* for testing */
	Route::any('store_insert/{count}','TestController@add_store_merchant');
	Route::any('restaurant_insert/{count}','TestController@add_merchant_restaurant');
	Route::any('restaurant_insert1/{count}','TestController@add_merchant_restaurant1');
	Route::any('delete_duplicate','TestController@delete_duplicate');
	Route::any('add_store_category/{count}','TestController@add_store_category');
	Route::any('add_restaurant_category/{count}','TestController@add_restaurant_category');
	Route::get('get-ip-details', function () {
	$ip = '106.51.49.53';
    $data = \Location::get($ip);
    dd($data);
});
