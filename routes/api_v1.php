<?php
Route::post('login', 'API\v1\LoginController@authenticate');
Route::post('user-login', 'API\v1\LoginController@user_login');
Route::post('power-user-login', 'API\v1\LoginController@power_user_login');
Route::post('register', 'API\v1\LoginController@register');
Route::group(['middleware' => ['jwt.verify']], function() {
    /* ====================
        Company Controller
    ==================== */
    Route::get('user', 'API\v1\CompanyController@user_details');

    /* ====================
        User Controller
    ==================== */
    Route::post('user-role-list', 'API\v1\UserController@user_role_list');
    Route::post('list-designation', 'API\v1\UserController@list_designation');
    Route::post('user-profile', 'API\v1\UserController@user_profile');
    Route::post('user-profile-update', 'API\v1\UserController@user_profile_update');
    Route::post('user-old-password-check', 'API\v1\UserController@user_old_password_check');
    Route::post('user-password-update', 'API\v1\UserController@user_password_update');
    Route::post('user-profile-pic-update', 'API\v1\UserController@user_profile_pic_update');
    Route::post('user-states', 'API\v1\UserController@list_user_state');
    Route::post('user-city', 'API\v1\UserController@list_user_city');

    /* ====================
        Distributor Controller
    ==================== */
    Route::post('distributor/list-distributor', 'API\v1\DistributorController@list_distributor');
    Route::post('distributor/distributor-profile-view', 'API\v1\DistributorController@distributor_profile_view');
    Route::post('distributor/distributor-feedback-pic-save', 'API\v1\DistributorController@distributor_feedback_pic_save');
    Route::post('distributor/save-distributor-feedback', 'API\v1\DistributorController@save_distributor_feedback');
    Route::post('distributor/save-schedule-visit', 'API\v1\DistributorController@save_schedule_visit');
    Route::post('distributor/get-schedule-visit', 'API\v1\DistributorController@get_schedule_visit');
    Route::post('distributor/distributor-schedule-visit-details', 'API\v1\DistributorController@distributor_schedule_visit_details');
    Route::post('distributor/distributor-schedule-details-by-id', 'API\v1\DistributorController@distributor_schedule_details_by_id');
    Route::post('distributor/update-schedule-visit', 'API\v1\DistributorController@update_schedule_visit');
    Route::post('distributor/delete-schedule-visit', 'API\v1\DistributorController@delete_schedule_visit');
    Route::post('distributor/save-take-note', 'API\v1\DistributorController@save_take_note');
    Route::post('distributor/get-take-note', 'API\v1\DistributorController@gate_take_note');
    Route::post('distributor/get-distributor-note-by-id', 'API\v1\DistributorController@distributor_note_by_id');
    Route::post('distributor/update-take-note', 'API\v1\DistributorController@update_take_note');
    Route::post('distributor/delete-distributor-note', 'API\v1\DistributorController@delete_distributor_note');
    Route::post('distributor/save-distributor-query', 'API\v1\DistributorController@save_distributor_query');

    /* ====================
        Retailer Controller
    ==================== */
    Route::post('create-retailer', 'API\v1\RetailerController@create_retailer');
    Route::post('retailers/retailer-sales-manager', 'API\v1\RetailerController@retailer_sales_manager');
    Route::post('retailers/retailer-territory-manager', 'API\v1\RetailerController@retailer_territory_manager');
    Route::post('retailers/retailer-profile-pic-update', 'API\v1\RetailerController@retailer_profile_pic_update');
    Route::post('retailers/retailer-profile-pic-save', 'API\v1\RetailerController@retailer_profile_pic_save');
    Route::post('retailers/check-retailer-mobile-exist', 'API\v1\RetailerController@check_retailer_mobile_exist');
    Route::post('retailers/retailer-profile-view', 'API\v1\RetailerController@retailer_profile_view');
    //Route::post('retailers/retailer-profile-view-by-id', 'API\v1\RetailerController@retailer_profile_view_by_id');
    Route::post('retailers/save-retailer-note', 'API\v1\RetailerController@save_retailer_note');
    Route::post('retailers/get-retailer-note', 'API\v1\RetailerController@get_retailer_note');
    Route::post('retailers/get-retailer-note-by-id', 'API\v1\RetailerController@get_retailer_note_by_id');
    Route::post('retailers/save-retailer-note-by-id', 'API\v1\RetailerController@save_retailer_note_by_id');
    Route::post('retailers/delete-retailer-note', 'API\v1\RetailerController@delete_retailer_note');
    Route::post('retailers/list-retailer-feedback-purpose', 'API\v1\RetailerController@list_retailer_feedback_purpose');
    Route::post('retailers/retailer-feedback-pic-save', 'API\v1\RetailerController@retailer_feedback_pic_save');
    Route::post('retailers/save-retailer-feedback', 'API\v1\RetailerController@save_retailer_feedback');
    Route::post('retailers/list-task-type', 'API\v1\RetailerController@list_task_type');
    Route::post('retailers/save-schedule-visit', 'API\v1\RetailerController@save_schedule_visit');
    Route::post('retailers/update-schedule-visit', 'API\v1\RetailerController@update_schedule_visit');
    Route::post('retailers/delete-schedule-visit', 'API\v1\RetailerController@delete_schedule_visit');
    Route::post('retailers/list-retailer-stage', 'API\v1\RetailerController@list_retailer_stage');
    Route::post('retailers/save-retailer-stage', 'API\v1\RetailerController@save_retailer_stage');
    Route::post('retailers/get-retailer-schedule-visit', 'API\v1\RetailerController@get_retailer_schedule_visit');
    Route::post('retailers/get-retailer-schedule-visit-details', 'API\v1\RetailerController@get_retailer_schedule_visit_details');
    Route::post('retailers/edit-retailer-schedule-visit-details', 'API\v1\RetailerController@edit_retailer_schedule_visit_details');
    Route::post('retailers/get-retailer-today-schedule', 'API\v1\RetailerController@get_retailer_today_schedule');
    Route::post('retailers/get-geolocation', 'API\v1\RetailerController@get_geolocation');
    Route::post('retailers/get-history', 'API\v1\RetailerController@get_history');
    Route::post('retailers/get-retailer-more-details', 'API\v1\RetailerController@get_retailer_more_details');
    Route::post('retailers/list-order-history', 'API\v1\RetailerController@list_order_history');

    /* ====================
        Retailer Feedback Controller
    ==================== */
    Route::post('retailers/get-retailer-feedback', 'API\v1\RetailerFeedbackController@get_retailer_feedback');
    Route::post('retailers/delete-retailer-feedback', 'API\v1\RetailerFeedbackController@delete_retailer_feedback');
    Route::post('retailers/get-retailer-feedback-details', 'API\v1\RetailerFeedbackController@get_retailer_feedback_details');
    Route::post('retailers/update-retailer-feedback', 'API\v1\RetailerFeedbackController@update_retailer_feedback');
    /* ====================
        Single Retailer Controller
    ==================== */
    //Route::post('create-retailer', 'API\v1\SingleRetailerCtrl@create_retailer');
    Route::post('retailers/retailer-profile-view-by-id', 'API\v1\SingleRetailerCtrl@retailer_profile_view_by_id');
    Route::post('retailers/get-single-retailer-schedule-visit', 'API\v1\SingleRetailerCtrl@get_retailer_schedule_visit');
    Route::post('retailers/retailer-single-profile-view', 'API\v1\SingleRetailerCtrl@retailer_profile_view_by_id');
    Route::post('retailers/save-single-schedule-visit', 'API\v1\SingleRetailerCtrl@save_schedule_visit');
    Route::post('retailers/get-single-retailer-note', 'API\v1\SingleRetailerCtrl@get_retailer_note');
    Route::post('retailers/save-single-retailer-note', 'API\v1\SingleRetailerCtrl@save_retailer_note');
    Route::post('retailers/get-single-retailer-feedback', 'API\v1\SingleRetailerCtrl@get_retailer_feedback');
    Route::post('retailers/list-single-retailer-feedback-purpose', 'API\v1\SingleRetailerCtrl@list_retailer_feedback_purpose');
    Route::post('retailers/list-customer-product-sku', 'API\v1\SingleRetailerCtrl@list_product_sku');
    Route::post('retailers/save-single-retailer-feedback', 'API\v1\SingleRetailerCtrl@save_retailer_feedback');
    Route::post('retailers/retailer-password-update', 'API\v1\SingleRetailerCtrl@retailer_password_update');
    Route::post('retailers/retailer-profile-details', 'API\v1\SingleRetailerCtrl@retailer_profile_details');
    Route::post('retailers/retailer-profile-update', 'API\v1\SingleRetailerCtrl@retailer_profile_update');
    Route::post('retailers/retailer-profile-pic-update', 'API\v1\SingleRetailerCtrl@retailer_profile_pic_update');
    Route::post('retailers/retailer-call-option-details', 'API\v1\SingleRetailerCtrl@retailer_call_option_details');

    /* ====================
        Retailer Attributes Controller
    ==================== */
    Route::post('retailers/retailers-type', 'API\v1\RetailerAttributesController@list_retailers_type');
    Route::post('retailers/retailers-subtype', 'API\v1\RetailerAttributesController@list_retailers_subtype');
    Route::post('retailers/retailers-classification', 'API\v1\RetailerAttributesController@list_retailers_classification');
    Route::post('retailers/retailers-labels', 'API\v1\RetailerAttributesController@list_retailers_labels');
    Route::post('retailers/retailers-chain', 'API\v1\RetailerAttributesController@list_retailers_chain');
    Route::post('retailers/retailers-stage', 'API\v1\RetailerAttributesController@list_retailers_stage');
    Route::post('retailers/list-retailers-attributes', 'API\v1\RetailerAttributesController@list_retailers_attributes');

    /* ====================
        Routes Controller
    ==================== */
    Route::post('list-routes', 'API\v1\RoutesController@list_routes');
    Route::post('routes-retailers-list', 'API\v1\RoutesController@routes_retailers');
    Route::post('filter-routes-retailers', 'API\v1\RoutesController@filter_routes_retailers');

    /* ====================
        Product Controller
    ==================== */
    Route::post('product/list-product-sku', 'API\v1\ProductController@list_product_sku');
    Route::post('product/list-products', 'API\v1\ProductController@list_products');
    Route::post('product/list-products-filter-by-name', 'API\v1\ProductController@list_products_filter_by_name');
    Route::post('product/add-order', 'API\v1\ProductController@add_order');
    Route::post('product/remove-order', 'API\v1\ProductController@remove_order');
    Route::post('retailers/list-orders', 'API\v1\ProductController@retailer_list_orders');
    Route::post('retailers/list-orders-filter-by-name', 'API\v1\ProductController@retailer_list_orders_filter_by_name');
    Route::post('retailers/list-orders-4-update-stock', 'API\v1\ProductController@list_orders_4_retailer_update_stock');
    Route::post('retailers/filter-orders-4-update-stock', 'API\v1\ProductController@filter_orders_4_update_stock');
    Route::post('retailers/list-single-retailer-stock', 'API\v1\ProductController@list_single_retailer_stock');
    Route::post('retailers/single-retailer-stock-filter', 'API\v1\ProductController@single_retailer_stock_filter');
    Route::post('retailer/add-product-sales-order', 'API\v1\ProductController@add_product_sales_order');
    Route::post('retailer/add-product-single-retailer-sales-order', 'API\v1\ProductController@add_product_single_retailer_sales_order');
    Route::post('retailer/add-stock-return', 'API\v1\ProductController@add_stock_return');
    Route::post('retailer/update-stock', 'API\v1\ProductController@update_stock');
    Route::post('retailer/single-retailer-update-stock', 'API\v1\ProductController@single_retailer_update_stock');
    Route::post('retailer/list-stock', 'API\v1\ProductController@list_stock');
    Route::post('retailer/list-single-retailer-stock-details', 'API\v1\ProductController@list_single_retailer_stock_details');
    Route::post('retailer/single-retailer-list-products', 'API\v1\ProductController@single_retailer_list_products');
    Route::post('retailer/single-retailer-add-order', 'API\v1\ProductController@single_retailer_add_order');
    Route::post('retailer/add-single-retailer-stock-return', 'API\v1\ProductController@add_single_retailer_stock_return');

    /* ====================
        Attendance Controller
    ==================== */
    Route::post('save-check-in-check-out', 'API\v1\AttendanceController@save_checkin_checkout');
    Route::post('check-in-check-out-position', 'API\v1\AttendanceController@checkin_checkout_position');
    Route::post('list-check-in-check-out-data', 'API\v1\AttendanceController@list_checkin_checkout');

    /* ====================
        Enquiry Controller
    ==================== */
    Route::post('save-enquiry', 'API\v1\EnquiryController@save_enquery');
    Route::post('retailer/single-retailer-save-enquiry', 'API\v1\EnquiryController@single_retailer_save_enquiry');

    /* ====================
        Power User Controller
    ==================== */
    Route::post('poweruser/poweruser-profile-details', 'API\v1\PoweruserController@poweruser_profile_details');
    Route::post('poweruser/poweruser-profile-update', 'API\v1\PoweruserController@poweruser_profile_update');
    Route::post('poweruser/poweruser-password-update', 'API\v1\PoweruserController@poweruser_password_update');
    Route::post('poweruser/poweruser-profile-pic-update', 'API\v1\PoweruserController@poweruser_profile_pic_update');

    /* ====================
        Others Controller (app others work include here)
    ==================== */
    Route::post('store-app-fcm-token', 'API\v1\OthersController@store_app_fcm_token');
    
});
/* ====================
    Retailer Login Controller
==================== */
Route::post('retailers/retailers-login-match', 'API\v1\RetailerLoginController@login_match');
/* ====================
    SuperAdmin API
==================== */
Route::post('superadmin-login', 'API\v1\SuperAdminController@superadmin_login');
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('company-management-list', 'API\v1\SuperAdminController@company_management_list');
});