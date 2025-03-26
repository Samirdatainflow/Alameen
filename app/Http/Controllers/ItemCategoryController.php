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
use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ItemCategoryController extends Controller {
    // ================*//
    //  Category
    // ================*//
    public function category()
    {
        return \View::make("backend/item/category")->with([
            'model_data' => Brand::select('brand_id', 'brand_name')->where([['status', '=', '1']])->orderBy('brand_id', 'desc')->get()->toArray()
        ]);
    } 
    public function add_category(){
        return \View::make("backend/item/category_form")->with([
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
            // 'brand_id' => Brand::where('status',1)->get()->toArray()
        ])->render();
    }
    public function get_model_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_model_list($request->search_key);
            
        }
        return response()->json($returnData);
    }
    // Category Insert/Update
    public function save_item_category(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=ProductCategories::where([['category_name', '=', $request->category_name], ['category_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Category name already exist. Please try with another Category name."];
            }else {
                $saveData = ProductCategories::where('category_id', $request->hidden_id)->update(array('category_name'=>$request->category_name, 'category_description'=>$request->category_description));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Item Category Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Category Update faild."];
                }
            }
        }else {
            $selectData=ProductCategories::where([['category_name', '=', $request->category_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Category name already exist. Please try with another Category name."];
            }else {
                $data = new ProductCategories;
                $data->category_name = $request->category_name;
                //$data->brand_id = $request->brand_id;
                $data->category_description = $request->category_description;
                //$data->warehouse_id = $request->warehouse_id;
                $data->status = "1";
                $saveData= $data->save();
                // $saveData=ProductCategories::insert(array('category_name'=>$request->category_name,'brand_id'=>$request->brand_id,'category_description'=>$request->category_description,'warehouse_id'=>$request->warehouse_id, 'status' => '0'));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Item Category Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Category Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Category DataTAble
    public function list_category(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('product_categories');
            $query->select('category_id','category_name', 'category_description');
            if($keyword)
            {
                $sql = "category_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('category_name', 'asc');
                else
                    $query->orderBy('category_id', 'desc');
            }
            else
            {
                $query->orderBy('category_id', 'DESC');
            }
            if(!empty($request->filter_model)) {
                $query->where([['brand_id', '=', $request->filter_model]]);
            }
            $query->where([['status', '!=', '2']]);

            $datatable_array=Datatables::of($query)
            // ->addColumn('model_name', function ($query) {
            //     $model_name = '';
            //     if(!empty($query->brand_id)) {
            //         $Brand = Brand::select('brand_name')->where([['brand_id', '=', $query->brand_id]])->get()->toArray();
            //         if(count($Brand) > 0) {
            //             if(!empty($Brand[0]['brand_name'])) $model_name = $Brand[0]['brand_name'];
            //         }
            //     }
            //     return $model_name;
            // })
            ->addColumn('action', function ($query) {
                $Products = Products::select('ct')->where([['ct', '=', $query->category_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-category" data-id="'.$query->category_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Category"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-category" data-id="'.$query->category_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Category"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-Category" data-id="'.$query->category_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Category"><i class="fa fa-trash"></i></button></a>';
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
    // Category Delete
    public function delete_item_category(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ProductCategories::where('category_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Category Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //  Category Edit 
    public function edit_item_category(Request $request) {
        if ($request->ajax()) {
            $dataBrand = ProductCategories::where([['category_id', '=', $request->id]])->get()->toArray();
            //$brand_data = Brand::where('status',1)->where('brand_id',$dataBrand[0]['brand_id'])->select('brand_name', 'brand_id')->get()->toArray();
            // print_r($brand_data); exit();
            $html = view('backend.item.category_form')->with([
                'category_data' => $dataBrand,
                'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            'brand_data' => array()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function category_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/category_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $category_name_exist = 0;
                    $selectData = ProductCategories::where([['category_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $category_name_exist = 1;
                    }else {
                        $category_name_exist = 0;
                    }
                    array_push($data, array('category_name' => $row[0], 'category_description' => $row[1], 'category_name_exist' => $category_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_category_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['category_name'] != "") {
                $pdata = new ProductCategories;
                $pdata->category_name = $data['category_name'];
                $pdata->category_description = $data['category_description'];
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
                    $category_name = "";
                    $category_description = "";
                    $selectData = ProductCategories::where([['category_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $category_name = "";
                        $category_description = "";
                    }else {
                        $category_name = $row[0];
                        $category_description = $row[1];
                    }
                    array_push($data, array('category_name' => $category_name, 'category_description' => $category_description));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function category_export(){
        $query = ProductCategories::OrderBy('category_id', 'ASC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Category Name');
        $sheet->setCellValue('C1', 'Category Description');
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['category_id']);
            $sheet->setCellValue('B' . $rows, $empDetails['category_name']);
            $sheet->setCellValue('C' . $rows, $empDetails['category_description']);
            $rows++;
        }
        $fileName = "category_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}