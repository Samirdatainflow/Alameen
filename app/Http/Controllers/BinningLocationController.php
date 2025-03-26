<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Suppliers;
use App\Orders;
use App\OrderDetail;
use App\OrderApproved;
use App\OrderReceived;
use App\Products;
use App\PartName;
use App\ConsignmentReceipt;
use App\CheckIn;
use App\CheckInDetails;
use App\BinningLocation;
use App\BinningLocationDetails;
use App\Location;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Plate;
use App\Place;
use App\BiningTask;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BinningLocationController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/binning_location")->with(array());
    }
    public function list_binning_location(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('binning_location');
            $query->select('binning_location_id', 'order_id', 'status');
            $query->where([['status', '!=', '2']]);
            //$query->groupBy('order_id');
            if($keyword) {
                $sql = "order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('order_id', 'asc');
                else
                    $query->orderBy('order_id', 'desc');
            }
            else
            {
                $query->orderBy('binning_location_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('quantity', function ($query) {
                $selectQty = BinningLocationDetails::where('order_id',$query->order_id)->sum('quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = "";
                if($query->status == 1) {
                    $details = '<a href="javascript:void(0)" class="view-binning-location" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                }
                return $details;
            })
            ->addColumn('action', function ($query) {
                $action = "";
                $BiningTask = BiningTask::where([['order_id', '=', $query->order_id]])->get()->toArray();
                if(sizeof($BiningTask) > 0) {
                    $action = "";
                }else {
                    $action = '<a href="javascript:void(0)" class="delete-binning-location" data-id="'.$query->order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                }
                return $action;
            })
            ->rawColumns(['details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Add
    public function add_binning_location(Request $request){
        $query = DB::table('check_in');
        $query->select('order_id');
        $query->where([['status', '=', 1]]);
        $query->whereNotIn('order_id', [DB::raw("SELECT order_id FROM `binning_location` WHERE `status` = 1")]);
        $listCheckIn = $query->get()->toArray();
        // print_r($listCheckIn); exit();
        // $CheckIn = CheckIn::pluck('order_id')->all();
        // $BinningLocation = BinningLocation::whereNotIn('order_id', $CheckIn)->select('order_id')->get()->toArray();
        // print_r($CheckIn); exit();
        // $query = DB::table('check_in as c');
        // $query->select('order_id1');
        //$query->join('binning_location as l', 'l.order_id', '=', 'c.order_id', 'left');
        //$query->where([['status', '=', '1']]);
        //$query->where([['c.status', '=', '1'], ['l.order_id', '=', null]]);
        //$query->orWhere('l.status', '=', 1);
        //$query->whereIn('order_id', [DB::raw("SELECT order_id FROM `binning_location` WHERE `order_id` IS null OR  order_id = 0")]);
        //$query->whereNull('l.order_id');
        //$listCheckIn = $query->get()->toArray();
        return \View::make("backend/receiving_and_putaway/binning_location_form")->with([
            'listCheckIn' => $listCheckIn
        ])->render();
    }
    public function get_order_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $orderDetails = [];
            $CheckInDetails = CheckInDetails::select('order_id', 'product_id', 'quantity', 'good_quantity')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            $orderDetails = $CheckInDetails;
            $LocationDetails = BinningLocationDetails::select('binning_location_details_id','order_id', 'product_id', 'quantity', 'status')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            if(sizeof($LocationDetails) > 0) {
                $orderDetails = $LocationDetails;
            }
            if(sizeof($orderDetails) > 0) {
                foreach($orderDetails as $data) {
                    $binning_location_details_id = "";
                    if(!empty($data['binning_location_details_id'])) $binning_location_details_id = $data['binning_location_details_id'];
                    $part_name = "";
                    $pmpno = "";
                    $price = "";
                    $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                    }
                    $location_id = "";
                    $zone_id = "";
                    $row_id = "";
                    $rack_id = "";
                    $plate_id = "";
                    $place_id = "";
                    $ZoneMaster = [];
                    $RowData = [];
                    $RackData = [];
                    $PlateData = [];
                    $PlaceData = [];
                    $max_capacity = "";
                    $remaining_capacity = "";
                    //echo $data['binning_location_details_id'];
                    $BinningLocationDetails = DB::select(DB::raw('select `bld`.* from `binning_location_details` as `bld` left join `place` as `pl` on `pl`.`place_id` = `bld`.`place_id` where (`bld`.`quantity` < pl.max_capacity and `product_id` = '.$data['product_id'].') LIMIT 1'));
                    if(sizeof($LocationDetails) > 0) {
                        $BinningLocationDetails = DB::table('binning_location_details')->select('*')->where('binning_location_details_id', $data['binning_location_details_id'])->get()->toArray();
                    }
                    //print_r($BinningLocationDetails);
                    //$query = DB::table('binning_location_details as bld')->select('bld.*')->join('place as pl', 'pl.place_id', '=', 'bld.place_id', 'left')->where([['bld.quantity', '<', 'pl.max_capacity'], ['product_id1', '=', $data['product_id']]])->get()->toArray();
                    // echo $BinningLocationDetails[0]->location_id;
                    // print_r($query); exit();
                    //$BinningLocationDetails = BinningLocationDetails::where([['product_id', '=', $data['product_id']]])->whereIn('product_id', [DB::raw("SELECT product_id FROM `product_price` WHERE `price` > 0 GROUP BY product_id")])->limit(1)->get()->toArray();
                    if(sizeof($BinningLocationDetails) > 0) {
                        if(!empty($BinningLocationDetails[0]->location_id)) {
                            $location_id = $BinningLocationDetails[0]->location_id;
                            $ZoneMaster = ZoneMaster::select('zone_id', 'zone_name')->where([['location_id', '=', $location_id]])->get()->toArray();
                        }
                        if(!empty($BinningLocationDetails[0]->zone_id)) {
                            $zone_id = $BinningLocationDetails[0]->zone_id;
                            $RowData = Row::select('row_id', 'row_name')->where([['zone_id', '=', $zone_id]])->get()->toArray();
                        }
                        if(!empty($BinningLocationDetails[0]->row_id)) {
                            $row_id = $BinningLocationDetails[0]->row_id;
                            $RackData = Rack::select('rack_id', 'rack_name')->where([['row_id', '=', $row_id]])->get()->toArray();
                        }
                        if(!empty($BinningLocationDetails[0]->rack_id)) {
                            $rack_id = $BinningLocationDetails[0]->rack_id;
                            $PlateData = Plate::select('plate_id', 'plate_name')->where([['rack_id', '=', $rack_id]])->get()->toArray();
                        }
                        if(!empty($BinningLocationDetails[0]->plate_id)) {
                            $plate_id = $BinningLocationDetails[0]->plate_id;
                            $PlaceData = Place::select('place_id', 'place_name')->where([['plate_id', '=', $plate_id]])->get()->toArray();
                        }
                        $present_quantity = 0;
                        if(!empty($BinningLocationDetails[0]->place_id)) {
                            $place_id = $BinningLocationDetails[0]->place_id;
                            $PlaceMax = Place::select('max_capacity')->where([['place_id', '=', $place_id]])->get()->toArray();
                            if(sizeof($PlaceMax) > 0) {
                                if(!empty($PlaceMax[0]['max_capacity'])) $max_capacity = $PlaceMax[0]['max_capacity'];
                            }
                            $present_quantity = BinningLocationDetails::where('place_id', $BinningLocationDetails[0]->place_id)->sum('quantity');
                        }
                        $remaining_capacity = $max_capacity;
                        //$present_quantity = $BinningLocationDetails[0]->quantity;
                        // if($BinningLocationDetails[0]->status == 0 && $request->order_id == $BinningLocationDetails[0]->order_id) {
                        //echo $data['status']." :present_quantity: ".$present_quantity." - ".$BinningLocationDetails[0]->quantity." -";
                        if(isset($data['status'])) {
                            if($data['status'] == 0) {
                                if($present_quantity > 0) {
                                    $present_quantity = $present_quantity - $BinningLocationDetails[0]->quantity;
                                }
                            }
                        }
                        //echo "Here ".$present_quantity. " end-";
                        if($present_quantity > 0) {
                            $remaining_capacity = $max_capacity - $present_quantity;
                        }
                        // $PresentQuantity = BinningLocationDetails::select(DB::raw('SUM(quantity) AS present_quantity'))->where([['product_id', '=', $data['product_id']]])->groupBy('place_id')->get()->toArray();
                        // if(sizeof($PresentQuantity) > 0) {
                        //     $present_quantity = $PresentQuantity[0]['present_quantity'];
                        //     $remaining_capacity = $max_capacity - $present_quantity;
                        // }
                        //print_r($PresentQuantity);
                    }
                    $good_quantity = "";
                    if(!empty($data['good_quantity'])) {
                        $good_quantity = $data['good_quantity'];
                    }else {
                        $good_quantity = $data['quantity'];
                    }
                    array_push($returnData, array('binning_location_details_id' => $binning_location_details_id,'product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'good_quantity' => $good_quantity, 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price, 'location_id' => $location_id, 'zone_id' => $zone_id, 'row_id' => $row_id, 'rack_id' => $rack_id, 'plate_id' => $plate_id, 'place_id' => $place_id, 'ZoneMaster' => $ZoneMaster, 'RowData' => $RowData, 'RackData' => $RackData, 'PlateData' => $PlateData, 'PlaceData' => $PlaceData, 'max_capacity' => $max_capacity, 'remaining_capacity' => $remaining_capacity));
                }
                $html = \View::make("backend/receiving_and_putaway/binning_location_order_details")->with([
                    'CheckInDetails' => $returnData,
                    'order_id' => $request->id,
                    'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', 0]])->get()->toArray()
                ])->render();
                return response()->json(["status" => 1, "message" => $html]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Save
    public function confirm_binning_location(Request $request) {
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->product_id) > 0) {
                $CheckIn = BinningLocation::where([['order_id', '=', $request->hidden_id]])->get()->toArray();
                if(sizeof($CheckIn) > 0) {
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        BinningLocationDetails::where([['order_id', '=', $request->hidden_id], ['product_id', '=', $request->product_id[$i]]])->update(['location_id' => $request->location_id[$i], 'zone_id' => $request->zone_id[$i], 'row_id' => $request->row_id[$i], 'rack_id' => $request->rack_id[$i], 'plate_id' => $request->plate_id[$i], 'place_id' => $request->place_id[$i]]);
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Update Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Update Faild!"]);
                    }
                }else {
                    $Select1 = BinningLocation::where([['order_id', '=', $request->order_id]])->get()->toArray();
                    if(sizeof($Select1) > 0) {
                        BinningLocation::where([['order_id', '=', $request->order_id]])->update(['status' => 1]);
                    }else {
                        $data = new BinningLocation;
                        $data->order_id = $request->order_id;
                        $data->status = "1";
                        $data->save();
                    }
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        //$Select2 = BinningLocationDetails::where([['order_id', '=', $request->order_id], ['product_id', '=', $request->product_id[$i]]])->get()->toArray();
                        if(!empty($request->binning_location_details_id[$i])) {
                            BinningLocationDetails::where([['binning_location_details_id', '=', $request->binning_location_details_id[$i]]])->update(['location_id' => $request->location_id[$i], 'zone_id' => $request->zone_id[$i], 'row_id' => $request->row_id[$i], 'rack_id' => $request->rack_id[$i], 'plate_id' => $request->plate_id[$i], 'place_id' => $request->place_id[$i], 'status' => 1]);
                        }else {
                            $data = new BinningLocationDetails;
                            $data->order_id = $request->order_id;
                            $data->product_id = $request->product_id[$i];
                            $data->quantity = $request->quantity[$i];
                            $data->location_id = $request->location_id[$i];
                            $data->zone_id = $request->zone_id[$i];
                            $data->row_id = $request->row_id[$i];
                            $data->rack_id = $request->rack_id[$i];
                            $data->plate_id = $request->plate_id[$i];
                            $data->place_id = $request->place_id[$i];
                            $data->status = "1";
                            $data->save();
                        }
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Save Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Save Faild!"]);
                    }
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Save Faild! Something is wrong!"]);
            }
        }
    }
    // Confirm
    public function save_binning_location(Request $request) {
        //echo sizeof($request->product_id); exit();
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->product_id) > 0) {
                $CheckIn = BinningLocation::where([['order_id', '=', $request->hidden_id]])->get()->toArray();
                if(sizeof($CheckIn) > 0) {
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        BinningLocationDetails::where([['order_id', '=', $request->hidden_id], ['product_id', '=', $request->product_id[$i]]])->update(['location_id' => $request->location_id[$i], 'zone_id' => $request->zone_id[$i], 'row_id' => $request->row_id[$i], 'rack_id' => $request->rack_id[$i], 'plate_id' => $request->plate_id[$i], 'place_id' => $request->place_id[$i]]);
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Update Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Update Faild!"]);
                    }
                }else {
                    $Select1 = BinningLocation::where([['order_id', '=', $request->order_id]])->get()->toArray();
                    if(empty($Select1)) {
                        $data = new BinningLocation;
                        $data->order_id = $request->order_id;
                        $data->status = "0";
                        $data->save();
                    }
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        //$Select2 = BinningLocationDetails::where([['order_id', '=', $request->order_id], ['product_id', '=', $request->product_id[$i]], ['quantity', '=', $request->quantity[$i]], ['place_id', '=', $request->place_id[$i]]])->get()->toArray();
                        if(!empty($request->binning_location_details_id[$i])) {
                            BinningLocationDetails::where([['binning_location_details_id', '=', $request->binning_location_details_id[$i]]])->update(['location_id' => $request->location_id[$i], 'zone_id' => $request->zone_id[$i], 'row_id' => $request->row_id[$i], 'rack_id' => $request->rack_id[$i], 'plate_id' => $request->plate_id[$i], 'place_id' => $request->place_id[$i], 'status' => 0]);
                        }else {
                            $data = new BinningLocationDetails;
                            $data->order_id = $request->order_id;
                            $data->product_id = $request->product_id[$i];
                            $data->quantity = $request->quantity[$i];
                            $data->location_id = $request->location_id[$i];
                            $data->zone_id = $request->zone_id[$i];
                            $data->row_id = $request->row_id[$i];
                            $data->rack_id = $request->rack_id[$i];
                            $data->plate_id = $request->plate_id[$i];
                            $data->place_id = $request->place_id[$i];
                            $data->status = "0";
                            $data->save();
                        }
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Save Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Save Faild!"]);
                    }
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Save Faild! Something is wrong!"]);
            }
        }
    }
    // Delete
    public function delete_binning_location(Request $request) {
        if ($request->ajax()) {
            $CheckIn = BinningLocation::where([['order_id', '=', $request->id]])->delete();
            if($CheckIn) {
                $CheckInDetails = BinningLocationDetails::where([['order_id', '=', $request->id]])->delete();
                if($CheckInDetails) {
                    return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Delete Faild! Something is wrong."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild!"]);
            }
        }
    }
    // View
    public function view_binning_location(Request $request){
        $returnData = [];
        $BinningLocationDetails = BinningLocationDetails::select('product_id', 'quantity', 'location_id', 'zone_id', 'row_id', 'rack_id', 'plate_id', 'place_id', 'status')->where([['order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($BinningLocationDetails) > 0) {
            foreach($BinningLocationDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $location_name = "";
                $zone_name = "";
                $row_name = "";
                $rack_name = "";
                $plate_name = "";
                $place_name = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                }
                $listZone = [];
                if(!empty($data['location_id'])) {
                    $Location = Location::select('location_name')->where([['location_id', '=', $data['location_id']]])->get()->toArray();
                    if(!empty($Location[0]['location_name'])) $location_name = $Location[0]['location_name'];
                    // $Zone = ZoneMaster::select('zone_id', 'zone_name')->where([['location_id', '=', $data['location_id']], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($Zone) > 0) {
                    //     $listZone = $Zone;
                    // }
                }
                $listRow = [];
                if(!empty($data['zone_id'])) {
                    $Zone = ZoneMaster::select('zone_name')->where([['zone_id', '=', $data['zone_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Zone[0]['zone_name'])) $zone_name = $Zone[0]['zone_name'];
                    // if(sizeof($Zone) > 0) {
                    //     $listZone = $Zone;
                    // }
                    // $Row = Row::select('row_id', 'row_name')->where([['zone_id', '=', $data['zone_id']], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($Row) > 0) {
                    //     $listRow = $Row;
                    // }
                }
                $listRack = [];
                if(!empty($data['row_id'])) {
                    $Row = Row::select('row_name')->where([['row_id', '=', $data['row_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Row[0]['row_name'])) $row_name = $Row[0]['row_name'];
                    // $Rack = Rack::select('rack_id', 'rack_name')->where([['row_id', '=', $data['row_id']], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($Zone) > 0) {
                    //     $listRack = $Rack;
                    // }
                }
                $listPlate = [];
                if(!empty($data['rack_id'])) {
                    $Rack = Rack::select('rack_name')->where([['rack_id', '=', $data['rack_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Rack[0]['rack_name'])) $rack_name = $Rack[0]['rack_name'];
                    // $Plate = Plate::select('plate_id', 'plate_name')->where([['rack_id', '=', $data['rack_id']], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($Plate) > 0) {
                    //     $listPlate = $Plate;
                    // }
                }
                $listPlace = [];
                if(!empty($data['plate_id'])) {
                    $Plate = Plate::select('plate_name')->where([['plate_id', '=', $data['plate_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Plate[0]['plate_name'])) $plate_name = $Plate[0]['plate_name'];
                    // $Place = Place::select('place_id', 'place_name')->where([['plate_id', '=', $data['plate_id']], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($Place) > 0) {
                    //     $listPlace = $Place;
                    //     //if(!empty($Place[0]['max_capacity'])) $max_capacity = $Place[0]['max_capacity'];
                    // }
                }
                if(!empty($data['place_id'])) {
                    $Place = Place::select('place_name')->where([['place_id', '=', $data['place_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Place[0]['place_name'])) $place_name = $Place[0]['place_name'];
                }
                $max_capacity = 0;
                $remaining_capacity = 0;
                $present_quantity = 0;
                if(!empty($data['place_id'])) {
                    //$place_id = $BinningLocationDetails[0]->place_id;
                    $PlaceMax = Place::select('max_capacity')->where([['place_id', '=', $data['place_id']]])->get()->toArray();
                    if(sizeof($PlaceMax) > 0) {
                        if(!empty($PlaceMax[0]['max_capacity'])) $max_capacity = $PlaceMax[0]['max_capacity'];
                    }
                    $present_quantity = BinningLocationDetails::where('place_id', $data['place_id'])->sum('quantity');
                }
                $remaining_capacity = $max_capacity;
                //$present_quantity = $data['quantity'];
                if($data['status'] == 0) {
                    $present_quantity = 0;
                }
                if($present_quantity > 0) {
                    $remaining_capacity = $max_capacity - $present_quantity;
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name, 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price, 'listZone' => $listZone, 'listRow' => $listRow, 'listRack' => $listRack, 'listPlate' => $listPlate, 'listPlace' => $listPlace, 'max_capacity' => $max_capacity, 'remaining_capacity' => $remaining_capacity));
            }
        }
        //print_r($returnData); exit();
        $html = \View::make("backend/receiving_and_putaway/view_binning_location")->with([
            'BinningLocationDetails' => $returnData,
            'order_id' => $request->id,
            'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', 0]])->get()->toArray()
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    public function binning_location_export(){
        $query = DB::table('binning_location')
        ->select('binning_location_id', 'order_id')
        ->where([['status', '!=', '2']])
        ->orderBy('binning_location_id', 'DESC');
        $data = $query->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Binning_location_id');
        $sheet->setCellValue('B1', 'Order_id');
        $sheet->setCellValue('C1', 'Quantity');
        $rows = 2;
        foreach($data as $empDetails){
            $quantity = '';
            if (!empty($empDetails->order_id)) {
                $quantity = BinningLocationDetails::where('order_id',$empDetails->order_id)->sum('quantity');
            }
            $sheet->setCellValue('A' . $rows, $empDetails->binning_location_id);
            $sheet->setCellValue('B' . $rows, $empDetails->order_id);
            $sheet->setCellValue('C' . $rows, $quantity);
            $rows++;
        }
        $fileName = "binning_location_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}