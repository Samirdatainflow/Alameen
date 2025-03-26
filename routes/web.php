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

/* ====================
    Middleware
==================== */
Route::group(['middleware' => ['activeuser','preventBackHistory']], function () {
    /* ====================
        Dashboard Controller
    ==================== */
    Route::get('dashboard', 'DashboardController@index');
    Route::get('logout', 'DashboardController@logout');
    /* ====================
        Profile Controller
    ==================== */
    Route::get('profile', 'ProfileController@index');
    Route::post('profile-save', 'ProfileController@profile_save');

    /* ====================
        Common Controller
    ==================== */
    Route::post('get-model-by-model-name', 'CommonController@get_model_by_model_name');
    Route::post('get-part-brand-for-search-box', 'CommonController@get_part_brand_for_search_box');
    Route::post('get-part-name-for-search-box', 'CommonController@get_part_name_for_search_box');
    Route::post('get-car-manufacture-for-search-box', 'CommonController@get_car_manufacture_for_search_box');
    Route::post('get-car-name-for-search-box', 'CommonController@get_car_name_for_search_box');
    Route::post('get-product-list-for-search-box', 'CommonController@get_product_list_for_search_box');
    Route::post('get-part-no-by-search', 'CommonController@get_part_no_by_search');
    Route::post('get-car-model-by-car-manufacture', 'CommonController@get_car_model_by_car_manufacture');
    Route::post('get-count-of-cart-product', 'CommonController@get_count_of_cart_product');
    Route::post('get-zone-by-location', 'CommonController@get_zone_by_location');
    Route::post('get-row-by-zone', 'CommonController@get_row_by_zone');
    Route::post('get-rack-by-row', 'CommonController@get_rack_by_row');
    Route::post('get-plate-by-rack', 'CommonController@get_plate_by_rack');
    Route::post('get-place-by-plate', 'CommonController@get_place_by_plate');
    Route::post('common-url/save-part-brand', 'CommonController@save_part_brand');
    Route::post('common-url/save-item-group', 'CommonController@save_item_group');
    Route::post('common-url/save-item-brand', 'CommonController@save_item_brand');
    Route::post('common-url/save-config-unit', 'CommonController@save_config_unit');
    Route::post('common-url/save-config-countries', 'CommonController@save_config_countries');
    Route::post('common-url/save-config-currency', 'CommonController@save_config_currency');
    Route::post('common-url/save-item-sub-category', 'CommonController@save_item_sub_category');
    Route::post('get-capacity-by-place', 'CommonController@get_capacity_by_place');

    /* =======================
        Warehouse Management
    ======================== */
    Route::get('warehouse-export', 'WarehouseManagementController@warehouse_export');
    Route::get('all-warehouse', 'WarehouseManagementController@all_warehouse');
    Route::get('warehouse-form', 'WarehouseManagementController@warehouse_form');
    Route::post('save-warehouse', 'WarehouseManagementController@save_warehouse');
    Route::post('list-warehouse', 'WarehouseManagementController@list_warehouse');
    Route::post('edit-warehouse', 'WarehouseManagementController@edit_warehouse');
    Route::post('delete-warehouse', 'WarehouseManagementController@delete_warehouse');
    Route::post('select-warehouse', 'WarehouseManagementController@select_warehouse');
    Route::post('warehouse/warehouse-preview', 'WarehouseManagementController@warehouse_preview');
    Route::post('warehouse/save-warehouse-bulk-csv', 'WarehouseManagementController@save_warehouse_bulk_csv');
    /* ====================
        User Management
    ===================== */
    Route::get('all-user','UserManagementController@index');
    Route::get('users/user-export','UserManagementController@user_export');
    Route::get('user-form','UserManagementController@user_form');
    Route::post('save-user','UserManagementController@save_user');
    Route::post('get-user','UserManagementController@get_user');
    Route::post('change-user-status','UserManagementController@change_user_status');
    Route::post('users/users-preview','UserManagementController@users_preview');
    Route::post('users/save-users-bulk-csv','UserManagementController@save_users_bulk_csv');
    // --Inactive User--
    Route::get('all-inactive-user','InactiveUserController@all_inactive_user');
    Route::get('all-inactive-user-export','InactiveUserController@all_inactive_user_export');
    Route::post('get-inactive-user','InactiveUserController@get_inactive_user');
    // Route::post('change-user-status','InactiveUserController@change_user_status');
    Route::post('edit-user','InactiveUserController@edit_user');
    // --User Role--
    Route::get('user-role','UserRoleController@user_role');
    Route::get('user-role-export','UserRoleController@user_role_export');
    Route::post('list-user-role','UserRoleController@list_user_role');
    Route::get('add-user-role','UserRoleController@add_user_role');
    Route::post('save-user-role','UserRoleController@save_user_role');
    Route::post('edit-user-role','UserRoleController@edit_user_role');
    Route::post('delete-user-role','UserRoleController@delete_user_role');
    Route::post('change-user-role-status','UserRoleController@change_user_role_status');
    // --user-access--
    Route::get('role-access','UserRoleAccessController@role_access');
    Route::get('role-access-export','UserRoleAccessController@role_access_export');
    Route::post('add-role-access','UserRoleAccessController@add_role_access');
    Route::post('list-role-access','UserRoleAccessController@list_role_access');
    Route::post('view-user-role-access','UserRoleAccessController@view_user_role_access');
    Route::post('save-user-role-access','UserRoleAccessController@save_user_role_access');
    // Phase -2 //
    /* ====================
        Location Management
    ==================== */
    Route::get('location-management', 'LocationManagementController@location_management');
    Route::post('list-location', 'LocationManagementController@list_location');
    Route::get('location-form', 'LocationManagementController@location_form');
    Route::post('save-location', 'LocationManagementController@save_location');
    Route::post('edit-location', 'LocationManagementController@edit_location');
    Route::post('delete-location', 'LocationManagementController@delete_location');
    Route::get('location-export', 'LocationManagementController@location_export');
    /* ====================
        Inventory Management
    ==================== */
    Route::get('inventory-management', 'InventoryManagementController@inventory_management');
    Route::post('list-inventory-management', 'InventoryManagementController@list_inventory_management');
    Route::get('inventory-customer-form', 'InventoryManagementController@inventory_customer_form');
    Route::get('inventory/quantity-on-hand-form-open', 'InventoryManagementController@quantity_on_hand_form_open');
    Route::post('inventory/save-quantity-on-hand-form-open', 'InventoryManagementController@save_quantity_on_hand_form_open');
    Route::get('inventory/view-binning-location', 'InventoryManagementController@view_binning_location');
    Route::post('inventory/product-auto-fill-binning-location', 'InventoryManagementController@product_auto_fill_binning_location');
    Route::get('inventory/auto-fill-binning-location-cronjob', 'InventoryManagementController@auto_fill_binning_location_cronjob');
    Route::post('inventory/check-location', 'InventoryManagementController@check_location');
    /* ====================
        Storage Management
    ==================== */
    Route::get('storage-management', 'StorageManagementController@storage_management');
    /* ====================
        Supplier Management
    ==================== */
    Route::get('supplier-management', 'SupplierManagementController@supplier_management');
    Route::get('supplier-export', 'SupplierManagementController@supplier_export');
    Route::get('add-supplier', 'SupplierManagementController@add_supplier');
    Route::post('save-supplier', 'SupplierManagementController@save_supplier');
    Route::post('list-supplier', 'SupplierManagementController@list_supplier');
    Route::post('delete-supplier', 'SupplierManagementController@delete_supplier');
    Route::post('edit-supplier', 'SupplierManagementController@edit_supplier');
    Route::post('change-supplier-status', 'SupplierManagementController@change_supplier_status');
    Route::post('supplier/supplier-bulk-preview', 'SupplierManagementController@supplier_bulk_preview');
    Route::post('supplier/save-supplier-bulk-csv', 'SupplierManagementController@save_supplier_bulk_csv');
    /* ====================
        Customer Management
    ==================== */
    Route::get('customer-management', 'CustomerManagementController@customer_management');
    Route::get('add-customer-management', 'CustomerManagementController@add_customer_management');
    Route::post('save-customer-management', 'CustomerManagementController@save_customer_management');
    Route::post('list-customer-management', 'CustomerManagementController@list_customer_management');
    Route::post('edit-customer-management', 'CustomerManagementController@edit_customer_management');
    Route::post('delete-customer-management', 'CustomerManagementController@delete_customer_management');
    Route::post('customer/customer-bulk-preview', 'CustomerManagementController@customer_bulk_preview');
    Route::post('customer/save-customer-bulk-csv', 'CustomerManagementController@save_customer_bulk_csv');
    Route::get('customer-management-export', 'CustomerManagementController@customer_management_export');
    Route::post('view-customer-documents', 'CustomerManagementController@viewCustomerDocuments');
     /* ====================
        Order Management
    ==================== */
    Route::get('sale-order-management', 'SaleOrderManagementController@sale_order_management');
    Route::get('sale-order-management-export', 'SaleOrderManagementController@sale_order_management_export');
    Route::post('get-sale-order', 'SaleOrderManagementController@get_sale_order');
    Route::post('get-sale-order-details', 'SaleOrderManagementController@get_sale_order_details');
    Route::post('get-sale-order-details-for-picking-slip', 'SaleOrderManagementController@get_sale_order_details_for_picking_slip');
    Route::get('sale-order/print-order-details', 'SaleOrderManagementController@print_order_details');
    Route::post('get-approve-sale-order-details', 'SaleOrderManagementController@get_approve_sale_order_details');
    
    Route::post('get-no-stock-sale-order-details', 'SaleOrderManagementController@get_no_stock_sale_order_details');
    Route::post('get-sale-order-details-for-approve', 'SaleOrderManagementController@get_sale_order_details_for_approve');
    Route::get('approve-sale-order-management', 'SaleOrderManagementController@approve_sale_order_management');
    Route::post('get-approve-sale-order', 'SaleOrderManagementController@get_approve_sale_order');
    
    Route::post('approve-order', 'SaleOrderManagementController@approve_order');
    Route::post('reject-sale-order', 'SaleOrderManagementController@reject_sale_order');
    Route::post('sale_order/view-order-reject-form', 'SaleOrderManagementController@view_order_reject_form');
    Route::post('sale-order/delete-sale-order', 'SaleOrderManagementController@delete_sale_order');
    Route::post('sale-order/delete-sale-order-details', 'SaleOrderManagementController@delete_sale_order_details');
    
    Route::post('sale-order/delete-approve-sale-order-details', 'SaleOrderManagementController@delete_approve_sale_order_details');
    
    Route::get('sales-order/sales-order-edit', 'SaleOrderManagementController@sales_order_edit');
    Route::post('sale-order/get-product-by-part-no', 'SaleOrderManagementController@get_product_by_part_no');
    Route::get('sale-order/download-invoice', 'SaleOrderManagementController@download_invoice');
    Route::get('sale-order/download-customer-invoice', 'SaleOrderManagementController@download_customer_invoice');
    Route::get('picking-slip', 'SaleOrderManagementController@picking_slip');
    Route::get('picking-slip-export', 'SaleOrderManagementController@picking_slip_export');
    Route::post('get-picking-order', 'SaleOrderManagementController@get_picking_order');
    Route::post('picking-approve', 'SaleOrderManagementController@picking_approve');
    Route::post('before-picking-approve', 'SaleOrderManagementController@before_picking_approve');
    Route::get('sale-order/print-picking-slip', 'SaleOrderManagementController@print_picking_slip');
    Route::get('sale-order/print-customer-picking-slip', 'SaleOrderManagementController@print_customer_picking_slip');
    Route::post('sale-order/reset-print', 'SaleOrderManagementController@reset_print');
    Route::post('sale-order/approve-packing-slip', 'SaleOrderManagementController@approve_packing_slip');
    Route::get('no-stock-order', 'SaleOrderManagementController@no_stock_order');
    Route::get('no-stock-order-export', 'SaleOrderManagementController@no_stock_order_export');
    Route::get('loss-of-sales-report-export', 'SaleOrderManagementController@loss_of_sales_report_export');
    Route::post('get-no-stock-order', 'SaleOrderManagementController@get_no_stock_order');
    Route::get('add-new-order', 'SaleOrderManagementController@add_new_order');
    Route::get('edit-new-order', 'SaleOrderManagementController@edit_new_order');
    Route::post('get-product-by-part-no-order', 'SaleOrderManagementController@get_product_by_part_no_order');
    Route::post('product-details', 'SaleOrderManagementController@product_details');
    Route::post('order-preview', 'SaleOrderManagementController@order_preview');
    Route::post('create-order', 'SaleOrderManagementController@create_order');
    Route::post('create-multiple-order', 'SaleOrderManagementController@create_multiple_order');
    Route::get('save-order', 'SaleOrderManagementController@save_order');
    Route::get('save-order-export', 'SaleOrderManagementController@save_order_export');
    Route::post('save-order-list', 'SaleOrderManagementController@save_order_list');
    Route::post('sale-order/create-no-stock-order', 'SaleOrderManagementController@create_no_stock_order');
    Route::post('sale-order/order-quantity-update-form', 'SaleOrderManagementController@order_quantity_update_form');
    
    Route::post('sale-order/approve-quantity-update-form', 'SaleOrderManagementController@approve_quantity_update_form');
    
    Route::post('sale-order/update-order-quantity', 'SaleOrderManagementController@update_order_quantity');
    
    Route::post('sale-order/update-approved-order-quantity', 'SaleOrderManagementController@update_approved_order_quantity');
    
    Route::post('sale-order/order-price-update-form', 'SaleOrderManagementController@order_price_update_form');
    Route::post('sale-order/update-order-price', 'SaleOrderManagementController@update_order_price');
    
    Route::get('print-merged-invoice', 'SaleOrderManagementController@print_merged_invoice');
    Route::post('print-merged-invoice-list', 'SaleOrderManagementController@print_merged_invoice_list');
    Route::get('sale-order/download-merged-invoice', 'SaleOrderManagementController@download_merged_invoice');
    Route::get('sale-order/print-merged-picking-slip', 'SaleOrderManagementController@print_merged_picking_slip');
    
    Route::get('sale-order-outstanding', 'SaleOrderManagementController@sale_order_outstanding');
    Route::post('list-sale-order-outstanding', 'SaleOrderManagementController@list_sale_order_outstanding');
    Route::get('add-outstanding-payment', 'SaleOrderManagementController@add_outstanding_payment');
    Route::post('save-outstanding-payment', 'SaleOrderManagementController@save_outstanding_payment');
    Route::post('get-customer-invoice-details', 'SaleOrderManagementController@get_customer_invoice_details');
    Route::get('sale-order-outstanding-export', 'SaleOrderManagementController@sale_order_outstanding_export');
    
    Route::get('sale-order-partial-outstanding', 'SaleOrderManagementController@sale_order_partial_outstanding');
    Route::post('list-sale-order-partial-outstanding', 'SaleOrderManagementController@list_sale_order_partial_outstanding');
    Route::get('add-partial-outstanding-payment', 'SaleOrderManagementController@add_partial_outstanding_payment');
    Route::post('get-customer-partial-details', 'SaleOrderManagementController@get_customer_partial_details');
    Route::post('save-partial-outstanding-payment', 'SaleOrderManagementController@save_partial_outstanding_payment');
    Route::get('sale-order-partial-outstanding-export', 'SaleOrderManagementController@sale_order_partial_outstanding_export');
    
    Route::get('sale-order-receipt', 'SaleOrderManagementController@sale_order_receipt');
    Route::post('list-sale-order-receipt', 'SaleOrderManagementController@list_sale_order_receipt');
    Route::get('add-sale-order-receipt', 'SaleOrderManagementController@add_sale_order_receipt_payment');
    Route::post('get-customer-receipt-details', 'SaleOrderManagementController@get_customer_receipt_details');
    Route::post('save-sale-order-receipt-payment', 'SaleOrderManagementController@save_sale_order_receipt_payment');
    Route::get('sale-order-receipt-export', 'SaleOrderManagementController@sale_order_receipt_export');
    Route::get('print-sale-order-receipt-slip', 'SaleOrderManagementController@print_sale_order_receipt_slip');
    
    Route::get('sale-order-sales-report', 'SaleOrderManagementController@sale_order_sales_report');
    Route::post('list-sale-order-sales-report', 'SaleOrderManagementController@list_sale_order_sales_report');

    /* ====================
        Purchase Order Management
    ==================== */
    Route::get('purchase-order-management', 'PurchaseOrderManagementController@purchase_order_management');
    Route::post('add-purchase-order-management', 'PurchaseOrderManagementController@add_purchase_order_management');
    Route::post('save-purchase-order-management', 'PurchaseOrderManagementController@save_purchase_order_management');
    Route::post('list-purchase-order-management', 'PurchaseOrderManagementController@list_purchase_order_management');
    Route::post('get-product-by-warehouse', 'PurchaseOrderManagementController@get_product_by_warehouse');
    Route::post('get-product-by-supplier', 'PurchaseOrderManagementController@get_product_by_supplier');
    Route::post('view-purchase-order-details', 'PurchaseOrderManagementController@view_purchase_order_details');
    Route::post('delete-purchase-order', 'PurchaseOrderManagementController@delete_purchase_order');
    Route::post('purchase_order/get-product-by-part-no', 'PurchaseOrderManagementController@get_product_by_part_no');
    Route::post('purchase_order/get-product-details', 'PurchaseOrderManagementController@get_product_details');
    Route::post('purchase_order/order-preview', 'PurchaseOrderManagementController@order_preview');
    Route::post('purchase_order/create-multiple-order', 'PurchaseOrderManagementController@create_multiple_order');
    Route::post('purchase-order/delete-order-details', 'PurchaseOrderManagementController@delete_order_details');
    Route::post('purchase-order/get-order-request-details', 'PurchaseOrderManagementController@get_order_request_details');
    Route::post('purchase-order/upload-invoice', 'PurchaseOrderManagementController@upload_invoice');
    Route::post('purchase-order/list-order-from-cart', 'PurchaseOrderManagementController@list_order_from_cart');
    Route::get('save-purchase-order', 'PurchaseOrderManagementController@save_purchase_order');
    Route::post('list-save-purchase-order-management', 'PurchaseOrderManagementController@list_save_purchase_order_management');
    Route::get('purchase-order-export', 'PurchaseOrderManagementController@purchase_order_export');
    Route::get('save-purchase-order-export', 'PurchaseOrderManagementController@save_purchase_order_export');
    Route::post('purchase-order-invoice', 'PurchaseOrderManagementController@purchase_order_invoice');
    Route::post('purchase-order-add-expenses', 'PurchaseOrderManagementController@purchase_order_add_expenses');
    Route::post('save-purchase-order-invoice', 'PurchaseOrderManagementController@save_purchase_order_invoice');
    Route::get('print-purchase-order-invoice', 'PurchaseOrderManagementController@print_purchase_order_invoice');
    Route::get('excess-purchase-order', 'PurchaseOrderManagementController@excess_purchase_order');
    Route::post('list-excess-purchase-order', 'PurchaseOrderManagementController@list_excess_purchase_order');
    Route::post('view-excess-purchase-order-details', 'PurchaseOrderManagementController@view_excess_purchase_order_details');
    Route::get('damage-purchase-order', 'PurchaseOrderManagementController@damage_purchase_order');
    Route::post('list-damage-purchase-order', 'PurchaseOrderManagementController@list_damage_purchase_order');
    Route::post('view-damage-purchase-order-details', 'PurchaseOrderManagementController@view_damage_purchase_order_details');
    Route::get('shortage-purchase-order', 'PurchaseOrderManagementController@shortage_purchase_order');
    Route::post('list-shortage-purchase-order', 'PurchaseOrderManagementController@list_shortage_purchase_order');
    Route::post('view-shortage-purchase-order-details', 'PurchaseOrderManagementController@view_shortage_purchase_order_details');
    
    Route::get('generate-barcode', 'PurchaseOrderManagementController@generate_barcode');
    
    Route::get('purchase-order-outstanding', 'PurchaseOrderManagementController@purchase_order_outstanding');
    Route::post('list-purchase-order-outstanding', 'PurchaseOrderManagementController@list_purchase_order_outstanding');
    Route::get('add-purchase-order-outstanding', 'PurchaseOrderManagementController@add_purchase_order_outstanding');
    Route::post('save-purchase-order-outstanding', 'PurchaseOrderManagementController@save_purchase_order_outstanding');
    Route::post('get-supplier-invoice-details', 'PurchaseOrderManagementController@get_supplier_invoice_details');
    Route::get('purchase-order-outstanding-export', 'PurchaseOrderManagementController@purchase_order_outstanding_export');
    
    Route::get('purchase-partial-outstanding', 'PurchaseOrderManagementController@purchase_partial_outstanding');
    Route::post('list-purchase-partial-outstanding', 'PurchaseOrderManagementController@list_purchase_partial_outstanding');
    Route::get('add-purchase-partial-outstanding', 'PurchaseOrderManagementController@add_purchase_partial_outstanding');
    Route::post('save-purchase-partial-outstanding', 'PurchaseOrderManagementController@save_purchase_partial_outstanding');
    Route::post('get-supplier-partial-details', 'PurchaseOrderManagementController@get_supplier_partial_details');
    Route::get('purchase-partial-outstanding-export', 'PurchaseOrderManagementController@purchase_partial_outstanding_export');
    
    Route::get('purchase-receipt', 'PurchaseOrderManagementController@purchase_receipt');
    Route::post('list-purchase-receipt', 'PurchaseOrderManagementController@list_purchase_receipt');
    Route::get('add-purchase-receipt', 'PurchaseOrderManagementController@add_purchase_receipt');
    Route::post('save-purchase-receipt', 'PurchaseOrderManagementController@save_purchase_receipt');
    Route::post('get-supplier-receipt-details', 'PurchaseOrderManagementController@get_supplier_receipt_details');
    Route::get('purchase-receipt-export', 'PurchaseOrderManagementController@purchase_receipt_export');
    
    Route::get('purchase-order-purchase-report', 'PurchaseOrderManagementController@purchase_order_purchase_report');
    Route::post('list-purchase-order-purchase-report', 'PurchaseOrderManagementController@list_purchase_order_purchase_report');
    
    /* ====================
        Order Request
    ==================== */
    Route::get('re-order-view', 'ReOrderController@re_order_view');
    Route::post('list-re-order', 'ReOrderController@list_re_order');
    
    /* ====================
        Order Request
    ==================== */
    Route::get('order-request', 'OrderRequestController@order_request');
    Route::post('order-request/list-order-request', 'OrderRequestController@list_order_request');
    Route::post('order-request/add-order-request', 'OrderRequestController@add_order_request');
    Route::post('order-request/get-product-by-part-no', 'OrderRequestController@get_product_by_part_no');
    Route::post('order-request/get-product-details', 'OrderRequestController@get_product_details');
    Route::post('order-request/save-order-request', 'OrderRequestController@save_order_request');
    Route::post('order-request/view-request-order-details', 'OrderRequestController@view_request_order_details');
    Route::post('order-request/delete-order-request-details', 'OrderRequestController@delete_order_request_details');
    Route::post('order-request/delete-request-order', 'OrderRequestController@delete_request_order');
    Route::get('order-request/pdf-request-order', 'OrderRequestController@pdf_request_order');
    Route::post('order-request/order-preview', 'OrderRequestController@order_preview');
    Route::post('order-request/save-quotation', 'OrderRequestController@save_quotation');
    Route::post('order-request/confirm-order-request', 'OrderRequestController@confirm_order_request');
    Route::post('order-request/upload-performa-invoice', 'OrderRequestController@upload_performa_invoice');
    Route::post('order-request/create-multiple-order', 'OrderRequestController@create_multiple_order');
    Route::post('order-request/pdf-generate', 'OrderRequestController@order_request_pdf_generate');
    Route::post('order-request/view-order-details-4-price', 'OrderRequestController@view_order_details_4_price');
    Route::post('order-request/save-quotation-prices', 'OrderRequestController@save_quotation_prices');
    Route::post('order-request/view-quotation-price', 'OrderRequestController@view_quotation_price');
    Route::post('order-request/compare-price', 'OrderRequestController@compare_price');
    Route::get('order-request/download-request-order', 'OrderRequestController@download_request_order');
    Route::get('save-order-request', 'OrderRequestController@save_order_request_page');
    Route::get('save-order-request-export', 'OrderRequestController@save_order_request_export');
    Route::post('order-request/list-save-order-request', 'OrderRequestController@list_save_order_request');
    Route::get('order-request-export', 'OrderRequestController@order_request_export');

    /* ====================
        Quotation Order
    ==================== */
    Route::get('quotation-order', 'QuotationOrderController@quotation_order');
    Route::post('quotation-order/list-quotation-order', 'QuotationOrderController@list_quotation_order');
    Route::post('quotation-order/add-quotation', 'QuotationOrderController@add_quotation');
    Route::post('quotation-order/get-order-request-by-id', 'QuotationOrderController@get_order_request_by_id');
    Route::post('quotation-order/save-quotation-order', 'QuotationOrderController@save_quotation_order');
    Route::post('quotation-order/delete-quotation-order', 'QuotationOrderController@delete_quotation_order');
    Route::post('quotation-order/view-quotation-order', 'QuotationOrderController@view_quotation_order');

    /* ====================
        Order Confirmation
    ==================== */
    Route::get('order-confirmation', 'OrderConfirmationController@order_confirmation');
    Route::post('order-confirmation/list-order-confirmation', 'OrderConfirmationController@list_quotation_order');
    Route::post('order-confirmation/add-order-confirmation', 'OrderConfirmationController@add_quotation');
    Route::post('order-confirmation/view-quotation-order-details', 'OrderConfirmationController@view_quotation_order_details');
    Route::post('order-confirmation/chnage-confirmation', 'OrderConfirmationController@chnage_confirmation');

    /* ====================
        Performa Invoice
    ==================== */
    Route::get('performa-invoice', 'PerformaInvoiceController@performa_invoice');
    Route::post('performa-invoice/list-performa-invoice', 'PerformaInvoiceController@list_performa_invoice');
    Route::post('performa-invoice/add-invoice', 'PerformaInvoiceController@add_invoice');
    Route::post('performa-invoice/save-invoice', 'PerformaInvoiceController@save_invoice');
    Route::post('performa-invoice/delete-invoice', 'PerformaInvoiceController@delete_invoice');
    Route::post('performa-invoice/view-invoice', 'PerformaInvoiceController@view_invoice');

    /* ====================
       Delivery Management
    ==================== */
    Route::get('delivery-management', 'DeliveryManagementController@index');
    Route::post('list-delivery-management', 'DeliveryManagementController@list_delivery_management');
    Route::post('delivery-management/get-shipping-details', 'DeliveryManagementController@get_shipping_details');
    Route::get('add-delivery-management', 'DeliveryManagementController@add_delivery_management');
    Route::post('save-delivery-management', 'DeliveryManagementController@save_delivery_management');
    Route::post('delivery-management/get-client-oders', 'DeliveryManagementController@get_client_oders');
    Route::post('edit-delivery-management', 'DeliveryManagementController@edit_delivery_management');
    Route::post('delete-delivery-management', 'DeliveryManagementController@delete_delivery_management');
    Route::get('print-delivery-management', 'DeliveryManagementController@print_delivery_management');
    Route::get('delivery-management-export', 'DeliveryManagementController@delivery_management_export');
    /* ====================
       Item Management
    ==================== */
    Route::get('item-management', 'ItemManagementController@item_management');
    Route::get('item-management-export', 'ItemManagementController@item_management_export');
    Route::get('add-item-management', 'ItemManagementController@add_item_management');
    Route::post('list-item-management', 'ItemManagementController@list_item_management');
    Route::post('save-item-management', 'ItemManagementController@save_item_management');
    Route::post('delete-item-management', 'ItemManagementController@delete_item_management');
    Route::post('edit-item-management', 'ItemManagementController@edit_item_management');
    Route::post('item-management/get-category-by-model', 'ItemManagementController@get_category_by_model');
    Route::post('item-management/get-subcategory-by-category', 'ItemManagementController@get_subcategory_by_category');
    Route::post('item-management/get-oem-no-by-sub-category', 'ItemManagementController@get_oem_no_by_sub_category');
    Route::post('item-management/remove-engine', 'ItemManagementController@remove_engine');
    Route::post('item-management/remove-chassis-model', 'ItemManagementController@remove_chassis_model');
    Route::post('item-management/remove-manufacturing-no', 'ItemManagementController@remove_manufacturing_no');
    Route::post('item-management/remove-alternate-no', 'ItemManagementController@remove_alternate_no');
    Route::post('item-management/view-item', 'ItemManagementController@view_item');
    Route::get('add-item-management-bulk-upload', 'ItemManagementController@item_management_bulk_upload');
    Route::post('item-management/item-management-bulk-preview', 'ItemManagementController@item_management_bulk_preview');
    Route::post('item-management/save-item-management-bulk', 'ItemManagementController@save_item_management_bulk');

    /* ====================
       Item Management
    ==================== */
    Route::get('item-search', 'ItemSearchController@index');
    Route::post('item-search/get-category-by-model', 'ItemSearchController@get_category_by_model');
    Route::post('item-search/get-subcategory-by-category', 'ItemSearchController@get_subcategory_by_category');
    Route::post('item-search/get-oem-no-by-sub-category', 'ItemSearchController@get_oem_no_by_sub_category');
    Route::post('item-search/get-item-details', 'ItemSearchController@get_search_item_details');
    Route::post('item-search/filter-item-search', 'ItemSearchController@filter_item_search');
    Route::post('item-search/add-to-cart', 'ItemSearchController@add_to_cart');

    // --Category--
    Route::get('category', 'ItemCategoryController@category');
    Route::get('add-category', 'ItemCategoryController@add_category');
    Route::post('save-item-category', 'ItemCategoryController@save_item_category');
    Route::post('list-category', 'ItemCategoryController@list_category');
    Route::post('delete-item-category', 'ItemCategoryController@delete_item_category');
    Route::post('edit-item-category', 'ItemCategoryController@edit_item_category');
    Route::post('get-model-name', 'ItemCategoryController@get_model_name');
    Route::post('category/category-bulk-preview', 'ItemCategoryController@category_bulk_preview');
    Route::post('category/save-category-bulk-csv', 'ItemCategoryController@save_category_bulk_csv');
    Route::get('category-export', 'ItemCategoryController@category_export');
    // --Car Model--
    Route::get('car-model', 'CarModelController@car_model');
    Route::post('list-car-model', 'CarModelController@list_car_model');
    Route::get('add-brand', 'CarModelController@add_brand');
    Route::post('save-item-brand', 'CarModelController@save_item_brand');
    Route::post('change-brand-status', 'CarModelController@change_brand_status');
    Route::post('edit-item-brand', 'CarModelController@edit_item_brand');
    Route::post('delete-item-brand', 'CarModelController@delete_item_brand');
    Route::post('car-model/car-model-bulk-preview', 'CarModelController@car_model_bulk_preview');
    Route::post('car-model/save-car-model-bulk-csv', 'CarModelController@save_car_model_bulk_csv');
    Route::get('car-model-export', 'CarModelController@car_model_export');
    // --Group --
    Route::get('group', 'ItemGroupController@group');
    Route::get('add-group', 'ItemGroupController@add_group');
    Route::post('save-item-group', 'ItemGroupController@save_item_group');
    Route::post('list-item-group', 'ItemGroupController@list_item_group');
    Route::post('change-group-status', 'ItemGroupController@change_group_status');
    Route::post('edit-item-group', 'ItemGroupController@edit_item_group');
    Route::post('delete-item-group', 'ItemGroupController@delete_item_group');
    Route::post('gorup/group-bulk-preview', 'ItemGroupController@group_bulk_preview');
    Route::post('gorup/save-group-bulk', 'ItemGroupController@save_group_bulk');
    Route::get('group-export', 'ItemGroupController@gorup_export');
    // --SubCategory --
    Route::get('subcategory', 'SubCategoryController@subcategory');
    Route::get('add-sub-category', 'SubCategoryController@add_sub_category');
    Route::post('get-category-name', 'SubCategoryController@get_category_name');
    Route::post('save-item-sub-category', 'SubCategoryController@save_item_sub_category');
    Route::post('list-sub-category', 'SubCategoryController@list_sub_category');
    Route::post('delete-item-sub-category', 'SubCategoryController@delete_item_sub_category');
    Route::post('edit-item-sub-category', 'SubCategoryController@edit_item_sub_category');
    Route::post('get-category', 'SubCategoryController@get_category');
    Route::post('get-category-data', 'SubCategoryController@get_category_data');
    Route::post('sub-category/sub-category-bulk-preview', 'SubCategoryController@sub_category_bulk_preview');
    Route::post('sub-category/save-sub-category-bulk-csv', 'SubCategoryController@save_sub_category_bulk_csv');
    Route::get('sub-category-export', 'SubCategoryController@sub_category_export');
    // --OEM--
    Route::get('oem-no', 'OemNoController@oem_no');
    Route::get('add-oem', 'OemNoController@add_oem');
    Route::post('get-sub-category-name', 'OemNoController@get_sub_category_name');
    Route::post('save-item-oem', 'OemNoController@save_item_oem');
    Route::post('list-oem', 'OemNoController@list_oem');
    Route::post('delete-item-oem', 'OemNoController@delete_item_oem');
    Route::post('edit-item-oem', 'OemNoController@edit_item_oem');
    Route::post('get-model-name-by-oem', 'OemNoController@get_model_name');
    Route::post('get-category-oem', 'OemNoController@get_category_oem');
    Route::post('get-sub-category-oem', 'OemNoController@get_sub_category_oem');

    // --Car Manufacture--
    Route::get('car-manufacture', 'CarManufactureController@car_manufacture');
    Route::post('list-car-manufacture', 'CarManufactureController@list_car_manufacture');
    Route::post('add-car-manufacture', 'CarManufactureController@add_car_manufacture');
    Route::post('save-car-manufacture', 'CarManufactureController@save_car_manufacture');
    Route::post('edit-car-manufacture', 'CarManufactureController@edit_car_manufacture');
    Route::post('delete-car-manufacture', 'CarManufactureController@delete_car_manufacture');
    Route::post('car-manufacture/car-manufacture-bulk-preview', 'CarManufactureController@car_manufacture_bulk_preview');
    Route::post('car-manufacture/save-car-manufacture-bulk-csv', 'CarManufactureController@save_car_manufacture_bulk_csv');
    Route::get('car-manufacture-export', 'CarManufactureController@car_manufacture_export');

    // --Car Name--
    Route::get('car-name', 'CarNameController@car_name');
    Route::post('list-car-name', 'CarNameController@list_car_name');
    Route::post('add-car-name', 'CarNameController@add_car_name');
    Route::post('get-car-model-by-car-manufacture', 'CarNameController@get_car_model_by_car_manufacture');
    Route::post('save-car-name', 'CarNameController@save_car_name');
    Route::post('edit-car-name', 'CarNameController@edit_car_name');
    Route::post('delete-car-name', 'CarNameController@delete_car_name');
    Route::get('car-name-export', 'CarNameController@car_name_export');

    // --Part Brand--
    Route::get('part-brand', 'PartBrandController@part_brand');
    Route::post('list-part-brand', 'PartBrandController@list_part_brand');
    Route::post('add-part-brand', 'PartBrandController@add_part_brand');
    Route::post('save-part-brand', 'PartBrandController@save_part_brand');
    Route::post('edit-part-brand', 'PartBrandController@edit_part_brand');
    Route::post('delete-part-brand', 'PartBrandController@delete_part_brand');
    Route::post('part-brand/part-brand-bulk-preview', 'PartBrandController@part_brand_bulk_preview');
    Route::post('part-brand/save-part-brand-bulk-csv', 'PartBrandController@save_part_brand_bulk_csv');
    Route::get('part-brand-export', 'PartBrandController@part_brand_export');

    // --Part Name--
    Route::get('part-name', 'PartNameController@part_name');
    Route::post('list-part-name', 'PartNameController@list_part_name');
    Route::post('add-part-name', 'PartNameController@add_part_name');
    Route::post('save-part-name', 'PartNameController@save_part_name');
    Route::post('edit-part-name', 'PartNameController@edit_part_name');
    Route::post('delete-part-name', 'PartNameController@delete_part_name');
    Route::post('part-name/part-name-bulk-preview', 'PartNameController@part_name_bulk_preview');
    Route::post('part-name/save-part-name-bulk-csv', 'PartNameController@save_part_name_bulk_csv');
    Route::get('part-name-export', 'PartNameController@part_name_export');
    
    // --Engine--
    Route::get('engine', 'EngineController@engine');
    Route::post('list-engine', 'EngineController@list_engine');
    Route::post('add-engine', 'EngineController@add_engine');
    Route::post('save-engine', 'EngineController@save_engine');
    Route::post('edit-engine', 'EngineController@edit_engine');
    Route::post('delete-engine', 'EngineController@delete_engine');

    /* ====================
       Reporting Management
    ==================== */
    Route::get('reporting-management', 'ReportingManagementController@reporting_management');
    Route::post('add-reporting-management', 'ReportingManagementController@add_reporting_management');
    Route::post('save-reporting-management', 'ReportingManagementController@save_reporting_management');
    Route::post('list-reporting-management', 'ReportingManagementController@list_reporting_management');
    Route::post('edit-reporting-management', 'ReportingManagementController@edit_reporting_management');
    Route::post('delete-reporting-management', 'ReportingManagementController@delete_reporting_management');
    /* ====================
       Receiving And Putaway
    ==================== */
    Route::get('create-bining-advice', 'ReceivingAndPutawayController@index');
    Route::post('list-receiving-order', 'ReceivingAndPutawayController@list_receiving_order');
    Route::post('get-order-with-detals', 'ReceivingAndPutawayController@get_order_with_detals');
    Route::post('approved-receiving-order', 'ReceivingAndPutawayController@approved_receiving_order');
    Route::post('received-receiving-order', 'ReceivingAndPutawayController@received_receiving_order');
    Route::post('receiving-order/view-order-details', 'ReceivingAndPutawayController@view_order_details');
    Route::get('bining-advice/pdf-bining-advice', 'ReceivingAndPutawayController@pdf_bining_advice');
    Route::post('bining-advice/add-bining-advice', 'ReceivingAndPutawayController@add_bining_advice');
    Route::post('bining-advice/get-purchase-order-detals', 'ReceivingAndPutawayController@get_purchase_order_detals');
    Route::post('bining-advice/save-bining-advice', 'ReceivingAndPutawayController@save_bining_advice');
    Route::post('bining-advice/delete-bining-advice', 'ReceivingAndPutawayController@delete_bining_advice');
    
    Route::post('bining-advice/download-barcode-modal', 'ReceivingAndPutawayController@download_barcode_modal');
    Route::get('bining-advice/download-barcode', 'ReceivingAndPutawayController@download_barcode');

    /* ====================
       Gate Entry Controller
    ==================== */
    Route::get('gate-entry', 'GateEntryController@index');
    Route::get('add-gate-entry', 'GateEntryController@add_gate_entry');
    Route::post('list-gate-entry', 'GateEntryController@list_gate_entry');
    Route::post('save-gate-entry', 'GateEntryController@save_gate_entry');
    Route::post('edit-gate-entry', 'GateEntryController@edit_gate_entry');
    Route::post('check-order-number', 'GateEntryController@check_order_number');
    Route::post('delete-gate-entry', 'GateEntryController@delete_gate_entry');
    Route::get('gate-entry-export', 'GateEntryController@gate_entry_export');

    /* ====================
       Consignment Receipt Controller
    ==================== */
    Route::get('consignment-receipt', 'ConsignmentReceiptController@index');
    Route::post('list-consignment-receipt', 'ConsignmentReceiptController@list_consignment_receipt');
    Route::get('add-consignment-receipt', 'ConsignmentReceiptController@add_consignment_receipt');
    Route::post('save-consignment-receipt', 'ConsignmentReceiptController@save_consignment_receipt');
    Route::post('delete-consignment-receipt', 'ConsignmentReceiptController@delete_consignment_receipt');
    Route::post('view-consignment-receipt', 'ConsignmentReceiptController@view_consignment_receipt');
    Route::post('consignment-receipt/get-order-details', 'ConsignmentReceiptController@get_order_details');
    Route::get('consignment-receipt-export', 'ConsignmentReceiptController@consignment_receipt_export');

    /* ====================
       Check In Controller
    ==================== */
    Route::get('check-in', 'CheckInController@index');
    Route::get('add-check-in', 'CheckInController@add_check_in');
    Route::post('check-in/get-order-details', 'CheckInController@get_order_details');
    Route::post('save-check-in', 'CheckInController@save_check_in');
    Route::post('list-check-in', 'CheckInController@list_check_in');
    Route::post('view-check-in', 'CheckInController@view_check_in');
    Route::post('view-check-in-details', 'CheckInController@view_check_in_details');
    Route::post('delete-check-in', 'CheckInController@delete_check_in');
    Route::get('check-in-export', 'CheckInController@check_in_export');
    Route::post('check-in/view-barcode-modal', 'CheckInController@view_barcode_modal');
    Route::post('check-in/save-barcode-details-by-scann', 'CheckInController@save_barcode_details_by_scann');

    /* ====================
       Purchase Order Return Controller
    ==================== */
    Route::get('purchase-order-return', 'PurchaseOrderReturnController@index');
    Route::get('purchase-order-return-export', 'PurchaseOrderReturnController@purchase_order_return_export');
    Route::post('list-purchase-order-return', 'PurchaseOrderReturnController@list_purchase_order_return');
    Route::get('add-purchase-order-return', 'PurchaseOrderReturnController@add_purchase_order_return');
    Route::post('purchase-order-return/get-order-details', 'PurchaseOrderReturnController@get_order_details');
    Route::post('save-purchase-order-return', 'PurchaseOrderReturnController@save_purchase_order_return');
    Route::post('delete-purchase-order-return', 'PurchaseOrderReturnController@delete_purchase_order_return');
    Route::post('view-purchase-order-return', 'PurchaseOrderReturnController@view_purchase_order_return');
    Route::post('view-files-purchase-order-return', 'PurchaseOrderReturnController@view_files_purchase_order_return');
    Route::post('purchase-order-return/delete-file', 'PurchaseOrderReturnController@delete_file');
    Route::post('purchase-order-return/save-files', 'PurchaseOrderReturnController@save_files');
    Route::post('purchase-order-return/view-return-details', 'PurchaseOrderReturnController@viewReturnDetails');
    

    /* ====================
       Bining Location Controller
    ==================== */
    Route::get('binning-location', 'BinningLocationController@index');
    Route::get('add-binning-location', 'BinningLocationController@add_binning_location');
    Route::post('binning-location/get-order-details', 'BinningLocationController@get_order_details');
    Route::post('confirm-binning-location', 'BinningLocationController@confirm_binning_location');
    Route::post('save-binning-location', 'BinningLocationController@save_binning_location');
    Route::post('list-binning-location', 'BinningLocationController@list_binning_location');
    Route::post('view-binning-location', 'BinningLocationController@view_binning_location');
    Route::post('delete-binning-location', 'BinningLocationController@delete_binning_location');
    Route::get('binning-location-export', 'BinningLocationController@binning_location_export');

    /* ====================
       Bining Task Controller
    ==================== */
    Route::get('binning-task', 'BinningTaskController@index');
    Route::get('binning-task-export', 'BinningTaskController@binning_task_export');
    Route::post('list-binning-task', 'BinningTaskController@list_binning_task');
    Route::get('add-binning-task', 'BinningTaskController@add_binning_task');
    Route::post('save-binning-task', 'BinningTaskController@save_binning_task');
    Route::post('binning-task/status-change', 'BinningTaskController@status_change');
    Route::post('binning-task/close-status-change', 'BinningTaskController@close_status_change');
    Route::post('delete-binning-task', 'BinningTaskController@delete_binning_task');
    Route::get('binning-task/download-binning-invoice', 'BinningTaskController@download_binning_invoice');
    Route::post('binning-task/reset-print', 'BinningTaskController@reset_print');
    
    /* ====================
       Barcode Controller
    ==================== */
    Route::get('barcode-list', 'BarcodeController@index');
    Route::post('list-barcode-table-data', 'BarcodeController@list_barcode_table_data');
    Route::get('barcode-list-export', 'BarcodeController@barcode_list_export');
    
    
    /* ====================
       Packing
    ==================== */
    Route::get('packing', 'PackingController@index');
    Route::get('add-packing', 'PackingController@add_packing');
    Route::post('packing/get-order-details', 'PackingController@get_order_details');
    Route::post('save-packing', 'PackingController@save_packing');
    Route::post('list-packing', 'PackingController@list_packing');
    Route::post('view-packing', 'PackingController@view_packing');
    Route::post('delete-packing', 'PackingController@delete_packing');
    Route::get('print-packing', 'PackingController@print_packing');
    Route::get('packing-export', 'PackingController@packing_Export');
    Route::get('packing-export-details', 'PackingController@packing_Export_details');

    /* ====================
       Shipping
    ==================== */
    Route::get('shipping', 'ShippingController@index');
    Route::get('shipping-export', 'ShippingController@shipping_export');
    Route::get('add-shipping', 'ShippingController@add_shipping');
    Route::post('list-shipping', 'ShippingController@list_shipping');
    Route::post('shipping/get-order-details', 'ShippingController@get_order_details');
    Route::post('save-shipping', 'ShippingController@save_shipping');
    Route::post('view-shipping', 'ShippingController@view_shipping');
    Route::post('get-packing-ids-by-customer', 'ShippingController@get_packing_ids_by_customer');
    /* ====================
       Returns Management
    ==================== */
    Route::get('returns', 'ReturnsManagementController@returns');
    Route::get('returns/returns-export-table', 'ReturnsManagementController@returns_export_table');
    Route::get('returns-form', 'ReturnsManagementController@returns_form');
    Route::post('list-of-returns', 'ReturnsManagementController@list_of_returns');
    Route::post('returns/get-order-details', 'ReturnsManagementController@get_order_details');
    Route::post('returns/save-returns', 'ReturnsManagementController@save_returns');
    Route::post('returns/view-returns', 'ReturnsManagementController@view_returns');
    Route::post('returns/get-sales-order-ids-by-delivery', 'ReturnsManagementController@getSalesOrderIdsByDelivery');
    /* ====================
       Config Start
    ==================== */
    // --Currency--
    Route::get('config-currency', 'ConfigCurrencyController@config_currency');
    Route::get('add-currency', 'ConfigCurrencyController@add_currency');
    Route::post('save-config-currency', 'ConfigCurrencyController@save_config_currency');
    Route::post('list-config-currency', 'ConfigCurrencyController@list_config_currency');
    Route::post('change-currency-status', 'ConfigCurrencyController@change_currency_status');
    Route::post('edit-config-currency', 'ConfigCurrencyController@edit_config_currency');
    Route::post('delete-config-currency', 'ConfigCurrencyController@delete_config_currency');
    Route::post('currency/currency-bulk-preview', 'ConfigCurrencyController@currency_bulk_preview');
    Route::post('currency/save-currency-bulk-csv', 'ConfigCurrencyController@save_currency_bulk_csv');
    Route::get('currency-export', 'ConfigCurrencyController@currency_export');
      // --Exchange Rate--
    Route::get('config-exchange-rate', 'ConfigExchangeRateController@config_exchange_rate');
    Route::get('add-exchange-rate', 'ConfigExchangeRateController@add_exchange_rate');
    Route::post('save-config-exchange-rate', 'ConfigExchangeRateController@save_config_exchange_rate');
    Route::post('list-config-exchange-rate', 'ConfigExchangeRateController@list_config_exchange_rate');
    Route::post('edit-config-exchange-rate', 'ConfigExchangeRateController@edit_config_exchange_rate');
    Route::post('delete-config-exhange-rate', 'ConfigExchangeRateController@delete_config_exhange_rate');
    Route::post('exchange-rate/exchange-rate-bulk-preview', 'ConfigExchangeRateController@exchange_rate_bulk_preview');
    Route::post('exchange-rate/save-exchange-rate-bulk', 'ConfigExchangeRateController@save_exchange_rate_bulk');
    Route::get('exchange-rate-export', 'ConfigExchangeRateController@exchange_rate_export');
    // --Class--
    Route::get('config-class', 'ConfigClassController@config_class');
    Route::get('config-class-export', 'ConfigClassController@config_class_export');
    Route::get('add-config-class', 'ConfigClassController@add_config_class');
    Route::post('save-config-class', 'ConfigClassController@save_config_class');
    Route::post('list-config-class', 'ConfigClassController@list_config_class');
    Route::post('edit-config-class', 'ConfigClassController@edit_config_class');
    Route::post('delete-config-class', 'ConfigClassController@delete_config_class');
    Route::post('class/class-bulk-preview', 'ConfigClassController@class_bulk_preview');
    Route::post('class/save-class-bulk', 'ConfigClassController@save_class_bulk');

     // --Zone Master--
    Route::get('zone-master', 'ZoneMasterController@zone_master');
    Route::get('zone-master-export', 'ZoneMasterController@zone_master_export');
    Route::get('add-zone-master', 'ZoneMasterController@add_zone_master');
    Route::post('save-zome-master', 'ZoneMasterController@save_zome_master');
    Route::post('list-config-zone-master', 'ZoneMasterController@list_config_zone_master');
    Route::post('edit-zone-master', 'ZoneMasterController@edit_zone_master');
    Route::post('delete-zone-master', 'ZoneMasterController@delete_zone_master');
    Route::post('zone-master/zone-master-bulk-preview', 'ZoneMasterController@zone_master_bulk_preview');
    Route::post('zone-master/save-zone-master-bulk', 'ZoneMasterController@save_zone_master_bulk');
    // --Cities--

    // Row
    Route::get('row','RowController@config_row');
    Route::get('row-export','RowController@row_export');
    Route::get('add-row','RowController@add_row');
    Route::post('save-row','RowController@save_row');
    Route::post('list-config-row','RowController@list_config_row');
    Route::post('delete-row','RowController@delete_row');
    Route::post('edit-row','RowController@edit_row');
    Route::post('row/row-bulk-preview','RowController@row_bulk_preview');
    Route::post('row/save-row-bulk','RowController@save_row_bulk');

    // Rack
    Route::get('config/rack','RackController@index');
    Route::post('config/add-rack','RackController@add_rack');
    Route::get('config/rack-export-table','RackController@rack_export_table');
    Route::post('config/list-rack','RackController@list_rack');
    Route::post('config/save-rack','RackController@save_rack');
    Route::post('config/edit-rack','RackController@edit_rack');
    Route::post('config/delete-rack','RackController@delete_rack');
    Route::post('rack/rack-bulk-preview','RackController@rack_bulk_preview');
    Route::post('rack/save-rack-bulk','RackController@save_rack_bulk');

    // Plate
    Route::get('config/level','PlateController@index');
    Route::post('config/add-plate','PlateController@add_plate');
    Route::post('config/list-plate','PlateController@list_plate');
    Route::get('config/plate-export','PlateController@plate_export');
    Route::post('config/save-plate','PlateController@save_plate');
    Route::post('config/edit-plate','PlateController@edit_plate');
    Route::post('config/delete-plate','PlateController@delete_plate');
    Route::post('plate/plate-bulk-preview','PlateController@plate_bulk_preview');
    Route::post('plate/save-plate-bulk','PlateController@save_plate_bulk');

    // Place
    Route::get('config/position','PlaceController@index');
    Route::post('config/add-place','PlaceController@add_place');
    Route::post('config/list-place','PlaceController@list_place');
    Route::post('config/save-place','PlaceController@save_place');
    Route::post('config/edit-place','PlaceController@edit_place');
    Route::post('config/delete-place','PlaceController@delete_place');
    Route::post('place/place-bulk-preview','PlaceController@place_bulk_preview');
    Route::post('place/save-place-bulk','PlaceController@save_place_bulk');
    Route::get('place/place-export','PlaceController@place_export');

    // Citiy
    Route::get('cities', 'CitiesController@cities');
    Route::get('cities-form', 'CitiesController@cities_form');
    Route::post('save-config-cities', 'CitiesController@save_config_cities');
    Route::post('list-config-cities', 'CitiesController@list_config_cities');
    Route::post('edit-config-city', 'CitiesController@edit_config_city');
    Route::post('delete-config-city', 'CitiesController@delete_config_city');
    Route::post('get-city', 'CitiesController@city_list_by_state');
    Route::post('cities/cities-bulk-preview', 'CitiesController@cities_bulk_preview');
    Route::post('cities/save-cities-bulk-csv', 'CitiesController@save_cities_bulk_csv');
    Route::get('cities-export', 'CitiesController@cities_export');
    // --Countries--
    Route::get('countries', 'CountriesController@countries');
    Route::get('countries-form', 'CountriesController@countries_form');
    Route::post('save-config-countries', 'CountriesController@save_config_countries');
    Route::post('list-config-country', 'CountriesController@list_config_country');
    Route::post('edit-config-country', 'CountriesController@edit_config_country');
    Route::post('delete-config-country', 'CountriesController@delete_config_country');
    Route::post('countries/countries-bulk-preview', 'CountriesController@countries_bulk_preview');
    Route::post('countries/save-countries-bulk-csv', 'CountriesController@save_countries_bulk_csv');
    Route::get('countries-export', 'CountriesController@countries_export');
    
    // --Payment--
    Route::get('payment', 'PaymentController@payment');
    Route::get('Payment-form', 'PaymentController@Payment_form');
    Route::post('save-config-payment', 'PaymentController@save_config_payment');
    Route::post('list-config-payment', 'PaymentController@list_config_payment');
    Route::post('edit-config-payment', 'PaymentController@edit_config_payment');
    Route::post('delete-config-payment', 'PaymentController@delete_config_payment');
    Route::post('payment/payment-bulk-preview', 'PaymentController@payment_bulk_preview');
    Route::post('payment/save-payment-bulk', 'PaymentController@save_payment_bulk');
    Route::get('payment-export-excel', 'PaymentController@payment_export_excel');

    // --Functional Area--
    Route::get('functional-area', 'FunctionalAreaController@functional_area');
    Route::get('functinal-area-form', 'FunctionalAreaController@functinal_area_form');
    Route::post('save-config-function-area', 'FunctionalAreaController@save_config_function_area');
    Route::post('list-functional-area', 'FunctionalAreaController@list_functional_area');
    Route::post('edit-config-functional-area', 'FunctionalAreaController@edit_config_functional_area');
    Route::post('delete-config-functional-area', 'FunctionalAreaController@delete_config_functional_area');
    Route::post('functional-area/functional-area-bulk-preview', 'FunctionalAreaController@functional_area_bulk_preview');
    Route::post('functional-area/save-functional-area-bulk-csv', 'FunctionalAreaController@save_functional_area_bulk_csv');
    Route::get('functional-area-export', 'FunctionalAreaController@functional_area_export');
    // -- Units --
    Route::get('unit', 'UnitsController@units');
    Route::get('add-units', 'UnitsController@add_units');
    Route::post('save-config-unit', 'UnitsController@save_config_unit');
    Route::post('list-config-units', 'UnitsController@list_config_units');
    Route::post('edit-config-units', 'UnitsController@edit_config_units');
    Route::post('delete-config-unit', 'UnitsController@delete_config_unit');
    Route::post('unit/unit-bulk-preview', 'UnitsController@unit_bulk_preview');
    Route::post('unit/save-unit-bulk', 'UnitsController@save_unit_bulk');
    Route::get('units-export', 'UnitsController@units_export');
    // -- Unit Load --
    Route::get('unit-load', 'UnitLoadController@unit_load');
    Route::get('add-unit-load', 'UnitLoadController@add_unit_load');
    Route::post('list-unit-load', 'UnitLoadController@list_unit_load');
    Route::post('save-unit-load', 'UnitLoadController@save_unit_load');
    Route::post('edit-unit-load', 'UnitLoadController@edit_unit_load');
    Route::post('delete-unit-load', 'UnitLoadController@delete_unit_load');
    Route::post('unit-load/unit-load-bulk-preview', 'UnitLoadController@unit_load_bulk_preview');
    Route::post('unit-load/save-unit-load-bulk', 'UnitLoadController@save_unit_load_bulk');
    Route::get('unit-load-export', 'UnitLoadController@unit_load_export');
    // --Lots--
    Route::get('lots', 'LotsController@lots');
    Route::get('add-lots', 'LotsController@add_lots');
    Route::post('save-lots', 'LotsController@save_lots');
    Route::post('list-lots', 'LotsController@list_lots');
    Route::post('edit-lots', 'LotsController@edit_lots');
    Route::post('delete-lots', 'LotsController@delete_lots');
    Route::post('lots/lots-bulk-preview', 'LotsController@lots_bulk_preview');
    Route::post('lots/save-lots-bulk', 'LotsController@save_lots_bulk');
    Route::get('lots-export', 'LotsController@lots_export');
    // --Product Rate--
    Route::get('product-rate', 'ProductRateController@product_rate');
    Route::get('add-product-rate', 'ProductRateController@add_product_rate');
    Route::post('save-product-rate', 'ProductRateController@save_product_rate');
    Route::post('list-product-rate', 'ProductRateController@list_product_rate');
    Route::post('delete-product-rate', 'ProductRateController@delete_product_rate');
    Route::post('edit-product-rate', 'ProductRateController@edit_product_rate');
    Route::post('get-product-name', 'ProductRateController@get_product_name');
    Route::post('product-rate/product-rate-bulk-preview', 'ProductRateController@product_rate_bulk_preview');
    Route::post('product-rate/save-product-rate-bulk', 'ProductRateController@save_product_rate_bulk');
    Route::get('product-rate-export', 'ProductRateController@product_rate_export');
    // --Product Tax--
    Route::get('product-tax', 'ProductTaxController@product_tax');
    Route::get('add-product-tax', 'ProductTaxController@add_product_tax');
    Route::post('save-product-tax', 'ProductTaxController@save_product_tax');
    Route::post('list-Product-Tax', 'ProductTaxController@list_Product_Tax');
    Route::post('delete-product-tax', 'ProductTaxController@delete_product_tax');
    Route::post('edit-product-tax', 'ProductTaxController@edit_product_tax');
    Route::post('product-tax/product-tax-bulk-preview', 'ProductTaxController@product_tax_bulk_preview');
    Route::post('product-tax/save-product-tax-bulk', 'ProductTaxController@save_product_tax_bulk');
    Route::get('product-tax-export', 'ProductTaxController@product_tax_export');
    /* ====================
      State Controller
    ==================== */
    Route::get('state', 'StateController@state');
    Route::get('state-export', 'StateController@state_export');
    Route::get('state-form', 'StateController@state_form');
    Route::post('save-config-state', 'StateController@save_config_state');
    Route::post('list-config-state', 'StateController@list_config_state');
    Route::post('edit-config-State', 'StateController@edit_config_State');
    Route::post('delete-config-state', 'StateController@delete_config_state');
    Route::post('get-state', 'StateController@state_list_by_country');
    Route::post('state/state-bulk-preview', 'StateController@state_bulk_preview');
    Route::post('state/save-state-bulk-csv', 'StateController@save_state_bulk_csv');

    /* ====================
       Delivery Method Controller
    ==================== */
    Route::get('config/delivery-method', 'DeliveryMethodController@index');
    Route::post('config/list-delivery-method', 'DeliveryMethodController@list_delivery_method');
    Route::post('config/add-delivery-method', 'DeliveryMethodController@add_delivery_method');
    Route::post('config/save-delivery-method', 'DeliveryMethodController@save_delivery_method');
    Route::post('config/edit-delivery-method', 'DeliveryMethodController@edit_delivery_method');
    Route::post('config/delete-delivery-method', 'DeliveryMethodController@delete_delivery_method');
    Route::post('delivery-method/delivery-method-bulk-preview', 'DeliveryMethodController@delivery_method_bulk_preview');
    Route::post('delivery-method/save-delivery-method-bulk-csv', 'DeliveryMethodController@save_delivery_method_bulk_csv');
    Route::get('delivery-method-export', 'DeliveryMethodController@delivery_method_export');
    /* ====================
       Sms Api Key Controller
    ==================== */ 
    Route::get('config/sms-api-key', 'SmsApiKeyController@sms_api_key');
    Route::get('config/sms-api-key-form', 'SmsApiKeyController@sms_api_key_form');
    Route::post('config/save-sms-api-key', 'SmsApiKeyController@save_sms_api_key');
    Route::post('config/list-sms-api-key', 'SmsApiKeyController@list_sms_api_key');
    Route::post('config/edit-api-key', 'SmsApiKeyController@edit_api_key');
    Route::post('config/delete-api-key', 'SmsApiKeyController@delete_api_key');
    Route::post('config/change-api-status', 'SmsApiKeyController@change_api_status');
    Route::get('config/sms-api-key-export', 'SmsApiKeyController@sms_api_key_export');

    /* ====================
       Mail Api Key Controller
    ==================== */ 
    Route::get('config/mail-config', 'MailApiKeyController@mail_api_key');
    Route::get('config/mail-api-key-form', 'MailApiKeyController@mail_api_key_form');
    Route::post('config/save-mail-api-key', 'MailApiKeyController@save_mail_api_key');
    Route::post('config/list-mail-api-key', 'MailApiKeyController@list_mail_api_key');
    Route::post('config/edit-mail-api-key', 'MailApiKeyController@edit_mail_api_key');
    Route::post('config/delete-mail-api-key', 'MailApiKeyController@delete_mail_api_key');
    Route::post('config/change-mail-api-status', 'MailApiKeyController@change_mail_api_status');
    Route::get('config/mail-config-export', 'MailApiKeyController@mail_config_export');

    /* ====================
       Transport Mode Controller
    ==================== */ 
    Route::get('config/transport-mode', 'TransportModeController@transport_mode');
    Route::get('config/transport-mode-export', 'TransportModeController@transport_mode_export');
    Route::get('config/transport-mode-form', 'TransportModeController@transport_mode_form');
    Route::post('config/save-transport-mode', 'TransportModeController@save_transport_mode');
    Route::post('config/list-transport-mode', 'TransportModeController@list_transport_mode');
    Route::post('config/edit-transport-mode', 'TransportModeController@edit_transport_mode');
    Route::post('config/delete-transport-mode', 'TransportModeController@delete_transport_mode');

    /* ====================
       Courier Company Controller
    ==================== */ 
    Route::get('config/courier-company', 'CourierCompanyController@courier_company');
    Route::get('config/courier-company-form', 'CourierCompanyController@courier_company_form');
    Route::post('config/save-courier-company', 'CourierCompanyController@save_courier_company');
    Route::post('config/list-courier-company', 'CourierCompanyController@list_courier_company');
    Route::post('config/edit-courier-company', 'CourierCompanyController@edit_courier_company');
    Route::post('config/delete-courier-company', 'CourierCompanyController@delete_courier_company');
    Route::get('courier-company-export', 'CourierCompanyController@courier_company_export');
    /* ====================
       Additional Charges Controller
    ==================== */ 
    Route::get('config/additional-charges', 'AdditionalChargesController@additional_charges');
    Route::get('config/additional-charges-export', 'AdditionalChargesController@additional_charges_export');
    Route::post('config/list-additional-charges', 'AdditionalChargesController@list_additional_charges');
    Route::get('config/additional-charges-form', 'AdditionalChargesController@additional_charges_form');
    Route::post('config/save-additional-charges', 'AdditionalChargesController@save_additional_charges');
    Route::post('config/edit-additional-charges', 'AdditionalChargesController@edit_additional_charges');
    Route::post('config/delete-additional-charges', 'AdditionalChargesController@delete_additional_charges');

    /* ====================
       Expenses Controller
    ==================== */ 
    Route::get('config/expenses', 'ExpensesController@index');
    Route::get('config/expenses-export', 'ExpensesController@expenses_export');
    Route::post('config/list-expenses', 'ExpensesController@list_expenses');
    Route::get('config/add-expenses', 'ExpensesController@expenses_form');
    Route::post('config/save-expenses', 'ExpensesController@save_expenses');
    Route::post('config/edit-expenses', 'ExpensesController@edit_expenses');
    //Route::post('config/delete-additional-charges', 'ExpensesController@delete_expenses');
    
    /* ====================
       GST Type Controller
    ==================== */ 
    Route::get('config/vat-type', 'VatTypeController@index');
    Route::get('config/vat-type-export', 'VatTypeController@vat_type_export');
    Route::post('config/list-vat-type', 'VatTypeController@list_vat_type');
    Route::get('config/add-vat-type', 'VatTypeController@vat_type_form');
    Route::post('config/save-vat-type', 'VatTypeController@save_vat_type');
    Route::post('config/edit-vat-type', 'VatTypeController@edit_vat_type');
    Route::post('config/delete-vat-type', 'VatTypeController@delete_vat_type');
    
    /* ====================
       Config End
    ==================== */
    /* ====================
       Stock
    ==================== */
    Route::get('stock', 'StockController@stock');
    Route::get('add-stock', 'StockController@add_stock');
    Route::post('save-stock', 'StockController@save_stock');
    Route::post('list-stock', 'StockController@list_stock');
    Route::post('delete-stock', 'StockController@delete_stock');
    Route::post('get-product-details', 'StockController@get_product_details');
    Route::post('get-warehouse', 'StockController@warehouse_list_by_location');
    Route::get('stock-export', 'StockController@stock_export');

    /* ====================
       Inventory Report Controller
    ==================== */
    Route::get('report/inventory-report', 'InventoryReportController@index');
    Route::post('report/inventory-report-list', 'InventoryReportController@inventory_report_list');
    Route::get('report/inventory-report/top-5-highest-selling-price-product', 'InventoryReportController@top_5_highest_selling_price_product');
    Route::post('report/inventory-report/top-5-highest-selling-price-product-list', 'InventoryReportController@top_5_highest_selling_price_product_list');
    Route::get('report/inventory-report/top-5-high-profit-product', 'InventoryReportController@top_5_high_profit_product');
    Route::post('report/inventory-report/top-5-high-profit-product-list', 'InventoryReportController@top_5_high_profit_product_list');
    Route::get('report/inventory-report/top-5-high-moving-inventory', 'InventoryReportController@top_5_high_moving_inventory');
    Route::post('report/inventory-report/top-5-high-moving-inventory-list', 'InventoryReportController@top_5_high_moving_inventory_list');
    Route::get('report/inventory-report/top-5-high-damage-product', 'InventoryReportController@top_5_high_damage_product');
    Route::post('report/inventory-report/top-5-high-damage-product-list', 'InventoryReportController@top_5_high_damage_product_list');
    Route::get('report/inventory-report/top-5-high-damage-quantity-supplier', 'InventoryReportController@top_5_high_damage_quantity_supplier');
    Route::post('report/inventory-report/top-5-high-damage-quantity-supplier-list', 'InventoryReportController@top_5_high_damage_quantity_supplier_list');
    
    /* ====================
       Inventory Report Controller
    ==================== */
    Route::get('direct-return-bin', 'DefectiveBin@index');
    Route::post('direct-return-bin-list', 'DefectiveBin@direct_return_bin_list');
    Route::get('customer-return-bin', 'DefectiveBin@customerReturnBin');
    Route::post('customer-return-bin-list', 'DefectiveBin@customerReturnBinList');
    
    /* ====================
       Supplier Report Controller
    ==================== */
    Route::get('report/supplier-report', 'SupplierReportController@index');
    Route::post('report/supplier-report-list', 'SupplierReportController@supplier_report_list');

    /* ====================
       Customer Report Controller
    ==================== */
    Route::get('report/customer-report', 'CustomerReportController@index');
    Route::post('report/customer-report-list', 'CustomerReportController@customer_report_list');
    Route::get('report/customer-report/top-5-high-ordered-customer-number', 'CustomerReportController@top_5_high_ordered_customer_number');
    Route::post('report/customer-report/top-5-high-ordered-customer-number-list', 'CustomerReportController@top_5_high_ordered_customer_number_list');
    Route::get('report/customer-report/top-5-high-ordered-customer-value', 'CustomerReportController@top_5_high_ordered_customer_value');
    Route::post('report/customer-report/top-5-high-ordered-customer-value-list', 'CustomerReportController@top_5_high_ordered_customer_value_list');
    Route::get('report/customer-report/customer-report-by-inventory', 'CustomerReportController@customer_report_by_inventory');
    Route::post('report/customer-report/customer-report-by-inventory-list', 'CustomerReportController@customer_report_by_inventory_list');

    /* ====================
       Sales Order Report Controller
    ==================== */
    Route::get('report/sales-order-report', 'SalesOrderReportController@index');
    Route::post('report/sales-order/customer-order-report-list', 'SalesOrderReportController@customer_order_report_list');
    Route::get('report/sales-order-report/approved-orders', 'SalesOrderReportController@approved_orders');
    Route::post('report/sales-order-report/approved-orders-list', 'SalesOrderReportController@approved_orders_list');
    Route::get('report/sales-order-report/not-approved-orders', 'SalesOrderReportController@not_approved_orders');
    Route::post('report/sales-order-report/not-approved-orders-list', 'SalesOrderReportController@not_approved_orders_list');
    Route::get('report/sales-order-report/no-of-orders-by-dates', 'SalesOrderReportController@no_of_orders_by_dates');
    Route::post('report/sales-order-report/no-of-orders-by-dates-list', 'SalesOrderReportController@no_of_orders_by_dates_list');
    
    Route::get('report/sales-order-outstanding-report', 'SalesOrderReportController@sales_order_outstanding_report');
    Route::post('report/list-sales-order-outstanding-report', 'SalesOrderReportController@list_sales_order_outstanding_report');
    
    Route::get('report/sales-order-ageing-report', 'SalesOrderReportController@sales_order_ageing_report');
    Route::post('report/list-sales-order-ageing-report', 'SalesOrderReportController@list_sales_order_ageing_report');
    
    Route::get('report/sales-order-invoice-report', 'SalesOrderReportController@sales_order_invoice_report');
    Route::post('report/list-sales-order-invoice-report', 'SalesOrderReportController@list_sales_order_invoice_report');

    /* ====================
       Purchase Order Report Controller
    ==================== */
    Route::get('report/purchase-order-report', 'PurchaseOrderReportController@index');
    Route::post('report/purchase-order-report-list', 'PurchaseOrderReportController@purchase_order_report_list');
    
    Route::get('report/purchase-order-outstanding-report', 'PurchaseOrderReportController@purchase_order_outstanding_report');
    Route::post('report/list-purchase-order-outstanding-report', 'PurchaseOrderReportController@list_purchase_order_outstanding_report');
    Route::get('report/purchase-order-ageing-report', 'PurchaseOrderReportController@purchase_order_ageing_report');
    Route::post('report/list-purchase-order-ageing-report', 'PurchaseOrderReportController@list_purchase_order_ageing_report');
    Route::get('report/purchase-order-invoice-report', 'PurchaseOrderReportController@purchase_order_invoice_report');
    Route::post('report/list-purchase-order-invoice-report', 'PurchaseOrderReportController@list_purchase_order_invoice_report');

    /* ====================
       Receiving Put Away Report Controller
    ==================== */
    Route::get('report/receiving-put-away-report', 'ReceivingPutAwayOrderReportController@index');
    Route::post('report/receiving-put-away-report-list', 'ReceivingPutAwayOrderReportController@receiving_put_away_report_list');
    Route::get('report/receiving-put-away-report/number-of-picked-order-in-a-date', 'ReceivingPutAwayOrderReportController@number_of_picked_order_in_a_date');
    Route::post('report/receiving-put-away-report/number-of-picked-order-in-a-date-list', 'ReceivingPutAwayOrderReportController@number_of_picked_order_in_a_date_list');
    Route::get('report/receiving-put-away-report/good-or-bad-quantity-order', 'ReceivingPutAwayOrderReportController@good_or_bad_quantity_order');
    Route::post('report/receiving-put-away-report/good-or-bad-quantity-order-list', 'ReceivingPutAwayOrderReportController@good_or_bad_quantity_order_list');
    Route::get('report/receiving-put-away-report/binning-report', 'ReceivingPutAwayOrderReportController@binning_report');
    Route::post('report/receiving-put-away-report/binning-report-list', 'ReceivingPutAwayOrderReportController@binning_report_list');

    /* ====================
       Returns Management Report Controller
    ==================== */
    Route::get('report/returns-management-report', 'ReturnsManagementReportController@index');
    Route::post('report/returns-management-report-list', 'ReturnsManagementReportController@returns_management_report_list');

    /* ====================
       Stock Management Report Controller
    ==================== */
    Route::get('report/stock-management-report', 'StockManagementReportController@index');
    Route::post('report/stock-management-report-list', 'StockManagementReportController@stock_management_report_list');
    Route::get('report/stock-management-report/top-5-stocks-in-warehouse', 'StockManagementReportController@top_5_stocks_in_warehouse');
    Route::post('report/stock-management-report/top-5-stocks-in-warehouse-list', 'StockManagementReportController@top_5_stocks_in_warehouse_list');
    

});