<?php

namespace App\Http\Controllers;
// namespace App\Models;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\ReportingManagement;
use DB;
use DataTables;

class ReportingManagementController extends Controller {
    public function reporting_management() {
        return \View::make("backend/reporting/reporting_management")->with(array());
    }
    // Reporting Management Modal
    public function add_reporting_management(Request $request){
        if ($request->ajax()) {
            $html = view('backend.reporting.reporting_management_form')->with([
                'UsersData' => Users::select('user_id', 'first_name', 'last_name')->where([['status', '=', 'Active'], ['user_id', '!=', Session::get('user_id')]])->orderBy('first_name', 'ASC')->get()->toArray()
                ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    public function save_reporting_management(Request $request){
        //echo $request->hidden_id; exit();
        if(!empty($request->hidden_id)) {
            
            $reporting_manager_id = "";
            if(!empty($request->reporting_manager_id)) {
                $reporting_manager_id = serialize($request->reporting_manager_id);
            }
            
            $reporting_id = "";
            if(!empty($request->reporting_id)) {
                $reporting_id = serialize($request->reporting_id);
            }
            
            $saveData = ReportingManagement::where('reporting_management_id', $request->hidden_id)->update(array('reporting_manager_id' => $reporting_manager_id, 'reporting_id' => $reporting_id));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
            }
        }else {
            
            $reporting_manager_id = "";
            if(!empty($request->reporting_manager_id)) {
                $reporting_manager_id = serialize($request->reporting_manager_id);
            }
            
            $reporting_id = "";
            if(!empty($request->reporting_id)) {
                $reporting_id = serialize($request->reporting_id);
            }
            
            $data = new ReportingManagement;
        	$data->reporting_manager_id = $reporting_manager_id;
            $data->reporting_id = $reporting_id;
            $saveData= $data->save();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    
    public function list_reporting_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('reporting_management');
            $query->select('*');
            if($keyword) {
                //$query->whereRaw("(p.place_name like '%$keyword%' or l.location_name like '%$keyword%' or zm.zone_name like '%$keyword%' or ro.row_name like '%$keyword%' or ra.rack_name like '%$keyword%' or pl.plate_name like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('reporting_management_id', 'asc');
                else
                    $query->orderBy('reporting_management_id', 'desc');
            }else {
                $query->orderBy('reporting_management_id', 'DESC');
            }
            //$query->where([['p.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('reporting_manager', function ($query) {
                $reporting_manager = [];
                if(!empty($query->reporting_manager_id)) {
                    
                    $reporting_managerIds = unserialize($query->reporting_manager_id);
                    foreach($reporting_managerIds as $k=>$v) {
                        $userData = Users::select('first_name', 'last_name')->where([['user_id', '=', $v]])->get()->toArray();
                        if(sizeof($userData) > 0) {
                            $reporting_manager[] = $userData[0]['first_name']." ".$userData[0]['last_name'];
                        }
                    }
                }
                return implode(',', $reporting_manager);
            })
            ->addColumn('reporting_name', function ($query) {
                $reporting_name = [];
                if(!empty($query->reporting_id)) {
                    
                    $reporting_managerIds = unserialize($query->reporting_id);
                    foreach($reporting_managerIds as $k=>$v) {
                        $userData = Users::select('first_name', 'last_name')->where([['user_id', '=', $v]])->get()->toArray();
                        if(sizeof($userData) > 0) {
                            $reporting_name[] = $userData[0]['first_name']." ".$userData[0]['last_name'];
                        }
                    }
                }
                return implode(',', $reporting_name);
            })
            ->addColumn('action', function ($query) {
                
                $action = "";
                $action = '<a href="javascript:void(0)" class="edit-reporting-management" data-id="'.$query->reporting_management_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-reporting-management" data-id="'.$query->reporting_management_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button></a>';
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
    
    public function edit_reporting_management(Request $request) {
        
        if ($request->ajax()) {
            $html = view('backend/reporting/reporting_management_form')->with([
                'ReportingManagementData' => ReportingManagement::where([['reporting_management_id', '=', $request->id]])->get()->toArray(),
                'UsersData' => Users::select('user_id', 'first_name', 'last_name')->where([['status', '=', 'Active'], ['user_id', '!=', Session::get('user_id')]])->orderBy('first_name', 'ASC')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    public function delete_reporting_management(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ReportingManagement::where('reporting_management_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
}
