<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\ProductCategories;
use App\PartName;
use App\WmsUnit;
use App\CheckInDetails;
use App\PurchaseOrderReturnDetails;
use App\OrderDetail;
use App\Suppliers;
use App\Warehouses;
use App\Products;
use DB;
use DataTables;

class InventoryReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/inventory_report")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function inventory_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $stock_status = $request->input('stock_status');
            $keyword = $request->input('search.value');
            $query = DB::table('products as p');
            $query->select('p.*', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            if($keyword) {
                $query->whereRaw("(p.product_id like '%$keyword%' or pn.part_name like '%$keyword%' or replace(p.pmpno, '-','') like '%$keyword%')");
            }
            if(!empty($stock_status)) {
                if($stock_status == "o") {
                    $query->where("current_stock", 0);
                }else {
                    $query->whereRaw("current_stock > 0 AND stock_alert >= current_stock");
                }
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('p.product_id', 'asc');
                else
                    $query->orderBy('p.product_id', 'desc');
            }else {
                $query->orderBy('p.product_id', 'DESC');
            }
            if(!empty($request->filter_product_id)) {
                $query->where([['p.product_id', '=', $request->filter_product_id]]);
            }
            if(!empty($request->filter_part_no)) {
                $query->whereRaw('(replace(p.pmpno, "-","") LIKE "%'.$request->filter_part_no.'%")');
            }
            if(!empty($request->filter_part_name)) {
                $query->where([['p.part_name_id', '=', $request->filter_part_name]]);
            }
            if(!empty($request->filter_category)) {
                $query->where([['p.ct', '=', $request->filter_category]]);
            }
            if(!empty($request->filter_supplier)) {
                if(sizeof($request->filter_supplier) > 0) {
                    $query->whereIn('p.supplier_id', $request->filter_supplier);
                }
            }
            if(!empty($request->filter_warehouse)) {
                if(sizeof($request->filter_warehouse) > 0) {
                    $query->whereIn('p.warehouse_id', $request->filter_warehouse);
                }
            }
            if(!empty($request->filter_status)) {
                if($request->filter_status == 'Alert') {
                    $query->whereRaw("(`p`.`current_stock` > 0 and `p`.`stock_alert` >= p.current_stock)");
                }
                if($request->filter_status == 'Avilable') {
                    $query->whereRaw("(`p`.`current_stock` > 0 and `p`.`current_stock` > p.stock_alert)");
                }
                if($request->filter_status == 'Out of Stock') {
                    $query->where([['p.current_stock', '<', '1']]);
                }
            }
            $query->where([['p.is_deleted', '=', '0']]);
            $datatable_array=Datatables::of($query)
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
            ->addColumn('cost', function ($query) {
                return 0;
            })
            ->addColumn('part_no', function ($query) {
                return $query->pmpno;
            })
            ->addColumn('transit_quantity', function ($query) {
                $transit_quantity = OrderDetail::where([['product_id', '=', $query->product_id]])->sum('qty');
                $CheckInDetails = CheckInDetails::where([['product_id', '=', $query->product_id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($CheckInDetails) > 0) {
                    $transit_quantity = 0;
                }
                return $transit_quantity;
            })
            ->addColumn('supplier_name', function ($query) {
                $supplier_ids = explode(',', $query->supplier_id);
                $supplier_name = '';
                $Suppliers = Suppliers::select('full_name')->whereIn('supplier_id', $supplier_ids)->get()->toArray();
                if(sizeof($Suppliers) > 0) {
                    for ($i = 0; $i < sizeof($Suppliers); $i++) {
                        $supplier_name .= $Suppliers[0]['full_name'];
                        if (sizeof($Suppliers) - 1 > $i) {
                            $supplier_name .= ', ';
                        }
                    };
                }
                return $supplier_name;
            })
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query->warehouse_id);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->addColumn('damage_quantity', function ($query) {
                $CheckInDetails = CheckInDetails::where([['product_id', '=', $query->product_id], ['status', '=', '1']])->sum('bad_quantity');
                $PurchaseOrderReturnDetails = PurchaseOrderReturnDetails::where([['product_id', '=', $query->product_id], ['status', '=', '1']])->sum('return_quantity');
                return $CheckInDetails - $PurchaseOrderReturnDetails;
            })
            ->addColumn('status', function ($query) {
                $status = '';
                if($query->current_stock>0) {
                        if($query->stock_alert>=$query->current_stock)
                            $status .= '<a href="javascript:void(0)" class="inventory-change-status"><span class="badge badge-warning text-white">Alert</span></a>';
                        else
                            $status .= '<a href="javascript:void(0)" class="inventory-change-status"><span class="badge badge-success">Avilable</span></a>';   
                    }else {
                        $status .= '<a href="javascript:void(0)" class="inventory-change-status"><span class="badge badge-danger">Out of Stock</span></a>';
                    }
                return $status;
            })
            ->rawColumns(['pmrprc','status'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 Highest Selling Price Product
    public function top_5_highest_selling_price_product() {
        return \View::make("backend/reports/inventory_top_5_highest_selling_price")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function top_5_highest_selling_price_product_list(Request $request) {
        if ($request->ajax()) {
            //echo $_POST['start']; exit();
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('select `p`.*, `pn`.`part_name` from `products` as `p` left join `part_name` as `pn` on `pn`.`part_name_id` = `p`.`part_name_id` where (`p`.`is_deleted` = 0) order by `p`.`pmrprc` desc, `p`.`product_id` desc limit 5 offset 0');
            //print_r($query); exit();
            $datatable_array=Datatables::of($query)
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
            ->addColumn('cost', function ($query) {
                return 0;
            })
            ->addColumn('part_no', function ($query) {
                return $query->pmpno;
            })
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query->warehouse_id);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->rawColumns(['warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 Highest Profit Product
    public function top_5_high_profit_product() {
        return \View::make("backend/reports/inventory_top_5_high_profit_product")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function top_5_high_profit_product_list(Request $request) {
        if ($request->ajax()) {
            //echo $_POST['start']; exit();
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('select `p`.*, `pn`.`part_name`, (p.pmrprc-p.lc_price)*100/p.pmrprc as total_margin from `products` as `p` left join `part_name` as `pn` on `pn`.`part_name_id` = `p`.`part_name_id` where (`p`.`is_deleted` = 0) order by total_margin desc limit 5 offset 0');
            //print_r($query); exit();
            $datatable_array=Datatables::of($query)
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
            ->addColumn('total_margin', function ($query) {
                return $total_margin = $query->total_margin.' (%)';
            })
            ->addColumn('cost', function ($query) {
                return 0;
            })
            ->addColumn('part_no', function ($query) {
                return $query->pmpno;
            })
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query->warehouse_id);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->rawColumns(['warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 Highest Moving Invenory Product
    public function top_5_high_moving_inventory() {
        return \View::make("backend/reports/top_5_high_moving_inventory")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function top_5_high_moving_inventory_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT sale_order_details.product_id,count(sale_order_details.product_id) AS product_ids FROM sale_order_details GROUP BY product_id ORDER By product_ids desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $pmpno = "";
                    $ct = "";
                    $warehouse_id = "";
                    $supplier_id = "";
                    $part_name = "";
                    $Products = Products::select('pmpno', 'ct', 'warehouse_id', 'part_name_id', 'supplier_id')->where([['product_id', '=', $val->product_id]])->get()->toArray();
                    if(sizeof($Products)>0){
                        $pmpno = $Products[0]['pmpno'];
                        $ct = $Products[0]['ct'];
                        $warehouse_id = $Products[0]['warehouse_id'];
                        $supplier_id = $Products[0]['supplier_id'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName)>0){
                            $part_name = $PartName[0]['part_name'];
                        }
                    }
                    array_push($arrayData, array('product_id' => $val->product_id, 'pmpno' => $pmpno, 'ct' => $ct, 'warehouse_id' => $warehouse_id, 'part_name' => $part_name, 'supplier_id' => $supplier_id));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->addColumn('ct', function ($query) {
                $category_id = '';
                if(!empty($query['ct'])) {
                    $selectCategory = ProductCategories::select('category_name')->where([['category_id', '=', $query['ct']]])->get()->toArray();
                    if(count($selectCategory) > 0) {
                        if(!empty($selectCategory[0]['category_name'])) $category_id = $selectCategory[0]['category_name'];
                    }
                }
                return $category_id;
            })
            ->addColumn('supplier_name', function ($query) {
                $supplier_ids = explode(',', $query['supplier_id']);
                $supplier_name = '';
                $Suppliers = Suppliers::select('full_name')->whereIn('supplier_id', $supplier_ids)->get()->toArray();
                if(sizeof($Suppliers) > 0) {
                    for ($i = 0; $i < sizeof($Suppliers); $i++) {
                        $supplier_name .= $Suppliers[0]['full_name'];
                        if (sizeof($Suppliers) - 1 > $i) {
                            $supplier_name .= ', ';
                        }
                    };
                    //$supplier_name = $Suppliers[0]['full_name'];
                }
                return $supplier_name;
            })
            ->addColumn('transit_quantity', function ($query) {
                $transit_quantity = OrderDetail::where([['product_id', '=', $query['product_id']]])->sum('qty');
                $CheckInDetails = CheckInDetails::where([['product_id', '=', $query['product_id']], ['status', '=', '1']])->get()->toArray();
                if(sizeof($CheckInDetails) > 0) {
                    $transit_quantity = 0;
                }
                return $transit_quantity;
            })
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query['warehouse_id']);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->rawColumns(['warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 High Damage Product
    public function top_5_high_damage_product() {
        return \View::make("backend/reports/inventory_top_5_high_damage_product")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function top_5_high_damage_product_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT product_id,sum(bad_quantity) AS bad_product FROM check_in_details GROUP BY product_id ORDER By bad_product desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $pmpno = "";
                    $ct = "";
                    $warehouse_id = "";
                    $supplier_id = "";
                    $part_name = "";
                    $Products = Products::select('pmpno', 'warehouse_id', 'part_name_id')->where([['product_id', '=', $val->product_id]])->get()->toArray();
                    if(sizeof($Products)>0){
                        $pmpno = $Products[0]['pmpno'];
                        $warehouse_id = $Products[0]['warehouse_id'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName)>0){
                            $part_name = $PartName[0]['part_name'];
                        }
                    }
                    array_push($arrayData, array('product_id' => $val->product_id, 'bad_quantity' => $val->bad_product, 'pmpno' => $pmpno, 'warehouse_id' => $warehouse_id, 'part_name' => $part_name));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query['warehouse_id']);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->rawColumns(['warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 High Damage Supplier Product
    public function top_5_high_damage_quantity_supplier() {
        return \View::make("backend/reports/inventory_top_5_high_damage_quantity_supplier")->with([
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function top_5_high_damage_quantity_supplier_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT product_id,supplier_id,sum(bad_quantity) AS bad_product FROM check_in_details GROUP BY supplier_id,product_id ORDER By bad_product desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $pmpno = "";
                    $ct = "";
                    $warehouse_id = "";
                    $supplier_id = "";
                    $part_name = "";
                    $Products = Products::select('pmpno', 'warehouse_id', 'part_name_id')->where([['product_id', '=', $val->product_id]])->get()->toArray();
                    if(sizeof($Products)>0){
                        $pmpno = $Products[0]['pmpno'];
                        $warehouse_id = $Products[0]['warehouse_id'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName)>0){
                            $part_name = $PartName[0]['part_name'];
                        }
                    }
                    array_push($arrayData, array('product_id' => $val->product_id, 'supplier_id' => $val->supplier_id, 'bad_quantity' => $val->bad_product, 'pmpno' => $pmpno, 'warehouse_id' => $warehouse_id, 'part_name' => $part_name));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->addColumn('supplier_name', function ($query) {
                $supplier_name = '';
                $Suppliers = Suppliers::select('full_name')->where([['supplier_id', '=', $query['supplier_id']]])->get()->toArray();
                if(sizeof($Suppliers) > 0) {
                    $supplier_name = $Suppliers[0]['full_name'];
                }
                return $supplier_name;
            })
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_ids = explode(',', $query['warehouse_id']);
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->whereIn('warehouse_id', $warehouse_ids)->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    for ($i = 0; $i < sizeof($Warehouses); $i++) {
                        $warehouse_name .= $Warehouses[0]['name'];
                        if (sizeof($Warehouses) - 1 > $i) {
                            $warehouse_name .= ', ';
                        }
                    };
                }
                return $warehouse_name;
            })
            ->rawColumns(['supplier_name','warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}