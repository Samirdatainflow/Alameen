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
class InactiveUserController extends Controller {
   
    /* ===========================
        All Inactive User
    =========================== */
    public function all_inactive_user(){
        return \View::make("backend/user/all_inactive_user")->with(array());
    }

    public function get_inactive_user(Request $request){
       if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $query = DB::table('users');
            $query->select('*');
            $query->where([['status', '=', 'Inactive']]);
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
                        $status .= '<a href="javascript:void(0)" class="change-status" data-id="'.$query->user_id.'" data-status="Active"><span class="badge badge-danger">Inactive</span></a>';
                    }
                    return $status;
                })
                ->addColumn('user_type', function ($query) {
                    $user_type = '';
                    if(!empty($query->user_type)) {
                        $user_type .= $query->user_type=="A"?"Admin":"Subscriber";
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
                ->rawColumns(['name', 'gender', 'date_of_birth', 'address', 'mobile', 'email', 'status', 'user_type', 'user_role'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        } 
    }
    // export table
    public function all_inactive_user_export()
    {
        
        $query = DB::table('users')
        ->select('*')
        ->where([['status', '=', 'Inactive']]);
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Gender');
        $sheet->setCellValue('C1', 'Dob');
        $sheet->setCellValue('D1', 'Address');
        $sheet->setCellValue('E1', 'Mobile');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'User_type');
        $sheet->setCellValue('I1', 'User_role');


        
        $rows = 2;
        foreach($data as $empDetails){
            $name = '';
            if(!empty($empDetails->first_name)) {
                $name .= $empDetails->first_name . ' '.$empDetails->last_name;
            }
            $gender = '';
            if(!empty($empDetails->gender)) {
                $gender .= $empDetails->gender=="m"?"Male":"Female";
            }
            $date_of_birth = '';
            if(!empty($empDetails->date_of_birth)) {
                $date_of_birth .= date("d M Y",strtotime($empDetails->date_of_birth));
            }
            $address = '';
            if(!empty($empDetails->address)) {
                $address .= $empDetails->address;
            }
            $mobile = '';
            if(!empty($empDetails->mobile)) {
                $mobile .= $empDetails->mobile;
            }
            $email = '';
            if(!empty($empDetails->email)) {
                $email .= $empDetails->email;
            }
            $user_type = '';
            if(!empty($empDetails->user_type)) {
                $user_type .= $empDetails->user_type=="A"?"Admin":"Subscriber";
            }
            $user_role = '';
            if(!empty($empDetails->fk_user_role)) {
                $selectRoll = User_Role::select('name')->where([['user_role_id', '=', $empDetails->fk_user_role]])->get()->toArray();
                if(count($selectRoll) > 0) {
                    if(!empty($selectRoll[0]['name'])) $user_role = $selectRoll[0]['name'];
                }
            }
            $status = '';
            if(!empty($empDetails->status)) {
                $status .= 'Inactive';
            }
            $sheet->setCellValue('A' . $rows, $name);
            $sheet->setCellValue('B' . $rows, $gender);
            $sheet->setCellValue('C' . $rows, $date_of_birth);
            $sheet->setCellValue('D' . $rows, $address);
            $sheet->setCellValue('E' . $rows, $mobile);
            $sheet->setCellValue('F' . $rows, $email);
            $sheet->setCellValue('G' . $rows, $status);
            $sheet->setCellValue('H' . $rows, $user_type);
            $sheet->setCellValue('I' . $rows, $user_role);
            
            $rows++;
        }
        $fileName = "Inactive_User.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    // Edit Users
    public function edit_user(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.user.user_form')->with([
                'user_data' => Users::where([['user_id', '=', $request->id]])->get()->toArray(),
                'user_roll_data' => User_Role::where([['status', '=', '1']])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    
}