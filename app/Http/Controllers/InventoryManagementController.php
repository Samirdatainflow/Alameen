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
use DB;
use DataTables;
use App\Products;
use App\BinningLocationDetails;
use App\Location;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Plate;
use App\Place;
use App\AutoFillBinningLocation;
use App\SaleOrderDetails;
use App\AlternatePartNo;

class InventoryManagementController extends Controller {
    public function inventory_management() {
        return \View::make("backend/inventory/inventory_management")->with([
            'unit_data' => WmsUnit::select('unit_id', 'unit_name')->get()->toArray(),
            'product_categoriy_data' => ProductCategories::select('category_id', 'category_name')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name_id', 'desc')->get()->toArray(),
        ]);
    }
    // Inventory DataTAble
    public function list_inventory_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $stock_status = $request->input('stock_status');
            $keyword = $request->input('search.value');
            $query = DB::table('products as p');
            $query->select('p.*', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            if($keyword) {
                $query->whereRaw("(pn.part_name like '%$keyword%' or replace(p.pmpno, '-','') like '%$keyword%' or p.pmpno like '%$keyword%' or p.pmpno like '%$keyword%')");
                // $sql = "pn.part_name like ?";
                // $sql2 = "p.pmpno like ?";
                // $query->whereRaw($sql, ["%{$keyword}%"]);
                // $query->orWhereRaw($sql2, ["%{$keyword}%"]);
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
                    $query->orderBy('product_id', 'asc');
                else
                    $query->orderBy('product_id', 'desc');
            }else {
                $query->orderBy('product_id', 'DESC');
            }
            if(!empty($request->filter_part_no)) {
                $query->whereRaw('(replace(p.pmpno, "-","") LIKE "%'.$request->filter_part_no.'%" or p.pmpno like "%'.$request->filter_part_no.'%")');
                //$query->where('replace(p.pmpno, "-","")', 'like', '%' . $request->filter_part_no . '%');
            }
            if(!empty($request->filter_part_name)) {
                $query->where([['p.part_name_id', '=', $request->filter_part_name]]);
            }
            if(!empty($request->filter_units)) {
                $query->where([['unit', '=', $request->filter_units]]);
            }
            if(!empty($request->filter_category)) {
                $query->where([['ct', '=', $request->filter_category]]);
            }
            $query->where([['is_deleted', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('unit', function ($query) {
                $unit = '';
                if(!empty($query->unit)) {
                    $selectUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $query->unit]])->get()->toArray();
                    if(count($selectUnit) > 0) {
                        if(!empty($selectUnit[0]['unit_name'])) $unit = $selectUnit[0]['unit_name'];
                    }
                }
                return $unit;
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
            ->addColumn('stock_alert', function ($query) {
                return $query->stock_alert;
            })
            // ->addColumn('alternate_part_no', function ($query) {
                
            //     $AliPartNo = [];
            //     $selectAltPart = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $query->product_id]])->get()->toArray();
            //     if(sizeof($selectAltPart) > 0)
            //     {
            //         foreach($selectAltPart as $altp)
            //         {
            //             $AliPartNo[] = $altp['alternate_no'];
            //         }
            //     }
            //     return implode(',', $AliPartNo);
            //     //return "";
            // })
            ->addColumn('current_stock', function ($query) {
                $available_stock = $query->current_stock;
                // if($query->qty_on_order > 0) {
                //     $available_stock = $query->current_stock - $query->qty_on_order;
                // }
                // if($available_stock < 0) {
                //     $available_stock = 0;
                // }
                // $approve_quantity = SaleOrderDetails::where([['product_id', '=', $query->product_id]])->sum('qty_appr');
                // if($approve_quantity > 0) {
                //     $available_stock = $available_stock - $approve_quantity;
                // }
                // if($available_stock < 1) {
                //     $available_stock = 0;
                // }
                return $available_stock.' <a href="javascript:void(0)" class="quantity-on-hand-form-open" data-product_id="'.$query->product_id.'" data-pmpno="'.$query->pmpno.'" data-part_name="'.$query->part_name.'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            })
            ->addColumn('cost', function ($query) {
                return 0;
            })
            ->addColumn('pmrprc', function ($query) {
                $pmrprc = '';
                $pmrprc .= $query->pmrprc.'<br><a href="javascript:void(0)" class="customer_wise" data-product_id="'.$query->product_id.'"><span class="badge badge-success text-white">Customer wise price</span></a>';
                return $pmrprc;
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
            ->addColumn('location', function ($query) {
                return '<a href="javascript:void(0)" class="view-binning-location" data-product_id="'.$query->product_id.'"><span class="badge badge-primary"><i class="fa fa-eye" aria-hidden="true"></i> View</span></a>';
            })
            ->rawColumns(['current_stock','pmrprc','status', 'location'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function inventory_customer_form(Request $request){
        // $sale_order_details = 
        if ($request->ajax()) {
            $price_data=DB::table('sale_order_details as s')
            ->join('products','s.product_id','=','products.product_id')
            ->join('sale_order','s.sale_order_id','=','sale_order.sale_order_id')
            ->join('part_name','products.part_name_id','=','part_name.part_name_id')
            ->join('clients','sale_order.client_id','=','clients.client_id')
            ->select('s.product_price','s.qty_appr','products.product_id','products.pmpno','part_name.part_name','clients.customer_name', 'clients.customer_id')
            ->where([['s.product_id', '=', $request->product_id]])->orderBy('s.product_price','desc')->get()->toArray();
            $html = view('backend/inventory/customer_wise_model')->with([
                'price_data' => $price_data
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function quantity_on_hand_form_open(Request $request){
        if ($request->ajax()) {
            $ZoneMaster = [];
            $RowData = [];
            $RackData = [];
            $PlateData = [];
            $PlaceData = [];
            $BinningLocationDetails = BinningLocationDetails::select('*')->where('product_id', $request->product_id)->get()->toArray();
            if(sizeof($BinningLocationDetails) > 0) {
                if(!empty($BinningLocationDetails[0]['location_id'])) {
                    $location_id = $BinningLocationDetails[0]['location_id'];
                    $ZoneMaster = ZoneMaster::select('zone_id', 'zone_name')->where([['location_id', '=', $location_id]])->get()->toArray();
                }
                if(!empty($BinningLocationDetails[0]['zone_id'])) {
                    $zone_id = $BinningLocationDetails[0]['zone_id'];
                    $RowData = Row::select('row_id', 'row_name')->where([['zone_id', '=', $zone_id]])->get()->toArray();
                }
                if(!empty($BinningLocationDetails[0]['row_id'])) {
                    $row_id = $BinningLocationDetails[0]['row_id'];
                    $RackData = Rack::select('rack_id', 'rack_name')->where([['row_id', '=', $row_id]])->get()->toArray();
                }
                if(!empty($BinningLocationDetails[0]['rack_id'])) {
                    $rack_id = $BinningLocationDetails[0]['rack_id'];
                    $PlateData = Plate::select('plate_id', 'plate_name')->where([['rack_id', '=', $rack_id]])->get()->toArray();
                }
                if(!empty($BinningLocationDetails[0]['plate_id'])) {
                    $plate_id = $BinningLocationDetails[0]['plate_id'];
                    $PlaceData = Place::select('place_id', 'place_name')->where([['plate_id', '=', $plate_id]])->get()->toArray();
                }
            }
            $html = view('backend/inventory/quantity_on_hand_form')->with([
                'product_id' => $request->product_id,
                'Location' => Location::select('location_id', 'location_name')->where('is_deleted', 0)->get()->toArray(),
                'BinningLocationDetails' => $BinningLocationDetails,
                'ZoneMaster' => $ZoneMaster,
                'RowData' => $RowData,
                'RackData' => $RackData,
                'PlateData' => $PlateData,
                'PlaceData' => $PlaceData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function save_quantity_on_hand_form_open(Request $request)
    {
        if(!empty($request->product_id))
        {
            $current_stock = 0;
            if(!empty($request->current_stock))
            {
                $current_stock = $request->current_stock;
            }
            $Products = Products::where('product_id', $request->product_id)->update(array('current_stock' => DB::raw('current_stock + '.$current_stock)));
            if($Products) {
                $checkLocation = DB::table('binning_location_details')->where([['product_id', '=', $request->product_id], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '=', $request->rack_id], ['plate_id', '=', $request->plate_id], ['place_id', '=', $request->place_id]])->get()->toArray();
                if(sizeof($checkLocation) > 0) {
                    BinningLocationDetails::where('product_id', $request->product_id)->update(['quantity' => $request->current_stock, 'location_id' => $request->location_id, 'zone_id' => $request->zone_id, 'row_id' => $request->row_id, 'rack_id' => $request->rack_id, 'plate_id' => $request->plate_id, 'place_id' => $request->place_id]);
                }else {
                    $data = new BinningLocationDetails;
                    $data->quantity = $request->current_stock;
                    $data->product_id = $request->product_id;
                    $data->location_id = $request->location_id;
                    $data->zone_id = $request->zone_id;
                    $data->row_id = $request->row_id;
                    $data->rack_id = $request->rack_id;
                    $data->plate_id = $request->plate_id;
                    $data->place_id = $request->place_id;
                    $data->save();
                }
                return response()->json(["status" => 1, "msg" => "Update Succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Update Faild!"]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Something is wrong!"]);
        }
    }
    public function view_binning_location(Request $request){
        if ($request->ajax()) {
            $query = DB::table('binning_location_details as bd');
            $query->join('products as p', 'p.product_id', '=', 'bd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('location as l', 'l.location_id', '=', 'bd.location_id', 'left');
            $query->join('zone_master as z', 'z.zone_id', '=', 'bd.zone_id', 'left');
            $query->join('row as r', 'r.row_id', '=', 'bd.row_id', 'left');
            $query->join('rack as ra', 'ra.rack_id', '=', 'bd.rack_id', 'left');
            $query->join('plate as plt', 'plt.plate_id', '=', 'bd.plate_id', 'left');
            $query->join('place as plc', 'plc.place_id', '=', 'bd.place_id', 'left');
            $query->where([['bd.product_id', '=', $request->product_id]]);
            $query->select('pn.part_name', 'p.pmpno', 'bd.quantity', 'l.location_name', 'z.zone_name', 'r.row_name', 'ra.rack_name', 'plt.plate_name', 'plc.place_name');
            $LocationDetails = $query->get()->toArray();
            $binningData = [];
            if(sizeof($LocationDetails) < 1) {
                $count_no = 1;
                $auto_fill_binning_location = AutoFillBinningLocation::select('count_no')->get()->toArray();
                if(sizeof($auto_fill_binning_location) >0) {
                    $count_no = $auto_fill_binning_location[0]['count_no'] + 1;
                }
                $part_name = "";
                $pmpno = "";
                $price = "";
                $quantity = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'current_stock')->where([['product_id', '=', $request->product_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                    if(!empty($Products[0]['current_stock'])) $quantity = $Products[0]['current_stock'];
                }
                $location_id = "";
                $zone_id = "Z".$count_no;
                $row_id = "Ro".$count_no;
                $rack_id = "Ra".$count_no;
                $plate_id = "Le".$count_no;
                $place_id = "Po".$count_no;
                array_push($binningData, array('product_id' => $request->product_id, 'quantity' => $quantity, 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price, 'location_id' => $location_id, 'zone_id' => $zone_id, 'row_id' => $row_id, 'rack_id' => $rack_id, 'plate_id' => $plate_id, 'place_id' => $place_id));
            }
            $html = view('backend/inventory/view_binning_location')->with([
                'LocationDetails' => $LocationDetails,
                'binningData' => $binningData,
                'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', 0]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function product_auto_fill_binning_location(Request $request) {
        if ($request->ajax()) {
            $zone = new ZoneMaster;
            $zone->location_id = $request->location_id;
            $zone->zone_name = $request->zone_id;
            $zone->status = "1";
            $zone->save();
            $last_zone_id = $zone->id;
            //echo $last_zone_id; exit();
            // Row
            $row = new Row;
            $row->location_id = $request->location_id;
            $row->zone_id = $last_zone_id;
            $row->row_name = $request->row_id;
            $row->status = "1";
            $row->save();
            $last_row_id = $row->id;
            // Rack
            $rack = new Rack;
            $rack->location_id = $request->location_id;
            $rack->zone_id = $last_zone_id;
            $rack->row_id = $last_row_id;
            $rack->rack_name = $request->rack_id;
            $rack->status = "1";
            $rack->save();
            $last_rack_id = $rack->id;
            // Plate
            $plate = new Plate;
            $plate->location_id = $request->location_id;
            $plate->zone_id = $last_zone_id;
            $plate->row_id = $last_row_id;
            $plate->rack_id = $last_rack_id;
            $plate->plate_name = $request->plate_id;
            $plate->status = "1";
            $plate->save();
            $last_plate_id = $plate->id;
            // Rack
            $place = new Place;
            $place->location_id = $request->location_id;
            $place->zone_id = $last_zone_id;
            $place->row_id = $last_row_id;
            $place->rack_id = $last_row_id;
            $place->plate_id = $last_plate_id;
            $place->place_name = $request->place_id;
            $place->max_capacity = $request->max_capacity;
            $place->status = "1";
            $place->save();
            $last_place_id = $place->id;
            //echo "product_id- ".$request->product_id." quantity- ".$request->quantity." location_id- ".$request->location_id." last_zone_id- ".$last_zone_id." last_row_id- ".$last_row_id." last_rack_id- ".$last_rack_id." last_plate_id- ".$last_plate_id." last_place_id- ".$last_place_id;
            $binning = new BinningLocationDetails;
            $binning->product_id = $request->product_id;
            $binning->quantity = $request->quantity;
            $binning->location_id = $request->location_id;
            $binning->zone_id = $last_zone_id;
            $binning->row_id = $last_row_id;
            $binning->rack_id = $last_rack_id;
            $binning->plate_id = $last_plate_id;
            $binning->place_id = $last_place_id;
            $binning->status = "1";
            $binning->save();
            //echo $data->id; exit();
            $count_no = 1;
            $auto_fill_binning_location = AutoFillBinningLocation::select('*')->get()->toArray();
            if(sizeof($auto_fill_binning_location) >0){
                $count_no = $auto_fill_binning_location[0]['count_no'] + 1;
                $auto_fill_binning_location_id = $auto_fill_binning_location[0]['auto_fill_binning_location_id'];
                AutoFillBinningLocation::where([['auto_fill_binning_location_id', '=', $auto_fill_binning_location_id]])->update(['count_no' => $count_no]);
            }else {
                $data2 = new AutoFillBinningLocation;
                $data2->count_no = $count_no;
                $data2->save();
            }
            return response()->json(["status" => 1, "msg" => "Save Succesful."]);
            // if($qry) {
                
            // }else {
            //     return response()->json(["status" => 0, "msg" => "Save Faild!"]);
            // }
        }
    }
    public function auto_fill_binning_location_cronjob(Request $request){
        $flag = 0;
        $query = DB::table('products AS t1')->select('t1.product_id', 't1.current_stock')->leftJoin('binning_location_details AS t2','t2.product_id','=','t1.product_id')->whereNull('t2.product_id')->limit(5000)->get()->toArray();
        if(sizeof($query) > 0) {
            foreach ($query as $val) {
                $location_id = "";
                $Location = Location::select('location_id')->where([['is_deleted', '=', 0]])->orderBy('location_id', 'desc')->limit(1)->get()->toArray();
                if(sizeof($Location) > 0) {
                    $location_id = $Location[0]['location_id'];
                }
                $count_no = 1;
                $auto_fill_binning_location = AutoFillBinningLocation::select('count_no')->get()->toArray();
                if(sizeof($auto_fill_binning_location) >0) {
                    $count_no = $auto_fill_binning_location[0]['count_no'] + 1;
                }
                $zone_id = "Z".$count_no;
                $row_id = "Ro".$count_no;
                $rack_id = "Ra".$count_no;
                $plate_id = "Le".$count_no;
                $place_id = "Po".$count_no;
                $zone = new ZoneMaster;
                $zone->location_id = $location_id;
                $zone->zone_name = $zone_id;
                $zone->status = "1";
                $zone->save();
                $last_zone_id = $zone->id;
                // Row
                $row = new Row;
                $row->location_id = $location_id;
                $row->zone_id = $last_zone_id;
                $row->row_name = $row_id;
                $row->status = "1";
                $row->save();
                $last_row_id = $row->id;
                // Rack
                $rack = new Rack;
                $rack->location_id = $location_id;
                $rack->zone_id = $last_zone_id;
                $rack->row_id = $last_row_id;
                $rack->rack_name = $rack_id;
                $rack->status = "1";
                $rack->save();
                $last_rack_id = $rack->id;
                // Plate
                $plate = new Plate;
                $plate->location_id = $location_id;
                $plate->zone_id = $last_zone_id;
                $plate->row_id = $last_row_id;
                $plate->rack_id = $last_rack_id;
                $plate->plate_name = $plate_id;
                $plate->status = "1";
                $plate->save();
                $last_plate_id = $plate->id;
                // Rack
                $place = new Place;
                $place->location_id = $location_id;
                $place->zone_id = $last_zone_id;
                $place->row_id = $last_row_id;
                $place->rack_id = $last_row_id;
                $place->plate_id = $last_plate_id;
                $place->place_name = $place_id;
                $place->max_capacity = "50";
                $place->status = "1";
                $place->save();
                $last_place_id = $place->id;
                $binning = new BinningLocationDetails;
                $binning->product_id = $val->product_id;
                $binning->quantity = $val->current_stock;
                $binning->location_id = $location_id;
                $binning->zone_id = $last_zone_id;
                $binning->row_id = $last_row_id;
                $binning->rack_id = $last_rack_id;
                $binning->plate_id = $last_plate_id;
                $binning->place_id = $last_place_id;
                $binning->status = "1";
                $binning->save();
                //echo $data->id; exit();
                $count_no = 1;
                $auto_fill_binning_location = AutoFillBinningLocation::select('*')->get()->toArray();
                if(sizeof($auto_fill_binning_location) >0){
                    $count_no = $auto_fill_binning_location[0]['count_no'] + 1;
                    $auto_fill_binning_location_id = $auto_fill_binning_location[0]['auto_fill_binning_location_id'];
                    AutoFillBinningLocation::where([['auto_fill_binning_location_id', '=', $auto_fill_binning_location_id]])->update(['count_no' => $count_no]);
                }else {
                    $data2 = new AutoFillBinningLocation;
                    $data2->count_no = $count_no;
                    $data2->save();
                }
                $flag++;
            }
        }
        if($flag == sizeof($query)) {
            echo "success";
        }else {
            echo "error";
        }
    }
    public function check_location(Request $request) {
        $query = DB::table('binning_location_details')->where([['product_id', '!=', $request->product_id], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '=', $request->rack_id], ['plate_id', '=', $request->plate_id], ['place_id', '=', $request->place_id]])->get()->toArray();
        if(sizeof($query) > 0) {
            return response()->json(["status" => 0, "msg" => "This location already asigned for another product"]);
        }else {
            return response()->json(["status" => 1]);
        }
    }
}