<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\DeliveryManagement;
use App\Orders;
use App\ConsignmentReceipt;
use App\Shipping;
use App\SaleOrder;
use App\CourierCompany;
use App\ShippingDetails;
use App\Products;
use App\PartName;
use App\Clients;
use App\ShippingAddress;
use App\SaleOrderDetails;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DeliveryManagementController  extends Controller {

    public function index() {
        return \View::make("backend/delivery/delivery_management")->with(array());
    }
    // Add Form
    public function add_delivery_management(Request $request){
        if ($request->ajax()) {
            
            $ShippingIds = DeliveryManagement::pluck('shipping_id')->all();
            $html = view('backend.delivery.delivery_management_form')->with([
                'courier_company' => CourierCompany::get()->toArray(),
                'ShippingData' => Shipping::select('shipping_id')->whereNotIn('shipping_id', $ShippingIds)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Check Order No
    public function get_shipping_details(Request $request) {
        if ($request->ajax()) {
            $sale_order_id = "";
            $order_date = "";
            $Shipping = Shipping::where([['shipping_id', '=', $request->shipping_id]])->get()->toArray();
            if(sizeof($Shipping) > 0) {
                if(!empty($Shipping[0]['sale_order_id'])) {
                    $sale_order_id = $Shipping[0]['sale_order_id'];
                    $SaleOrder = SaleOrder::where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
                    if(sizeof($SaleOrder) > 0) {
                        $order_date = date('d/m/Y', strtotime($SaleOrder[0]['created_at']));
                    }
                } else {
                    $ShippingDetails = ShippingDetails::select('sale_order_id')->where([['shipping_id', '=', $request->shipping_id]])->groupBy('sale_order_id')->get()->toArray();
                    if(sizeof($ShippingDetails) > 0) {
                        $sale_order_ids = [];
                        foreach($ShippingDetails as $sdet) {
                            $sale_order_ids[] = $sdet['sale_order_id'];
                        }
                        $sale_order_id = implode(', ', $sale_order_ids);
                    }
                }
                return response()->json(["status" => 1, 'sale_order_id' => $sale_order_id, 'order_date' => $order_date]);
            }else {
                return response()->json(["status" => 0, "msg" => "You have enter incorrect Shipping ID!"]);
            }
        }
    }
    public function save_delivery_management(Request $request){
        if(!empty($request->hidden_id)) {
            $order_date = str_replace('/', '-', $request->order_date);
            $vehicle_in_out_date = NULL;
            if(!empty($request->vehicle_in_out_date)) {
                $vehicle_in_out_date = str_replace('/', '-', $request->vehicle_in_out_date);
                $vehicle_in_out_date = date('Y-m-d', strtotime($vehicle_in_out_date));
            }
            
            $courier_date = NULL;
            if(!empty($request->courier_date)) {
                $courier_date = str_replace('/', '-', $request->courier_date);
                $courier_date = date('Y-m-d', strtotime($courier_date));
            }
            $saveData=DeliveryManagement::where('delivery_management_id', $request->hidden_id)->update(array('shipping_id' => $request->shipping_id, 'vehicle_no' => $request->vehicle_no, 'driver_name' => $request->driver_name, 'contact_no' => $request->contact_no, 'vehicle_in_out_date' => $vehicle_in_out_date, 'courier_company_id' => $request->courier_company_id, 'courier_date' => $courier_date, 'courier_number' => $request->courier_number, 'no_of_box' => $request->no_of_box));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
            }
        }else {
            $DeliveryManagement = DeliveryManagement::where([['shipping_id', '=', $request->shipping_id]])->get()->toArray();
            if(sizeof($DeliveryManagement) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter shipping id already exist. Please try with another shipping id."];
            }else {
                $order_date = str_replace('/', '-', $request->order_date);
                $vehicle_in_out_date = NULL;
                if(!empty($request->vehicle_in_out_date)) {
                    $vehicle_in_out_date = str_replace('/', '-', $request->vehicle_in_out_date);
                    $vehicle_in_out_date = date('Y-m-d', strtotime($vehicle_in_out_date));
                }
                
                $courier_date = NULL;
                if(!empty($request->courier_date)) {
                    $courier_date = str_replace('/', '-', $request->courier_date);
                    $courier_date = date('Y-m-d', strtotime($courier_date));
                }
                
                $data = new DeliveryManagement;
                $data->transaction_type = 'Outbound';
                $data->shipping_id = $request->shipping_id;
                $data->sale_order_id = $request->sale_order_id;
                $data->order_date = date('Y-m-d', strtotime($order_date));
                $data->vehicle_no = $request->vehicle_no;
                $data->driver_name = $request->driver_name;
                $data->contact_no = $request->contact_no;
                $data->vehicle_in_out_date = $vehicle_in_out_date;
                $data->courier_company_id = $request->courier_company_id;
                $data->courier_date = $courier_date;
                $data->courier_number = $request->courier_number;
                $data->no_of_box = $request->no_of_box;
                $data->status = "1";
                $saveData= $data->save();
                if($saveData) {
                    $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                    if(sizeof($SaleOrderDetails) > 0) {
                        foreach($SaleOrderDetails as $od) {
                            $available_stock = 0;
                            $current_stock = 0;
                            $available_qty_on_order = 0;
                            $Products = Products::select('current_stock', 'qty_on_order')->where([['product_id', '=', $od['product_id']]])->get()->toArray();
                            if(sizeof($Products) > 0) {
                                $current_stock = $Products[0]['current_stock'];
                                if($current_stock > 0) {
                                    $available_stock = $current_stock - $od['qty_appr'];
                                }

                                $qty_on_order = $Products[0]['qty_on_order'];
                                if($current_stock > 0) {
                                    $available_qty_on_order = $qty_on_order - $od['qty_appr'];
                                }
                                Products::where([['product_id', '=', $od['product_id']]])->update(['current_stock' => $available_stock, 'qty_on_order' => $available_qty_on_order]);
                            }
                        }
                    }
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // List
    public function list_delivery_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('delivery_management as d');
            $query->join('courier_company as c', 'c.courier_company_id', '=', 'd.courier_company_id', 'left');
            $query->select('d.*', 'c.company_name');
            //$query->where([['d.status', '!=', '2']]);
            if($keyword)
            {
                //$query->whereRaw("(d.shipping_id like '%$keyword%' or d.sale_order_id like '%$keyword%' or cc.company_name like '%$keyword%')");
                $sql = "d.transaction_type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('d.transaction_type', 'asc');
                else
                    $query->orderBy('d.transaction_type', 'desc');
            }
            else
            {
                $query->orderBy('d.delivery_management_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('sale_order_date', function ($query) {
                $sale_order_date = '';
                if(!empty($query->order_date)) {
                    $sale_order_date = date('M d Y', strtotime($query->order_date));
                }
                return $sale_order_date;
            })
            ->addColumn('vehicle_in_out_date_show', function ($query) {
                $vehicle_in_out_date_show = '';
                if(!empty($query->vehicle_in_out_date)) {
                    $vehicle_in_out_date_show = date('M d Y', strtotime($query->vehicle_in_out_date));
                }
                return $vehicle_in_out_date_show;
            })
            // ->addColumn('courier_company', function ($query) {
            //     $courier_company = '';
            //     $CourierCompany = CourierCompany::where([['courier_company_id', '=', $query->courier_company_id]])->get()->toArray();
            //     if(sizeof($CourierCompany) > 0) {
            //         $courier_company = $CourierCompany[0]['company_name'];
            //     }
            //     return $courier_company;
            // })
            ->addColumn('courier_date_show', function ($query) {
                $courier_date_show = '';
                if(!empty($query->courier_date)) {
                    $courier_date_show = date('M d Y', strtotime($query->courier_date));
                }
                return $courier_date_show;
            })
            ->addColumn('action', function ($query) {
                //$ConsignmentReceipt = ConsignmentReceipt::where([['order_id', '=', $query->sale_order_id]])->get()->toArray();
                //if(sizeof($ConsignmentReceipt) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-delivery-management" data-id="'.$query->delivery_management_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="print-delivery-management" data-shipping_id="'.$query->shipping_id.'"><button type="button" class="btn btn-success btn-sm" title="Print Delivery"><i class="fa fa-print"></i></button></a>';
                //}else {
                    //$action = '<a href="javascript:void(0)" class="edit-delivery-management" data-id="'.$query->delivery_management_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-delivery-management" data-id="'.$query->delivery_management_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button></a>';
                //}
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
    // Edit
    public function edit_delivery_management(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/delivery/delivery_management_form')->with([
                'delivery_management_data' => DeliveryManagement::where([['delivery_management_id', '=', $request->id]])->get()->toArray(),
                'courier_company' => CourierCompany::get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_delivery_management(Request $request) {
        if ($request->ajax()) {
            $DeliveryManagement = DeliveryManagement::where([['delivery_management_id', '=', $request->id]])->update(['status' => '2']);
            if($DeliveryManagement) {
                return response()->json(["status" => 1, "msg" => "Delete Successful."]);
            }else {
                return response()->json(["status" => 1, "msg" => "Delete Faild."]);
            }
        }
    }
    public function print_delivery_management(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_request_order_to_html($id));
        return $pdf->stream();
    }
    function convert_request_order_to_html($shipping_id) {
        $ProductData = [];
        $ClientsData = [];
        $ShippingAddressData = [];
        $SaleOrderData = [];
        $ShippingData = [];
        $ShippingDetails = ShippingDetails::select('product_id', 'quantity', 'price')->where([['shipping_id', '=', $shipping_id]])->get()->toArray();
        if(sizeof($ShippingDetails) > 0) {
            foreach($ShippingDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $pmrprc = "";
                $unit_name = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'unit')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($WmsUnit[0]['unit'])) {
                        $WmsUnit = WmsUnit::select('unit_name ')->where([['unit_id', '=', $Products[0]['unit']]])->get()->toArray();
                        if(sizeof($WmsUnit) > 0) {
                            if(!empty($WmsUnit[0]['unit_name'])) $unit_name = $WmsUnit[0]['unit_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                    if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                }
                array_push($ProductData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'pmrprc' => $pmrprc, 'price' => $data['price'], 'unit_name' => $unit_name));
            }
            
            $Shipping = Shipping::where([['shipping_id', '=', $shipping_id]])->get()->toArray();
            if(sizeof($Shipping) > 0) {
                $ShippingData = $Shipping;
                if(!empty($Shipping[0]['shipping_address_id'])) {
                    $ShippingAddress = ShippingAddress::where([['shipping_address_id', '=', $Shipping[0]['shipping_address_id']]])->get()->toArray();
                    if(sizeof($ShippingAddress) > 0) {
                        $ShippingAddressData = $ShippingAddress;
                    }
                }
                
                if(!empty($Shipping[0]['client_id'])) {
                    $Clients = Clients::where([['client_id', '=', $Shipping[0]['client_id']]])->get()->toArray();
                    if(sizeof($Clients) > 0) {
                        $ClientsData = $Clients;
                    }
                }
                
            }
            $DeliveryData = DeliveryManagement::where([['shipping_id', '=', $shipping_id]])->get()->toArray();
        } else {
            $Shipping = Shipping::select('sale_order_id')->where([['shipping_id', '=', $shipping_id]])->get()->toArray();
            $ShippingDetails = ShippingDetails::select('product_id', 'quantity', 'price')->where([['sale_order_id', '=', $Shipping[0]['sale_order_id']]])->get()->toArray();
            if(sizeof($ShippingDetails) > 0) {
                foreach($ShippingDetails as $data) {
                    $part_name = "";
                    $pmpno = "";
                    $price = "";
                    $pmrprc = "";
                    $unit_name = "";
                    $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'unit')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                        if(!empty($WmsUnit[0]['unit'])) {
                            $WmsUnit = WmsUnit::select('unit_name ')->where([['unit_id', '=', $Products[0]['unit']]])->get()->toArray();
                            if(sizeof($WmsUnit) > 0) {
                                if(!empty($WmsUnit[0]['unit_name'])) $unit_name = $WmsUnit[0]['unit_name'];
                            }
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                        if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                    }
                    array_push($ProductData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'pmrprc' => $pmrprc, 'price' => $data['price'], 'unit_name' => $unit_name));
                }
                
                $Shipping = Shipping::where([['sale_order_id', '=', $Shipping[0]['sale_order_id']]])->get()->toArray();
                if(sizeof($Shipping) > 0) {
                    $ShippingData = $Shipping;
                    if(!empty($Shipping[0]['shipping_address_id'])) {
                        $ShippingAddress = ShippingAddress::where([['shipping_address_id', '=', $Shipping[0]['shipping_address_id']]])->get()->toArray();
                        if(sizeof($ShippingAddress) > 0) {
                            $ShippingAddressData = $ShippingAddress;
                        }
                    }
                    if(!empty($Shipping[0]['client_id'])) {
                        $Clients = Clients::where([['client_id', '=', $Shipping[0]['client_id']]])->get()->toArray();
                        if(sizeof($Clients) > 0) {
                            $ClientsData = $Clients;
                        }
                    }
                }
            }
            
            $DeliveryData = DeliveryManagement::where([['sale_order_id', '=', $Shipping[0]['sale_order_id']]])->get()->toArray();
        }
        return view('backend.delivery.delivery_print')->with([
            'ProductData' => $ProductData,
            'clients_data' => $ClientsData,
            'shipping_address' => $ShippingAddressData,
            'ShippingData' => $ShippingData,
            'SaleOrderData' => $SaleOrderData,
            'DeliveryData' => $DeliveryData
        ]);
    }
    // Export
    public function delivery_management_export(){
        $query = DB::table('delivery_management as d')
        ->join('courier_company as c', 'c.courier_company_id', '=', 'd.courier_company_id', 'left')
        ->select('d.*', 'c.company_name')
        ->orderBy('d.delivery_management_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Delivery_id');
        $sheet->setCellValue('B1', 'Shipping_id');
        $sheet->setCellValue('C1', 'Order_id');
        $sheet->setCellValue('D1', 'Order_date');
        $sheet->setCellValue('E1', 'Vehicle_no');
        $sheet->setCellValue('F1', 'Driver_name');
        $sheet->setCellValue('G1', 'Contact_no');
        $sheet->setCellValue('H1', 'Vechile IN/OUT date');
        $sheet->setCellValue('I1', 'Courier_company');
        $sheet->setCellValue('J1', 'Courier_date');
        $sheet->setCellValue('K1', 'Courier_number');
        $sheet->setCellValue('L1', 'No of Box');
        $rows = 2;
        foreach($data as $empDetails){
            $sale_order_date = '';
            if(!empty($empDetails->order_date)) {
                $sale_order_date = date('M d Y', strtotime($empDetails->order_date));
            }
            $vehicle_in_out_date_show = '';
            if(!empty($empDetails->vehicle_in_out_date)) {
                $vehicle_in_out_date_show = date('M d Y', strtotime($empDetails->vehicle_in_out_date));
            }
            $courier_date_show = '';
            if(!empty($empDetails->courier_date)) {
                $courier_date_show = date('M d Y', strtotime($empDetails->courier_date));
            }
            $sheet->setCellValue('A' . $rows, $empDetails->delivery_management_id);
            $sheet->setCellValue('B' . $rows, $empDetails->shipping_id);
            $sheet->setCellValue('C' . $rows, $empDetails->sale_order_id);
            $sheet->setCellValue('D' . $rows, $sale_order_date);
            $sheet->setCellValue('E' . $rows, $empDetails->vehicle_no);
            $sheet->setCellValue('F' . $rows, $empDetails->driver_name);
            $sheet->setCellValue('G' . $rows, $empDetails->contact_no);
            $sheet->setCellValue('H' . $rows, $vehicle_in_out_date_show);
            $sheet->setCellValue('I' . $rows, $empDetails->company_name);
            $sheet->setCellValue('J' . $rows, $courier_date_show);
            $sheet->setCellValue('K' . $rows, $empDetails->courier_number);
            $sheet->setCellValue('L' . $rows, $empDetails->no_of_box);
            $rows++;
        }
        $fileName = "delivery_management.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}