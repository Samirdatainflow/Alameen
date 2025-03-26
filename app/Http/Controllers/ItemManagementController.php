<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Suppliers;
use App\Products;
use App\Brand;
use App\ProductCategories;
use App\Group;
use App\WmsUnit;
use App\Countries;
use DB;
use DataTables;
use App\Currency;
use App\ProductSubCategory;
use App\Oem;
use App\ApplicationNo;
use App\ManufacturingNo;
use App\AlternatePartNo;
use App\PartBrand;
use App\PartName;
use App\CarManufacture;
use App\CarName;
use App\Engine;
use App\ChassisModel;
use App\TempProducts;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ItemManagementController extends Controller {
    // ================*//
	// Item Management
    // ================*//
    public function item_management() {
        return \View::make("backend/item/item_management")->with([
            'unit_data' => WmsUnit::select('unit_id', 'unit_name')->get()->toArray(),
            'Brand' => Brand::select('brand_id', 'brand_name')->where('status', 1)->orderBy('brand_id', 'desc')->get()->toArray(),
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->where('status',0)->orderBy('category_id', 'desc')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'PartBrand' => PartBrand::select('part_brand_id','part_brand_name')->where('status', 1)->orderBy('part_brand_id', 'desc')->get()->toArray(),
            'car_manufacture' => CarManufacture::select('car_manufacture_id','car_manufacture')->where('status', 1)->orderBy('car_manufacture_id', 'desc')->get()->toArray(),
            'car_model' => Brand::select('brand_id', 'brand_name')->where('status', 1)->orderBy('brand_id', 'desc')->get()->toArray(),
        ]);
    }
    // Item Modal
    public function add_item_management(){
        return \View::make("backend/item/item_management_form")->with([
            //'CarModel' => Brand::select('brand_id', 'brand_name')->where([['status', '=', '1']])->orderBy('brand_id', 'desc')->get()->toArray(),
            'CarModel' => [],
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            'supplier_id' => Suppliers::where('status',1)->get()->toArray(),
            'unit_id' => WmsUnit::orderBy('unit_id', 'DESC')->get()->toArray(),
            'group_id' => Group::where('status',1)->orderBy('group_id', 'DESC')->get()->toArray(),
            'currency' => Currency::where('status',1)->orderBy('currency_id', 'DESC')->get()->toArray(),
            'country_id' => Countries::where('status',0)->orderBy('country_id', 'DESC')->get()->toArray()
        ])->render();
    }
    public function item_management_bulk_upload(){
        return \View::make("backend/item/item_management_bulk_upload_form")->with([])->render();
    }
    // Item DataTAble
    public function list_item_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('products as p');
            $query->select('p.*', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            if($keyword) {
                $query->whereRaw("(p.product_id like '%$keyword%' or pn.part_name like '%$keyword%' or replace(p.pmpno, '-','') like '%$keyword%' or p.pmpno like '%$keyword%')");
                // $sql = "pn.part_name like ?";
                // $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('pn.part_name', 'asc');
                else
                    $query->orderBy('p.product_id', 'desc');
            }else {
                $query->orderBy('p.product_id', 'DESC');
            }
            if(!empty($request->filter_car_manufacture)) {
                $query->where([['p.car_manufacture_id', '=', $request->filter_car_manufacture]]);
            }
            if(!empty($request->filter_car_model)) {
                $query->whereRaw("FIND_IN_SET('".$request->filter_car_model."',p.car_model)");
            }
            if(!empty($request->filter_from_year)) {
                $query->where([['p.from_year', '>=', $request->filter_from_year]]);
            }
            if(!empty($request->filter_from_month)) {
                $query->where([['p.from_month', '>=', $request->filter_from_month]]);
            }
            if(!empty($request->filter_to_year)) {
                $query->where([['p.to_year', '<=', $request->filter_to_year]]);
            }
            if(!empty($request->filter_to_month)) {
                $query->where([['p.to_month', '<=', $request->filter_to_month]]);
            }
            if(!empty($request->filter_category)) {
                $query->where([['p.ct', '=', $request->filter_category]]);
            }
            if(!empty($request->filter_sub_category)) {
                $query->where([['p.sct', '=', $request->filter_sub_category]]);
            }
            if(!empty($request->filter_part_name)) {
                $query->where([['p.part_name_id', '=', $request->filter_part_name]]);
            }
            if(!empty($request->filter_part_no)) {
                $query->whereRaw("(replace(p.pmpno, '-','') like '%$request->filter_part_no%' or p.pmpno like '%$request->filter_part_no%')");
                //$query->where('p.pmpno', 'like', '%' . $request->filter_part_no . '%');
            }
            if(!empty($request->filter_part_brand)) {
                $query->where([['p.part_brand_id', '=', $request->filter_part_brand]]);
            }
            if(!empty($request->engine_no)) {
                $query1 = DB::table('engine as en');
                $query1->select('en.product_id');
                $query1->where('en.engine_name', 'like', '%'.$request->engine_no.'%');
                $query1->groupBy('en.product_id');
                $data=$query1->get()->toArray();
                if(sizeof($data)>0)
                {
                    foreach($data as $val)
                    {
                        $query->orWhere([['p.product_id', '=', $val->product_id]]);
                    }
                }
                else
                {
                    $query->Where([['p.product_id', '=', 0]]);
                }

            }
            if(!empty($request->chassis)) {
                $query1 = DB::table('chassis_model as cm');
                $query1->select('cm.product_id');
                $query1->where('cm.chassis_model', 'like', '%'.$request->chassis.'%');
                $query1->groupBy('cm.product_id');
                $data=$query1->get()->toArray();
                if(sizeof($data)>0)
                {
                    foreach($data as $val)
                    {
                        $query->orWhere([['p.product_id', '=', $val->product_id]]);
                    }
                }
                else
                {
                    $query->Where([['p.product_id', '=', 0]]);
                }

            }
            if(!empty($request->manufacturer_no)) {
                $query1 = DB::table('manufacturing_no as mn');
                $query1->select('mn.product_id');
                $query1->where('mn.manufacturing_no', 'like', '%'.$request->manufacturer_no.'%');
                $query1->groupBy('mn.product_id');
                $data=$query1->get()->toArray();
                if(sizeof($data)>0)
                {
                    foreach($data as $val)
                    {
                        $query->orWhere([['p.product_id', '=', $val->product_id]]);
                    }
                }
                else
                {
                    $query->Where([['p.product_id', '=', 0]]);
                }

            }
            if(!empty($request->alternate_part_no)) {
                $query1 = DB::table('alternate_part_no as apn');
                $query1->select('apn.product_id');
                $query1->where('apn.alternate_no', 'like', '%'.$request->alternate_part_no.'%');
                $query1->groupBy('apn.product_id');
                $data=$query1->get()->toArray();
                if(sizeof($data)>0)
                {
                    foreach($data as $val)
                    {
                        $query->orWhere([['p.product_id', '=', $val->product_id]]);
                    }
                }
                else
                {
                    $query->Where([['p.product_id', '=', 0]]);
                }

            }
            if(!empty($request->filter_units)) {
                $query->where([['p.unit', '=', $request->filter_units]]);
            }
            $query->where([['p.is_deleted', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('part_no', function ($query) {
                return $query->pmpno;
            })
            ->addColumn('current_stock', function ($query) {
                return $query->current_stock;
            })
            ->addColumn('cost', function ($query) {
                return 0;
            })
            ->addColumn('pmrprc', function ($query) {
                return $query->pmrprc;
            })
            ->addColumn('unit', function ($query) {
                $unit_id = '';
                if(!empty($query->unit)) {
                    $selectUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $query->unit]])->get()->toArray();
                    if(count($selectUnit) > 0) {
                        if(!empty($selectUnit[0]['unit_name'])) $unit_id = $selectUnit[0]['unit_name'];
                    }
                }
                return $unit_id;
            })
            ->addColumn('ct', function ($query) {
                $category_id = '';
                if(!empty($query->ct)) {
                    $selectCategory = ProductCategories::select('category_name')->where([['category_id', '=', $query->ct]])->get()->toArray();
                    if(count($selectCategory) > 0) {
                        if(!empty($selectCategory[0]['category_name'])) $category_id = $selectCategory[0]['category_name'];
                    }
                }
                return $category_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="view-item" data-id="'.$query->product_id.'"><button type="button" class="btn btn-warning btn-sm" title="View Item"><i class="fa fa-eye"></i></button></a> <a href="javascript:void(0)" class="edit-item" data-id="'.$query->product_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Item"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-item" data-id="'.$query->product_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Item"><i class="fa fa-trash"></i></button></a>';
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

    // Export ItemManagement
    public function item_management_export()
    {
        $start=$_GET['start'];
        $end=$_GET['end'];
        $query = DB::table('products as p')
        ->select('p.*', 'pn.part_name')
        ->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left')->where([['p.is_deleted', '=', '0']])
        ->orderBy('p.product_id', 'DESC')->skip($start)->take('50');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Item_id');
        $sheet->setCellValue('B1', 'Part_no');
        $sheet->setCellValue('C1', 'Product_name');
        $sheet->setCellValue('D1', 'Unit');
        $sheet->setCellValue('E1', 'Category');
        $sheet->setCellValue('F1', 'Alert');
        $sheet->setCellValue('G1', 'Qty_on_hand');
        $sheet->setCellValue('H1', 'Selling_Price');

        $rows = 2;
        foreach($data as $empDetails){
            $ct = '';
                if(!empty($empDetails->ct)) {
                    $selectCategory = ProductCategories::select('category_name')->where([['category_id', '=', $empDetails->ct]])->get()->toArray();
                    if(count($selectCategory) > 0) {
                        if(!empty($selectCategory[0]['category_name'])) $ct = $selectCategory[0]['category_name'];
                    }
                }
            $unit_id = '';
                if(!empty($empDetails->unit)) {
                    $selectUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $empDetails->unit]])->get()->toArray();
                    if(count($selectUnit) > 0) {
                        if(!empty($selectUnit[0]['unit_name'])) $unit_id = $selectUnit[0]['unit_name'];
                    }
                }
            $sheet->setCellValue('A' . $rows, $empDetails->product_id);
            $sheet->setCellValue('B' . $rows, $empDetails->pmpno);
            $sheet->setCellValue('C' . $rows, $empDetails->part_name);
            $sheet->setCellValue('D' . $rows, $unit_id);
            $sheet->setCellValue('E' . $rows, $ct);
            $sheet->setCellValue('F' . $rows, $empDetails->stock_alert);
            $sheet->setCellValue('G' . $rows, $empDetails->current_stock);
            $sheet->setCellValue('H' . $rows, $empDetails->pmrprc);
            $rows++;
        }
        $fileName = "ItemManagement.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    // Insert/Update
    public function save_item_management(Request $request)
    {
        $lc_date = NULL;
        if(!empty($request->lc_date))
        {
            $lc_date = date('Y-m-d',strtotime(str_replace('-','/',$request->lc_date)));
        }
        
        $previous_lc_date = NULL;
        if(!empty($request->prvious_lc_date))
        {
            $previous_lc_date = date('Y-m-d',strtotime(str_replace('-','/',$request->prvious_lc_date)));
        }
        
        if(!empty($request->hidden_id))
        {
            $selectData=Products::where([['pmpno', '=', $request->pmpno], ['product_id', '!=', $request->hidden_id]])->get()->toArray();
            
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Part No already exist. Please try with another Part No."];
            }else {
                $supplier_id = "";
                if(!empty($request->supplier_id)) {
                    $supplier_id = implode(',',$request->supplier_id);
                }
                $warehouse_id = "";
                if(!empty($request->warehouse_id)) {
                    $warehouse_id = implode(',',$request->warehouse_id);
                }
                $car_model_id = "";
                if(!empty($request->car_model_id)) {
                    $car_model_id = implode(',',$request->car_model_id);
                }
                $saveData=Products::where('product_id', $request->hidden_id)->update(['part_brand_id' => $request->part_brand_id, 'pmpno' => strtoupper($request->pmpno), 'alternate_part_no' => strtoupper($request->alternate_part_no), 'part_name_id' => $request->part_name_id, 'product_desc' => $request->product_desc, 'car_manufacture_id' => $request->car_manufacture_id, 'car_name_id' => $request->car_name_id, 'from_year' => $request->from_year, 'from_month' => $request->from_month, 'to_year' => $request->to_year, 'to_month' => $request->to_month, 'car_model' => $car_model_id, 'ct' => $request->ct, 'sct' => $request->sct, 'gr' => $request->gr, 'unit' => $request->unit, 'pmrprc' => $request->pmrprc, 'selling_price' => $request->selling_price, 'mark_up' => $request->mark_up, 'lc_price' => $request->lc_price, 'lc_date' => $lc_date, 'prvious_lc_price' => $request->prvious_lc_price, 'prvious_lc_date' => $previous_lc_date, 'moq' => $request->moq, 'country_of_origin' => $request->country_id, 'supplier_id' => $supplier_id, 'supplier_currency' => $request->supplier_currency, 're_order_level' => $request->re_order_level, 'no_re_order' => $request->no_re_order, 'stop_sale' => $request->stop_sale, 'warehouse_id' => $warehouse_id, 'reserved_qty' => $request->reserved_qty, 'allocation_qty' => $request->allocation_qty, 'last_month_stock' => $request->last_month_stock, 'qty_in_transit' => $request->qty_in_transit, 'qty_on_order' => $request->qty_on_order,'stock_alert' => $request->stock_alert]);
                if($saveData) {
                    if(!empty($request->engine)) {
                        $application = explode(",",$request->engine);
                        foreach($application as $k=>$v) {
                            $app = new Engine;
                            $app->engine_name = $v;
                            $app->product_id = $request->hidden_id;
                            $app->status = "1";
                            $app->save();
                        }
                    }
                    if(!empty($request->chassis_model)) {
                        $application = explode(",",$request->chassis_model);
                        foreach($application as $k=>$v) {
                            $app = new ChassisModel;
                            $app->chassis_model = $v;
                            $app->product_id = $request->hidden_id;
                            $app->status = "1";
                            $app->save();
                        }
                    }
                    if(!empty($request->manfg_no)) {
                        $manfg_no = explode(",",$request->manfg_no);
                        foreach($manfg_no as $k=>$v) {
                            $manfg = new ManufacturingNo;
                            $manfg->manufacturing_no = $v;
                            $manfg->product_id = $request->hidden_id;
                            $manfg->status = "1";
                            $manfg->save();
                        }
                    }
                    if(!empty($request->altn_part)) {
                        $altn_part = explode(",",$request->altn_part);
                        foreach($altn_part as $k=>$v) {
                            $altn = new AlternatePartNo;
                            $altn->alternate_no = $v;
                            $altn->product_id = $request->hidden_id;
                            $altn->status = "1";
                            $altn->save();
                        }
                    }
                    $returnData = ["status" => 1, "msg" => "Item Details Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Details Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Products::where(['pmpno' => $request->pmpno])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Part No already exist. Please try with another Part No."];
            }else {
                // echo "<pre>";
                // print_r(explode(",",$request->application));
                // exit();
                $data = new Products;
                $data->part_brand_id = $request->part_brand_id;
                $data->car_manufacture_id = $request->car_manufacture_id;
                $data->car_name_id = $request->car_name_id;
                $data->from_year = $request->from_year;
                $data->from_month = $request->from_month;
                $data->to_year = $request->to_year;
                $data->to_month = $request->to_month;
                $car_model_id = "";
                if(!empty($request->car_model_id)) {
                    $car_model_id = implode(',',$request->car_model_id);
                }
                $data->car_model = $car_model_id;
                $data->gr = $request->gr;
                $data->ct = $request->ct;
                $data->sct = $request->sct;
                $data->pmpno = strtoupper($request->pmpno);
                $data->alternate_part_no = strtoupper($request->alternate_part_no);
                $data->part_name_id = $request->part_name_id;
                $data->product_desc = $request->product_desc;
                $data->unit = $request->unit;
                $data->pmrprc = $request->pmrprc;
                $data->selling_price = $request->selling_price;
                $data->mark_up = $request->mark_up;
                $data->lc_price = $request->lc_price;
                $data->lc_date = $lc_date;
                $data->prvious_lc_price = $request->prvious_lc_price;
                $data->prvious_lc_date = $previous_lc_date;
                $data->moq = $request->moq;
                $data->country_of_origin = $request->country_id;
                if(!empty($request->supplier_id)) {
                    $data->supplier_id = implode(',',$request->supplier_id);
                }
                $data->supplier_currency = $request->supplier_currency;
                $data->re_order_level = $request->re_order_level;
                $data->no_re_order = $request->no_re_order;
                $data->stop_sale = $request->stop_sale;
                if(!empty($request->warehouse_id)) {
                    $data->warehouse_id = implode(',',$request->warehouse_id);
                }
                $data->reserved_qty = $request->reserved_qty;
                $data->allocation_qty = $request->allocation_qty;
                $data->last_month_stock = $request->last_month_stock;
                $data->qty_in_transit = $request->qty_in_transit;
                $data->qty_on_order = "0";
                if(!empty($request->qty_on_order)) {
                    $data->qty_on_order = $request->qty_on_order;
                }
                $data->stock_alert = $request->stock_alert;
                $data->current_stock = $request->current_stock;
                
                $data->is_deleted = "0";
                $saveData = $data->save();
                if($saveData) {
                    $last_id = $data->id;
                    if(!empty($request->engine)) {
                        $application = explode(",",$request->engine);
                        foreach($application as $k=>$v) {
                            $app = new Engine;
                            $app->engine_name = $v;
                            $app->product_id = $last_id;
                            $app->status = "1";
                            $app->save();
                        }
                    }
                    if(!empty($request->chassis_model)) {
                        $application = explode(",",$request->chassis_model);
                        foreach($application as $k=>$v) {
                            $app = new ChassisModel;
                            $app->chassis_model = $v;
                            $app->product_id = $last_id;
                            $app->status = "1";
                            $app->save();
                        }
                    }
                    if(!empty($request->manfg_no)) {
                        $manfg_no = explode(",",$request->manfg_no);
                        foreach($manfg_no as $k=>$v) {
                            $manfg = new ManufacturingNo;
                            $manfg->manufacturing_no = $v;
                            $manfg->product_id = $last_id;
                            $manfg->status = "1";
                            $manfg->save();
                        }
                    }
                    if(!empty($request->altn_part)) {
                        $altn_part = explode(",",$request->altn_part);
                        foreach($altn_part as $k=>$v) {
                            $altn = new AlternatePartNo;
                            $altn->alternate_no = $v;
                            $altn->product_id = $last_id;
                            $altn->status = "1";
                            $altn->save();
                        }
                    }
                    $returnData = ["status" => 1, "msg" => "Item Details Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Item Details Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Item Delete 
    public function delete_item_management(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Products::where('product_id', $request->id)->update(['is_deleted' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // Item Edit 
    public function edit_item_management(Request $request) {
        if ($request->ajax()) {
            $PartBrand = [];
            $PartName = [];
            $CarManufacture = [];
            $CarName = [];
            $ProductCategories = [];
            $ProductSubCategory = [];
            $Oem = [];
            $CarModel = [];
            $Products = Products::where([['product_id', '=', $request->id]])->get()->toArray();
            if(sizeof($Products)) {
                if(!empty($Products[0]['part_brand_id'])) {
                    $PartBrand = PartBrand::select('part_brand_id', 'part_brand_name')->where([['part_brand_id', '=', $Products[0]['part_brand_id']],['status', '=', '1']])->get()->toArray();
                }
                if(!empty($Products[0]['part_name_id'])) {
                    $PartName = PartName::select('part_name_id', 'part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']],['status', '=', '1']])->get()->toArray();
                }
                if(!empty($Products[0]['car_manufacture_id'])) {
                    $CarManufacture = CarManufacture::select('car_manufacture_id', 'car_manufacture')->where([['car_manufacture_id', '=', $Products[0]['car_manufacture_id']],['status', '=', '1']])->get()->toArray();
                }
                if(!empty($Products[0]['car_name_id'])) {
                    $CarName = CarName::select('car_name_id', 'car_name')->where([['car_name_id', '=', $Products[0]['car_name_id']],['status', '=', '1']])->get()->toArray();
                }
                if(!empty($Products[0]['ct'])) {
                    $ProductCategories = ProductCategories::select('category_id', 'category_name')->where([['category_id', '=', $Products[0]['ct']]])->get()->toArray();
                }
                if(!empty($Products[0]['ct'])) {
                    $ProductSubCategory = ProductSubCategory::select('sub_category_id', 'sub_category_name')->where([['category_id', '=', $Products[0]['ct']]])->get()->toArray();
                }
                if(!empty($Products[0]['car_manufacture_id'])) {
                    $CarModel = Brand::select('brand_id', 'brand_name')->where([['car_manufacture_id', '=', $Products[0]['car_manufacture_id']], ['status', '=', '1']])->orderBy('brand_id', 'desc')->get()->toArray();
                }
            }
            $html = view('backend.item.item_management_form')->with([
                'item_data' => $Products,
                'PartBrand' => $PartBrand,
                'PartName' => $PartName,
                'CarName' => $CarName,
                'CarManufacture' => $CarManufacture,
                'CarModel' => $CarModel,
                'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
                'supplier_id' => Suppliers::where('status',1)->get()->toArray(),
                'category_data' => $ProductCategories,
                'subcategory_id' => $ProductSubCategory,
                'oem_no_id' => $Oem,
                'unit_id' => WmsUnit::get()->toArray(),
                'group_id' => Group::where('status',1)->get()->toArray(),
                'currency' => Currency::where('status',1)->get()->toArray(),
                'country_id' => Countries::where('status',0)->get()->toArray(),
                'Engine' => Engine::where([['product_id', '=', $Products[0]['product_id']], ['status','=','1']])->get()->toArray(),
                'ChassisModel' => ChassisModel::where([['product_id', '=', $Products[0]['product_id']], ['status','=','1']])->get()->toArray(),
                'ManufacturingNo' => ManufacturingNo::where([['product_id', '=', $Products[0]['product_id']], ['status','=','1']])->get()->toArray(),
                'AlternatePartNo' => AlternatePartNo::where([['product_id', '=', $Products[0]['product_id']], ['status','=','1']])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function view_item(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $query = DB::table('products as p');
            $query->select('p.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'cm.car_manufacture', 'cn.car_name', 'p.from_year', 'p.from_month', 'p.to_year', 'p.to_month', 'p.car_model', 'pc.category_name', 'psc.sub_category_name', 'p.gr', 'p.pmrprc', 'p.mark_up', 'p.lc_price', 'p.lc_date', 'p.prvious_lc_price', 'p.prvious_lc_date', 'p.moq', 'co.country_name', 'su.full_name as supplier_name', 'p.supplier_currency', 'p.no_re_order', 'p.stop_sale', 'w.name as warehouse_name', 'p.stock_alert', 'p.reserved_qty', 'p.allocation_qty', 'p.last_month_stock', 'p.qty_in_transit', 'p.qty_on_order');
            $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('car_manufacture as cm', 'cm.car_manufacture_id', '=', 'p.car_manufacture_id', 'left');
            $query->join('car_name as cn', 'cn.car_name_id', '=', 'p.car_name_id', 'left');
            //$query->join('engine as e', 'e.engine_id', '=', 'p.engine_id', 'left');
            $query->join('product_categories as pc', 'pc.category_id', '=', 'p.ct', 'left');
            $query->join('product_sub_category as psc', 'psc.sub_category_id', '=', 'p.sct', 'left');
            $query->join('countries as co', 'co.country_id', '=', 'p.country_of_origin', 'left');
            $query->join('suppliers as su', 'su.supplier_id', '=', 'p.supplier_id', 'left');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'p.warehouse_id', 'left');
            $query->where([['p.product_id', '=', $request->id]]);
            $Products = $query->get()->toArray();
            // $Products = Products::where([['product_id', '=', $request->id]])->get()->toArray();
            if(sizeof($Products) > 0) {
                $car_model_name = "";
                if(!empty($Products[0]->car_model)) {
                    $carModelArray = explode(',',$Products[0]->car_model);
                    foreach($carModelArray as $k=>$v) {
                        $Brand = Brand::select('brand_name')->where([['brand_id', '=', $v]])->get()->toArray();
                        if(sizeof($Brand) > 0) {
                            if(!empty($Brand[0]['brand_name'])) $car_model_name .= $Brand[0]['brand_name'].", ";
                        }
                    }
                }
            //     $warehouses_name = "";
            //     if(!empty($Products[0]['warehouse_id'])) {
            //         $Countries = Warehouses::select('name')->where([['warehouse_id', '=', $Products[0]['warehouse_id']]])->get()->toArray();
            //         if(sizeof($Countries) > 0) {
            //             if(!empty($Countries[0]['name'])) $warehouses_name = $Countries[0]['name'];
            //         }
            //     }
            }
            //$ApplicationNo = ApplicationNo::select('application_no')->where([['product_id', '=', $request->id]])->get()->toArray();
            $Engine = Engine::select('engine_name')->where([['product_id', '=', $request->id]])->get()->toArray();
            $ChassisModel = ChassisModel::select('chassis_model')->where([['product_id', '=', $request->id]])->get()->toArray();
            $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $request->id]])->get()->toArray();
            $AlternatePartNo = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $request->id]])->get()->toArray();
            $html = view('backend.item.view_item')->with([
                'item_data' => $Products,
                'car_model_name' => $car_model_name,
                'manufacturing_no' => $ManufacturingNo,
                'alternate_no' => $AlternatePartNo,
                'Engine' => $Engine,
                'ChassisModel' => $ChassisModel,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Get Category By Model
    public function get_category_by_model(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ProductCategories = ProductCategories::select('category_id', 'category_name')->where([['brand_id', '=', $request->id], ['status', '=', '0']])->get()->toArray();
            if(sizeof($ProductCategories) > 0) {
                $returnData = ["status" => 1, "data" => $ProductCategories];
            }else {
                $returnData = ["status" => 0, "msg" => "No record found."];
            }
            return response()->json($returnData);
        }
    }
    // Get Sub Category By Category
    public function get_subcategory_by_category(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ProductSubCategories = ProductSubCategory::select('sub_category_id', 'sub_category_name')->where([['category_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($ProductSubCategories) > 0) {
                $returnData = ["status" => 1, "data" => $ProductSubCategories];
            }else {
                $returnData = ["status" => 0, "msg" => "No record found."];
            }
            return response()->json($returnData);
        }
    }
    // Get OEM No By Sub Category
    public function get_oem_no_by_sub_category(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Oem = Oem::select('oem_id', 'oem_no')->where([['sub_category_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($Oem) > 0) {
                $returnData = ["status" => 1, "data" => $Oem];
            }else {
                $returnData = ["status" => 0, "msg" => "No record found."];
            }
            return response()->json($returnData);
        }
    }
    // Remove Engine
    public function remove_engine(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ApplicationNo = Engine::where([['engine_id', '=', $request->id]])->delete();
            if($ApplicationNo) {
                $returnData = ["status" => 1];
            }else {
                $returnData = ["status" => 0, "msg" => "Remove faild."];
            }
            return response()->json($returnData);
        }
    }
    // Remove Engine
    public function remove_chassis_model(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ChassisModel = ChassisModel::where([['chassis_model_id', '=', $request->id]])->delete();
            if($ChassisModel) {
                $returnData = ["status" => 1];
            }else {
                $returnData = ["status" => 0, "msg" => "Remove faild."];
            }
            return response()->json($returnData);
        }
    }
    // Remove Manufacturing No
    public function remove_manufacturing_no(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ApplicationNo = ManufacturingNo::where([['manufacturing_no_id', '=', $request->id]])->delete();
            if($ApplicationNo) {
                $returnData = ["status" => 1];
            }else {
                $returnData = ["status" => 0, "msg" => "Remove faild."];
            }
            return response()->json($returnData);
        }
    }
    // Remove Alternate No
    public function remove_alternate_no(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $ApplicationNo = AlternatePartNo::where([['alternate_part_no_id', '=', $request->id]])->delete();
            if($ApplicationNo) {
                $returnData = ["status" => 1];
            }else {
                $returnData = ["status" => 0, "msg" => "Remove faild."];
            }
            return response()->json($returnData);
        }
    }
    public function item_management_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/item_management_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    if(!empty($row[1]))
                    {
                        $part_no_exist = 0;
                        $selectData = Products::where([['pmpno', '=', $row[1]], ['is_deleted', '=', '0']])->get()->toArray();
                        if(count($selectData) > 0) {
                            $part_no_exist = 1;
                        }else {
                            $part_no_exist = 0;
                        }
                        // $part_brand_name = "";
                        // if(!empty($row[0])) {
                        //     $PartBrand = PartBrand::select('part_brand_name')->where('part_brand_id', $row[0])->get()->toArray();
                        //     if(sizeof($PartBrand) > 0) {
                        //         $part_brand_name = $PartBrand[0]['part_brand_name'];
                        //     }
                        // }
                        // $part_name = "";
                        // if(!empty($row[0])) {
                        //     $PartName = PartName::select('part_name')->where('part_name_id', $row[2])->get()->toArray();
                        //     if(sizeof($PartName) > 0) {
                        //         $part_name = $PartName[0]['part_name'];
                        //     }
                        // }
                        // $car_manufacture = "";
                        // if(!empty($row[0])) {
                        //     $CarManufacture = CarManufacture::select('car_manufacture')->where('car_manufacture_id', $row[3])->get()->toArray();
                        //     if(sizeof($CarManufacture) > 0) {
                        //         $car_manufacture = $CarManufacture[0]['car_manufacture'];
                        //     }
                        // }
                        // $car_model = "";
                        // if(!empty($row[0])) {
                        //     $Brand = Brand::select('brand_name')->where('brand_id', $row[4])->get()->toArray();
                        //     if(sizeof($Brand) > 0) {
                        //         $car_model = $Brand[0]['brand_name'];
                        //     }
                        // }
                        // $category_name = "";
                        // if(!empty($row[0])) {
                        //     $ProductCategories = ProductCategories::select('category_name')->where('category_id', $row[5])->get()->toArray();
                        //     if(sizeof($ProductCategories) > 0) {
                        //         $category_name = $ProductCategories[0]['category_name'];
                        //     }
                        // }
                        // $group_name = "";
                        // if(!empty($row[0])) {
                        //     $Group = Group::select('group_name')->where('group_id', $row[6])->get()->toArray();
                        //     if(sizeof($Group) > 0) {
                        //         $group_name = $Group[0]['group_name'];
                        //     }
                        // }
                        // $unit_name = "";
                        // if(!empty($row[0])) {
                        //     $WmsUnit = WmsUnit::select('unit_name')->where('unit_id', $row[7])->get()->toArray();
                        //     if(sizeof($WmsUnit) > 0) {
                        //         $unit_name = $WmsUnit[0]['unit_name'];
                        //     }
                        // }
                        $current_stock = 0;
                        if(!empty($row[7]))
                        {
                            $current_stock = $row[7];
                        }
                        $unit_name = "";
                        array_push($data, array('part_no' => $row[1], 'part_no_exist' => $part_no_exist, 'part_brand_name' => $row[0], 'part_name' => $row[2], 'car_manufacture' => $row[3], 'car_model' => $row[4], 'category_name' => $row[5], 'group_name' => $row[6], 'unit_name' => $unit_name, 'pmrprc' => $row[8], 'current_stock' => $current_stock));
                    }
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_item_management_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        // echo "<pre>";
        // print_r($productArr); exit();
        foreach($productArr['data'] as $data) {
            if($data['part_no_exist'] == "0") {
                $pdata = new Products;
                $pdata->part_brand_id = $data['part_brand'];
                $pdata->pmpno = $data['part_no'];
                $pdata->part_name_id = $data['part_name'];
                $pdata->car_manufacture_id = $data['car_manufacture'];
                $pdata->car_model = $data['car_model'];
                $pdata->ct = $data['category'];
                $pdata->gr = $data['group'];
                if(!empty($data['unit']))
                {
                    $pdata->unit = $data['unit'];
                }
                if(!empty($data['lc_price']))
                {
                    $pdata->lc_price = $data['lc_price'];
                }
                $pdata->current_stock = $data['current_stock'];
                $pdata->selling_price = $data['selling_price'];
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
                    if(!empty($row[1]))
                    {
                        $part_no_exist = 0;
                        $selectData = Products::where([['pmpno', '=', $row[1]], ['is_deleted', '=', '0']])->get()->toArray();
                        if(count($selectData) > 0) {
                            $part_no_exist = 1;
                        }else {
                            $part_no_exist = 0;
                        }
                        
                        $part_brand_id = "";
                        $selectPartBrand = PartBrand::select('part_brand_id')->where([['part_brand_name', '=', $row[0]]])->get()->toArray();
                        if(sizeof($selectPartBrand) > 0) {
                            $part_brand_id = $selectPartBrand[0]['part_brand_id'];
                        }else {
                            $data1 = new PartBrand;
                            $data1->part_brand_name = $row[0];
                            $data1->status = 1;
                            $data1->save();
                            $part_brand_id = $data1->id;
                        }
                        
                        $part_name_id = "";
                        $selectPartName = PartName::select('part_name_id')->where([['part_name', '=', $row[2]]])->get()->toArray();
                        if(sizeof($selectPartName) > 0) {
                            $part_name_id = $selectPartName[0]['part_name_id'];
                        }else {
                            $data2 = new PartName;
                            $data2->part_name = $row[2];
                            $data2->status = 1;
                            $data2->save();
                            $part_name_id = $data2->id;
                        }
                        
                        $category_id = "";
                        $selectProductCategories = ProductCategories::select('category_id')->where([['category_name', '=', $row[5]]])->get()->toArray();
                        if(sizeof($selectProductCategories) > 0) {
                            $category_id = $selectProductCategories[0]['category_id'];
                        }else {
                            $data3 = new ProductCategories;
                            $data3->category_name = $row[5];
                            $data3->status = 1;
                            $data3->save();
                            $category_id = $data3->id;
                        }
                        
                        $group_id = "";
                        $selectGroup = Group::select('group_id')->where([['group_name', '=', $row[6]]])->get()->toArray();
                        if(sizeof($selectGroup) > 0) {
                            $group_id = $selectGroup[0]['group_id'];
                        }else {
                            $data5 = new Group;
                            $data5->group_name = $row[6];
                            $data5->status = 1;
                            $data5->save();
                            $group_id = $data5->id;
                        }
                        
                        $unit_id = NULL;
                        // $selectUom = WmsUnit::select('unit_id')->where([['unit_name', '=', $row[7]]])->get()->toArray();
                        // if(sizeof($selectUom) > 0) {
                        //     $unit_id = $selectUom[0]['unit_id'];
                        // }else {
                        //     $data6 = new WmsUnit;
                        //     $data6->unit_name = $row[7];
                        //     $data6->save();
                        //     $unit_id = $data6->id;
                        // }
                        
                        $car_manufacture_id = "";
                        $selectUom = CarManufacture::select('car_manufacture_id')->where([['car_manufacture', '=', $row[3]]])->get()->toArray();
                        if(sizeof($selectUom) > 0) {
                            $car_manufacture_id = $selectUom[0]['car_manufacture_id'];
                        }else {
                            $data6 = new CarManufacture;
                            $data6->car_manufacture = $row[3];
                            $data6->save();
                            $car_manufacture_id = $data6->id;
                        }
                        
                        $brand_id = "";
                        $selectUom = Brand::select('brand_id')->where([['brand_name', '=', $row[4]]])->get()->toArray();
                        if(sizeof($selectUom) > 0) {
                            $brand_id = $selectUom[0]['brand_id'];
                        }else {
                            $data6 = new Brand;
                            $data6->brand_name = $row[4];
                            $data6->car_manufacture_id = $car_manufacture_id;
                            $data6->save();
                            $brand_id = $data6->id;
                        }
                        $part_no = NULL;
                        if(!empty($row[1]))
                        {
                            $part_no = $row[1];
                        }
                        $current_stock = 0;
                        if(!empty($row[7]))
                        {
                            $current_stock = $row[7];
                        }
                        $selling_price = 0;
                        $lc_price = NULL;
                        if(!empty($row[8]))
                        {
                            $lc_price = $row[8];
                            $selling_price = $lc_price + (($lc_price *30)/100);
                        }
                        
                        array_push($data, array('part_no_exist' => $part_no_exist, 'part_brand' => $part_brand_id, 'part_no' => $part_no, 'part_name' => $part_name_id, 'car_manufacture' => $car_manufacture_id, 'car_model' => $brand_id, 'category' => $category_id, 'group' => $group_id, 'unit' => $unit_id, 'lc_price' => $lc_price, 'current_stock' => $current_stock, 'selling_price' => $selling_price));
                    }
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
   
}