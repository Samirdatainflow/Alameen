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
use Helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserManagementController extends Controller {

    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     $main_menu_id=3;
        //     $sub_menu_id=4;
        //     $check_access=Helper::check_access($main_menu_id,$sub_menu_id);
        //     if(!$check_access)
        //     {
        //         return abort(404);
        //     }
        //     return $next($request);
        //  });
       
    }
    /* ===========================
        All User
    =========================== */
    public function index() {
        return \View::make("backend/user/all_user")->with(array());
    }
     // User Modal
    public function user_form(){
    	return \View::make("backend/user/user_form")->with([
            'user_roll_data' => User_Role::where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    // User Insert/Update
    public function save_user(Request $request){
        if(!empty($request->hidden_id)) {
            $saveData=Users::where([['user_id', '=', $request->hidden_id]])->update(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'gender' => $request->gender, 'date_of_birth' => date('Y-m-d', strtotime(str_replace('/', '-',$request->date_of_birth))), 'address' => $request->address, 'mobile' =>  $request->mobile, 'phone' => $request->phone, 'driving_licence' => $request->driving_licence, 'date_of_joining' => date('Y-m-d', strtotime(str_replace('/', '-',$request->date_of_joining))), 'experience' => $request->experience, 'status' => $request->status, 'user_type' => $request->user_type, 'fk_user_role' => $request->fk_user_role, 'updated_at' => date('Y-m-d H:i:s')]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "User Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "User Update failed! Something is wrong."];
            }
        }else {
            $selectUsername = Users::where([['username', '=', $request->username]])->get();
            if(count($selectUsername) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter username already taken, please try with another username."];
            }else {
                $selectEmail = Users::where([['email', '=', $request->email]])->get();
                if(count($selectEmail) > 0) {
                    $returnData = ["status" => 0, "msg" => "Enter email already taken, please try with another email."];
                }
                else
                {
                    $data = new Users;
                    $data->first_name = $request->first_name;
                    $data->last_name = $request->last_name;
                    $data->gender = $request->gender;
                    $data->date_of_birth = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_of_birth)));
                    $data->address = $request->address;
                    $data->mobile = $request->mobile;
                    $data->phone = $request->phone;
                    $data->driving_licence = $request->driving_licence;
                    $data->date_of_joining = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_of_joining)));
                    $data->experience = $request->experience;
                    $data->email = $request->email;
                    $data->username = $request->username;
                    $data->password = md5($request->password);
                    $data->status = $request->status;
                    $data->user_type = $request->user_type;
                    $data->fk_user_role = $request->fk_user_role;
                    $saveData = $data->save();
                    if($saveData) {
                        $returnData = ["status" => 1, "msg" => "User Save successful."];
                    }else {
                        $returnData = ["status" => 0, "msg" => "User Save failed! Something is wrong."];
                    }
                }
            	
            }
        }
    	return response()->json($returnData);
    }
    // User dataTable
    public function get_user(Request $request){
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $query = DB::table('users');
            $query->select('*');
            $query->where([['status', '=', 'Active']]);
            $query->where([['user_id', '!=', Session::get('user_id')]]);
            $keyword = $request->input('search.value');
            if($keyword)
            {
                $sql = "CONCAT(first_name, '', last_name) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('first_name', 'asc');
                else
                    $query->orderBy('first_name', 'desc');
            }
            else
            {
                $query->orderBy('user_id', 'DESC');
            }
            $query->orderBy('user_id', 'DESC');
            $datatable_array=Datatables::of($query)
                ->addColumn('name', function ($query) {
                    $name = '';
                    if(!empty($query->first_name)) {
                        $name .= $query->first_name . ' '.$query->last_name;
                    }
                    return $name;
                })
                ->addColumn('gender', function ($query) {
                    $gender = '';
                    if(!empty($query->gender)) {
                        $gender .= $query->gender=="m"?"Male":"Female";
                    }
                    return $gender;
                })
                ->addColumn('date_of_birth', function ($query) {
                    $date_of_birth = '';
                    if(!empty($query->date_of_birth)) {
                        $date_of_birth .= date("d M Y",strtotime($query->date_of_birth));
                    }
                    return $date_of_birth;
                })
                ->addColumn('address', function ($query) {
                    $address = '';
                    if(!empty($query->address)) {
                        $address .= $query->address;
                    }
                    return $address;
                })
                ->addColumn('mobile', function ($query) {
                    $mobile = '';
                    if(!empty($query->mobile)) {
                        $mobile .= $query->mobile;
                    }
                    return $mobile;
                })
                ->addColumn('email', function ($query) {
                    $email = '';
                    if(!empty($query->email)) {
                        $email .= $query->email;
                    }
                    return $email;
                })
                ->addColumn('status', function ($query) {
                    $status = '';
                    if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="change-status" data-id="'.$query->user_id.'" data-status="Inactive"><span class="badge badge-success">Active</span></a>';
                    }
                    return $status;
                })
                ->addColumn('user_type', function ($query) {
                    $user_type = '';
                    if(!empty($query->user_type)) {
                        $user_type .= $query->user_type=="A"?"Admin":"Employee";
                    }
                    return $user_type;
                })
                ->addColumn('user_role', function ($query) {
                    $user_role = '';
                    if(!empty($query->fk_user_role)) {
                        $selectRoll = User_Role::select('name')->where([['user_role_id', '=', $query->fk_user_role]])->get()->toArray();
                        if(count($selectRoll) > 0) {
                            if(!empty($selectRoll[0]['name'])) $user_role = $selectRoll[0]['name'];
                        }
                    }
                    return $user_role;
                })
                ->addColumn('action', function ($query) {
                    $action = '<a href="javascript:void(0)" class="edit-user" data-id="'.$query->user_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit User"><i class="fa fa-pencil"></i></button></a>';
                    return $action;
                })
                ->rawColumns(['name', 'gender', 'date_of_birth', 'address', 'mobile', 'email', 'status', 'user_type', 'user_role', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    public function change_user_status(Request $request){
        $res=Users::where('user_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful.", 'user_status' => $request->status];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    public function users_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/user/users_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
    }
    function csvToArrayWithAll($filename = '', $supplier = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $username_exist = 0;
                    $usernameData = Users::where([['username', '=', $row[11]]])->get()->toArray();
                    if(count($usernameData) > 0) {
                        $username_exist = 1;
                    }
                    $email_exist = 0;
                    $EmailData = Users::where([['email', '=', $row[10]]])->get()->toArray();
                    if(count($EmailData) > 0) {
                        $email_exist = 1;
                    }
                    $gender = "";
                    if($row[2] == "m" || $row[2] == "Male") {
                        $gender = "Male";
                    }
                    if($row[2] == "f" || $row[2] == "Female") {
                        $gender = "Female";
                    }
                    $user_type = "";
                    if($row[14] == "A" || $row[14] == "Admin") {
                        $user_type = "Admin";
                    }
                    if($row[14] == "S" || $row[14] == "Employee") {
                        $user_type = "Employee";
                    }
                    // $user_role_name = "";
                    // $User_Role = User_Role::select('name')->where([['user_role_id', '=', $row[15]], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($User_Role) > 0) {
                    //     if(!empty($User_Role[0]['name'])) $user_role_name = $User_Role[0]['name'];
                    // }
                    
                    $date_of_birth = "";
                    
                    if(!empty($row[3])) {
                        
                        $date_of_birth = date("Y-m-d", strtotime($row[3]));
                    }
                    
                    array_push($data, array('first_name' => $row[0], 'last_name' => $row[1], 'gender' => $gender, 'date_of_birth' => $date_of_birth, 'address' => $row[9], 'mobile' => $row[4], 'phone' => $row[5], 'username' => $row[11], 'email' => $row[10], 'password' => $row[12], 'user_type' => $user_type, 'fk_user_role' => $row[15], 'username_exist' => $username_exist, 'email_exist' => $email_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_users_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $UserArr = $this->csvToArray($file);
        foreach($UserArr['data'] as $data) {
            if($data['username_exist'] == "0" && $data['email_exist'] == "0" && $data['username'] !== "" && $data['email'] !== "") {
                $pdata = new Users;
                $pdata->first_name = $data['first_name'];
                $pdata->last_name = $data['last_name'];
                $pdata->gender = $data['gender'];
                $pdata->date_of_birth = $data['date_of_birth'];
                $pdata->driving_licence = $data['driving_licence'];
                $pdata->date_of_joining = $data['date_of_joining'];
                $pdata->experience = $data['experience'];
                $pdata->address = $data['address'];
                $pdata->mobile = $data['mobile'];
                $pdata->phone = $data['phone'];
                $pdata->username = $data['username'];
                $pdata->email = $data['email'];
                $pdata->password = md5($data['password']);
                $pdata->status = "Active";
                $pdata->user_type = $data['user_type'];
                $pdata->fk_user_role = $data['fk_user_role'];
                $pdata->save();
            }
            $flag++;
        }
        if($flag == sizeof($UserArr['data'])) {
            $returnData = ["status" => 1, "msg" => "Save successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Something is wrong."];
        }
        return response()->json($returnData);
    }
    function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $username_exist = 0;
                    $usernameData = Users::where([['username', '=', $row[11]]])->get()->toArray();
                    if(count($usernameData) > 0) {
                        $username_exist = 1;
                    }
                    $email_exist = 0;
                    $EmailData = Users::where([['email', '=', $row[10]]])->get()->toArray();
                    if(count($EmailData) > 0) {
                        $email_exist = 1;
                    }
                    
                    $date_of_birth = NULL;
                    
                    if(!empty($row[3])) {
                        
                        $date_of_birth = date("Y-m-d", strtotime($row[3]));
                    }
                    
                    $date_of_joining = NULL;
                    if(!empty($row[7])) {
                        
                        $date_of_joining = date("Y-m-d", strtotime($row[7]));
                    }
                    
                    $fk_user_role = NULL;
                    $User_Role = User_Role::select('user_role_id')->where([['name', '=', $row[15]], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($User_Role) > 0) {
                        if(!empty($User_Role[0]['user_role_id'])) $fk_user_role = $User_Role[0]['user_role_id'];
                    }
                    
                    $gender = "";
                    if($row[2] == "m" || $row[2] == "Male") {
                        $gender = "m";
                    }
                    if($row[2] == "f" || $row[2] == "Female") {
                        $gender = "f";
                    }
                    $user_type = "";
                    if($row[14] == "A" || $row[14] == "Admin") {
                        $user_type = "A";
                    }
                    if($row[14] == "S" || $row[14] == "Employee") {
                        $user_type = "S";
                    }
                    
                    array_push($data, array('first_name' => $row[0], 'last_name' => $row[1], 'gender' => $gender, 'date_of_birth' => $date_of_birth, 'mobile' => $row[4], 'phone' => $row[5], 'driving_licence' => $row[6], 'date_of_joining' => $date_of_joining, 'experience' => $row[8], 'address' => $row[9], 'email' => $row[10], 'username' => $row[11], 'password' => $row[12], 'user_type' => $user_type, 'fk_user_role' => $fk_user_role, 'username_exist' => $username_exist, 'email_exist' => $email_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function user_export() {
        $query = Users::where([['status', '=', 'Active'], ['user_id', '!=', Session::get('user_id')]])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Gender');
        $sheet->setCellValue('C1', 'DOB');
        $sheet->setCellValue('D1', 'Address');
        $sheet->setCellValue('E1', 'Mobile');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'User Type');
        $sheet->setCellValue('I1', 'User Role');
        
        $rows = 2;
        foreach($query as $td){
            $gender = $td['gender']=="m"?"Male":"Female";
            $user_type = $td['user_type']=="A"?"Admin":"Employee";
            $user_role = '';
            if(!empty($td['fk_user_role'])) {
                $User_Role = User_Role::select('name')->where([['user_role_id', '=', $td['fk_user_role']]])->get()->toArray();
                if(count($User_Role) > 0) {
                    if(!empty($User_Role[0]['name'])) $user_role = $User_Role[0]['name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['first_name']." ".$td['last_name']);
            $sheet->setCellValue('B' . $rows, $gender);
            $sheet->setCellValue('C' . $rows, date("d M Y",strtotime($td['date_of_birth'])));
            $sheet->setCellValue('D' . $rows, $td['address']);
            $sheet->setCellValue('E' . $rows, $td['mobile']);
            $sheet->setCellValue('F' . $rows, $td['email']);
            $sheet->setCellValue('G' . $rows, $td['status']);
            $sheet->setCellValue('H' . $rows, $user_type);
            $sheet->setCellValue('I' . $rows, $user_role);
            $rows++;
        }
        $fileName = "User.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}