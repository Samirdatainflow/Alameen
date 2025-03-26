<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\State;
use App\Countries;
use App\ProductCategories;
use App\ProductSubCategory;
use DB;
use DataTables;
use App\Helpers\Helper;
use App\Brand;
use App\Products;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SubCategoryController extends Controller {
	// ================*//
    //  Category
    // ================*//
    public function subcategory(){
        return \View::make("backend/item/subcategory")->with([
            'model_data' => Brand::select('brand_id', 'brand_name')->where('status',1)->orderBy('brand_id', 'desc')->get()->toArray(),
            'ProductCategories' => ProductCategories::select('category_id', 'category_name')->where('status',0)->orderBy('category_id', 'desc')->get()->toArray()
        ]);
    } 
    public function add_sub_category(){
        return \View::make("backend/item/subcategory_form")->with([
            // 'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
            'category_data' => ProductCategories::select('category_id','category_name')->where('status',0)->orderBy('category_name', 'asc')->get()->toArray()
        ])->render();
    }
    public function get_model_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_model_list($request->search_key);
        }
        return response()->json($returnData);
    }
    public function get_category_data(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_category_list($request->search_key);
        }
        return response()->json($returnData);
    }
    public function get_category(Request $request){
        $brand_id = $request->brand_id;
        $html = "";
        $category =  ProductCategories::where([['brand_id', '=',$brand_id],['status','=', '0']])->get()->toArray();
        foreach($category as $categorys) {
            $html .='<option value="'.$categorys['category_id'].'">'.$categorys['category_name'].'</option>';
        }
        if(sizeof($category) > 0) {
            $returnData = ["status" => 1, "data"=>$html];
        }else {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return response()->json($returnData);
    }
    // Sub Category Insert/Update
    public function save_item_sub_category(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=ProductSubCategory::where([['category_id', '=', $request->category_id],['sub_category_name', '=', $request->sub_category_name], ['sub_category_id', '!=', $request->hidden_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Sub Category name already exist. Please try with another Sub Category name."];
            }else {
                $saveData=ProductSubCategory::where('sub_category_id', $request->hidden_id)->update(array('category_id'=>$request->category_id,'sub_category_name'=>$request->sub_category_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Sub Category Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Sub Category Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=ProductSubCategory::where([['sub_category_name', '=', $request->sub_category_name], ['category_id', '=', $request->category_id], ['status', '=', '1']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Sub Category name already exist. Please try with another Sub Category name."];
            }else {
            	$data = new ProductSubCategory;
            	$data->category_id = $request->category_id;
            	$data->sub_category_name = $request->sub_category_name;
                $data->status = "1";
                // print_r($data); exit();
                $saveData= $data->save();
                // $saveData=ProductSubCategory::insert(array('category_id'=>$request->category_id,'sub_category_name'=>$request->sub_category_name, 'status' => '1'));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Sub Category Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Sub Category Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
     // Sub Category DataTAble
    public function list_sub_category(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('product_sub_category as sc');
            $query->join('product_categories as pc','pc.category_id', 'sc.category_id', 'left');
            //$query->join('brand as b','b.brand_id', 'pc.brand_id', 'left');
            $query->select('sc.sub_category_id','sc.sub_category_name', 'pc.category_name');
            if($keyword) {
                $sql = "sc.sub_category_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('sc.sub_category_name', 'asc');
                else
                    $query->orderBy('sc.sub_category_id', 'desc');
            }else {
                $query->orderBy('sc.sub_category_id', 'DESC');
            }
            // if(!empty($request->filter_model)) {
            //     $query->where('b.brand_id', $request->filter_model);
            // }
            if(!empty($request->filter_category)) {
                $query->where('sc.category_id', $request->filter_category);
            }
            $query->where([['sc.status', '=', '1']]);

            $datatable_array=Datatables::of($query)
            // ->addColumn('category_id', function ($query) {
            //     $category_id = '';
            //     if(!empty($query->category_id)) {
            //         $selectCategory = ProductCategories::select('category_name')->where([['category_id', '=', $query->category_id]])->get()->toArray();
            //         if(count($selectCategory) > 0) {
            //             if(!empty($selectCategory[0]['category_name'])) $category_id = $selectCategory[0]['category_name'];
            //         }
            //     }
            //     return $category_id;
            // })
            ->addColumn('action', function ($query) {
                $Products = Products::select('sct')->where([['sct', '=', $query->sub_category_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-sub-category" data-id="'.$query->sub_category_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Sub Category"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-sub-category" data-id="'.$query->sub_category_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Sub Category"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-sub-Category" data-id="'.$query->sub_category_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Sub Category"><i class="fa fa-trash"></i></button></a>';
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
    // Sub Category Delete
    public function delete_item_sub_category(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ProductSubCategory::where('sub_category_id', $request->id)->update(['status' => "0"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Sub Category Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //  Sub Category Edit 
    public function edit_item_sub_category(Request $request) {
        if ($request->ajax()) {
        	$dataSubCategory = ProductSubCategory::where([['sub_category_id', '=', $request->id]])->get()->toArray();
        	$category_data = ProductCategories::select('category_name', 'category_id')->where([['category_id', '=', $dataSubCategory[0]['category_id']], ['status', '=', '0']])->get()->toArray();
        	$modelData = [];
            // if(sizeof($category_data) > 0) {
            //     $modelData = Brand::select('brand_id', 'brand_name')->where([['brand_id', '=', $category_data[0]['brand_id']],['status', '=', '1']])->get()->toArray();
            // }
            $html = view('backend/item/subcategory_form')->with([
          	'sub_category_data' =>  $dataSubCategory,
            'category_data' => $category_data,
          	'model_data' => $modelData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function sub_category_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/sub_category_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $sub_category_exist = 0;
                    $category_name = "";
                    $selectData = ProductSubCategory::where([['sub_category_name', '=', $row[1]], ['status', '=', '1']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $sub_category_exist = 1;
                    }else {
                        $sub_category_exist = 0;
                    }
                    
                    $category_exist = "";
                    $selectCatData = ProductCategories::where([['category_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectCatData) > 0) {
                        $category_exist = "1";
                        
                    }
                    array_push($data, array('sub_category_name' => $row[1], 'sub_category_exist' => $sub_category_exist, 'category_name' => $row[0], 'category_exist' => $category_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_sub_category_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['sub_category_name'] != "" && $data['category_exist'] != "") {
                $pdata = new ProductSubCategory;
                $pdata->sub_category_name = $data['sub_category_name'];
                $pdata->category_id = $data['category_id'];
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
                    $sub_category_name = "";
                    $selectData = ProductSubCategory::where([['sub_category_name', '=', $row[1]], ['status', '=', '1']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $sub_category_name = "";
                    }else {
                        $sub_category_name = $row[1];
                    }
                    
                    $category_exist = "";
                    $category_id = "";
                    $selectCatData = ProductCategories::where([['category_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectCatData) > 0) {
                        if(!empty($selectCatData[0]['category_id'])) $category_id = $selectCatData[0]['category_id'];
                        $category_exist = "1";
                        
                    }
                    
                    array_push($data, array('sub_category_name' => $sub_category_name, 'category_name' => $row[0], 'category_id' => $category_id, 'category_exist' => $category_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function sub_category_export(){
        $query = DB::table('product_sub_category as sc')
        ->join('product_categories as pc','pc.category_id', 'sc.category_id', 'left')
        ->select('sc.sub_category_id','sc.sub_category_name', 'pc.category_name')
        ->where([['sc.status', '=', '1']])
        ->orderBy('sc.sub_category_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('A1', 'Sub_Category_Name');
        $sheet->setCellValue('B1', 'Category_Name');
        
        $rows = 2;
        foreach($data as $empDetails){
            // $status = ($empDetails->status == 1)?'Active':'Inactive'; 
            // $sheet->setCellValue('A' . $rows, $empDetails['sub_category_id']);
            $sheet->setCellValue('A' . $rows, $empDetails->sub_category_name);
            $sheet->setCellValue('B' . $rows, $empDetails->category_name);
            
            $rows++;
        }
        $fileName = "sub_category_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
}