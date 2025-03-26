<?php

namespace App\Http\Controllers\API\v1;

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\SaleOrder;
use App\Deliveries;
//use App\DeliveryManagement;
//use Hash;
//use JWTAuth;
//use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class DashboardController extends Controller
{
	public function dashboard_data(Request $request) {
		$validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        //$returnData = [];
        $SaleOrder = SaleOrder::where([['delete_status', '=', '0'],['client_id','=',$request->user_id]])->count();
        $ApproveOrder = SaleOrder::where([['delete_status', '=', '0'], ['is_approved', '=', '1'],['client_id','=',$request->user_id]])->count();
        $RejectOrder = SaleOrder::where([['delete_status', '=', '0'], ['is_rejected', '=', '1'],['client_id','=',$request->user_id]])->count();
        $Deliveries = DB::table('shipping as s')->join('delivery_management as dm', 'dm.shipping_id', '=', 's.shipping_id', 'left')->where([['s.status','=',1], ['s.client_id','=',$request->user_id]])->count();
        $PendingShipment = DB::table('packing as p')->join('sale_order as so', 'p.sale_order_id', '=', 'so.sale_order_id', 'left')->join('shipping as sh', 'sh.sale_order_id', '=', 'p.sale_order_id', 'left')->where([['so.order_status','=',1], ['so.client_id','=',$request->user_id]])->whereNull('sh.sale_order_id')->count();
        return response()->json(["status" => 1, 'SaleOrder' => $SaleOrder, 'ApproveOrder' => $ApproveOrder, 'RejectOrder' => $RejectOrder, 'Deliveries' => $Deliveries, 'PendingShipment' => $PendingShipment]);
	}
}