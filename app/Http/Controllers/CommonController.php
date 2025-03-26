<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Brand;
use App\Products;
use App\Helpers\Helper;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Plate;
use App\Place;
use App\PartBrand;
use App\Group;
use App\WmsUnit;
use App\Countries;
use App\Currency;
use App\ProductSubCategory;
use App\BinningLocationDetails;
use App\Location;

class CommonController extends Controller {

    public function get_model_by_model_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::model_list_by_model_name($request->search_key);
        }
        return response()->json($returnData);
    }
    // Get part brand
    public function get_part_brand_for_search_box(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::part_brand_list_for_search_box($request->search_key);
        }
        return response()->json($returnData);
    }
    // Get part name
    public function get_part_name_for_search_box(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::part_name_list_for_search_box($request->search_key);
        }
        return response()->json($returnData);
    }
    // Get car Manufacture
    public function get_car_manufacture_for_search_box(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::car_manufacture_list_for_search_box($request->search_key);
        }
        return response()->json($returnData);
    }
    // Get car Manufacture
    public function get_car_name_for_search_box(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::car_name_list_for_search_box($request->search_key);
        }
        return response()->json($returnData);
    }
    // Get Product
    public function get_product_list_for_search_box(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_product_list($request->search_key);
        }
        return response()->json($returnData);
    }
    public function get_part_no_by_search(Request $request) {
        $search_key = $request->search_key;
        $returnData = [];
        $arrayData = [];
        $modelDatas = Products::take(40)->where('is_deleted',0)->where('pmpno', 'like', '%' . $search_key . '%')->select('pmpno')->get()->toArray();
        if(sizeof($modelDatas) > 0) {
            foreach($modelDatas as $data) {
                array_push($arrayData, array('pmpno' => $data['pmpno']));
            }
            $returnData = ["status" => 1, "data"=>$arrayData];
        }else {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public function get_car_model_by_car_manufacture(Request $request) {
        $returnData = [];
        $arrayData = [];
        $modelDatas = Brand::where([['status', '=', '1'], ['car_manufacture_id', '=', $request->id]])->select('brand_id', 'brand_name')->get()->toArray();
        if(sizeof($modelDatas) > 0) {
            foreach($modelDatas as $data) {
                array_push($arrayData, array('brand_id' => $data['brand_id'], 'brand_name' => $data['brand_name']));
            }
            $returnData = ["status" => 1, "data"=>$arrayData];
        }else {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public function get_count_of_cart_product(Request $request) {
        $cart_data = $request->cookie('cart_data');
        $data = json_decode($cart_data,true);
        //print_r($data); exit();
        echo sizeof($data);
    }
    // Get Zone By Location
    public function get_zone_by_location(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ZoneMaster = ZoneMaster::select('zone_id', 'zone_name')->where([['status', '=', '1'], ['location_id', '=', $request->id]])->orderBy('zone_id', 'desc')->get()->toArray();
            if(sizeof($ZoneMaster) > 0) {
                foreach($ZoneMaster as $data) {
                    array_push($returnData, array('zone_id' => $data['zone_id'], 'zone_name' => $data['zone_name']));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Get Row By Zone
    public function get_row_by_zone(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Row = Row::select('row_id', 'row_name')->where([['status', '=', '1'], ['zone_id', '=', $request->id]])->orderBy('row_id', 'desc')->get()->toArray();
            if(sizeof($Row) > 0) {
                foreach($Row as $data) {
                    array_push($returnData, array('row_id' => $data['row_id'], 'row_name' => $data['row_name']));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Get Rack By Row
    public function get_rack_by_row(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Rack = Rack::select('rack_id', 'rack_name')->where([['status', '=', '1'], ['row_id', '=', $request->id]])->orderBy('rack_id', 'desc')->get()->toArray();
            if(sizeof($Rack) > 0) {
                foreach($Rack as $data) {
                    array_push($returnData, array('rack_id' => $data['rack_id'], 'rack_name' => $data['rack_name']));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Get Plate By Rack
    public function get_plate_by_rack(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Plate = Plate::select('plate_id', 'plate_name')->where([['status', '=', '1'], ['rack_id', '=', $request->id]])->orderBy('plate_id', 'desc')->get()->toArray();
            if(sizeof($Plate) > 0) {
                foreach($Plate as $data) {
                    array_push($returnData, array('plate_id' => $data['plate_id'], 'plate_name' => $data['plate_name']));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Get Place By Plate
    public function get_place_by_plate(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Plate = Place::select('place_id', 'place_name')->where([['status', '=', '1'], ['plate_id', '=', $request->id]])->orderBy('place_id', 'desc')->get()->toArray();
            if(sizeof($Plate) > 0) {
                foreach($Plate as $data) {
                    array_push($returnData, array('place_id' => $data['place_id'], 'place_name' => $data['place_name']));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    public function save_part_brand(Request $request){
        $selectData = PartBrand::where([['part_brand_name', '=', $request->part_brand_name], ['status', '!=', '2']])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
        }else {
            $data = new PartBrand;
            $data->part_brand_name = $request->part_brand_name;
            $data->status = "1";
            $saveData= $data->save();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_item_group(Request $request){
        $selectData=Group::where([['group_name', '=', $request->group_name], ['status', '=', '1']])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter Group name already exist. Please try with another Group name."];
        }else {
            $data = new Group;
            $data->group_name = $request->group_name;
            $data->status = "1";
            $saveData = $data->save();
            if($saveData) {
                $Group = Group::where('status',1)->orderBy('group_id', 'DESC')->get()->toArray();
                $returnData = ["status" => 1, "msg" => "Group Save successful.", 'data' => $Group];
            }else {
                $returnData = ["status" => 0, "msg" => "Group Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_item_brand(Request $request){
        $selectData=Brand::where([['Brand_name', '=', $request->Brand_name], ['car_manufacture_id', '=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Model name already exist. Please try with another Brand name."];
        }else {
            $data = new Brand;
            $data->car_manufacture_id = $request->hidden_id;
            $data->Brand_name = $request->Brand_name;
            $data->status = "1";
            $saveData = $data->save();
            if($saveData) {
                $Brand = Brand::orderBy('brand_id', 'desc')->limit('1')->get()->toArray();
                $returnData = ["status" => 1, "msg" => "Model Save successful.", "data" => $Brand];
            }else {
                $returnData = ["status" => 0, "msg" => "Model Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_config_unit(Request $request){
        $selectData=WmsUnit::where([['unit_name', '=', $request->unit_name]])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter Units name already exist. Please try with another Units name."];
        }else {
            $data = new WmsUnit;
            $data->unit_name = $request->unit_name;
            $data->unit_type = $request->unit_type;
            $data->base_factor = $request->base_factor;
            $data->base_measurement_unit = $request->base_measurement_unit;
            $saveData= $data->save();
            if($saveData) {
                $WmsUnit = WmsUnit::orderBy('unit_id', 'DESC')->get()->toArray();
                $returnData = ["status" => 1, "msg" => "Units Save successful.", "data" => $WmsUnit];
            }else {
                $returnData = ["status" => 0, "msg" => "Units Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_config_countries(Request $request){
        $selectData = Countries::where([['country_code', '=', $request->country_code]])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
        }else {
            $data = new Countries;
            $data->country_code = $request->country_code;
            $data->country_name = $request->country_name;
            $saveData= $data->save();
            if($saveData) {
                $Countries = Countries::where('status', '0')->orderBy('country_id', 'DESC')->get()->toArray();
                $returnData = ["status" => 1, "msg" => "Countries Save successful.", 'data' => $Countries];
            }else {
                $returnData = ["status" => 0, "msg" => "Countries Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_config_currency(Request $request){
        $selectData=Currency::where([['currency_code', '=', $request->currency_code], ['status', '!=', '2']])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter Currency Code already exist. Please try with another Currency Code."];
        }else {
            $data = new Currency;
            $data->currency_code = $request->currency_code;
            $data->currency_description = $request->currency_description;
            $data->status = "1";
            $saveData= $data->save();
            if($saveData) {
                $Currency = Currency::where('status', '1')->orderBy('currency_id', 'DESC')->get()->toArray();
                $returnData = ["status" => 1, "msg" => "Currency Save successful.", "data" => $Currency];
            }else {
                $returnData = ["status" => 0, "msg" => "Currency Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function save_item_sub_category(Request $request){
        $selectData = ProductSubCategory::where([['sub_category_name', '=', $request->sub_category_name], ['category_id', '=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
        if(count($selectData) > 0) {
            $returnData = ["status" => 0, "msg" => "Enter Sub Category name already exist. Please try with another Sub Category name."];
        }else {
            $data = new ProductSubCategory;
            $data->category_id = $request->hidden_id;
            $data->sub_category_name = $request->sub_category_name;
            $data->status = "1";
            $saveData= $data->save();
            $ProductSubCategory = ProductSubCategory::orderBy('sub_category_id', 'desc')->limit('1')->get()->toArray();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Sub Category Save successful.", 'data' => $ProductSubCategory];
            }else {
                $returnData = ["status" => 0, "msg" => "Sub Category Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function get_capacity_by_place(Request $request) {
        $max_capacity = "";
        $newLine = "";
        $Location = Location::select('location_id', 'location_name')->where([['is_deleted', '=', 0]])->get()->toArray();
        $selectData = Place::select('max_capacity')->where([['place_id', '=', $request->id]])->get()->toArray();
        if(sizeof($selectData) > 0) {
            if(!empty($selectData[0]['max_capacity'])) $max_capacity = $selectData[0]['max_capacity'];
        }
        $BinningLocationDetails = BinningLocationDetails::select('quantity')->where([['product_id', '!=', $request->product_id], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '=', $request->rack_id], ['plate_id', '=', $request->plate_id], ['place_id', '=', $request->place_id]])->get()->toArray();
        $accep_quantity = $request->quantity;
        $rest_quantity = "";
        if($request->quantity > $max_capacity) {
            $accep_quantity = $max_capacity;
            $rest_quantity = $request->quantity - $max_capacity;
            $newLine .= '<tr>
                <td><input type="hidden" class="product-id" name="product_id[]" value="'.$request->product_id.'"><input type="text" class="form-control part-name" value="'.$request->part_name.'" readonly></td>
                <td><input type="text" class="form-control pmpno" value="'.$request->pmpno.'" readonly></td>
                <td><input type="number" class="form-control quantity" name="quantity[]" value="'.$rest_quantity.'" readonly></td>
                <td>
                    <select class="form-control location-id" name="location_id[]">
                        <option value="">Select</option>';
                        if(!empty($Location)) {
                            foreach($Location as $ldata) {
                            $newLine.='<option value="'.$ldata['location_id'].'">'.$ldata['location_name'].'</option>';
                            }
                        }
                    $newLine.='</select>
                </td>
                <td>
                    <select class="form-control zone-id" name="zone_id[]">
                        <option value="">Select</option>
                    </select>
                </td>
                <td>
                    <select class="form-control row-id" name="row_id[]">
                        <option value="">Select</option>
                    </select>
                </td>
                <td>
                    <select class="form-control rack-id" name="rack_id[]">
                        <option value="">Select</option>
                    </select>
                </td>
                <td>
                    <select class="form-control plate-id" name="plate_id[]">
                        <option value="">Select</option>
                    </select>
                </td>
                <td>
                    <select class="form-control place-id" name="place_id[]">
                        <option value="">Select</option>
                    </select>
                    <input type="hidden" class="hidden-position" name="hidden_position" value="">
                </td>
                <td>
                    <span class="max-capacity">
                    </span>
                </td>
                <td>
                    <span class="remaining-capacity">
                    </span>
                </td>
            </tr>';
        }
        if(sizeof($BinningLocationDetails) > 0) {
            $returnData = ['status' => 0, 'msg' => 'Enter location already exist / capacity not available in this location!'];
        }else {
            $returnData = ['status' => 1, 'max_capacity' => $max_capacity, 'newLine' => $newLine, 'accep_quantity' => $accep_quantity];
        }
        return response()->json($returnData);
    }
}