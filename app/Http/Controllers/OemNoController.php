<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Oem;
use App\ProductCategories;
use App\ProductSubCategory;
use DB;
use DataTables;
use App\Helpers\Helper;
use App\Brand;

class OemNoController extends Controller {
    // ================*//
    //  Category
    // ================*//
    public function oem_no(){
        return \View::make("backend/item/oem")->with([
            'model_data' => Brand::where('status',1)->get()->toArray()
        ]);
    } 
    public function add_oem(){
        return \View::make("backend/item/oem_form")->with([
        ])->render();
    }
    public function get_model_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_model_list($request->search_key);
        }
        return response()->json($returnData);
    }
    public function get_category_oem(Request $request){
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
    public function get_sub_category_oem(Request $request){
        $category_id = $request->category_id;
        $html = "";
        $subcategory =  ProductSubCategory::where([['category_id', '=',$category_id],['status','=', '1']])->get()->toArray();
            foreach($subcategory as $subcategorys)
                {
                    $html .='<option value="'.$subcategorys['sub_category_id'].'">'.$subcategorys['sub_category_name'].'</option>';
                }
                if(sizeof($subcategory) > 0) {
                    $returnData = ["status" => 1, "data"=>$html];
                }else {
                    $returnData = ["status" => 0, "msg" => " No records found."];
                }
            // return $returnData;
        return response()->json($returnData);
    }
    // Sub Category Insert/Update
    public function save_item_oem(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Oem::where([['oem_no', '=', $request->oem_no],['oem_details', '=', $request->oem_details],['sub_category_id', '=', $request->sub_category_id], ['oem_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Oem No already exist. Please try with another Oem No."];
            }else {
                $saveData=Oem::where('oem_id', $request->hidden_id)->update(array('oem_no'=>$request->oem_no,'oem_details'=>$request->oem_details, 'sub_category_id'=>$request->sub_category_id));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Sub Category Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Sub Category Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Oem::where([['oem_no', '=', $request->oem_no]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Oem No already exist. Please try with another Oem No."];
            }else {
            	$data = new Oem;
            	$data->oem_no = $request->oem_no;
            	$data->oem_details = $request->oem_details;
            	$data->sub_category_id = $request->sub_category_id;
                $data->status = "1";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Oem No Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Oem No Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Oem DataTAble
    public function list_oem(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('oem as o');
            $query->select('o.oem_id','o.oem_no', 'o.oem_details','o.status', 'ps.sub_category_name', 'pc.category_name', 'b.brand_name');
            $query->join('product_sub_category as ps','ps.sub_category_id', 'o.sub_category_id', 'left');
            $query->join('product_categories as pc','pc.category_id', 'ps.category_id', 'left');
            $query->join('brand as b','b.brand_id', 'pc.brand_id', 'left');
            if($keyword) {
                $sql = "oem_no like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('o.oem_no', 'asc');
                else
                    $query->orderBy('o.oem_id', 'desc');
            }else {
                $query->orderBy('o.oem_id', 'DESC');
            }
            if(!empty($request->filter_model)) {
                $query->where('b.brand_id', $request->filter_model);
            }
            if(!empty($request->filter_category)) {
                $query->where('pc.category_id', $request->filter_category);
            }
            if(!empty($request->filter_subcategory)) {
                $query->where('o.sub_category_id', $request->filter_subcategory);
            }
            $query->where([['o.status', '=', '1']]);
            $datatable_array=Datatables::of($query)
            // ->addColumn('sub_category_id', function ($query) {
            //     $sub_category_id = '';
            //     if(!empty($query->sub_category_id)) {
            //         $selectSubcategory = ProductSubCategory::select('sub_category_name')->where([['sub_category_id', '=', $query->sub_category_id]])->get()->toArray();
            //         if(count($selectSubcategory) > 0) {
            //             if(!empty($selectSubcategory[0]['sub_category_name'])) $sub_category_id = $selectSubcategory[0]['sub_category_name'];
            //         }
            //     }
            //     return $sub_category_id;
            // })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-oem" data-id="'.$query->oem_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Oem"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-oem" data-id="'.$query->oem_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Oem"><i class="fa fa-trash"></i></button></a>';
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
    // Delete
    public function delete_item_oem(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Oem::where('oem_id', $request->id)->update(['status' => "0"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Oem Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //  Sub Category Edit 
    public function edit_item_oem(Request $request) {
        if ($request->ajax()) {
        	$dataOem = Oem::where([['oem_id', '=', $request->id]])->get()->toArray();
        	$subcategory_data = ProductSubCategory::select('sub_category_name', 'sub_category_id', 'category_id')->where([['status', '=', 1], ['sub_category_id', '=', $dataOem[0]['sub_category_id']]])->get()->toArray();
            $ProductCategories = [];
            if(sizeof($subcategory_data) > 0) {
                $ProductCategories = ProductCategories::select('category_id', 'category_name', 'brand_id')->where([['status', '=', 0], ['category_id', '=', $subcategory_data[0]['category_id']]])->get()->toArray();
            }
            $Brand = [];
            if(sizeof($ProductCategories) > 0) {
                $Brand = Brand::select('brand_id', 'brand_name')->where([['status', '=', 1], ['brand_id', '=', $ProductCategories[0]['brand_id']]])->get()->toArray();
            }
            $html = view('backend/item/oem_form')->with([
          	'oem_data' =>  $dataOem,
            'subcategory_data' => $subcategory_data,
            'category_data' => $ProductCategories,
          	'model_data' => $Brand,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
}