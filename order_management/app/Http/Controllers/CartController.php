<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\ClientOrder;
use App\Clients;
use App\ClientOrderDetails;
use App\Products;
use Cookie;
class CartController extends Controller {
   
    public function cart_item(Request $request)
    {
        //Cookie::queue(Cookie::forget('cart_data'));
        $previous_data=[];
        $cart_data = $request->cookie('cart_data');
        if(isset($cart_data) && !empty($cart_data))
        {
            $cart_data_array=json_decode($cart_data,true);
            foreach ($cart_data_array as $key => $value) {
                $res=$this->product_details_by_id($value['product_id']);
                $res['qty']=$value['qty'];
                $previous_data[]=$res;
            }
        }
    	return \View::make("backend/cart/cart")->with(array('cart_datas'=>$previous_data));
    }
    public function product_details_by_id($product_id){
        $query = DB::table('products');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct', 'left');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name');
        $query->where('products.product_id', '=', $product_id);
        $data=$query->get()->toArray();
        $model = new DB;
        $available_stock = available_stock($model,$product_id);
        $data_array=array('product_id'=>$data[0]->product_id,'part_name'=>$data[0]->part_name,'pmpno'=>$data[0]->pmpno,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>$data[0]->pmrprc,'current_stock'=>$available_stock);
        return $data_array;
    }

    public function update_cart(Request $request){
      $product_id = $request->product_id;
      $qty = $request->qty;
      $cart_data = $request->cookie('cart_data');
      $data=[];
      if($cart_data)
      {
        $data=json_decode($cart_data,true);
        foreach($data as $key=>$val)
        {
            if($val['product_id'] == $product_id)
            {
                $product_details = $this->product_details_by_id($product_id);
                if($product_details['current_stock'] >= $qty)
                {
                    $data[$key]['qty'] = $qty;
                }
                else
                {
                    $returnData = ["status" => 0, "msg" => "This quantity is not available"];
                    return response()->json($returnData);
                }
                break;
            }
            
        }
      }
      $minutes = 60*24;
      $cookie = cookie('cart_data', json_encode($data), $minutes);
      return response(json_encode(array('status'=>1)))->cookie($cookie);
    }

    public function delete_cart_item(Request $request){
      $product_id = $request->product_id;
      $qty = $request->qty;
      $cart_data = $request->cookie('cart_data');
      $data=[];
      if($cart_data)
      {
        $data=json_decode($cart_data,true);
        foreach($data as $key=>$val)
        {
            if($val['product_id'] == $product_id)
            {
                unset($data[$key]);
                break;
            }
            
        }
      }
      $minutes = 60*24;
      $cookie = cookie('cart_data', json_encode($data), $minutes);
      return response(json_encode(array('status'=>1)))->cookie($cookie);
    }
}