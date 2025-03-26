<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Warehouses;
use DB;
use DataTables;
use App\Deliveries;
use App\DeliveryDetail;
use App\Products;
use App\Returns;
use App\ReturnDetail;
use App\Orders;
use App\OrderDetail;
use App\PartName;
use App\DeliveryManagement;
use App\ShippingDetails;
use App\DefectiveBinDetails;
use App\SaleOrder;
use App\WmsStock;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReturnsManagementController extends Controller {
    public function returns() {
        return \View::make("backend/returns/returns_management")->with(array());
    }
    // Returns Modal
    public function returns_form(){
        
        $query = DB::table('delivery_management');
        $query->select('delivery_management_id');
        //$query->join('purchase_order_return as p', 'p.order_id', '=', 'c.order_id', 'left');
        //$query->where([['c.status', '=', '1']]);
        //$query->whereNull('p.order_id');
        $listDeliveryIds = $query->get()->toArray();
        
        return \View::make("backend/returns/returns_form")->with([
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            'listDeliveryIds' => $listDeliveryIds
        ])->render();
    }
    // Returns dataTable
    public function list_of_returns(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('returns');
            $query->select('*');
            if($keyword)
            {
                $sql = "return_date like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('return_date', 'asc');
                else
                    $query->orderBy('return_id', 'desc');
            }
            else
            {
                $query->orderBy('return_id', 'DESC');
            }
            $query->where([['status', '=', '1']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('delivery_id', function ($query) {
                $delivery_id = '';
                if(!empty($query->sale_order_id)) {
                    $delivery_id = "#".$query->sale_order_id;
                }else {
                    $delivery_id = "#".$query->purchase_order_id;
                }
                return $delivery_id;
            })
            ->addColumn('warehouse', function ($query) {
                $warehouse = '';
                if(!empty($query->warehouse_id)) {
                    $Warehouses = Warehouses::select('name')->where([['warehouse_id', '=', $query->warehouse_id]])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        if(!empty($Warehouses[0]['name'])) $warehouse = $Warehouses[0]['name'];
                    }
                }
                return $warehouse;
            })
            ->addColumn('user', function ($query) {
                $user = '';
                if(!empty($query->user_id)) {
                    $Users = Users::select('first_name', 'last_name')->where([['user_id', '=', $query->user_id]])->get()->toArray();
                    if(count($Users) > 0) {
                        if(!empty($Users[0]['first_name'])) $user .= $Users[0]['first_name'];
                        if(!empty($Users[0]['last_name'])) $user .= " ".$Users[0]['last_name'];
                    }
                }
                return $user;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="view-returns" data-id="'.$query->return_id.'"><button type="button" class="btn btn-primary btn-sm" title="View Returns"><i class="fa fa-eye"></i></button></a>';
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
    // Export
    public function returns_export_table()
    {
        
        $query = returns::OrderBy('return_id', 'ASC')->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Return_data');
        $sheet->setCellValue('B1', 'Return_type');
        $sheet->setCellValue('C1', 'Delivery_id');
        $sheet->setCellValue('D1', 'Warehouse');
        $sheet->setCellValue('E1', 'Form');

        $rows = 2;
        foreach($query as $empDetails){
            $return_type = '';
                if(!empty($empDetails->sale_order_id)) {
                    $return_type = "Customer Return";
                }else {
                    $return_type = "Supplier Return";
                }
            $delivery_id = '';
                if(!empty($empDetails['sale_order_id'])) {
                    $delivery_id = "#".$empDetails['sale_order_id'];
                }else {
                    $delivery_id = "#".$empDetails['purchase_order_id'];
                }    
            $warehouse = '';
                if(!empty($empDetails['warehouse_id'])) {
                    $Warehouses = Warehouses::select('name')->where([['warehouse_id', '=', $empDetails['warehouse_id']]])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        if(!empty($Warehouses[0]['name'])) $warehouse = $Warehouses[0]['name'];
                    }
                }
            $user = '';
                if(!empty($empDetails['user_id'])) {
                    $Users = Users::select('first_name', 'last_name')->where([['user_id', '=', $empDetails['user_id']]])->get()->toArray();
                    if(count($Users) > 0) {
                        if(!empty($Users[0]['first_name'])) $user .= $Users[0]['first_name'];
                        if(!empty($Users[0]['last_name'])) $user .= " ".$Users[0]['last_name'];
                    }
                }    
            $sheet->setCellValue('A' . $rows, $empDetails['return_date']);
            $sheet->setCellValue('B' . $rows, $return_type);
            $sheet->setCellValue('C' . $rows, $delivery_id);
            $sheet->setCellValue('D' . $rows, $warehouse);
            $sheet->setCellValue('E' . $rows, $user);
            $rows++;
        }
        $fileName = "Returns.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Get Sales Order Ids by Delivery Id
    public function getSalesOrderIdsByDelivery(Request $request) {
        
        $DeliveryManagement = DB::table('delivery_management as dm')->select('dm.sale_order_id')->join('returns as r', 'r.sale_order_id', '=', 'dm.sale_order_id', 'left')->where([['dm.delivery_management_id', '=', $request->delivery_id], ['dm.status', '=', '1']])->whereNull('r.sale_order_id')->get()->toArray();
        
        if(sizeof($DeliveryManagement) > 0) {
            
            $deliveryIds = explode(',', $DeliveryManagement[0]->sale_order_id);
            
            return response()->json(["status" => 1, "data" => $deliveryIds]);
            
        }else {
            
            return response()->json(["status" => 0, "message" => 'No record found']);
        }
    }

    // Get Order Details 
    public function get_order_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnDeliveryDetail = [];
            $ShippingDetails = ShippingDetails::select('product_id', 'quantity')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            if(sizeof($ShippingDetails) > 0) {
                foreach($ShippingDetails as $detail) {
                    if(!empty($detail['product_id'])) {
                        $pmpno = "";
                        $part_name = "";
                        $pmrprc = "";
                        $Products = Products::select('pmpno', 'pmrprc', 'part_name_id')->where([['product_id', '=', $detail['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                        if(sizeof($Products) > 0) {
                            if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                            if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                $part_name = $PartName[0]['part_name'];
                            }
                        }
                    }
                    array_push($returnDeliveryDetail, array('product_id' => $detail['product_id'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'pmrprc' => $pmrprc, 'qty' => $detail['quantity']));
                }
            }
            
            //echo "<pre>";
            //print_r($returnDeliveryDetail); exit();
            if(sizeof($returnDeliveryDetail) > 0) {
                $html = view('backend.returns.returns_details')->with([
                    'deliverie_details' => $returnDeliveryDetail
                ])->render();
                return response()->json(["status" => 1, "message" => $html]);
            }else {
                return response()->json(["status" => 0, "message" => "No record found."]);
            }
        }
    }
    // Save Returns
    public function save_returns(Request $request) {
        if ($request->ajax()) {
            $countOrder = 0;
            $client_id = "";
            $getClientId = SaleOrder::select('client_id')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            
            if(sizeof($getClientId) > 0) {
                $client_id = $getClientId[0]['client_id'];
            }
            $data = new Returns;
            $data->return_type = $request->return_type;
            $data->delivery_id = $request->delivery_id;
            $data->sale_order_id = $request->sale_order_id;
            $data->return_date = date('Y-m-d');
            $data->user_id = Session::get('user_id');
            $data->status = "1";
            $saveData = $data->save();
            if($saveData) {
                $return_id = $data->id;
                if(!empty($request->product_id)) {
                    $flag = 0;
                    for($i=0; $i< sizeof($request->product_id); $i++) {
                        $data2 = new ReturnDetail;
                        $data2->return_id = $return_id;
                        $data2->product_id = $request->product_id[$i];
                        $data2->qty_ret = $request->pmrprc[$i];
                        $data2->qty = $request->qty[$i];
                        $data2->received_quantity = $request->received_quantity[$i];
                        $data2->good_quantity = $request->good_quantity[$i];
                        $data2->bad_quantity = $request->bad_quantity[$i];
                        //$data2->ret_reason = $request->reason[$i];
                        $data2->remarks = $request->remarks[$i];
                        $data2->save();
                        
                        if(!empty($request->bad_quantity[$i])) {
                            
                            $supplier_id = NULL;
                            $selectSupplier = DB::table('check_in_details')->select('supplier_id')->where([['product_id', '=', $request->product_id[$i]]])->get()->toArray();
                            if(sizeof($selectSupplier) > 0) {
                                $supplier_id = $selectSupplier[0]->supplier_id;
                            }
                            
                            $efective_bin = new DefectiveBinDetails;
                            $efective_bin->order_id = $request->sale_order_id;
                            $efective_bin->product_id = $request->product_id[$i];
                            $efective_bin->quantity = $request->qty[$i];
                            $efective_bin->bad_quantity = $request->bad_quantity[$i];
                            $efective_bin->client_id = $client_id;
                            $efective_bin->supplier_id = $supplier_id;
                            $efective_bin->status = '1';
                            $efective_bin->save();
                        }
                        //echo $request->good_quantity[$i];
                        //DB::enableQueryLog();
                        if(!empty($request->good_quantity[$i])) {
                            
                            Products::where('product_id', $request->product_id[$i])->update(array('current_stock' => DB::raw('current_stock + '.$request->good_quantity[$i])));
                        }
                        //$query = DB::getQueryLog();
                        //dd($query);
                        $flag++;
                    }
                    //exit();
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Return successful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Return faild. Something is wrong."]);
                    }
                }else {
                    //exit();
                    return response()->json(["status" => 1, "msg" => "Return successful."]);
                }
            }else {
                exit();
                return response()->json(["status" => 0, "msg" => "Return faild."]);
            }
        }
    }
    public function view_returns(Request $request) {
        $Returns = [];
        $ReturnDetail = [];
        if(!empty($request->return_id)) {
            $Returns = Returns::where([['return_id', '=', $request->return_id]])->get()->toArray();
            $ReturnDetailSelect = ReturnDetail::where([['return_id', '=', $request->return_id]])->get()->toArray();
            if(sizeof($ReturnDetailSelect) > 0) {
                foreach($ReturnDetailSelect as $rdata) {
                    $pmpno = '';
                    $part_name = '';
                    $Products = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $rdata['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    array_push($ReturnDetail, array('pmpno' => $pmpno, 'part_name' => $part_name, 'qty' => $rdata['qty'], 'received_quantity' => $rdata['received_quantity'] , 'good_quantity' => $rdata['good_quantity'] , 'bad_quantity' => $rdata['bad_quantity'], 'ret_reason' => $rdata['ret_reason'], 'remarks' => $rdata['remarks']));
                }
            }
        }
        return \View::make("backend/returns/view_returns")->with([
            'Returns' => $Returns,
            'ReturnDetail' => $ReturnDetail
        ])->render();
    }
}