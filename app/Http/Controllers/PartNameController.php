<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\PartName;
use App\Products;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PartNameController extends Controller {
    // View
    public function part_name(){
        return \View::make("backend/item/part_name")->with([]);
    }
    // List
    public function list_part_name(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('part_name');
            $query->select('*');
            if($keyword) {
                $sql = "part_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('part_name', 'asc');
                else
                    $query->orderBy('part_name_id', 'desc');
            }else {
                $query->orderBy('part_name_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Products = Products::select('part_name_id')->where([['part_name_id', '=', $query->part_name_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-part-name" data-id="'.$query->part_name_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-part-name" data-id="'.$query->part_name_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-part-name" data-id="'.$query->part_name_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                }
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
    // Add
    public function add_part_name(Request $request){
        return \View::make("backend/item/part_name_form")->with([])->render();
    }
    // Save
    public function save_part_name(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = PartName::where([['part_name', '=', $request->part_name], ['part_name_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData = PartName::where('part_name_id', $request->hidden_id)->update(array('part_name'=>$request->part_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = PartName::where([['part_name', '=', $request->part_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new PartName;
            	$data->part_name = $request->part_name;
                $data->status = "1";
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Edit
    public function edit_part_name(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/item/part_name_form')->with([
          	'PartBrand' =>  PartName::where([['part_name_id', '=', $request->id]])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_part_name(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = PartName::where('part_name_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function part_name_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/part_name_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $part_name_exist = 0;
                    $selectData = PartName::where([['part_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $part_name_exist = 1;
                    }else {
                        $part_name_exist = 0;
                    }
                    array_push($data, array('part_name' => $row[0], 'part_name_exist' => $part_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_part_name_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['part_name'] != "") {
                $pdata = new PartName;
                $pdata->part_name = $data['part_name'];
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
                    $part_name = "";
                    $selectData = PartName::where([['part_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $part_name = "";
                    }else {
                        $part_name = $row[0];
                    }
                    array_push($data, array('part_name' => $part_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function part_name_export(){
        $query = PartName::OrderBy('part_name_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Part Name');
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['part_name']);
            $rows++;
        }
        $fileName = "part_name.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}