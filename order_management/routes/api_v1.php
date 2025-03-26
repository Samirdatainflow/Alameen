<?php
Route::post('login', 'API\v1\LoginController@authenticate');
Route::post('dashboard-data', 'API\v1\DashboardController@dashboard_data');
Route::get('car-manufacture-list', 'API\v1\CommonController@car_manufacture_list');
Route::get('car-model-list', 'API\v1\CommonController@car_model_list');
Route::get('category-list', 'API\v1\CommonController@category_list');
Route::post('sub-category-list', 'API\v1\CommonController@sub_category_list');
Route::get('part-name-list', 'API\v1\CommonController@part_name_list');
Route::get('part-brand-list', 'API\v1\CommonController@part_brand_list');
Route::post('item-list', 'API\v1\ItemListController@item_list');
Route::post('item-search', 'API\v1\API_ItemSearchController@item_search');
Route::post('order-list', 'API\v1\OrderController@order_list');
Route::post('order-approved-list', 'API\v1\OrderController@order_approved_list');
Route::post('order-reject-list', 'API\v1\OrderController@order_reject_list');
Route::post('order-deliveries-list', 'API\v1\OrderController@order_deliveries_list');
Route::post('pending-shipment-list', 'API\v1\OrderController@pending_shipment_list');
Route::post('pending-shipment-details', 'API\v1\OrderController@pending_shipment_details');
Route::post('view-order-details', 'API\v1\OrderController@view_order_details');
Route::post('remove-order-item', 'API\v1\OrderController@remove_order_item');
Route::post('view-reason', 'API\v1\OrderController@view_reason');
Route::post('part-no-list-by-search', 'API\v1\OrderController@get_product_by_part_no');
Route::post('product-details-by-part-no', 'API\v1\OrderController@product_details_by_part_no');
Route::post('submit-order', 'API\v1\OrderController@submit_order');
Route::post('cart-item-details', 'API\v1\CartController@cart_item_details');
Route::post('submit-cart-order', 'API\v1\CartController@submit_cart_order');
Route::group(['middleware' => ['jwt.verify']], function() {
   
    
});
