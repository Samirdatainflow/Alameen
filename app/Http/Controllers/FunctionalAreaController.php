<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\FunctionalArea;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class FunctionalAreaController extends Controller {

    public function functional_area() {
        return \View::make("backend/config/functional_area")->with(array());
    }
    // Functional Modal
    public function functinal_area_form(){
    	return \View::make("backend/config/functinal_area_form")->with([
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
        ])->render();
    }
     // Functional Area Insert/Update
    public function save_config_function_area(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=FunctionalArea::where([['function_area_name', '=', $request->function_area_name], ['warehouseid', '=', $request->warehouseid], ['functional_area_id', '!=', $request->hidden_id]])->get()->toArray();
            // print_r($selectData); exit();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Functional area Name already exist. Please try with another Functional area name."];
            }else {
                $saveData=FunctionalArea::where('functional_area_id', $request->hidden_id)->update(array('function_area_name'=>$request->function_area_name,'warehouseid'=>$request->warehouseid));
                // print_r($saveData); exit();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Functional area Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Functional area Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=FunctionalArea::where([['function_area_name', '=', $request->function_area_name], ['warehouseid', '=', $request->warehouseid]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Functional area name already exist. Please try with another Functional area name."];
            }else {
            	$data = new FunctionalArea;
            	$data->function_area_name = $request->function_area_name;
            	$data->warehouseid = $request->warehouseid;
                $data->status = "0";
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Functional Area Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Functional Area Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Function Area dataTable
    public function list_functional_area(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('functional_area');
            $query->select('*');
            if($keyword)
            {
                $sql = "function_area_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('function_area_name', 'asc');
                else
                    $query->orderBy('functional_area_id', 'desc');
            }
            else
            {
                $query->orderBy('functional_area_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('warehouseid', function ($query) {
                $warehouseid = '';
                if(!empty($query->warehouseid)) {
                    $selectWarehouse = Warehouses::select('name')->where([['warehouse_id', '=', $query->warehouseid]])->get()->toArray();
                    if(count($selectWarehouse) > 0) {
                        if(!empty($selectWarehouse[0]['name'])) $warehouseid = $selectWarehouse[0]['name'];
                    }
                }
                return $warehouseid;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-functional-area" data-id="'.$query->functional_area_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Functional Area"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-functional-area" data-id="'.$query->functional_area_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Functional Area"><i class="fa fa-trash"></i></button></a>';
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
     // Functonal Area Edit
    public function edit_config_functional_area(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/functinal_area_form')->with([
                'functional_area_data' => FunctionalArea::where([['functional_area_id', '=', $request->id]])->get()->toArray(),
                'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Functional Area Delete
    public function delete_config_functional_area(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = FunctionalArea::where('functional_area_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function functional_area_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/functional_area_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $function_area_name_exist = 0;
                    $FunctionalArea = FunctionalArea::where([['function_area_name', '=', $row[0]], ['status', '=', '0']])->get()->toArray();
                    if(count($FunctionalArea) > 0) {
                        $function_area_name_exist = 1;
                    }
                    
                    $warehouse_exist = "";
                    $Warehouses = Warehouses::where([['name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $warehouse_exist = "1";
                    }
                    array_push($data, array('function_area_name' => $row[0], 'function_area_name_exist' => $function_area_name_exist, 'warehouse_name' => $row[1], 'warehouse_exist' => $warehouse_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_functional_area_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['function_area_name'] != "" && $data['warehouse_exist'] !== "") {
                $pdata = new FunctionalArea;
                $pdata->function_area_name = $data['function_area_name'];
                $pdata->warehouseid = $data['warehouse_id'];
                $pdata->status = "0";
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
                    $function_area_name = "";
                    $selectData = FunctionalArea::where([['function_area_name', '=', $row[0]], ['status', '=', '0']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $function_area_name = "";
                    }else {
                        $function_area_name = $row[0];
                    }
                    
                    $warehouse_exist = "";
                    $warehouse_id = "";
                    $Warehouses = Warehouses::where([['name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        if(!empty($Warehouses[0]['warehouse_id'])) $warehouse_id = $Warehouses[0]['warehouse_id'];
                        $warehouse_exist = "1";
                    }
                    
                    array_push($data, array('function_area_name' => $function_area_name, 'warehouse_id' => $warehouse_id, 'warehouse_exist' => $warehouse_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function functional_area_export(){
        $query = DB::table('functional_area')
        ->select('*')
        ->orderBy('functional_area_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Function Area Name');
        $sheet->setCellValue('B1', 'Warehouse');
        $rows = 2;
        foreach($data as $empDetails){
            $warehouse_name = "";
            $Warehouses = Warehouses::select('name')->where('warehouse_id', $empDetails->warehouseid)->get()->toArray();
            if(sizeof($Warehouses) > 0) {
                $warehouse_name = $Warehouses[0]['name'];
            }
            
            $sheet->setCellValue('A' . $rows, $empDetails->function_area_name);
            $sheet->setCellValue('B' . $rows, $warehouse_name);
            $rows++;
        }
        $fileName = "functional_area_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
}