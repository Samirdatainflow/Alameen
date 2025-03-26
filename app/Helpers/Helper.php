<?php
namespace App\Helpers;
use App\Tbl_User_Menu;
use App\Tbl_Menu_Management;
use App\Users;
use App\Role_Access;
use App\Products;
use App\Brand;
use App\ProductCategories;
use App\ProductSubCategory;
use App\PartBrand;
use App\PartName;
use App\CarManufacture;
use App\CarName;
use DB;
use Session;

class Helper {
    public static function user_name(){
        $user=Users::select('username')->where([['user_id', '=', Session::get('user_id')]])->get();
        return $user[0]->username;
    }
    public static function menuData() {
        $menuData = [];
        $select_menu = Tbl_Menu_Management::where([['fk_parent_id', '=', null], ['Inactive', '=', '1']])->get();
        if(count($select_menu) > 0) {
            foreach($select_menu as $m_data) {
                $SubmenuData = [];
                $sub_select_menu = Tbl_Menu_Management::where([['fk_parent_id', '=', $m_data->id], ['Inactive', '=', '1']])->get();
                if(count($sub_select_menu) > 0) {
                    foreach($sub_select_menu as $sm_data) {
                        array_push($SubmenuData, array('name' => $sm_data->name, 'id' => $sm_data->id, 'url_slug' => $sm_data->url_slug, 'last_segment' => $sm_data->last_segment));
                    }
                }
                array_push($menuData, array('parent_name' => $m_data->name, 'parent_id' => $m_data->id, 'icon' => $m_data->icon, 'sub_menu' => $SubmenuData));
            }
        }
        return $menuData;
    }
    public static function userMenu() {
        $userMenu = [];
        $select_user_menu = Tbl_User_Menu::select('menu_id')->where([['fk_user_id', '=', Session::get('user_id')]])->get()->toArray();
        if(sizeof($select_user_menu) > 0) {
            $userMenu = $select_user_menu;
        }
        return $userMenu;
    }
    public static function get_user_role_access() {
        
        $roleAccessData = [];
        
        if(!empty(Session::get('user_id'))) {
            
            $selectUserRole = Users::select('fk_user_role')->where([['user_id', '=', Session::get('user_id')]])->get()->toArray();
            
            if(count($selectUserRole) > 0) {
                
                if(!empty($selectUserRole[0]['fk_user_role'])) {
                    
                    $selectRoleAccess = Role_Access::select('parent_menu_id', 'submenu_id')->where([['fk_role_id', '=', $selectUserRole[0]['fk_user_role']]])->get()->toArray();
                    
                    if(count($selectRoleAccess) > 0) {
                        foreach($selectRoleAccess as $rData) {
                            array_push($roleAccessData, array('parent_menu_ids' => $rData['parent_menu_id'], 'submenu_ids' => $rData['submenu_id']));
                        }
                    }
                }
            }
        }
        return $roleAccessData;
    }
    public static function check_access($main_menu_id,$sub_menu_id){
        $selectUserRole = Users::select('fk_user_role')->where([['user_id', '=', Session::get('user_id')]])->get()->toArray();
        if(count($selectUserRole) > 0) {
            if(!empty($selectUserRole[0]['fk_user_role'])) {
                $selectRoleAccess = Role_Access::select('parent_menu_id', 'submenu_id')->where([['fk_role_id', '=', $selectUserRole[0]['fk_user_role']]])->get()->toArray();
                if(count($selectRoleAccess) > 0) {
                    if(in_array($main_menu_id, json_decode($selectRoleAccess[0]['parent_menu_id'],true))&&in_array($sub_menu_id, json_decode($selectRoleAccess[0]['submenu_id'],true)))
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function search_product_list($search_key){
        if($search_key != "") {
            $html = "";
            $query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->where('p.pmpno', 'like', '%' . $search_key . '%');
            $query->orWhere('pn.part_name', 'like', '%' . $search_key . '%');
            $productDatas = $query->get()->toArray();
            foreach($productDatas as $data)
            {
                $html .='<option value="'.$data->product_id.'">'.$data->part_name.' ('.$data->pmpno.')</option>';
            }
            //$productData = Products::where([['product_id', '=', $request->product_id]])->get()->toArray();
            // print_r($productData); exit();
            if(sizeof($productDatas) > 0) {
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function search_model_list($search_key){
        if($search_key != "")
        {
            $html = "";
            $modelDatas = Brand::take(40)->where('status',1)->where('brand_name', 'like', '%' . $search_key . '%')->select('brand_name','brand_id')->get()->toArray();
            foreach($modelDatas as $modelData)
            {
                $html .='<option value="'.$modelData['brand_id'].'">'.$modelData['brand_name'].'</option>';
            }
            if(sizeof($modelDatas) > 0) {
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function model_list_by_model_name($search_key) {
        $returnData = [];
        $arrayData = [];
        $modelDatas = Brand::take(40)->where('status',1)->where('brand_name', 'like', '%' . $search_key . '%')->select('brand_name','brand_id')->get()->toArray();
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
    public static function search_category_list($search_key){
        if($search_key != "")
        {
            $html = "";
            $categoryDatas = ProductCategories::take(40)->where([['status', '=',1], ['category_name', 'like', '%' . $search_key . '%']])->select('category_name','category_id')->get()->toArray();
            foreach($categoryDatas as $categoryData)
            {
                $html .='<option value="'.$categoryData['category_id'].'">'.$categoryData['category_name'].'</option>';
            }
            if(sizeof($categoryDatas) > 0) {
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function search_subcategory_list($search_key){
        if($search_key != "")
        {
            $html = "";
            $subcategoryDatas = ProductSubCategory::take(40)->where('status',1)->where('sub_category_name', 'like', '%' . $search_key . '%')->select('sub_category_name','sub_category_id')->get()->toArray();
            foreach($subcategoryDatas as $subcategoryData)
            {
                $html .='<option value="'.$subcategoryData['sub_category_id'].'">'.$subcategoryData['sub_category_name'].'</option>';
            }
            //$productData = Products::where([['product_id', '=', $request->product_id]])->get()->toArray();
            // print_r($productData); exit();
            if(sizeof($subcategoryDatas) > 0) {
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function part_brand_list_for_search_box($search_key) {
        if($search_key != "") {
            $html = "";
            $PartBrand = PartBrand::take(40)->where('status',1)->where('part_brand_name', 'like', '%' . $search_key . '%')->select('part_brand_name','part_brand_id')->get()->toArray();
            if(sizeof($PartBrand) > 0) {
                foreach($PartBrand as $data) {
                    $html .='<option value="'.$data['part_brand_id'].'">'.$data['part_brand_name'].'</option>';
                }
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function part_name_list_for_search_box($search_key) {
        if($search_key != "") {
            $html = "";
            $PartBrand = PartName::take(40)->where('status',1)->where('part_name', 'like', '%' . $search_key . '%')->select('part_name','part_name_id')->get()->toArray();
            if(sizeof($PartBrand) > 0) {
                foreach($PartBrand as $data) {
                    $html .='<option value="'.$data['part_name_id'].'">'.$data['part_name'].'</option>';
                }
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function car_manufacture_list_for_search_box($search_key) {
        if($search_key != "") {
            $html = "";
            $PartBrand = CarManufacture::take(40)->where('status',1)->where('car_manufacture', 'like', '%' . $search_key . '%')->select('car_manufacture','car_manufacture_id')->get()->toArray();
            if(sizeof($PartBrand) > 0) {
                foreach($PartBrand as $data) {
                    $html .='<option value="'.$data['car_manufacture_id'].'">'.$data['car_manufacture'].'</option>';
                }
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
    public static function car_name_list_for_search_box($search_key) {
        if($search_key != "") {
            $html = "";
            $CarName = CarName::take(40)->where('status',1)->where('car_name', 'like', '%' . $search_key . '%')->select('car_name','car_name_id')->get()->toArray();
            if(sizeof($CarName) > 0) {
                foreach($CarName as $data) {
                    $html .='<option value="'.$data['car_name_id'].'">'.$data['car_name'].'</option>';
                }
                $returnData = ["status" => 1, "data"=>$html];
            }else {
                $returnData = ["status" => 0, "msg" => " No records found."];
            }
        }
        else
        {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
}
?>