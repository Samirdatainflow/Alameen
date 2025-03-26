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
use App\PartBrand;
use App\Products;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PartBrandController extends Controller {
    // View
    public function part_brand(){
        return \View::make("backend/item/part_brand")->with([]);
    }
    // List
    public function list_part_brand(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('part_brand');
            $query->select('*');
            if($keyword) {
                $sql = "part_brand_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('part_brand_name', 'asc');
                else
                    $query->orderBy('part_brand_id', 'desc');
            }else {
                $query->orderBy('part_brand_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Products = Products::select('part_brand_id')->where([['part_brand_id', '=', $query->part_brand_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-part-brand" data-id="'.$query->part_brand_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-part-brand" data-id="'.$query->part_brand_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-part-brand" data-id="'.$query->part_brand_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    public function add_part_brand(Request $request){
        return \View::make("backend/item/part_brand_form")->with([])->render();
    }
    // Save
    public function save_part_brand(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = PartBrand::where([['part_brand_name', '=', $request->part_brand_name], ['part_brand_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData = PartBrand::where('part_brand_id', $request->hidden_id)->update(array('part_brand_name'=>$request->part_brand_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = PartBrand::where([['part_brand_name', '=', $request->part_brand_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new PartBrand;
            	$data->part_brand_name = $request->part_brand_name;
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
    public function edit_part_brand(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/item/part_brand_form')->with([
          	'PartBrand' =>  PartBrand::where([['part_brand_id', '=', $request->id]])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_part_brand(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = PartBrand::where('part_brand_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function part_brand_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/part_brand_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $part_brand_exist = 0;
                    $selectData = PartBrand::where([['part_brand_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $part_brand_exist = 1;
                    }else {
                        $part_brand_exist = 0;
                    }
                    array_push($data, array('part_brand' => $row[0], 'part_brand_exist' => $part_brand_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_part_brand_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['part_brand'] != "") {
                $pdata = new PartBrand;
                $pdata->part_brand_name = $data['part_brand'];
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
                    $part_brand = "";
                    $selectData = PartBrand::where([['part_brand_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $part_brand = "";
                    }else {
                        $part_brand = $row[0];
                    }
                    array_push($data, array('part_brand' => $part_brand));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function part_brand_export(){
        $query = PartBrand::OrderBy('part_brand_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Part Brand Name');
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['part_brand_name']);
            $rows++;
        }
        $fileName = "part_brand.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}