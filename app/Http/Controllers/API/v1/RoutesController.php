<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Company_Master;
use App\Tbl_Role;
use App\Tbl_Retailers;
use App\Tbl_States;
use App\Tbl_Routes;
use App\Tbl_Retailers_Route;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class RoutesController extends Controller
{
    public function list_routes(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_route_data = Tbl_Routes::select('id', 'name')->where([['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_route_data) > 0) {
                return response()->json(["status" => 1, "data" => $select_route_data]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function routes_retailers(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $routeRetailers = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_route_data = Tbl_Routes::select('id', 'name')->where([['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_route_data) > 0) {
                $no_of_retailers = 0;
                foreach($select_route_data as $r_data) {
                    $select_retailers_route = Tbl_Retailers_Route::select('fk_retailers_id')->where([['fk_route_id', '=', $r_data['id']], ['Inactive', '=', '1'], ['fk_company_id', '=', $fk_company_id]])->orderBy('id', 'DESC')->get();
                    $retailersData = [];
                    if(count($select_retailers_route) > 0) {
                        $no_of_retailers += count($select_retailers_route);
                        foreach($select_retailers_route as $rr_data) {
                            $select_retailers = Tbl_Retailers::select('id', 'name', 'street', 'locality', 'retailer_image')->where([['id', '=', $rr_data['fk_retailers_id']], ['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
                            if(count($select_retailers) > 0) {
                                $street = '';
                                if(!empty($select_retailers[0]['street'])) {
                                    $street = $select_retailers[0]['street'];
                                }
                                $locality = '';
                                if(!empty($select_retailers[0]['locality'])) {
                                    $locality = $select_retailers[0]['locality'];
                                }
                                $retailer_image = '';
                                if(!empty($select_retailers[0]['retailer_image'])) {
                                    //$retailer_image = url('/').'/public/retailers/images/'.$select_retailers[0]['retailer_image'];
                                    $retailer_image = $select_retailers[0]['retailer_image'];
                                }
                                array_push($retailersData, array('id' => $select_retailers[0]['id'], 'name' => $select_retailers[0]['name'], 'retailer_image' => $retailer_image, 'street' => $street, 'locality' => $locality, 'distance' => '0KM'));
                            }
                        }
                    }
                    array_push($routeRetailers, array('id' => $r_data['id'], 'name' => $r_data['name'], 'retailers' => $retailersData));
                }
                return response()->json(["status" => 1, "data" => $routeRetailers, 'total_retailers' => $no_of_retailers]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function filter_routes_retailers(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_name' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $routeRetailers = [];
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $no_of_retailers = 0;
            $retailersData = [];
            $filterRetailers = [];
            $query = DB::table('tbl_retailers');
            $query->select('id', 'name', 'street', 'locality', 'retailer_image');
            $query->where([['name', 'like', '%' .$request->retailer_name.'%'], ['Inactive', '=', '1']]);
            $query->orderBy('id', 'DESC');
            $retailer_data = $query->get();
            if(count($retailer_data) > 0) {
                foreach($retailer_data as $r_data) {
                    $street = '';
                    if(!empty($r_data->street)) {
                        $street = $r_data->street;
                    }
                    $locality = '';
                    if(!empty($r_data->locality)) {
                        $locality = $r_data->locality;
                    }
                    $retailer_image = '';
                    if(!empty($r_data->retailer_image)) {
                        $retailer_image = $r_data->retailer_image;
                    }
                    $route_name = '';
                    $selectRetailersRoute = Tbl_Retailers_Route::select('fk_route_id')->where([['fk_retailers_id', '=', $r_data->id], ['Inactive', '=', '1']])->get();
                    if(count($selectRetailersRoute) > 0) {
                        $fk_route_id = $selectRetailersRoute[0]->fk_route_id;
                        $selectRoute = Tbl_Routes::select('name', 'id')->where([['id', '=', $fk_route_id], ['inactive', '=', '1'], ['fk_company_id', '=', $fk_company_id]])->get();
                        if(count($selectRoute) > 0) {
                            $route_name = $selectRoute[0]->name;
                        }
                    }
                    if(!empty($route_name)) {
                        $no_of_retailers ++;
                        array_push($retailersData, array('id' => $r_data->id, 'name' => $r_data->name, 'retailer_image' => $retailer_image, 'street' => $street, 'locality' => $locality, 'distance' => '0KM', 'route_id' => $selectRoute[0]->id, 'route_name' => $selectRoute[0]->name));
                    }
                }
            }
            $result = [];
            foreach ($retailersData as $element) {
                $result[$element['route_id']][] = $element;
            }
            foreach($result as $k=>$v) {
                $selectRoute = Tbl_Routes::select('name', 'id')->where([['id', '=', $k]])->get();
                if(count($selectRoute) > 0) {
                    array_push($filterRetailers, array('id' => $k, 'name' => $selectRoute[0]->name, 'retailers' => $v));
                }
            }
            return response()->json(["status" => 1, "data" => $filterRetailers, 'total_retailers' => $no_of_retailers]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    function is_in_array($array, $key, $key_value){
        $within_array = 'no';
        foreach( $array as $k=>$v ){
            if( is_array($v) ){
                $within_array = $this->is_in_array($v, $key, $key_value);
                if( $within_array == 'yes' ){
                    break;
                }
            } else {
                    if( $v == $key_value && $k == $key ){
                            $within_array = 'yes';
                            break;
                    }
            }
        }
        return $within_array;
    }
    
}
