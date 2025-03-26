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
use App\Tbl_Company_Retailer_Type;
use App\Tbl_Retailer_Type;
use App\Tbl_Retailer_Sub_Type;
use App\Tbl_Company_Retailer_Sub_Type;
use App\Tbl_Retailers_Classification;
use App\Tbl_Retailers_Labels;
use App\Tbl_Retailers_Chain;
use App\Tbl_Retailers_Stage;
use App\Tbl_Routes;
use App\Tbl_States;
use App\Tbl_Retailers_Custom_Attribute;
use DB;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RetailerAttributesController extends Controller
{
    public function list_retailers_type(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $returnData = [];
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_retailer_type = Tbl_Retailer_Type::select('id', 'type')->where('Inactive', '!=', '2')->get();
            if(count($select_retailer_type) > 0) {
                $select_company_retailer_type = Tbl_Company_Retailer_Type::select('fk_type_id')->where('fk_company_id', $fk_company_id)->get();
                if(count($select_company_retailer_type) > 0) {
                    foreach($select_retailer_type as $t_data) {
                        $company_type_array = unserialize($select_company_retailer_type[0]->fk_type_id);
                        if(count($company_type_array) > 0) {
                            if(in_array($t_data->id, $company_type_array)) {
                                array_push($returnData, array('id' => $t_data->id, 'type' => $t_data->type));
                            }
                        }
                    }
                    return response()->json(["status" => 1, "data" => $returnData]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_subtype(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $returnData = [];
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_retailer_type = Tbl_Retailer_Sub_Type::select('id', 'type')->where('Inactive', '!=', '2')->get();
            if(count($select_retailer_type) > 0) {
                $select_company_retailer_type = Tbl_Company_Retailer_Sub_Type::select('fk_type_id')->where('fk_company_id', $fk_company_id)->get();
                if(count($select_company_retailer_type) > 0) {
                    foreach($select_retailer_type as $t_data) {
                        $company_type_array = unserialize($select_company_retailer_type[0]->fk_type_id);
                        if(count($company_type_array) > 0) {
                            if(in_array($t_data->id, $company_type_array)) {
                                array_push($returnData, array('id' => $t_data->id, 'type' => $t_data->type));
                            }
                        }
                    }
                    return response()->json(["status" => 1, "data" => $returnData]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_classification(Request $request) {
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
            $select_classification = Tbl_Retailers_Classification::select('id', 'class_name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_classification) > 0) {
                return response()->json(["status" => 1, "data" => $select_classification]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_labels(Request $request) {
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
            $select_labels = Tbl_Retailers_Labels::select('id', 'label_text')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_labels) > 0) {
                return response()->json(["status" => 1, "data" => $select_labels]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_chain(Request $request) {
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
            $select_chain = Tbl_Retailers_Chain::select('id', 'name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_chain) > 0) {
                return response()->json(["status" => 1, "data" => $select_chain]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_stage(Request $request) {
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
            $select_stage = Tbl_Retailers_Stage::select('id', 'stage_name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_stage) > 0) {
                return response()->json(["status" => 1, "data" => $select_stage]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_retailers_attributes(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $retailersType = [];
        $retailersSubType = [];
        $classificationData = [];
        $labelsData = [];
        $chainData = [];
        $stageData = [];
        $salesManagerData = [];
        $territoryManagerData = [];
        $routeData = [];
        $statesData = [];
        $CustomAttributes = [];
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_retailer_type = Tbl_Retailer_Type::select('id', 'type')->where('Inactive', '!=', '2')->get();
            if(count($select_retailer_type) > 0) {
                $select_company_retailer_type = Tbl_Company_Retailer_Type::select('fk_type_id')->where('fk_company_id', $fk_company_id)->get();
                if(count($select_company_retailer_type) > 0) {
                    foreach($select_retailer_type as $t_data) {
                        $company_type_array = unserialize($select_company_retailer_type[0]->fk_type_id);
                        if(count($company_type_array) > 0) {
                            if(in_array($t_data->id, $company_type_array)) {
                                array_push($retailersType, array('label' => $t_data->type, 'value' => $t_data->id));
                            }
                        }
                    }
                }
            }
            $select_retailer_subtype = Tbl_Retailer_Sub_Type::select('id', 'type')->where('Inactive', '!=', '2')->get();
            if(count($select_retailer_subtype) > 0) {
                $select_company_retailer_subtype = Tbl_Company_Retailer_Sub_Type::select('fk_type_id')->where('fk_company_id', $fk_company_id)->get();
                if(count($select_company_retailer_subtype) > 0) {
                    foreach($select_retailer_subtype as $t_data) {
                        $company_type_array = unserialize($select_company_retailer_subtype[0]->fk_type_id);
                        if(count($company_type_array) > 0) {
                            if(in_array($t_data->id, $company_type_array)) {
                                array_push($retailersSubType, array('label' => $t_data->type, 'value' => $t_data->id));
                            }
                        }
                    }
                }
            }
            $select_classification = Tbl_Retailers_Classification::select('id', 'class_name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_classification) > 0) {
                foreach($select_classification as $c_data) {
                    array_push($classificationData, array('label' => $c_data->class_name, 'value' => $c_data->id));
                }
            }
            $select_labels = Tbl_Retailers_Labels::select('id', 'label_text')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_labels) > 0) {
                foreach($select_labels as $l_data) {
                    array_push($labelsData, array('label' => $l_data->label_text, 'value' => $l_data->id));
                }
            }
            $select_chain = Tbl_Retailers_Chain::select('id', 'name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_chain) > 0) {
                foreach($select_chain as $c_data) {
                    array_push($chainData, array('label' => $c_data->name, 'value' => $c_data->id));
                }
            }
            $select_stage = Tbl_Retailers_Stage::select('id', 'stage_name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_stage) > 0) {
                foreach($select_stage as $s_data) {
                    array_push($stageData, array('label' => $s_data->stage_name, 'value' => $s_data->id));
                }
            }
            $select_sales_manager = Tbl_User_Master::select('id', 'name')->where([['fk_role_id', '2'], ['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_sales_manager) > 0) {
                foreach($select_sales_manager as $s_data) {
                    array_push($salesManagerData, array('label' => $s_data->name, 'value' => $s_data->id));
                }
            }
            $select_territory_manager = Tbl_User_Master::select('id', 'name')->where([['fk_role_id', '3'], ['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_territory_manager) > 0) {
                foreach($select_territory_manager as $t_data) {
                    array_push($territoryManagerData, array('label' => $t_data->name, 'value' => $t_data->id));
                }
            }
            $select_route_data = Tbl_Routes::select('id', 'name')->where([['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_route_data) > 0) {
                foreach($select_route_data as $r_data) {
                    array_push($routeData, array('label' => $r_data->name, 'value' => $r_data->id));
                }
            }
            $query = DB::table('tbl_company_master as c');
            $query->select('c.country');
            $query->join('tbl_user_master as u', 'u.fk_parent_id', '=', 'c.id', 'left');
            $query->where([['u.inactive', '!=', '2'], ['u.id', '=', $fk_company_id]]);
            $user_data = $query->get();
            if(count($user_data) > 0) {
                $select_state = Tbl_States::select('id', 'name')->where('country_id', $user_data[0]->country)->get();
                if(count($select_state) > 0) {
                    foreach($select_state as $s_data) {
                        array_push($statesData, array('label' => $s_data->name, 'value' => $s_data->id));
                    }
                }
            }
            $select_custom_attribute = Tbl_Retailers_Custom_Attribute::select('field_type', 'field_name', 'mobile_status', 'mandatory_field', 'attribute', 'options')->where([['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_custom_attribute) > 0) {
                foreach($select_custom_attribute as $r_data) {
                    $attribute = [];
                    if(!empty($r_data->attribute)) {
                        $attribute = unserialize($r_data->attribute);
                    }
                    $options = [];
                    if(!empty($r_data->options)) {
                        $options = unserialize($r_data->options);
                    }
                    array_push($CustomAttributes, array('field_type' => $r_data->field_type, 'field_name' => $r_data->field_name, 'field_name' => $r_data->field_name, 'mobile_status' => $r_data->mobile_status, 'mandatory_field' => $r_data->mandatory_field, 'attribute' => $attribute, 'options' => $options));
                }
            }
            return response()->json(["status" => 1, "retailers_type" => $retailersType, 'retailers_subtype' => $retailersSubType, 'classification' => $classificationData, 'labels' => $labelsData, 'chains' => $chainData, 'stages' => $stageData, 'sales_managers' => $salesManagerData, 'territory_managers' => $territoryManagerData, 'routes' => $routeData, 'states' => $statesData, 'custom_attributes' => $CustomAttributes]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
}
