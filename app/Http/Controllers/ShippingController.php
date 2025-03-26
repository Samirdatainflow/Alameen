<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Packing;
use App\Shipping;
use App\SaleOrder;
use App\SaleOrderDetails;
use App\Products;
use App\PartName;
use App\PackingDetails;
use App\ShippingDetails;
use App\ShippingAddress;
use App\Clients;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShippingController extends Controller {

    public function index() {
        return \View::make("backend/packing_and_shipping/shipping")->with(array());
    }
    // List
    public function list_shipping(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('shipping');
            $query->select('shipping_id', 'sale_order_id');
            $query->where([['status', '!=', '2']]);
            //$query->groupBy('order_id');
            if($keyword) {
                $sql = "sale_order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('shipping_id', 'asc');
                else
                    $query->orderBy('shipping_id', 'desc');
            }
            else
            {
                $query->orderBy('shipping_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('items', function ($query) {
                $qty = 0;
                $selectQty = ShippingDetails::where('shipping_id', $query->shipping_id)->sum('quantity');
                if($selectQty > 0) {
                    $qty = $selectQty;
                }else {
                    $ShippingData = Shipping::select('sale_order_id')->where([['shipping_id', '=', $query->shipping_id]])->get()->toArray();
                    if(sizeof($ShippingData) > 0) {
                        $qty = ShippingDetails::where([['sale_order_id', '=', $ShippingData[0]['sale_order_id']]])->sum('quantity');
                    }
                    //$selectQty = PackingDetails::where('sale_order_id', $query->sale_order_id)->sum('quantity');
                }
                //$selectQty = PackingDetails::where('sale_order_id', $query->sale_order_id)->sum('quantity');
                return $qty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-shipping" data-id="'.$query->shipping_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('action', function ($query) {
                $action = '';
                //$action = '<a href="javascript:void(0)" class="delete-packing" data-id="'.$query->sale_order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    public function add_shipping(Request $request){
        return \View::make("backend/packing_and_shipping/shipping_form")->with([
            'customerData' => Clients::where('delete_status',0)->get()->toArray(),
            ])->render();
    }
    // Get
    public function get_order_details(Request $request) {
        $returnData = [];
        $orderDetailsData = [];
        $orderIds = $request->order_id;
        foreach($orderIds as $k=>$v) {
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $v], ['is_deleted', '=', '0']])->get()->toArray();
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
                        array_push($orderDetailsData, array('sale_order_id' => $v, 'quantity' => $data['qty_appr'], 'product_id' => $data['product_id'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $data['product_price']));
    			    }
                }
    		}
        }
        $ClientsData = [];
        $Clients = Clients::where([['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($Clients) > 0) {
            $ClientsData = $Clients;
        }
        $ShippingAddressData = [];
        $ShippingAddress = ShippingAddress::where([['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($ShippingAddress) > 0) {
            $ShippingAddressData = $ShippingAddress;
        }
        $html = view('backend.packing_and_shipping.shipping_order_details')->with([
            'order_details' => $orderDetailsData,
            'clients_data' => $ClientsData,
            'shipping_address' => $ShippingAddressData,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
        // echo "<pre>";
        // print_r($orderDetailsData);
    }
    public function get_order_details1(Request $request) {
        if ($request->ajax()) {
            $orderDetailsData = [];
            $Shipping = Shipping::where([['sale_order_id', '=', $request->order_id]])->get()->toArray();
            if(sizeof($Shipping) > 0) {
            	return response()->json(["status" => 0, "msg" => "This order ID already shipped."]);
            }else {
	            $SaleOrder = SaleOrder::select('sale_order_id', 'is_rejected', 'is_approved')->where([['sale_order_id', '=', $request->order_id], ['delete_status', '=', '0']])->get()->toArray();
	            if(sizeof($SaleOrder) > 0) {
                    $Packing = Packing::where([['sale_order_id', '=', $request->order_id]])->get()->toArray();
	            	if(sizeof($Packing) > 0) {
	            		$SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->order_id], ['is_deleted', '=', '0']])->get()->toArray();
	            		if(sizeof($SaleOrderDetails) > 0) {
	            			foreach($SaleOrderDetails as $data) {
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
                            $ShippingAddressData = [];
                            $ShippingAddress = ShippingAddress::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                            if(sizeof($ShippingAddress) > 0) {
                                $ShippingAddressData = $ShippingAddress;
                            }
                            $html = view('backend.packing_and_shipping.shipping_order_details')->with([
                                'order_details' => $orderDetailsData,
                                'clients_data' => $ClientsData,
                                'shipping_address' => $ShippingAddressData,
                            ])->render();
                            return response()->json(["status" => 1, "message" => $html]);

	            		}else {
	            			return response()->json(["status" => 0, "msg" => "No record found."]);
	            		}
	            	}else {
                        return response()->json(["status" => 0, "msg" => "This order id has not been packaged yet."]);
                    }
	            }else {
	                return response()->json(["status" => 0, "msg" => "No record found. Enter wrong order id."]);
	            }
	        }
        }
    }
    // Save
    public function save_shipping(Request $request) {
        //echo $request->hidden_client_id; exit();
    	if ($request->ajax()) {
            //print_r($request->address_status); exit();
            if(!empty($request->shipping_address)) {
                if($this->addressStatusCheck($request->address_status)) {
            		$data = new Shipping;
            		//$data->sale_order_id = $request->order_id;
                    $data->client_id = $request->hidden_client_id;
            		$data->status = "1";
            		$saveData = $data->save();
            		if($saveData) {
                        $shipping_id = $data->id;
            			$flag = 0;
            			for($i = 0; $i < sizeof($request->product_id); $i++) {
        	    			$data2 = new ShippingDetails;
        	    			$data2->sale_order_id = $request->sale_order_id[$i];
        	    			$data2->product_id = $request->product_id[$i];
        	    			$data2->price = $request->price[$i];
        	    			$data2->quantity = $request->quantity[$i];
        	    			$data2->shipping_id = $shipping_id;
        	    			$data2->save();
        	    			$flag++;
        	    		}
        	    		if($flag == sizeof($request->product_id)) {
                            $flag2 = 0;
                            //ShippingAddress::where([['client_id', '=', $request->hidden_client_id]])->delete();
                            for($s = 0; $s < sizeof($request->shipping_address); $s++) {
                                if(!empty($request->shipping_address_id[$s])) {
                                    ShippingAddress::where([['shipping_address_id', '=', $request->shipping_address_id[$s]]])->update(['address' => $request->shipping_address[$s]]);
                                    if(!empty($request->address_status[$s])) {
                                        Shipping::where('shipping_id', $shipping_id)->update(['shipping_address_id' => $request->shipping_address_id[$s]]);
                                    }
                                }else {
                                    $data3 = new ShippingAddress;
                                    $data3->client_id = $request->hidden_client_id;
                                    $data3->address = $request->shipping_address[$s];
                                    $data3->status = 0;
                                    $data3->save();
                                    $shipping_address_id = $data3->id;
                                    if(!empty($request->address_status[$s])) {
                                        Shipping::where('shipping_id', $shipping_id)->update(['shipping_address_id' => $shipping_address_id]);
                                    }
                                }
                                $flag2++;
                            }
                            if($flag2 == sizeof($request->shipping_address)) {
            	    			return response()->json(["status" => 1, "msg" => "Save Successful."]);
                            }else {
                                return response()->json(["status" => 0, "msg" => "Something is wrong!"]);
                            }
        	    		}else {
        	    			return response()->json(["status" => 0, "msg" => "Save Faild."]);
        	    		}
            		}else {
            			return response()->json(["status" => 0, "msg" => "Save Faild."]);
            		}
                }else {
                    return response()->json(["status" => 0, "msg" => "Please set a address as Primary Address!"]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Please add a shipping address!"]);
            }
    	}
    }
    public function addressStatusCheck($address_status) {
        $returnVal = 0;
        for($s = 0; $s < sizeof($address_status); $s++) {
            if($address_status[$s] > 0) {
                $returnVal = 1;
                break;
            }
        }
        return $returnVal;
    }
    // View
    public function view_shipping(Request $request){
        $returnData = [];
        $ClientsData = [];
        $ShippingAddressData = [];
        $ShippingDetails = ShippingDetails::select('product_id', 'quantity', 'price','sale_order_id')->where([['shipping_id', '=', $request->id]])->get()->toArray();
        if(sizeof($ShippingDetails) > 0) {
            
            foreach($ShippingDetails as $data) {
                
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
                array_push($returnData, array('sale_order_id' => $data['sale_order_id'], 'product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $data['price']));
            }
            $SaleOrder = SaleOrder::where([['sale_order_id', '=', $request->id]])->get()->toArray();
            if(sizeof($SaleOrder) > 0) {
                if(!empty($SaleOrder[0]['client_id'])) {
                    $Clients = Clients::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                    if(sizeof($Clients) > 0) {
                        $ClientsData = $Clients;
                    }
                }
            }
            $Shipping = Shipping::where([['sale_order_id', '=', $request->id]])->get()->toArray();
            if(sizeof($Shipping) > 0) {
                if(!empty($Shipping[0]['shipping_address_id'])) {
                    $ShippingAddress = ShippingAddress::where([['shipping_address_id', '=', $Shipping[0]['shipping_address_id']]])->get()->toArray();
                    if(sizeof($ShippingAddress) > 0) {
                        $ShippingAddressData = $ShippingAddress;
                    }
                }
            }
        } else {
            
            $ShippingDetails = [];
            $ShippingData = Shipping::select('sale_order_id')->where([['shipping_id', '=', $request->id]])->get()->toArray();
            
            if(sizeof($ShippingData) > 0) {
                $ShippingDetails = ShippingDetails::select('product_id', 'quantity', 'price')->where([['sale_order_id', '=', $ShippingData[0]['sale_order_id']]])->get()->toArray();
            }
            
            if(sizeof($ShippingDetails) > 0) {
                
                foreach($ShippingDetails as $data) {
                    
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
                    array_push($returnData, array('sale_order_id' => $ShippingData[0]['sale_order_id'], 'product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $data['price']));
                }
                $SaleOrder = SaleOrder::where([['sale_order_id', '=', $request->id]])->get()->toArray();
                if(sizeof($SaleOrder) > 0) {
                    if(!empty($SaleOrder[0]['client_id'])) {
                        $Clients = Clients::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                        if(sizeof($Clients) > 0) {
                            $ClientsData = $Clients;
                        }
                    }
                }
                $Shipping = Shipping::where([['sale_order_id', '=', $request->id]])->get()->toArray();
                if(sizeof($Shipping) > 0) {
                    if(!empty($Shipping[0]['shipping_address_id'])) {
                        $ShippingAddress = ShippingAddress::where([['shipping_address_id', '=', $Shipping[0]['shipping_address_id']]])->get()->toArray();
                        if(sizeof($ShippingAddress) > 0) {
                            $ShippingAddressData = $ShippingAddress;
                        }
                    }
                }
            }
        }
        
        $html = \View::make("backend/packing_and_shipping/view_shipping")->with([
            'ShippingData' => $returnData,
            'clients_data' => $ClientsData,
            'shipping_address' => $ShippingAddressData,
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
        $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.unit', 'so.product_tax', 'p.pmrprc', 'wu.unit_name');
        $query->where([['so.sale_order_id', '=', $id], ['so.is_deleted', '=', '0']]);
        $SaleOrderDetails = $query->get()->toArray();
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
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
                array_push($returnData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'unit' => $data->unit, 'product_tax' => $data->product_tax, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
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
    public function shipping_export(){
        $query = DB::table('shipping')->select('*')->orderBy('shipping_id', 'DESC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Shipping ID');
        $sheet->setCellValue('B1', 'Order ID');
        $sheet->setCellValue('C1', 'Item');
        $rows = 2;
        foreach($query as $d2){
            $item = 0;
            if(!empty($d2->sale_order_id)) {
                $item = PackingDetails::where('sale_order_id', $d2->sale_order_id)->sum('quantity');
            }
            $sheet->setCellValue('A' . $rows, $d2->shipping_id);
            $sheet->setCellValue('B' . $rows, $d2->sale_order_id);
            $sheet->setCellValue('C' . $rows, $item);
            $rows++;
        }
        $fileName = "shipping.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    public function get_packing_ids_by_customer(Request $request) {
        $returnData = [];
        $query = DB::table('sale_order as so');
        $query->select('so.sale_order_id');
        $query->join('packing as p', 'p.sale_order_id', '=', 'so.sale_order_id', 'left');
        $query->join('shipping_details as s', 's.sale_order_id', '=', 'p.sale_order_id', 'left');
        $query->where([['so.client_id', '=', $request->id], ['p.status', '=', '1']]);
        $query->whereNull('s.sale_order_id')->get();
        $selectData = $query->get()->toArray();
        if(sizeof($selectData) > 0) {
            return response()->json(["status" => 1, "data" => $selectData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found!"]);
        }
    }
    
}