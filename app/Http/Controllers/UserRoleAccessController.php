<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\User_Role;
use App\Menu;
use App\Role_Access;
use DB;
use DataTables;
use App\Warehouses;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class UserRoleAccessController extends Controller {

	/* ===========================
        User Role Acces
    =========================== */
    public function role_access(){
        return \View::make("backend/user/role_access")->with([]);
    }
    // Role Access Modal
    public function add_role_access(Request $request){
        if ($request->ajax()) {
            $roleData = [];
            // $selectRoleData = User_Role::where([['status', '=', '1']])->get()->toArray();
            // if(count($selectRoleData) > 0) {
            //     foreach($selectRoleData as $rData) {
            //         $selectRoleAccess = Role_Access::where([['fk_role_id', '=', $rData['user_role_id']]])->get()->toArray();
            //         if(count($selectRoleAccess) < 1) {
            //             array_push($roleData, array('user_role_id' => $rData['user_role_id'], 'name' => $rData['name']));
            //         }
            //     }
            // }
            $menuData = [];
            $selectMenu = Menu::select('menu_id', 'name')->where([['fk_parent_id', '=', null]])->get()->toArray();
            if(sizeof($selectMenu) > 0) {
                foreach($selectMenu as $menu) {
                    $submenuData = [];
                    $selectSubmenu = Menu::select('menu_id', 'name')->where([['fk_parent_id', '=', $menu['menu_id']]])->get()->toArray();
                    if(sizeof($selectSubmenu) > 0) {
                        foreach($selectSubmenu as $submenu) {
                            array_push($submenuData, array('menu_id' => $submenu['menu_id'], 'name' => $submenu['name']));
                        }
                    }
                    array_push($menuData, array('menu_id' => $menu['menu_id'], 'name' => $menu['name'], 'submenu_data' => $submenuData));
                }
            }
            $html = view('backend.user.role_access_form')->with([
                'user_role_data' => User_Role::select('user_role_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
                'menu_data' => $menuData,
                'warehouse_data' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Role Access Insert/Update
    public function save_user_role_access(Request $request) {
        $returnData = [];
        $parent_menu_id = "";
        $submenu_id = "";
        if(!empty($request->menu)) {
            $parent_menu_id = json_encode($request->menu);
        }
        if(!empty($request->submenu)) {
            $submenu_id = json_encode($request->submenu);
        }
        if(!empty($request->hidden_id)) {
            $selectRole_Access = Role_Access::where([['role_access_id', '!=', $request->hidden_id], ['fk_role_id', '=', $request->fk_role_id], ['warehouse_id', '=', $request->warehouse_id]])->get()->toArray();
            if(sizeof($selectRole_Access) > 0) {
                $returnData = ["status" => 0, "msg" => "Select Role ID already exist! Please try with another Role ID."];
            }else {
                $saveData = Role_Access::where([['role_access_id', '=', $request->hidden_id]])->update(['fk_role_id' => $request->fk_role_id, 'warehouse_id' => $request->warehouse_id, 'parent_menu_id' => $parent_menu_id, 'submenu_id' => $submenu_id]);
                if($saveData) {
                $returnData = ["status" => 1, "msg" => "Role Access update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Role Access update failed! Something is wrong."];
                }
            }
        }else {
            $selectRole_Access = Role_Access::where([['fk_role_id', '=', $request->fk_role_id], ['warehouse_id', '=', $request->warehouse_id]])->get()->toArray();
            if(sizeof($selectRole_Access) > 0) {
                $returnData = ["status" => 0, "msg" => "Select Role ID already exist! Please try with another Role ID."];
            }else {
                $data = new Role_Access;
                $data->fk_role_id = $request->fk_role_id;
                $data->warehouse_id = $request->warehouse_id;
                $data->parent_menu_id = $parent_menu_id;
                $data->submenu_id = $submenu_id;
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Role Access save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Role Access save failed! Something is wrong."];
                }
            }
        }
        // $selectData = Role_Access::where([['fk_role_id', '=', $request->user_role_id]])->get()->toArray();
        // if(count($selectData) > 0) {
        //     $parent_menu_id = "";
        //     $submenu_id = "";
        //     if(!empty($request->menu)) {
        //         $parent_menu_id = json_encode($request->menu);
        //     }
        //     if(!empty($request->submenu)) {
        //         $submenu_id = json_encode($request->submenu);
        //     }
        //     $saveData = Role_Access::where([['fk_role_id', '=', $request->user_role_id]])->update(array('warehouse_id' => $request->warehouse_id, 'parent_menu_id' => $parent_menu_id, 'submenu_id' => $submenu_id));
        //     if($saveData) {
        //         $returnData = ["status" => 1, "msg" => "User Role Access Update successful."];
        //     }else {
        //         $returnData = ["status" => 0, "msg" => "User Role Access Update failed! Something is wrong."];
        //     }
        // }else {
        //     $data = new Role_Access;
        //     $data->warehouse_id = $request->warehouse_id;
        //     $data->fk_role_id = $request->user_role_id;
        //     if(!empty($request->menu)) {
        //         $data->parent_menu_id = json_encode($request->menu);
        //     }
        //     if(!empty($request->submenu)) {
        //         $data->submenu_id = json_encode($request->submenu);
        //     }
        //     $saveData = $data->save();
        //     if($saveData) {
        //         $returnData = ["status" => 1, "msg" => "User Role Access Save successful."];
        //     }else {
        //         $returnData = ["status" => 0, "msg" => "User Role Access Save failed! Something is wrong."];
        //     }
        // }
        return response()->json($returnData);
    }
    // Role Access dateTable
    public function list_role_access(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('role_access as a');
            $query->join('user_role as r', 'r.user_role_id', '=', 'a.fk_role_id');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'a.warehouse_id');
            $query->select('a.role_access_id', 'r.name as role_name', 'w.name as warehouse_name');
            if($keyword)
            {
                $sql = "r.name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('r.name', 'asc');
                else
                    $query->orderBy('r.name', 'desc');
            }
            else
            {
                $query->orderBy('a.role_access_id', 'DESC');
            }
            //$query->orderBy('user_role_id', 'DESC');
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action="";
                //if($query->user_role_id !="1")
                $action = '<a href="javascript:void(0)" class="view-user-role-access" data-id="'.$query->role_access_id.'" data-name="'.$query->role_name.'"><button type="button" class="btn btn-primary btn-sm" title="View User Role"><i class="fa fa-eye"></i></button></a>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // export
    public function role_access_export()
    {
        $query = DB::table('role_access as a')
        ->select('a.role_access_id', 'r.name as role_name', 'w.name as warehouse_name')
        ->join('user_role as r', 'r.user_role_id', '=', 'a.fk_role_id')
        ->join('warehouses as w', 'w.warehouse_id', '=', 'a.warehouse_id')
        ->orderBy('a.role_access_id', 'DESC');
         $data = $query->get()->toArray();

        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Role');
        $sheet->setCellValue('B1', 'Warehouse');
        

        $rows = 2;
        foreach($data as $empDetails){
            
            $sheet->setCellValue('A' . $rows, $empDetails->role_name);
            $sheet->setCellValue('B' . $rows, $empDetails->warehouse_name);
            
            $rows++;
        }
        $fileName = "Role_Access.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function view_user_role_access(Request $request) {
        if ($request->ajax()) {
            $menuData = [];
            $selectMenu = Menu::select('menu_id', 'name')->where([['fk_parent_id', '=', null]])->get()->toArray();
            if(sizeof($selectMenu) > 0) {
                foreach($selectMenu as $menu) {
                    $submenuData = [];
                    $selectSubmenu = Menu::select('menu_id', 'name')->where([['fk_parent_id', '=', $menu['menu_id']]])->get()->toArray();
                    if(sizeof($selectSubmenu) > 0) {
                        foreach($selectSubmenu as $submenu) {
                            array_push($submenuData, array('menu_id' => $submenu['menu_id'], 'name' => $submenu['name']));
                        }
                    }
                    array_push($menuData, array('menu_id' => $menu['menu_id'], 'name' => $menu['name'], 'submenu_data' => $submenuData));
                }
            }
            $html = view('backend.user.role_access_form')->with([
                'user_role_data' => User_Role::select('user_role_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
                'menu_data' => $menuData,
                'warehouse_data' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
                'role_access_data' => Role_Access::where([['role_access_id', '=', $request->id]])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
}