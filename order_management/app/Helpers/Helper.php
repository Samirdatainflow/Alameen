<?php
namespace App\Helpers;
use App\Tbl_User_Menu;
use App\Tbl_Menu_Management;
use App\Brand;
use DB;
use Session;

class Helper {
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
}
?>