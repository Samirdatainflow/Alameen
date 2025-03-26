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
use DB;
use DataTables;
use App\CarManufacture;
use App\Products;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CarModelController extends Controller {
 // ================*//
    // --Brand
    // ================*//
    public function car_model(){
    	return \View::make("backend/item/car_model")->with([
            'CarManufacture' => CarManufacture::select('car_manufacture_id', 'car_manufacture')->where([['status', '=', '1']])->orderBy('car_manufacture_id', 'desc')->get()->toArray()
        ]);
    }
    // Barnd dataTable
    public function list_car_model(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('car_model as b');
            $query->select('b.brand_id','b.brand_name', 'b.status', 'c.car_manufacture');
            $query->join('car_manufacture as c','c.car_manufacture_id', '=', 'b.car_manufacture_id', 'left');
            if($keyword) {
                $sql = "b.brand_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('b.brand_name', 'asc');
                else
                    $query->orderBy('b.brand_id', 'desc');
            }else {
                $query->orderBy('b.brand_id', 'DESC');
            }
            if(!empty($request->filter_car_manufacture)) {
                $query->where([['b.car_manufacture_id', '=', $request->filter_car_manufacture]]);
            }
            $query->where([['b.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="brand-change-status" data-id="'.$query->brand_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="brand-change-status" data-id="'.$query->brand_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $Products = Products::select('car_model')->whereRaw('FIND_IN_SET('.$query->brand_id.',car_model)')->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-brand" data-id="'.$query->brand_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Brand"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-brand" data-id="'.$query->brand_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Brand"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-brand" data-id="'.$query->brand_id.'"><button type="button" class="btn btn-danger btn-sm" title="Edit Brand"><i class="fa fa-trash"></i></button></a>';
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
    // Brand Modal 
    public function add_brand(){
        return \View::make("backend/item/brand_form")->with([
            'CarManufacture' => CarManufacture::select('car_manufacture_id', 'car_manufacture')->where([['status', '=', '1']])->orderBy('car_manufacture_id', 'desc')->get()->toArray()
        ])->render();
    }
    // Brand Insert/Update
    public function save_item_brand(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Brand::where([['Brand_name', '=', $request->Brand_name], ['car_manufacture_id', '=', $request->car_manufacture_id], ['brand_id', '!=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => " Model name already exist. Please try with another Model name."];
            }else {
                $saveData=Brand::where('brand_id', $request->hidden_id)->update(['Brand_name' => $request->Brand_name, 'car_manufacture_id' => $request->car_manufacture_id]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Model Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Model Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Brand::where([['Brand_name', '=', $request->Brand_name], ['car_manufacture_id', '=', $request->car_manufacture_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Model name already exist. Please try with another Brand name."];
            }else {
                $data = new Brand;
                $data->car_manufacture_id = $request->car_manufacture_id;
                $data->Brand_name = $request->Brand_name;
                $data->status = "1";
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Model Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Model Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Brand Change Status
    public function change_brand_status(Request $request){
        $res=Brand::where('brand_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful."];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    // Brand Edit
    public function edit_item_brand(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.item.brand_form')->with([
                'brand_data' => Brand::where([['brand_id', '=', $request->id]])->get()->toArray(),
                'CarManufacture' => CarManufacture::select('car_manufacture_id', 'car_manufacture')->where([['status', '=', '1']])->orderBy('car_manufacture_id', 'desc')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Brand Delete
    public function delete_item_brand(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Brand::where('brand_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => " Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function car_model_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/car_model_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $car_manufacture_exist = "";
                    $car_manufacture = "";
                    $CarManufacture = CarManufacture::where([['car_manufacture', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($CarManufacture) > 0) {
                        $car_manufacture_exist = 1;
                    }else {
                        $car_manufacture_exist = 0;
                    }
                    $brand_name_exist = 0;
                    $Brand = Brand::where([['brand_name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Brand) > 0) {
                        $brand_name_exist = 1;
                    }else {
                        $brand_name_exist = 0;
                    }
                    array_push($data, array('car_manufacture' => $row[0],  'brand_name' => $row[1], 'car_manufacture_exist' => $car_manufacture_exist, 'brand_name_exist' => $brand_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_car_model_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['car_manufacture_exist'] != 0 && $data['brand_name_exist'] != 1) {
                $pdata = new Brand;
                $pdata->car_manufacture_id = $data['car_manufacture_id'];
                $pdata->brand_name = $data['brand_name'];
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
                    $car_manufacture_exist = 0;
                    $car_manufacture_id = "";
                    $CarManufacture = CarManufacture::where([['car_manufacture', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($CarManufacture) > 0) {
                        $car_manufacture_exist = 1;
                        $car_manufacture_id = $CarManufacture[0]['car_manufacture_id'];
                    }else {
                        $car_manufacture_exist = 0;
                    }
                    $brand_name_exist = 0;
                    $Brand = Brand::where([['brand_name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Brand) > 0) {
                        $brand_name_exist = 1;
                    }else {
                        $brand_name_exist = 0;
                    }
                    array_push($data, array('car_manufacture_id' => $car_manufacture_id, 'car_manufacture' => $row[0], 'brand_name' => $row[1], 'car_manufacture_exist' => $car_manufacture_exist, 'brand_name_exist' => $brand_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function car_model_export(){
        $query = Brand::OrderBy('brand_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Model Name');
        $sheet->setCellValue('B1', 'Car Manufacture');
        $sheet->setCellValue('C1', 'Status');
        $rows = 2;
        foreach($query as $td){
            $car_manufacture = '';
            if(!empty($td['car_manufacture_id'])) {
                $CarManufacture = CarManufacture::select('car_manufacture')->where([['car_manufacture_id', '=', $td['car_manufacture_id']]])->get()->toArray();
                if(sizeof($CarManufacture) > 0) {
                    $car_manufacture = $CarManufacture[0]['car_manufacture'];
                }
            }
            $status = ($td['status'] == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $td['brand_name']);
            $sheet->setCellValue('B' . $rows, $car_manufacture);
            $sheet->setCellValue('C' . $rows, $status);
            $rows++;
        }
        $fileName = "car_model.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}