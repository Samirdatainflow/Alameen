<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Packing;
use App\SaleOrder;
use App\SaleOrderDetails;
use App\Products;
use App\PartName;
use App\PackingDetails;
use App\Clients;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PackingController extends Controller {

    public function index() {
        return \View::make("backend/packing_and_shipping/packing")->with(array());
    }
    // List
    public function list_packing(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('packing');
            $query->select('packing_id', 'sale_order_id');
            $query->where([['status', '!=', '2']]);
            //$query->groupBy('order_id');
            if($keyword) {
                $sql = "sale_order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('packing_id', 'asc');
                else
                    $query->orderBy('packing_id', 'desc');
            }
            else
            {
                $query->orderBy('packing_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('items', function ($query) {
                $selectQty = PackingDetails::where('sale_order_id', $query->sale_order_id)->sum('quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-packing" data-id="'.$query->sale_order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('action', function ($query) {
                //$PurchaseOrderReturn = PurchaseOrderReturn::where([['order_id', '=', $query->order_id]])->get()->toArray();
                //if(sizeof($PurchaseOrderReturn) > 0) {
                    //$action = "";
                //}else {
                    $action = '<a href="javascript:void(0)" class="delete-packing" data-id="'.$query->sale_order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                //}
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
    public function add_packing(Request $request){
        
        $PackingIds = Packing::pluck('sale_order_id')->all();
        
        return \View::make("backend/packing_and_shipping/packing_form")->with([
            'SaleOrderData' => SaleOrder::select('sale_order_id')->where([['is_approved', '=', 1]])->whereNotIn('sale_order_id', $PackingIds)->get()->toArray()
            ])->render();
    }

    public function packing_Export(){
        $query = DB::table('packing')
        ->select('packing_id', 'sale_order_id')
        ->where([['status', '!=', '2']])
        ->orderBy('packing_id', 'DESC');
        $data = $query->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'packing_id');
        $sheet->setCellValue('B1', 'sale_order_id');
        $sheet->setCellValue('C1', 'Items');
        
        $rows = 2;
        foreach($data as $empDetails){
            $items = '';
            if (!empty($empDetails->sale_order_id)) {
                $items = PackingDetails::where('sale_order_id', $empDetails->sale_order_id)->sum('quantity');
            }
            $sheet->setCellValue('A' . $rows, $empDetails->packing_id);
            $sheet->setCellValue('B' . $rows, $empDetails->sale_order_id);
            $sheet->setCellValue('C' . $rows, $items);
            $rows++;
        }
        $fileName = "Packing.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    public function packing_Export_details(Request $request)
    {
        $id = $request->id;
        $query = PackingDetails::select('sale_order_id', 'product_id', 'price','quantity','status')->where([['sale_order_id', '=', $id]])->get()->toArray();
        // print_r($query); exit();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Product_Id');
        $sheet->setCellValue('B1', 'Sale_Order_Id');
        $sheet->setCellValue('C1', 'Status');
        
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['product_id']);
            $sheet->setCellValue('B' . $rows, $empDetails['sale_order_id']);
            $sheet->setCellValue('C' . $rows, $empDetails['status']);
            $rows++;
        }
        $fileName = "Packing.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    // Get
    public function get_order_details(Request $request) {
        if ($request->ajax()) {
            $orderDetailsData = [];
            $Packing = Packing::where([['sale_order_id', '=', $request->order_id]])->get()->toArray();
            if(sizeof($Packing) > 0) {
            	return response()->json(["status" => 0, "msg" => "This order ID already packed."]);
            }else {
	            $SaleOrder = SaleOrder::select('sale_order_id', 'is_rejected', 'is_approved', 'slip_approved')->where([['sale_order_id', '=', $request->order_id], ['delete_status', '=', '0']])->get()->toArray();
	            if(sizeof($SaleOrder) > 0) {
	            	if($SaleOrder[0]['is_rejected'] == "1") {
	            		return response()->json(["status" => 0, "msg" => "Enter order ID is rejected. Please try with another order ID."]);
	            	}else if($SaleOrder[0]['is_approved'] == "0") {
	            		return response()->json(["status" => 0, "msg" => "Order is not approved yet. Please try with another order ID."]);
	            	}else if($SaleOrder[0]['slip_approved'] > 0) {
	            		$SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->order_id], ['is_deleted', '=', '0']])->get()->toArray();
	            		if(sizeof($SaleOrderDetails) > 0) {
	            			foreach($SaleOrderDetails as $data) {
	            			    if($data['qty_appr'] > 0) {
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
    			                        //if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
    			                    }
    			                    array_push($orderDetailsData, array('quantity' => $data['qty_appr'], 'product_id' => $data['product_id'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $data['product_price']));
	            			    }
			                }
			                //return response()->json(["status" => 1, "data" => $returnData]);
                            $pickingData = [];
                            $query = DB::table('sale_order_details as so');
                            $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
                            $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                            $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.unit', 'p.pmrprc', 'wu.unit_name');
                            $query->where([['so.sale_order_id', '=', $request->order_id], ['so.is_deleted', '=', '0']]);
                            $SaleOrderDetails = $query->get()->toArray();
                            if(sizeof($SaleOrderDetails) > 0) {
                                foreach($SaleOrderDetails as $data) {
                                    
                                    if($data->qty_appr > 0) {
                                        
                                        $part_name = "";
                                        $pmpno = "";
                                        $location_name = "";
                                        $zone_name = "";
                                        $row_name = "";
                                        $rack_name = "";
                                        $plate_name = "";
                                        $place_name = "";
                                        $Products=DB::table('binning_location_details as b')
                                        ->join('products','b.product_id','=','products.product_id')
                                        ->join('location','b.location_id','=','location.location_id')
                                        ->join('zone_master','b.zone_id','=','zone_master.zone_id')
                                        ->join('row','b.row_id','=','row.row_id')
                                        ->join('rack','b.rack_id','=','rack.rack_id')
                                        ->join('plate','b.plate_id','=','plate.plate_id')
                                        ->join('place','b.place_id','=','place.place_id')
                                        ->select( 'products.product_id', 'products.part_name_id', 'products.pmpno', 'zone_master.zone_name','location.location_name', 'row.row_name', 'rack.rack_name', 'plate.plate_name', 'place.place_name')
                                        ->where([['b.product_id', '=', $data->product_id], ['products.is_deleted', '=', '0']])->get()->toArray();
                                        
                                        if(sizeof($Products)>0)
                                        {
                                            $location_name = $Products[0]->location_name;
                                            $zone_name = $Products[0]->zone_name;
                                            $row_name = $Products[0]->row_name;
                                            $rack_name = $Products[0]->rack_name;
                                            $plate_name = $Products[0]->plate_name;
                                            $place_name = $Products[0]->place_name;
                                        }
                                        array_push($pickingData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                                    }
                                }
                            }
                            $ClientsData = [];
                            $SaleOrder = SaleOrder::where([['sale_order_id', '=', $request->order_id]])->get()->toArray();
                            if(sizeof($SaleOrder) > 0) {
                                if(!empty($SaleOrder[0]['client_id'])) {
                                    $Clients = Clients::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                                    if(sizeof($Clients) > 0) {
                                        $ClientsData = $Clients;
                                    }
                                }
                            }
                            $html = view('backend.packing_and_shipping.get_order_details')->with([
                                'order_details' => $orderDetailsData,
                                'SaleOrderDetails' => $pickingData,
                                'clients_data' => $ClientsData,
                            ])->render();
                            return response()->json(["status" => 1, "message" => $html]);

	            		}else {
	            			return response()->json(["status" => 0, "msg" => "No record found."]);
	            		}
	            	}else {
                        return response()->json(["status" => 0, "msg" => "Picking slip is not approved yet. Please try with another order ID."]);
                    }
	            }else {
	                return response()->json(["status" => 0, "msg" => "No record found. Enter wrong order id."]);
	            }
	        }
        }
    }
    // Save
    public function save_packing(Request $request) {
    	if ($request->ajax()) {
    		$data = new Packing;
    		$data->sale_order_id = $request->order_id;
    		$data->status = "1";
    		$saveData = $data->save();
    		if($saveData) {
    			$flag = 0;
    			for($i = 0; $i < sizeof($request->product_id); $i++) {
	    			$data2 = new PackingDetails;
	    			$data2->sale_order_id = $request->order_id;
	    			$data2->product_id = $request->product_id[$i];
	    			$data2->price = $request->price[$i];
	    			$data2->quantity = $request->quantity[$i];
	    			$data2->status = "1";
	    			$data2->save();
	    			$flag++;
	    		}
	    		if($flag == sizeof($request->product_id)) {
	    			return response()->json(["status" => 1, "msg" => "Save Successful."]);
	    		}else {
	    			return response()->json(["status" => 0, "msg" => "Save Faild."]);
	    		}
    		}else {
    			return response()->json(["status" => 0, "msg" => "Save Faild."]);
    		}
    	}
    }
    // View
    public function view_packing(Request $request){
        $returnData = [];
        $PackingDetails = PackingDetails::select('product_id', 'quantity', 'price')->where([['sale_order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($PackingDetails) > 0) {
            foreach($PackingDetails as $data) {
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
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $data['price']));
            }
        }
        $html = \View::make("backend/packing_and_shipping/view_packing")->with([
            'PackingDetails' => $returnData
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    // Delete
    public function delete_packing(Request $request) {
        if ($request->ajax()) {
            $CheckIn = Packing::where([['sale_order_id', '=', $request->id]])->delete();
            if($CheckIn) {
                $PackingDetails = PackingDetails::where([['sale_order_id', '=', $request->id]])->delete();
                if($PackingDetails) {
                    return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Delete Faild! Something is wrong."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild!"]);
            }
        }
    }
    // Print
    public function print_packing(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_request_order_to_html($id));
        return $pdf->stream();
    }
    function convert_request_order_to_html($id) {
        $returnData = [];
        $query = DB::table('sale_order_details as so');
        $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
        $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.unit', 'p.pmrprc', 'wu.unit_name');
        $query->where([['so.sale_order_id', '=', $id], ['so.is_deleted', '=', '0']]);
        $SaleOrderDetails = $query->get()->toArray();
        
        if(sizeof($SaleOrderDetails) > 0) {
            
            foreach($SaleOrderDetails as $data) {
                
                if($data->qty_appr > 0) {
                    $part_name = "";
                    $pmpno = "";
                    $location_name = "";
                    $zone_name = "";
                    $row_name = "";
                    $rack_name = "";
                    $plate_name = "";
                    $place_name = "";
                    $Products=DB::table('binning_location_details as b')
                    ->join('products','b.product_id','=','products.product_id')
                    ->join('location','b.location_id','=','location.location_id')
                    ->join('zone_master','b.zone_id','=','zone_master.zone_id')
                    ->join('row','b.row_id','=','row.row_id')
                    ->join('rack','b.rack_id','=','rack.rack_id')
                    ->join('plate','b.plate_id','=','plate.plate_id')
                    ->join('place','b.place_id','=','place.place_id')
                    ->select( 'products.product_id', 'products.part_name_id', 'products.pmpno', 'zone_master.zone_name','location.location_name', 'row.row_name', 'rack.rack_name', 'plate.plate_name', 'place.place_name')
                    ->where([['b.product_id', '=', $data->product_id], ['products.is_deleted', '=', '0']])->get()->toArray();
                    
                    if(sizeof($Products)>0)
                    {
                        $location_name = $Products[0]->location_name;
                        $zone_name = $Products[0]->zone_name;
                        $row_name = $Products[0]->row_name;
                        $rack_name = $Products[0]->rack_name;
                        $plate_name = $Products[0]->plate_name;
                        $place_name = $Products[0]->place_name;
                    }
                    array_push($returnData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                }
            }
        }
        $ClientsData = [];
        $SaleOrder = SaleOrder::where([['sale_order_id', '=', $id]])->get()->toArray();
        if(sizeof($SaleOrder) > 0) {
            if(!empty($SaleOrder[0]['client_id'])) {
                $Clients = Clients::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                if(sizeof($Clients) > 0) {
                    $ClientsData = $Clients;
                }
            }
        }
        return view('backend.packing_and_shipping.packing_invoice')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $id
        ]);
    }
}