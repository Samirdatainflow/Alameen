<?php
if (! function_exists('user_details')) {
    function user_details($model, $resource = null)
    {
      $user_id=Session::get('user_id');
      $select_user = $model::where([['client_id', '=', $user_id]])->get();
      return $select_user;
    }
}
if (! function_exists('available_stock')) {
    function available_stock($model, $product_id)
    {
    	$query = $model::table('products');
	    $query->select('products.current_stock');
	    $query->where('products.product_id', '=', $product_id );
	    $data=$query->get();
	    // $used_stock_query=$model::table('client_order_details');
	    // $used_stock_query->select(DB::raw("SUM(qty) as total_qty"));
	    // $used_stock_query->where('client_order_details.product_id', '=', $product_id);
	    // $used_data=json_decode(json_encode($used_stock_query->get()->toArray()),TRUE);
	    // if(sizeof($used_data)>0)
	    // {
	    //     $current_stock = $data[0]->current_stock-$used_data[0]['total_qty'];
	    // }
	    // else
	    // {
	        $current_stock = $data[0]->current_stock;
	    //}
	    return $current_stock;      	
	}
}

if (! function_exists('available_stock_by_part_no')) {
    function available_stock_by_part_no($model, $part_no)
    {
    	$query = $model::table('products');
	    $query->select('products.current_stock');
	    $query->where('products.pmpno', '=', $part_no);
	    $data=$query->get();
	    // $used_stock_query=$model::table('client_order_details');
	    // $used_stock_query->select(DB::raw("SUM(qty) as total_qty"));
	    // $used_stock_query->where('client_order_details.product_id', '=', $product_id);
	    // $used_data=json_decode(json_encode($used_stock_query->get()->toArray()),TRUE);
	    // if(sizeof($used_data)>0)
	    // {
	    //     $current_stock = $data[0]->current_stock-$used_data[0]['total_qty'];
	    // }
	    // else
	    // {
	        $current_stock = $data[0]->current_stock;
	    //}
	    return $current_stock;      	
	}
}