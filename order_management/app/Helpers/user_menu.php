<?php
function userMenu() {
    $menuData = [];
    $select_menu = Tbl_Menu_Management::where([['fk_parent_id', '=', null], ['Inactive', '=', '1']])->get();
    if(count($select_menu) > 0) {
        foreach($select_menu as $m_data) {
            $SubmenuData = [];
            $sub_select_menu = Tbl_Menu_Management::where([['fk_parent_id', '=', $m_data->id], ['Inactive', '=', '1']])->get();
            if(count($sub_select_menu) > 0) {
                foreach($sub_select_menu as $sm_data) {
                    array_push($SubmenuData, array('name' => $sm_data->name, 'id' => $sm_data->id, 'url_slug' => $sm_data->url_slug));
                }
            }
            array_push($menuData, array('parent_name' => $m_data->name, 'parent_id' => $m_data->id, 'icon' => $m_data->icon, 'sub_menu' => $SubmenuData));
        }
    }
    $userMenu = [];
    $select_user_menu = Tbl_User_Menu::select('menu_id')->where([['fk_user_id', '=', Session::get('user_id')]])->get()->toArray();
    if(sizeof($select_user_menu) > 0) {
        $userMenu = $select_user_menu;
    }
}
?>