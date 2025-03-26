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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserRoleController extends Controller {

/* ===========================
        User Role
    =========================== */
    public function user_role() {
        return \View::make("backend/user/user_role")->with(array());
    }

    // User Role dataTable
    public function list_user_role(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('user_role');
            $query->select('user_role_id','name', 'status', 'default_status');
            $query->where([['status', '!=', '2']]);
            if($keyword)
            {
                $sql = "name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('name', 'asc');
                else
                    $query->orderBy('name', 'desc');
            }
            else
            {
                $query->orderBy('user_role_id', 'DESC');
            }
            $query->orderBy('user_role_id', 'DESC');

            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="role-change-status" data-id="'.$query->user_role_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="role-change-status" data-id="'.$query->user_role_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $action = '';
                if($query->name != "Admin") {
                    $users = Users::where([['fk_user_role', '=', $query->user_role_id], ['status', '=', 'Active']])->get()->toArray();
                    if(sizeof($users) > 0) {
                        $action = '<a href="javascript:void(0)" class="edit-role" data-id="'.$query->user_role_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit User Role"><i class="fa fa-pencil"></i></button></a>';
                    }else {
                        if($query->default_status == 1) {
                            $action = '<a href="javascript:void(0)" class="edit-role" data-id="'.$query->user_role_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit User Role"><i class="fa fa-pencil"></i></button></a>';
                        }else {
                            $action = '<a href="javascript:void(0)" class="edit-role" data-id="'.$query->user_role_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit User Role"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-role" data-id="'.$query->user_role_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete User Role"><i class="fa fa-trash"></i></button></a>';
                        }
                    }
                }
                
                return $action;
            })
            ->rawColumns(['status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // User Role Modal
    public function add_user_role(){
        return \View::make("backend/user/user_role_form")->with([
        ])->render();
    }
    // User Role Insert/Update
    public function save_user_role(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=User_Role::where([['name', '=', $request->name], ['user_role_id', '!=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData=User_Role::where('user_role_id', $request->hidden_id)->update(['name' => $request->name]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "User Role Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "User Role Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=User_Role::where([['name', '=', $request->name], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $data = new User_Role;
                $data->name = $request->name;
                $data->status = "1";
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "User Role Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "User Role Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // User Role Edit 
    public function edit_user_role(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.user.user_role_form')->with([
                'user_roll_data' => User_Role::where([['user_role_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // User Role Delete 
    public function delete_user_role(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = User_Role::where('user_role_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // User Role Change Status
    public function change_user_role_status(Request $request){
        $res=User_Role::where('user_role_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful."];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    public function user_role_export(){
        $query = DB::table('user_role')->select('*')->orderBy('user_role_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Status');
        $rows = 2;
        foreach($query as $d2){
            $status = ($d2->status == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $d2->name);
            $sheet->setCellValue('B' . $rows, $status);
            $rows++;
        }
        $fileName = "user_role.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}