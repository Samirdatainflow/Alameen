<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\ProductCategories;
use App\Brand;
use App\Group;
use App\Products;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ItemGroupController extends Controller {
    // ================*//
    //  Group
    // ================*//
    public function group(){
        return \View::make("backend/item/group")->with(array());
    }
    // Group dataTable
    public function list_item_group(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('group');
            $query->select('group_id','group_name', 'status');
            if($keyword)
            {
                $sql = "group_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('group_name', 'asc');
                else
                    $query->orderBy('group_name', 'desc');
            }
            else
            {
                $query->orderBy('group_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="group-change-status" data-id="'.$query->group_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="group-change-status" data-id="'.$query->group_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $Products = Products::select('gr')->where([['gr', '=', $query->group_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-group" data-id="'.$query->group_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Group"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-group" data-id="'.$query->group_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Group"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-group" data-id="'.$query->group_id.'"><button type="button" class="btn btn-danger btn-sm" title="Edit Group"><i class="fa fa-trash"></i></button></a>';
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
    // Group Modal
    public function add_group(){
        return \View::make("backend/item/group_form")->with([
        ])->render();
    }
    // Group Insert/Update
    public function save_item_group(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Group::where([['group_name', '=', $request->group_name], ['group_id', '!=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Group name already exist. Please try with another Group name."];
            }else {
                $saveData=Group::where('group_id', $request->hidden_id)->update(['group_name' => $request->group_name]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Item Group Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Group Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Group::where([['group_name', '=', $request->group_name], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Group name already exist. Please try with another Group name."];
            }else {
                $data = new Group;
                $data->group_name = $request->group_name;
                $data->status = "1";
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Item Group Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Group Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Group Change Status
    public function change_group_status(Request $request){
        $res=Group::where('group_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful."];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    // Group Edit 
    public function edit_item_group(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.item.group_form')->with([
                'group_data' => Group::where([['group_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Group Delete 
    public function delete_item_group(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Group::where('group_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function group_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/group_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $group_name_exist = 0;
                    $selectData = Group::where([['group_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $group_name_exist = 1;
                    }else {
                        $group_name_exist = 0;
                    }
                    array_push($data, array('group_name' => $row[0], 'group_name_exist' => $group_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_group_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['group_name'] != "") {
                $pdata = new Group;
                $pdata->group_name = $data['group_name'];
                $pdata->status = "1";
                $pdata->save();
            }
            $flag++;
        }
        if($flag == sizeof($productArr['data'])) {
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
                    $group_name = "";
                    $selectData = Group::where([['group_name', '=', $row[0]]])->get()->toArray();
                    if(count($selectData) > 0) {
                        $group_name = "";
                    }else {
                        $group_name = $row[0];
                    }
                    array_push($data, array('group_name' => $group_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function gorup_export(){
        $query = DB::table('group')
        ->select('group_id','group_name', 'status')
        ->orderBy('group_id', 'DESC')
        ->where([['status', '!=', '2']]);
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Group Name');
        $sheet->setCellValue('B1', 'Status');
        $rows = 2;
        foreach($data as $empDetails){

            $status = ($empDetails->status ==1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $empDetails->group_name);
            $sheet->setCellValue('B' . $rows, $status);
            $rows++;
        }
        $fileName = "group_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}