<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Product_Skus;
use App\Tbl_Product_Image;
use App\Tbl_Prices;
use App\Tbl_Products;
use App\Tbl_Order;
use App\Tbl_Order_Details;
use App\Tbl_Product_Terms;
use App\Tbl_Terms;
use App\Tbl_Order_Stock;
use App\Tbl_Retailers;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class ProductController extends Controller
{
    public function list_product_sku(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $skuData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_sku = Tbl_Product_Skus::select('id', 'sku_type')->where([['status', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_sku) > 0) {
                foreach($select_sku as $data) {
                    array_push($skuData, array('label' => $data->sku_type, 'value' => $data->id));
                }
                return response()->json(["status" => 1, "sku_data" => $skuData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_products(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_products as p');
        $query->select('p.id', 'p.sku_id', 'p.erp_code', 'p.product_name', 'p.product_description', 'p.base_sale_price', 'c.name as category_name', 'cs.name as sub_category');
        $query->join('tbl_product_category as c', 'c.id', '=', 'p.category', 'left');
        $query->join('tbl_product_category as cs', 'cs.id', '=', 'p.sub_category', 'left');
        $query->where([['p.fk_company_id', '=', $fk_company_id], ['p.active', '=', "1"]]);
        if(!empty($request->product_name)) {
            $query->where([['p.product_name', 'like', '%' .$request->product_name.'%']]);
        }
        $query->orderBy('p.id', 'DESC');
        $product_data = $query->get();
        if(sizeof($product_data) > 0) {
            foreach($product_data as $p_data) {
                $sell_price = 0;
                $default_image = "";
                $more_images = [];
                $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                if(sizeof($select_images) > 0) {
                    foreach($select_images as $imgdata) {
                        array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                    }
                }
                $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                if(sizeof($select_default_image) >0) {
                    $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                }
                $product_price = $p_data->base_sale_price;
                $sell_price = $p_data->base_sale_price;
                $productTerms = [];
                $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $p_data->id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($select_pTerms) > 0) {
                    foreach($select_pTerms as $tdata) {
                        $productTerms[] =$tdata['fk_terms_id'];
                    }
                }
                $PriceBreakups = [];
                if(sizeof($select_pTerms) > 0) {
                    $productPriceBreakups = $this->TotalPriceByFormula($p_data->base_sale_price, $productTerms, $p_data->id);
                    //$product_price = $p_data->base_sale_price;
                    $product_price = $productPriceBreakups[0]['sales_price'];
                    $PriceBreakups = $productPriceBreakups;
                }
                if(!empty($request->retailer_id)) {
                	$select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                	if(sizeof($select_product_price) > 0) {
                		if(!empty($select_product_price[0]['base_sale_price'])) {
                            //$product_price = $select_product_price[0]['base_sale_price'];
                			$sell_price = $select_product_price[0]['base_sale_price'];
                            $PriceBreakups = [];
                		}
                	}
                }
                if(!empty($request->distributor_id)) {
                	$select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                	if(sizeof($select_product_price) > 0) {
                		if(!empty($select_product_price[0]['base_sale_price'])) {
                            $product_price = $select_product_price[0]['base_sale_price'];
                			$sell_price = $select_product_price[0]['base_sale_price'];
                            $PriceBreakups = [];
                		}
                	}
                }
                array_push($returnData, array('id' => $p_data->id, 'sku_id' => $p_data->sku_id, 'erp_code' => $p_data->erp_code, 'product_name' => $p_data->product_name, 'product_description' => $p_data->product_description, 'product_price' => $product_price, 'category_name' => $p_data->category_name, 'sub_category' => $p_data->sub_category, 'default_image' => $default_image, 'product_images' => $more_images, 'PriceBreakups' => $PriceBreakups, 'base_sale_price' => $p_data->base_sale_price, 'sell_price' => $sell_price));
            }
        }
        if(count($product_data) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =======================
        Product Filter
    ======================= */
    public function list_products_filter_by_name(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'product_name' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_products as p');
        $query->select('p.id', 'p.sku_id', 'p.erp_code', 'p.product_name', 'p.product_description', 'p.base_sale_price', 'c.name as category_name', 'cs.name as sub_category');
        $query->join('tbl_product_category as c', 'c.id', '=', 'p.category', 'left');
        $query->join('tbl_product_category as cs', 'cs.id', '=', 'p.sub_category', 'left');
        $query->where([['p.fk_company_id', '=', $fk_company_id], ['p.active', '=', "1"], ['p.product_name', 'like', '%' .$request->product_name.'%']]);
        $query->orderBy('p.id', 'DESC');
        $product_data = $query->get();
        if(sizeof($product_data) > 0) {
            foreach($product_data as $p_data) {
                $default_image = "";
                $more_images = [];
                $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                if(sizeof($select_images) > 0) {
                    foreach($select_images as $imgdata) {
                        array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                    }
                }
                $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                if(sizeof($select_default_image) >0) {
                    $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                }
                $product_price = $p_data->base_sale_price;
                $productTerms = [];
                $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $p_data->id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($select_pTerms) > 0) {
                    foreach($select_pTerms as $tdata) {
                        $productTerms[] =$tdata['fk_terms_id'];
                    }
                }
                $PriceBreakups = [];
                if(sizeof($select_pTerms) > 0) {
                    $productPriceBreakups = $this->TotalPriceByFormula($p_data->base_sale_price, $productTerms, $p_data->id);
                    $product_price = $productPriceBreakups[0]['sales_price'];
                    $PriceBreakups = $productPriceBreakups;
                }
                if(!empty($request->retailer_id)) {
                    $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                    if(sizeof($select_product_price) > 0) {
                        if(!empty($select_product_price[0]['base_sale_price'])) {
                            $product_price = $select_product_price[0]['base_sale_price'];
                            $PriceBreakups = [];
                        }
                    }
                }
                if(!empty($request->distributor_id)) {
                    $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                    if(sizeof($select_product_price) > 0) {
                        if(!empty($select_product_price[0]['base_sale_price'])) {
                            $product_price = $select_product_price[0]['base_sale_price'];
                            $PriceBreakups = [];
                        }
                    }
                }
                array_push($returnData, array('id' => $p_data->id, 'sku_id' => $p_data->sku_id, 'erp_code' => $p_data->erp_code, 'product_name' => $p_data->product_name, 'product_description' => $p_data->product_description, 'product_price' => $product_price, 'category_name' => $p_data->category_name, 'sub_category' => $p_data->sub_category, 'default_image' => $default_image, 'product_images' => $more_images, 'PriceBreakups' => $PriceBreakups, 'base_sale_price' => $p_data->base_sale_price));
            }
        }
        if(count($product_data) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_products1(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_products as p');
        $query->select('p.id', 'p.sku_id', 'p.erp_code', 'p.product_name', 'p.product_description', 'p.base_sale_price', 'c.name as category_name', 'cs.name as sub_category');
        $query->join('tbl_product_category as c', 'c.id', '=', 'p.category', 'left');
        $query->join('tbl_product_category as cs', 'cs.id', '=', 'p.sub_category', 'left');
        $query->where([['p.fk_company_id', '=', $fk_company_id], ['p.active', '=', "1"]]);
        $query->orderBy('p.id', 'DESC');
        $product_data = $query->get();
        if(sizeof($product_data) > 0) {
            foreach($product_data as $p_data) {
                $o_user_id = "";
                if(!empty($request->retailer_id)) {
                    $o_user_id = $request->retailer_id;
                }
                $query2 = DB::table('tbl_order_details as od');
                $query2->select('od.id');
                $query2->join('tbl_order as o', 'o.id', '=', 'od.fk_order_id', 'left');
                $query2->where([['o.user_id', '=', $o_user_id], ['od.fk_product_id', '=', $p_data->id]]);
                $orderData = $query2->get();
                if(sizeof($orderData) < 1) {
                    $default_image = "";
                    $more_images = [];
                    $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                    if(sizeof($select_images) > 0) {
                        foreach($select_images as $imgdata) {
                            array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                        }
                    }
                    $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                    if(sizeof($select_default_image) >0) {
                        $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                    }
                    $product_price = $p_data->base_sale_price;
                    $productTerms = [];
                    $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $p_data->id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($select_pTerms) > 0) {
                        foreach($select_pTerms as $tdata) {
                            $productTerms[] =$tdata['fk_terms_id'];
                        }
                    }
                    if(sizeof($select_pTerms) > 0) {
                        $productPriceBreakups = $this->TotalPriceByFormula($p_data->base_sale_price, $productTerms, $p_data->id);
                        $product_price = $productPriceBreakups[0]['sales_price'];
                        $PriceBreakups = $productPriceBreakups;
                    }
                    if(!empty($request->retailer_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    if(!empty($request->distributor_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    array_push($returnData, array('id' => $p_data->id, 'sku_id' => $p_data->sku_id, 'erp_code' => $p_data->erp_code, 'product_name' => $p_data->product_name, 'product_description' => $p_data->product_description, 'product_price' => $product_price, 'category_name' => $p_data->category_name, 'sub_category' => $p_data->sub_category, 'default_image' => $default_image, 'product_images' => $more_images));
                }
            }
        }
        if(count($product_data) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    // Add order
    public function add_order(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $p_user_id = "";
        $user_type = "";
        if(isset($request->retailer_id)) {
            $p_user_id = $request->retailer_id;
            $user_type = "2";
        }
        if(isset($request->distributor_id)) {
            $p_user_id = $request->distributor_id;
            $user_type = "1";
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        //return response()->json(["status" => 1, 'productData' => $productData, 'quantityArray' => $quantityArray]);
        //exit();
        $i= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$i] > 0) {
                $totalAmount = $totalAmount + ($pdata['product_price'] * $quantityArray[$i]);
            }
            $i++;
        }
        // return response()->json(["status" => 1, 'data' => $totalAmount]);
        $data = Tbl_Order::create([
            'fk_company_id' => $fk_company_id,
            'user_id' => $request->user_id,
            'user_type' => $request->user_type,
            'order_by' => $p_user_id,
            'total_amount' => $totalAmount,
            'status' => "0",
            'remarks' => $request->remarks
        ]);
        if($data) {
        	$j= 0;
	        foreach($productData as $pdata) {
	        	if($quantityArray[$j] > 0) {
		            $data2 = Tbl_Order_Details::create([
			            'fk_company_id' => $fk_company_id,
			            'fk_order_id' => $data->id,
			            'fk_product_id' => $pdata['id'],
			            'sku_id' => $pdata['sku_id'],
			            'erp_code' => $pdata['erp_code'],
			            'product_name' => $pdata['product_name'],
			            'product_description' => $pdata['product_description'],
			            'quantity' => $quantityArray[$j],
			            'product_price' => $pdata['product_price'] * $quantityArray[$j],
			            'created_by' => $request->user_id,
                        'user_type' => $request->user_type,
                        'order_by' => $p_user_id,
			            'Inactive' => "1"
			        ]);
                    $data3 = Tbl_Order_Stock::create([
                        'fk_company_id' => $fk_company_id,
                        'fk_order_id' => $data->id,
                        'fk_order_detail_id' => $data2->id,
                        'fk_product_id' => $pdata['id'],
                        'stock_type' => "0",
                        'quantity' => $quantityArray[$j],
                        'user_type' => $user_type,
                        'stock_by' => $p_user_id
                    ]);
		        }
	            $j++;
	        }
	        if($j == sizeof($productData)) {
	            return response()->json(["status" => 1, 'msg' => 'Order proceed successful.']);
	        }else {
	        	return response()->json(["status" => 0, 'msg' => 'Something is wrong.']);
	        }
        }else {
            return response()->json(["status" => 0]);
        }
    }
    // Remove Order
    public function remove_order(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'product_id' => 'required|int',
            'user_type' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $p_user_id = "";
        if(isset($request->retailer_id)) {
            $p_user_id = $request->retailer_id;
        }
        $select_order_data = Tbl_Order::select('quantity')->where([['fk_company_id', '=', $fk_company_id], ['user_id', '=', $p_user_id], ['user_type', '=', $request->user_type], ['fk_product_id', '=', $request->product_id]])->get()->toArray();
        if(sizeof($select_order_data) > 0) {
            if($select_order_data[0]['quantity'] > 1) {
                $quantity = intval($select_order_data[0]['quantity']) - 1;
                $update_order_data = Tbl_Order::where([['fk_company_id', '=', $fk_company_id], ['user_id', '=', $p_user_id], ['user_type', '=', $request->user_type], ['fk_product_id', '=', $request->product_id]])->update(['quantity' => $quantity]);
                if($update_order_data) {
                    return response()->json(["status" => 1]);
                }else {
                    return response()->json(["status" => 0]);
                }
            }else {
                $delete_order =  Tbl_Order::where([['fk_company_id', '=', $fk_company_id], ['user_id', '=', $p_user_id], ['user_type', '=', $request->user_type], ['fk_product_id', '=', $request->product_id]])->delete();
                if($delete_order) {
                    return response()->json(["status" => 1]);
                }else {
                    return response()->json(["status" => 0]);
                }
            }
        }else {
            return response()->json(["status" => 0]);
        }
    }
    function TotalPriceByFormula($base_price, $productTerms, $p_id) {
        $returnData = [];
        $sales_price = 0;
        $tramsDetails = [];
        $formulaDetails = [];
        if(!empty($productTerms)) {
            foreach($productTerms as $tId) {
                $select_data = Tbl_Product_Terms::select('terms_name', 'load_discount', 'rate', 'qty_value', 'formula')->where([['fk_terms_id', '=', $tId], ['fk_product_id', '=', $p_id]])->get()->toArray();
                if(sizeof($select_data) > 0) {
                    if($select_data[0]['qty_value'] == "1") {
                        if($select_data[0]['load_discount'] == "1") {
                            $sales_price += (int)($base_price) + (int)$select_data[0]['rate'];
                            array_push($tramsDetails, array('terms_name' => $select_data[0]['terms_name'], 'qty_value' => 'Qty', 'load_discount' => 'Load', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                        }
                        if($select_data[0]['load_discount'] == "2") {
                            $sales_price += (int)($base_price) - (int)$select_data[0]['rate'];
                            array_push($tramsDetails, array('terms_name' => $select_data[0]['terms_name'], 'qty_value' => 'Qty', 'load_discount' => 'Load', 'Discount' => $select_data[0]['rate'], 'base_price' => $base_price));
                        }
                    }
                    if($select_data[0]['qty_value'] == "2") {
                        if($select_data[0]['load_discount'] == "1") {
                            $load_discount = ((int)($base_price) * (int)$select_data[0]['rate']) / 100;
                            $sales_price += (int)$base_price + $load_discount;
                            array_push($tramsDetails, array('terms_name' => $select_data[0]['terms_name'], 'qty_value' => 'Value', 'load_discount' => 'Load', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                        }
                        if($select_data[0]['load_discount'] == "2") {
                            $load_discount = ((int)($base_price) * (int)$select_data[0]['rate']) / 100;
                            $sales_price += (int)$base_price - $load_discount;
                            array_push($tramsDetails, array('terms_name' => $select_data[0]['terms_name'], 'qty_value' => 'Value', 'load_discount' => 'Discount', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                        }
                    }
                    if(!empty($select_data[0]['formula'])) {
                        $formula = unserialize($select_data[0]['formula']);
                        foreach($formula as $k=>$v) {
                            if($v> 0) {
                                $select_data = Tbl_Terms::select('load_discount', 'rate', 'qty_value', 'formula')->where([['term_master_id', '=', $v]])->get()->toArray();
                                if(sizeof($select_data) > 0) {
                                    if($select_data[0]['qty_value'] == "1") {
                                        if($select_data[0]['load_discount'] == "1") {
                                            $sales_price += (int)($base_price) + (int)$select_data[0]['rate'];
                                            array_push($formulaDetails, array('formula' => 'formula', 'qty_value' => 'Qty', 'load_discount' => 'Load', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                                        }
                                        if($select_data[0]['load_discount'] == "2") {
                                            $sales_price += (int)($base_price) - (int)$select_data[0]['rate'];
                                            array_push($formulaDetails, array('formula' => 'formula', 'qty_value' => 'Qty', 'load_discount' => 'Discount', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                                        }
                                    }
                                    if($select_data[0]['qty_value'] == "2") {
                                        if($select_data[0]['load_discount'] == "1") {
                                            $load_discount = ((int)($base_price) * (int)$select_data[0]['rate']) / 100;
                                            $sales_price += (int)$base_price + $load_discount;
                                            array_push($formulaDetails, array('formula' => 'formula', 'qty_value' => 'Value', 'load_discount' => 'Load', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                                        }
                                        if($select_data[0]['load_discount'] == "2") {
                                            $load_discount = ((int)($base_price) * (int)$select_data[0]['rate']) / 100;
                                            $sales_price += (int)$base_price - $load_discount;
                                            array_push($formulaDetails, array('formula' => 'formula', 'qty_value' => 'Value', 'load_discount' => 'Discount', 'rate' => $select_data[0]['rate'], 'base_price' => $base_price));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        array_push($returnData, array('sales_price' => $sales_price, 'base_price' => $base_price, 'tramsDetails' => $tramsDetails, 'formulaDetails' => $formulaDetails));
        return $returnData;
    }
    // List Retailer Order
    public function retailer_list_orders(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['fk_company_id', '=', $fk_company_id], ['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        //print_r($selectOrder);
        //exit();
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                if(sizeof($productData) > 0) {
                    $default_image = "";
                    $more_images = [];
                    $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                    if(sizeof($select_images) > 0) {
                        foreach($select_images as $imgdata) {
                            array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                        }
                    }
                    $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                    if(sizeof($select_default_image) >0) {
                        $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                    }
                    $product_price = $productData[0]['base_sale_price'];
                    $productTerms = [];
                    $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($select_pTerms) > 0) {
                        foreach($select_pTerms as $tdata) {
                            $productTerms[] =$tdata['fk_terms_id'];
                        }
                    }
                    if(sizeof($select_pTerms) > 0) {
                        $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                        $product_price = $productPriceBreakups[0]['sales_price'];
                        $PriceBreakups = $productPriceBreakups;
                    }
                    if(!empty($request->retailer_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    if(!empty($request->distributor_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    $available = 0;
                    $sales_product = 0;
                    $return_product = 0;
                    $product_order = 0;
                    $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['user_type', '=', '2'], ['stock_by', '=', $request->retailer_id]])->get()->toArray();
                    if(sizeof($salesProduct) > 0) {
                        $sales_product = $salesProduct[0]['sales'];
                    }
                    $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($returnProduct) > 0) {
                        $return_product = $returnProduct[0]['return_product'];
                    }
                    $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($orderProduct) > 0) {
                        $product_order = $orderProduct[0]['order_product'];
                    }
                    $available = $product_order - ($sales_product + $return_product);
                    array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        List order filter by name
    ===================== */
    public function retailer_list_orders_filter_by_name(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'product_name' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $FilterProduct = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['fk_company_id', '=', $fk_company_id], ['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        $SelectFilterProduct = Tbl_Products::select('id')->where([['product_name', 'like', '%' .$request->product_name.'%']])->get()->toArray();
        if(sizeof($SelectFilterProduct)) {
            foreach($SelectFilterProduct as $fdata) {
                array_push($FilterProduct, $fdata['id']);
            }
        }
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                if (in_array($product_id, $FilterProduct)) {
                    $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                    if(sizeof($productData) > 0) {
                        $default_image = "";
                        $more_images = [];
                        $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                        if(sizeof($select_images) > 0) {
                            foreach($select_images as $imgdata) {
                                array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                            }
                        }
                        $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                        if(sizeof($select_default_image) >0) {
                            $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                        }
                        $product_price = $productData[0]['base_sale_price'];
                        $productTerms = [];
                        $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                        if(sizeof($select_pTerms) > 0) {
                            foreach($select_pTerms as $tdata) {
                                $productTerms[] =$tdata['fk_terms_id'];
                            }
                        }
                        if(sizeof($select_pTerms) > 0) {
                            $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                            $product_price = $productPriceBreakups[0]['sales_price'];
                            $PriceBreakups = $productPriceBreakups;
                        }
                        if(!empty($request->retailer_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        if(!empty($request->distributor_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        $available = 0;
                        $sales_product = 0;
                        $return_product = 0;
                        $product_order = 0;
                        $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['user_type', '=', '2'], ['stock_by', '=', $request->retailer_id]])->get()->toArray();
                        if(sizeof($salesProduct) > 0) {
                            $sales_product = $salesProduct[0]['sales'];
                        }
                        $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($returnProduct) > 0) {
                            $return_product = $returnProduct[0]['return_product'];
                        }
                        $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($orderProduct) > 0) {
                            $product_order = $orderProduct[0]['order_product'];
                        }
                        $available = $product_order - ($sales_product + $return_product);
                        array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                    }
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        List order for update stock
    ===================== */
    public function list_orders_4_retailer_update_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['fk_company_id', '=', $fk_company_id], ['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        //$selectOrder = Tbl_Order::select('id')->where([['fk_company_id', '=', $fk_company_id], ['user_id', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                if(sizeof($productData) > 0) {
                    $default_image = "";
                    $more_images = [];
                    $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                    if(sizeof($select_images) > 0) {
                        foreach($select_images as $imgdata) {
                            array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                        }
                    }
                    $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                    if(sizeof($select_default_image) >0) {
                        $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                    }
                    $product_price = $productData[0]['base_sale_price'];
                    $productTerms = [];
                    $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($select_pTerms) > 0) {
                        foreach($select_pTerms as $tdata) {
                            $productTerms[] =$tdata['fk_terms_id'];
                        }
                    }
                    if(sizeof($select_pTerms) > 0) {
                        $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                        $product_price = $productPriceBreakups[0]['sales_price'];
                        $PriceBreakups = $productPriceBreakups;
                    }
                    if(!empty($request->retailer_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    if(!empty($request->distributor_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    $available = 0;
                    $sales_product = 0;
                    $return_product = 0;
                    $product_order = 0;
                    $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($salesProduct) > 0) {
                        $sales_product = $salesProduct[0]['sales'];
                    }
                    $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($returnProduct) > 0) {
                        $return_product = $returnProduct[0]['return_product'];
                    }
                    $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($orderProduct) > 0) {
                        $product_order = $orderProduct[0]['order_product'];
                    }
                    $available = $product_order - ($sales_product + $return_product);
                    array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        List order for update stock
    ===================== */
    public function filter_orders_4_update_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'product_name' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $FilterProduct = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['fk_company_id', '=', $fk_company_id], ['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        $SelectFilterProduct = Tbl_Products::select('id')->where([['product_name', 'like', '%' .$request->product_name.'%']])->get()->toArray();
        if(sizeof($SelectFilterProduct)) {
            foreach($SelectFilterProduct as $fdata) {
                array_push($FilterProduct, $fdata['id']);
            }
        }
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                if (in_array($product_id, $FilterProduct)) {
                    $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                    if(sizeof($productData) > 0) {
                        $default_image = "";
                        $more_images = [];
                        $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                        if(sizeof($select_images) > 0) {
                            foreach($select_images as $imgdata) {
                                array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                            }
                        }
                        $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                        if(sizeof($select_default_image) >0) {
                            $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                        }
                        $product_price = $productData[0]['base_sale_price'];
                        $productTerms = [];
                        $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                        if(sizeof($select_pTerms) > 0) {
                            foreach($select_pTerms as $tdata) {
                                $productTerms[] =$tdata['fk_terms_id'];
                            }
                        }
                        if(sizeof($select_pTerms) > 0) {
                            $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                            $product_price = $productPriceBreakups[0]['sales_price'];
                            $PriceBreakups = $productPriceBreakups;
                        }
                        if(!empty($request->retailer_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        if(!empty($request->distributor_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        $available = 0;
                        $sales_product = 0;
                        $return_product = 0;
                        $product_order = 0;
                        $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($salesProduct) > 0) {
                            $sales_product = $salesProduct[0]['sales'];
                        }
                        $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($returnProduct) > 0) {
                            $return_product = $returnProduct[0]['return_product'];
                        }
                        $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($orderProduct) > 0) {
                            $product_order = $orderProduct[0]['order_product'];
                        }
                        $available = $product_order - ($sales_product + $return_product);
                        array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                    }
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        List ritailer stock by retailer ID
    ===================== */
    public function list_single_retailer_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                if(sizeof($productData) > 0) {
                    $default_image = "";
                    $more_images = [];
                    $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                    if(sizeof($select_images) > 0) {
                        foreach($select_images as $imgdata) {
                            array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                        }
                    }
                    $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                    if(sizeof($select_default_image) >0) {
                        $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                    }
                    $product_price = $productData[0]['base_sale_price'];
                    $productTerms = [];
                    $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($select_pTerms) > 0) {
                        foreach($select_pTerms as $tdata) {
                            $productTerms[] =$tdata['fk_terms_id'];
                        }
                    }
                    if(sizeof($select_pTerms) > 0) {
                        $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                        $product_price = $productPriceBreakups[0]['sales_price'];
                        $PriceBreakups = $productPriceBreakups;
                    }
                    if(!empty($request->retailer_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    if(!empty($request->distributor_id)) {
                        $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                        if(sizeof($select_product_price) > 0) {
                            if(!empty($select_product_price[0]['base_sale_price'])) {
                                $product_price = $select_product_price[0]['base_sale_price'];
                            }
                        }
                    }
                    $available = 0;
                    $sales_product = 0;
                    $return_product = 0;
                    $product_order = 0;
                    $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($salesProduct) > 0) {
                        $sales_product = $salesProduct[0]['sales'];
                    }
                    $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($returnProduct) > 0) {
                        $return_product = $returnProduct[0]['return_product'];
                    }
                    $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                    if(sizeof($orderProduct) > 0) {
                        $product_order = $orderProduct[0]['order_product'];
                    }
                    $available = $product_order - ($sales_product + $return_product);
                    array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        List ritailer stock filter
    ===================== */
    public function single_retailer_stock_filter(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'product_name' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $FilterProduct = [];
        $selectOrder = Tbl_Order_Details::select('fk_product_id')->where([['order_by', '=', $request->retailer_id], ['user_type', '=', '2']])->groupBy('fk_product_id')->get()->toArray();
        $SelectFilterProduct = Tbl_Products::select('id')->where([['product_name', 'like', '%' .$request->product_name.'%']])->get()->toArray();
        if(sizeof($SelectFilterProduct)) {
            foreach($SelectFilterProduct as $fdata) {
                array_push($FilterProduct, $fdata['id']);
            }
        }
        $productData = [];
        if(sizeof($selectOrder) > 0) {
            foreach($selectOrder as $oData) {
                $product_id = $oData['fk_product_id'];
                if (in_array($product_id, $FilterProduct)) {
                    $productData = Tbl_Products::where([['id', '=', $product_id], ['active', '=', '1']])->get()->toArray();
                    if(sizeof($productData) > 0) {
                        $default_image = "";
                        $more_images = [];
                        $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                        if(sizeof($select_images) > 0) {
                            foreach($select_images as $imgdata) {
                                array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                            }
                        }
                        $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $product_id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                        if(sizeof($select_default_image) >0) {
                            $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                        }
                        $product_price = $productData[0]['base_sale_price'];
                        $productTerms = [];
                        $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $product_id], ['status', '=', '1']])->get()->toArray();
                        if(sizeof($select_pTerms) > 0) {
                            foreach($select_pTerms as $tdata) {
                                $productTerms[] =$tdata['fk_terms_id'];
                            }
                        }
                        if(sizeof($select_pTerms) > 0) {
                            $productPriceBreakups = $this->TotalPriceByFormula($productData[0]['base_sale_price'], $productTerms, $product_id);
                            $product_price = $productPriceBreakups[0]['sales_price'];
                            $PriceBreakups = $productPriceBreakups;
                        }
                        if(!empty($request->retailer_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        if(!empty($request->distributor_id)) {
                            $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $product_id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                            if(sizeof($select_product_price) > 0) {
                                if(!empty($select_product_price[0]['base_sale_price'])) {
                                    $product_price = $select_product_price[0]['base_sale_price'];
                                }
                            }
                        }
                        $available = 0;
                        $sales_product = 0;
                        $return_product = 0;
                        $product_order = 0;
                        $salesProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as sales'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '1'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($salesProduct) > 0) {
                            $sales_product = $salesProduct[0]['sales'];
                        }
                        $returnProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as return_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '2'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($returnProduct) > 0) {
                            $return_product = $returnProduct[0]['return_product'];
                        }
                        $orderProduct = Tbl_Order_Stock::select(DB::raw('SUM(quantity) as order_product'))->where([['fk_product_id', '=', $product_id], ['stock_type', '=', '0'], ['stock_by', '=', $request->retailer_id], ['user_type', '=', '2']])->get()->toArray();
                        if(sizeof($orderProduct) > 0) {
                            $product_order = $orderProduct[0]['order_product'];
                        }
                        $available = $product_order - ($sales_product + $return_product);
                        array_push($returnData, array('id' => $product_id, 'sku_id' => $productData[0]['sku_id'], 'erp_code' => $productData[0]['erp_code'], 'product_name' => $productData[0]['product_name'], 'product_description' => $productData[0]['product_description'], 'product_price' => $product_price, 'default_image' => $default_image, 'product_images' => $more_images, 'available' => $available, 'total_order' => $product_order, 'total_sales' => $sales_product, 'total_return' => $return_product));
                    }
                }
            }
        }
        if(count($productData) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        Order Sales Details Store
    ===================== */
    public function add_product_sales_order(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "1",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Sales proceed successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Sales proceed faild.']);
        }
    }
    /* =====================
        Order Sales Details Store of SIngle Retailer
    ===================== */
    public function add_product_single_retailer_sales_order(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "1",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Sales proceed successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Sales proceed faild.']);
        }
    }
    /* =====================
        Return Order Details Store
    ===================== */
    public function add_stock_return(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "2",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Stock return proceed successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Stock return faild.']);
        }
    }
    /* =====================
        Single Retailer Return Order Details Store
    ===================== */
    public function add_single_retailer_stock_return(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "2",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Stock return proceed successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Stock return faild.']);
        }
    }
    /* =====================
        Update Stock Details
    ===================== */
    public function update_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "0",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Stock update successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Stock update faild.']);
        }
    }
    /* =====================
        Update Stock Details by Retailer
    ===================== */
    public function single_retailer_update_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        $j= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$j] > 0) {
                $data2 = Tbl_Order_Stock::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_product_id' => $pdata['id'],
                    'stock_type' => "0",
                    'quantity' => $quantityArray[$j],
                    'user_type' => "2",
                    'stock_by' => $request->retailer_id
                ]);
            }
            $j++;
        }
        if($j == sizeof($productData)) {
            return response()->json(["status" => 1, 'msg' => 'Stock update successful.']);
        }else {
            return response()->json(["status" => 0, 'msg' => 'Stock update faild.']);
        }
    }
    /* =====================
        Stock List
    ===================== */
    public function list_stock(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'product_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $a_user_id = "";
        $user_type = "";
        if(isset($request->retailer_id)) {
            $a_user_id = $request->retailer_id;
            $user_type = "2";
        }
        if(isset($request->distributor_id)) {
            $a_user_id = $request->distributor_id;
            $user_type = "1";
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $query = DB::table('tbl_order_stock as s');
            $query->select('s.id', 's.fk_order_id', 's.stock_type', 's.quantity', 's.created_at', 'p.product_name');
            $query->join('tbl_products as p', 'p.id', '=', 's.fk_product_id', 'left');
            $query->where([['s.fk_company_id', '=', $fk_company_id], ['s.user_type', '=', $user_type], ['s.stock_by', '=', $a_user_id], ['s.fk_product_id', '=', $request->product_id]]);
            $query->orderBy('s.id', 'DESC');
            $stock_data = $query->get();
            if(count($stock_data) > 0) {
                foreach($stock_data as $data) {
                    $stock_type = "";
                    if($data->stock_type == "2") {
                        $stock_type = "Return";
                    }else if($data->stock_type == "1") {
                        $stock_type = "Sale";
                    }else {
                        $stock_type = "Order";
                    }
                    $date = "";
                    if(!empty($data->created_at)) {
                        $date = date('d M Y', strtotime($data->created_at));
                    }
                    array_push($returnData, array('fk_order_id' => $data->fk_order_id, 'stock_type' => $stock_type, 'quantity' => $data->quantity, 'product_name' => $data->product_name, 'date' => $date));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        Stock Details List by Retailer
    ===================== */
    public function list_single_retailer_stock_details(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'product_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $a_user_id = "";
        $user_type = "";
        if(isset($request->retailer_id)) {
            $a_user_id = $request->retailer_id;
            $user_type = "2";
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $query = DB::table('tbl_order_stock as s');
            $query->select('s.id', 's.fk_order_id', 's.stock_type', 's.quantity', 's.created_at', 'p.product_name');
            $query->join('tbl_products as p', 'p.id', '=', 's.fk_product_id', 'left');
            $query->where([['s.fk_company_id', '=', $fk_company_id], ['s.user_type', '=', $user_type], ['s.stock_by', '=', $a_user_id], ['s.fk_product_id', '=', $request->product_id]]);
            $query->orderBy('s.id', 'DESC');
            $stock_data = $query->get();
            if(count($stock_data) > 0) {
                foreach($stock_data as $data) {
                    $stock_type = "";
                    if($data->stock_type == "2") {
                        $stock_type = "Return";
                    }else if($data->stock_type == "1") {
                        $stock_type = "Sale";
                    }else {
                        $stock_type = "Order";
                    }
                    $date = "";
                    if(!empty($data->created_at)) {
                        $date = date('d M Y', strtotime($data->created_at));
                    }
                    array_push($returnData, array('fk_order_id' => $data->fk_order_id, 'stock_type' => $stock_type, 'quantity' => $data->quantity, 'product_name' => $data->product_name, 'date' => $date));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        Single Retailer Product List
    ===================== */
    public function single_retailer_list_products(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_products as p');
        $query->select('p.id', 'p.sku_id', 'p.erp_code', 'p.product_name', 'p.product_description', 'p.base_sale_price', 'c.name as category_name', 'cs.name as sub_category');
        $query->join('tbl_product_category as c', 'c.id', '=', 'p.category', 'left');
        $query->join('tbl_product_category as cs', 'cs.id', '=', 'p.sub_category', 'left');
        $query->where([['p.fk_company_id', '=', $fk_company_id], ['p.active', '=', "1"]]);
        if(!empty($request->product_name)) {
            $query->where([['p.product_name', 'like', '%' .$request->product_name.'%']]);
        }
        $query->orderBy('p.id', 'DESC');
        $product_data = $query->get();
        if(sizeof($product_data) > 0) {
            foreach($product_data as $p_data) {
                $default_image = "";
                $more_images = [];
                $select_images = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['Inactive', '=', '1']])->whereNull('default_image')->get()->toArray();
                if(sizeof($select_images) > 0) {
                    foreach($select_images as $imgdata) {
                        array_push($more_images, array('image' => url('/')."/public/product_image/".$imgdata['image_name']));
                    }
                }
                $select_default_image = Tbl_Product_Image::select('image_name')->where([['fk_product_id', '=', $p_data->id], ['default_image', '=', '1'], ['Inactive', '=', '1']])->get()->toArray();
                if(sizeof($select_default_image) >0) {
                    $default_image = url('/')."/public/product_image/".$select_default_image[0]['image_name'];
                }
                $product_price = $p_data->base_sale_price;
                $productTerms = [];
                $select_pTerms = Tbl_Product_Terms::select('fk_terms_id')->where([['fk_product_id', '=', $p_data->id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($select_pTerms) > 0) {
                    foreach($select_pTerms as $tdata) {
                        $productTerms[] =$tdata['fk_terms_id'];
                    }
                }
                if(sizeof($select_pTerms) > 0) {
                    $productPriceBreakups = $this->TotalPriceByFormula($p_data->base_sale_price, $productTerms, $p_data->id);
                    $product_price = $productPriceBreakups[0]['sales_price'];
                    $PriceBreakups = $productPriceBreakups;
                }
                if(!empty($request->retailer_id)) {
                    $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->retailer_id]])->get()->toArray();
                    if(sizeof($select_product_price) > 0) {
                        if(!empty($select_product_price[0]['base_sale_price'])) {
                            $product_price = $select_product_price[0]['base_sale_price'];
                        }
                    }
                }
                if(!empty($request->distributor_id)) {
                    $select_product_price = Tbl_Prices::select('base_sale_price')->where([['product_id', '=', $p_data->id], ['user_id', '=', $request->distributor_id]])->get()->toArray();
                    if(sizeof($select_product_price) > 0) {
                        if(!empty($select_product_price[0]['base_sale_price'])) {
                            $product_price = $select_product_price[0]['base_sale_price'];
                        }
                    }
                }
                array_push($returnData, array('id' => $p_data->id, 'sku_id' => $p_data->sku_id, 'erp_code' => $p_data->erp_code, 'product_name' => $p_data->product_name, 'product_description' => $p_data->product_description, 'product_price' => $product_price, 'category_name' => $p_data->category_name, 'sub_category' => $p_data->sub_category, 'default_image' => $default_image, 'product_images' => $more_images));
            }
        }
        if(count($product_data) > 0) {
            return response()->json(["status" => 1, "product_data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        Single Retailer Add Order
    ===================== */
    public function single_retailer_add_order(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'productData' => 'required|string',
            'quantityArray' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $totalAmount = 0;
        $productData = json_decode($request->productData,true);
        $quantityArray = json_decode($request->quantityArray,true);
        //return response()->json(["status" => 1, 'productData' => $productData, 'quantityArray' => $quantityArray]);
        //exit();
        $i= 0;
        foreach($productData as $pdata) {
            if($quantityArray[$i] > 0) {
                $totalAmount = $totalAmount + ($pdata['product_price'] * $quantityArray[$i]);
            }
            $i++;
        }
        // return response()->json(["status" => 1, 'data' => $totalAmount]);
        $data = Tbl_Order::create([
            'fk_company_id' => $fk_company_id,
            //'user_id' => $request->user_id,
            'user_type' => '2',
            'order_by' => $request->retailer_id,
            'total_amount' => $totalAmount,
            'status' => "0",
            'remarks' => $request->remarks
        ]);
        if($data) {
            $j= 0;
            foreach($productData as $pdata) {
                if($quantityArray[$j] > 0) {
                    $data2 = Tbl_Order_Details::create([
                        'fk_company_id' => $fk_company_id,
                        'fk_order_id' => $data->id,
                        'fk_product_id' => $pdata['id'],
                        'sku_id' => $pdata['sku_id'],
                        'erp_code' => $pdata['erp_code'],
                        'product_name' => $pdata['product_name'],
                        'product_description' => $pdata['product_description'],
                        'quantity' => $quantityArray[$j],
                        'product_price' => $pdata['product_price'] * $quantityArray[$j],
                        'created_by' => $request->retailer_id,
                        'user_type' => '2',
                        'order_by' => $request->retailer_id,
                        'Inactive' => "1"
                    ]);
                    $data3 = Tbl_Order_Stock::create([
                        'fk_company_id' => $fk_company_id,
                        'fk_order_id' => $data->id,
                        'fk_order_detail_id' => $data2->id,
                        'fk_product_id' => $pdata['id'],
                        'stock_type' => "0",
                        'quantity' => $quantityArray[$j],
                        'user_type' => '2',
                        'stock_by' => $request->retailer_id
                    ]);
                }
                $j++;
            }
            if($j == sizeof($productData)) {
                return response()->json(["status" => 1, 'msg' => 'Order proceed successful.']);
            }else {
                return response()->json(["status" => 0, 'msg' => 'Something is wrong.']);
            }
        }else {
            return response()->json(["status" => 0]);
        }
    }
}
