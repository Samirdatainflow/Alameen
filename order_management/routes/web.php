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
/* ====================
    Login Section
==================== */
Route::get('/', 'LoginController@index');
Route::post('login-match', 'LoginController@login_match');
Route::get('create-order', 'OtherController@create_order');
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});
/* ====================
    Middleware
==================== */
Route::group(['middleware' => ['activeuser']], function () {
    /* ====================
        Dashboard Controller
    ==================== */
    Route::get('dashboard', 'DashboardController@index');
    Route::get('logout', 'DashboardController@logout');

    /* ====================
        Common Controller
    ==================== */
    Route::post('get-model-by-model-name', 'CommonController@get_model_by_model_name');
    Route::post('get-car-model-by-car-manufacture', 'CommonController@get_car_model_by_car_manufacture');
    
    /* ====================
        Item
    ==================== */
    Route::get('item', 'ItemController@item');
    Route::post('get-item', 'ItemController@get_item');
    /* ====================
        Item List
    ==================== */
    Route::get('item-list', 'ItemListController@item_list');
    Route::post('get-item-list', 'ItemListController@get_item_list');
    Route::get('add-product-form', 'ItemListController@add_product_form');
    Route::post('export-item-csv', 'ItemListController@export_item_csv');
    Route::post('add-to-cart','ItemListController@add_to_cart');
    Route::post('get-category','ItemListController@get_category');
    Route::post('get-sub-category','ItemListController@get_sub_category');
    Route::post('get-oem-no-list','ItemListController@get_oem_no_list');
    /* ====================
        Order
    ==================== */
    Route::get('item-search', 'ItemSearchController@item_search');
    Route::post('get-category-id', 'ItemSearchController@get_category_id');
    Route::post('get-subcategory-id', 'ItemSearchController@get_subcategory_id');
    Route::post('get-oem-no', 'ItemSearchController@get_oem_no');
    Route::post('get-items', 'ItemSearchController@get_search_items');
    Route::post('item-search/filter-item-search', 'ItemSearchController@filter_item_search');
    /* ====================
        Order
    ==================== */
    Route::get('order', 'OrderController@order');
    Route::get('new-order', 'OrderController@new_order');
    Route::post('get-order', 'OrderController@get_order');
    Route::post('create-order', 'OrderController@create_order');
    Route::post('product-details', 'OrderController@product_details');
    Route::post('get-sale-order-details', 'OrderController@get_sale_order_details');
    Route::post('create-multiple-order', 'OrderController@create_multiple_order');
    Route::post('order-preview', 'OrderController@order_preview');
    Route::post('remove-order-item', 'OrderController@remove_order_item');
    Route::post('reject-sale-order', 'OrderController@reject_sale_order');
    Route::post('get-product-by-part-no', 'OrderController@get_product_by_part_no');
    Route::post('order/view-reason', 'OrderController@view_reason');
    /* ====================
        Cart
    ==================== */
    Route::get('cart', 'CartController@cart_item');
    Route::post('update-cart', 'CartController@update_cart');
    Route::post('delete-cart-item', 'CartController@delete_cart_item');
    /* ====================
        Profile
    ==================== */
    Route::get('profile', 'ProfileController@profile');
    Route::post('update-profile', 'ProfileController@update_profile');
    Route::get('change-pass', 'ProfileController@change_pass');
    Route::post('check-current-password', 'ProfileController@check_current_password');
    Route::post('update-password', 'ProfileController@update_password');
    /* ====================
        Clients
    ==================== */
    Route::get('clients', 'ClientsController@clients');
    Route::get('add-client', 'ClientsController@add_client');
    Route::get('recover-client', 'ClientsController@recover_client');
    /* ====================
        Settings
    ==================== */
    Route::get('settings', 'SettingsController@settings');
});