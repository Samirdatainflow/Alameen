<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use File;
use Session;
use App\Users;
use App\Products;
use App\SaleOrder;
use App\SaleOrderRejectReason;
use App\SaleOrderDetails;
use App\WmsSaleOrderAproved;
use App\PartName;
use DB;
use DataTables;
use App\SmsApiKey;
use App\MailApiKey;
use Cookie;
use App\SaleOrderTemplate;
use App\WmsStock;
use PDF;
use App\Clients;
use App\ProductCategories;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\AlternatePartNo;
use App\BinningLocationDetails;
use App\SaleOrderPickingApproveDetails;
use App\WmsProductTaxes;
use App\OrderDetail;
use App\CheckInDetails;
use App\PartBrand;
use App\Invoice;
use App\InvoiceDetails;
use App\Packing;
use App\ShippingDetails;
use App\DeliveryManagement;
use App\SalesReceipt;
use App\VatType;
use App\Returns;
use App\ReturnDetail;

class SaleOrderManagementController extends Controller {

    public function sale_order_management() {

        return \View::make("backend/sale_order/sale_order_management")->with([]);
    }
    public function get_sale_order(Request $request){
    	if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');
            //$keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id', 'sale_order.invoice_no','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected','sale_order.order_status', 'clients.customer_name', 'clients.sponsor_name');
            
            if(!empty($request->client_id)) {
                $query->where('sale_order.client_id', '=', $request->client_id);
            }
            
            // if($keyword)
            // {
            //     $query->whereRaw("(sale_order.sale_order_id like '%$keyword%' or sale_order.invoice_no like '%$keyword%' or clients.customer_name like '%$keyword%')");
            // }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order.sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order.sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order.sale_order_id', 'DESC');
            }

            $query->where('sale_order.order_status', '=', '1');
            $data_sale=$query->get()->toArray();
            $sale= new Collection;

            foreach($data_sale as $data_array){
                
                $status = "";
                $Packing = Packing::where([['sale_order_id', '=', $data_array->sale_order_id]])->get()->toArray();
                if(sizeof($Packing) > 0) {
                    $status = '<span class="badge badge-success">Packed</span>';
                }else if($data_array->is_approved == 1) {
                    $status = '<span class="badge badge-warning">Approved</span>';
                }else {
                    $status = '<span class="badge badge-danger">Not Approved</span>';
                }
                $product_status = $this->chcekProductStock4Create($data_array->sale_order_id);
                if($product_status == 1) {
                    $actions = '';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" data-ordersatatus="StockOrder" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> <a data-sale_order_id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="btn btn-warning action-btn print-order-details" title="Print order details"><i class="fa fa-print" aria-hidden="true"></i></a> ';
                    if($data_array->is_rejected == "1") {
                        $actions .= '<a href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn" title="Rejected"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                    }else {
                        if($data_array->is_approved == "0") {
                            
                            $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-primary action-btn sales-order-edit" title="Edit Order"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                            $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn sales-order-delete" title="Delete Order"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                            
                        }else {
                            //$actions .= '<a  href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn" title="Approved" ><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> <a href="javascript:void(0);" data-sale-order-id="'.$query->sale_order_id.'" class="download-invoice btn btn-warning action-btn" title="Download invoice" ><i class="fa fa-download" aria-hidden="true"></i> Print Invoice</a> ';
                        }
                        
                    }
                    $SaleOrderTemplate = SaleOrderTemplate::where('sale_order_id', $data_array->sale_order_id)->get()->toArray();
                    if(sizeof($SaleOrderTemplate) > 0) {
                        $actions .= '<a  href="javascript:void(0);" name="button" class="view-subbrand btn btn-warning download-order-template" data-template_name="'.$SaleOrderTemplate[0]['template_name'].'" title="Download Template" ><i class="fa fa-download" aria-hidden="true"></i></a> ';
                    }
                    if($data_array->order_status == 2) {
                        $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-primary action-btn edit-order-details" title="Edit Order"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    }
                    $grand_total = $this->calculateorderGrandTotal($data_array->sale_order_id);
                    $sale->push(['order_id' => $data_array->sale_order_id, 'invoice_no' => $data_array->invoice_no, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions, 'status' => $status]);
                }
                
            }
                $datatable_array=Datatables::of($sale)
                 ->filter(function ($instance) use ($request) {
   
                        if (!empty($request->input('search.value'))) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                //echo Str::lower($row['client_name'])."<br>";
                                if (Str::contains(Str::lower($row['company_name']), Str::lower($request->input('search.value')))){
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['invoice_no']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['order_id']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['client_name']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
   
                                return false;
                            });
                        }
   
                    })
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action', 'status'])
                ->toJson();
                //$data=(array)$datatable_array->getData();
                //print_r($data);
                //$data['data']=$x;
                //$data['recordsFiltered']=count($x);
                //$data['page']=($_POST['start']/$_POST['length'])+1;
                //$data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $datatable_array;
            
        }else{
            //
        }
    }
    function calculateorderGrandTotal($sale_order_id) {
        $grandtotal = 0;
        $selectData = DB::table('sale_order_details')->select('*')->where('sale_order_id', '=', $sale_order_id)->get()->toArray();
        if(sizeof($selectData) > 0) {
            foreach($selectData as $data)
            {
                $sub_total = $data->product_price * $data->qty;
                $grandtotal +=$sub_total;
            }
        }
        $taxTotal = 0;
        $SaleOrderData = SaleOrder::select('vat_type_id')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderData) > 0)
        {
            if(!empty($SaleOrderData[0]['vat_type_id']))
            {
                $VatTypeData = VatType::select('percentage')->where([['vat_type_id', '=', $SaleOrderData[0]['vat_type_id']]])->get()->toArray();
                if(sizeof($VatTypeData) > 0)
                {
                    if($VatTypeData[0]['percentage'] != 0 && $VatTypeData[0]['percentage'] != 'Nil')
                    {
                        $taxTotal = ($grandtotal * $VatTypeData[0]['percentage'])/100;
                    }
                }
            }
        }
        $grandtotal +=$taxTotal;
        $grandtotal = round($grandtotal,3);
        return $grandtotal;
    }
    function calculateGrandTotal($sale_order_id) {
        $grandtotal = 0;
        $selectData = DB::table('sale_order_details')->select('*')->where('sale_order_id', '=', $sale_order_id)->get()->toArray();
        if(sizeof($selectData) > 0) {
            foreach($selectData as $data) {
                $current_stock = 0;
                $Products = Products::select('current_stock', 'qty_on_order')->where([['product_id', '=', $data->product_id], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['current_stock'])) $current_stock = $Products[0]['current_stock'];
                    if(!empty($Products[0]['qty_on_order'])) {
                        $current_stock = $current_stock - $Products[0]['qty_on_order'];
                    }
                }
                //if($current_stock > 0) {
                    $sub_total = $data->product_price * $data->qty;
                    $grandtotal +=$sub_total;
                //}
            }
        }
        $taxTotal = 0;
        $SaleOrderData = SaleOrder::select('vat_type_id')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderData) > 0)
        {
            if(!empty($SaleOrderData[0]['vat_type_id']))
            {
                $VatTypeData = VatType::select('percentage')->where([['vat_type_id', '=', $SaleOrderData[0]['vat_type_id']]])->get()->toArray();
                if(sizeof($VatTypeData) > 0)
                {
                    if($VatTypeData[0]['percentage'] != 0 && $VatTypeData[0]['percentage'] != 'Nil')
                    {
                        $taxTotal = ($grandtotal * $VatTypeData[0]['percentage'])/100;
                    }
                }
            }
        }
        $grandtotal +=$taxTotal;
        $grandtotal = round($grandtotal,3);
        return $grandtotal;
    }
    // export
    public function sale_order_management_export()
    {   
        
        $query = DB::table('sale_order')
        ->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected','sale_order.order_status', 'clients.customer_name', 'clients.sponsor_name')
        ->join('clients', 'sale_order.client_id', '=', 'clients.client_id')
        
        ->where('sale_order.order_status', '!=', '2')
        ->orderBy('sale_order.sale_order_id', 'desc');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order_id');
        $sheet->setCellValue('B1', 'Client_name');
        $sheet->setCellValue('C1', 'Sponsor_name');
        $sheet->setCellValue('D1', 'Grand_total');
        $sheet->setCellValue('E1', 'Created_on');
        
        $rows = 2;
        foreach($data as $td){
            $product_status = $this->chcekProductStock($td->sale_order_id);
            if($product_status == 1) {
                $sheet->setCellValue('A' . $rows, $td->sale_order_id);
                $sheet->setCellValue('B' . $rows, $td->customer_name);
                $sheet->setCellValue('C' . $rows, $td->sponsor_name);
                $sheet->setCellValue('D' . $rows, $td->grand_total);
                $sheet->setCellValue('E' . $rows, date('d M Y',strtotime($td->created_at)));
                $rows++;
            }
        }
        $fileName = "Sale_Order.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    public function get_sale_order_details(Request $request){
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
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

                $current_stock = 0;
                $Products = Products::select('product_id', 'part_name_id', 'pmpno', 'current_stock', 'qty_on_order')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['current_stock'])) $current_stock = $Products[0]['current_stock'];
                    // if(!empty($Products[0]['qty_on_order'])) {
                    //     $current_stock = $current_stock - $Products[0]['qty_on_order'];
                    // }
                    // $transit_quantity = SaleOrderDetails::where([['product_id', '=', $data['product_id']]])->sum('qty');
                    // if($transit_quantity > 0) {
                    //     $current_stock = $current_stock - $transit_quantity;
                    // }
                    // if($current_stock < 0) {
                    //     $current_stock = 0;
                    // }
        
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                }
                
                //if($current_stock > 0) {
                    
                    $binning_location_details = DB::table('binning_location_details')->select('location_id', 'zone_id', 'row_id', 'rack_id', 'plate_id', 'place_id')->where('product_id', $data['product_id'])->get()->toArray();
                    if(sizeof($binning_location_details) > 0) {
                        if(!empty($binning_location_details[0]->location_id)) {
                            $location = DB::table('location')->select('location_name')->where('location_id', $binning_location_details[0]->location_id)->get()->toArray();
                            if(sizeof($location) > 0) {
                                if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->zone_id)) {
                            $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $binning_location_details[0]->zone_id)->get()->toArray();
                            if(sizeof($zone_master) > 0) {
                                if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->row_id)) {
                            $row = DB::table('row')->select('row_name')->where('row_id', $binning_location_details[0]->row_id)->get()->toArray();
                            if(sizeof($row) > 0) {
                                if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->rack_id)) {
                            $rack = DB::table('rack')->select('rack_name')->where('rack_id', $binning_location_details[0]->rack_id)->get()->toArray();
                            if(sizeof($rack) > 0) {
                                if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->plate_id)) {
                            $plate = DB::table('plate')->select('plate_name')->where('plate_id', $binning_location_details[0]->plate_id)->get()->toArray();
                            if(sizeof($plate) > 0) {
                                if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->place_id)) {
                            $place = DB::table('place')->select('place_name')->where('place_id', $binning_location_details[0]->place_id)->get()->toArray();
                            if(sizeof($place) > 0) {
                                if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                            }
                        }
                    }
                    
                    $qty_appr = '';
                    $SaleOrder = SaleOrder::select('is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                    if(sizeof($SaleOrder)>0) {
                        $SaleOrderDetails = SaleOrderDetails::select('qty_appr')->where([['sale_order_details_id', '=', $data['sale_order_details_id']], ['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                        if(sizeof($SaleOrderDetails) > 0) {
                            $qty_appr = $SaleOrderDetails[0]['qty_appr'];
                        }
                    }
                    $max_price = "";
                    $getMaxPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'desc')->limit(1)->get()->toArray();
                    if(sizeof($getMaxPrice) > 0) {
                        $max_price = $getMaxPrice[0]['product_price'];
                    }
                    $min_price = "";
                    $getMinPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'asc')->limit(1)->get()->toArray();
                    if(sizeof($getMinPrice) > 0) {
                        $min_price = $getMinPrice[0]['product_price'];
                    }
    
                    array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'current_stock' => $current_stock, 'qty_appr'=> $qty_appr, 'max_price' => $max_price, 'min_price' => $min_price,'location_name'=>$location_name,'zone_name'=>$zone_name, 'row_name'=>$row_name, 'rack_name'=>$rack_name, 'plate_name'=>$plate_name, 'place_name'=>$place_name));
                //}
            }
        }
        $is_rejected=0;
        $is_approved=0;
        $SaleOrder = SaleOrder::select('is_rejected', 'is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrder)>0) {
            if(!empty($SaleOrder[0]['is_rejected'])) $is_rejected = $SaleOrder[0]['is_rejected'];
            if(!empty($SaleOrder[0]['is_approved'])) $is_approved = $SaleOrder[0]['is_approved'];
        }
        $reject_reason = '';
        if($is_rejected == "1") {
            $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
            if(sizeof($SaleOrderRejectReason) > 0) {
                $reject_reason = $SaleOrderRejectReason[0]['reason'];
            }
        }
        return \View::make("backend/sale_order/sale_order_details")->with([
            'products' => $returnData,
            'is_rejected' => $is_rejected,
            'reject_reason' => $reject_reason,
            'is_approved' => $is_approved,
            'sale_order_id' => $request->sale_order_id,
            'ordersatatus' => $request->ordersatatus
        ]);
    }
    
    public function get_sale_order_details_for_picking_slip(Request $request){
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
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

                $current_stock = 0;
                $Products = Products::select('product_id', 'part_name_id', 'pmpno', 'current_stock', 'qty_on_order')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['current_stock'])) $current_stock = $Products[0]['current_stock'];
                    // if(!empty($Products[0]['qty_on_order'])) {
                    //     $current_stock = $current_stock - $Products[0]['qty_on_order'];
                    // }
                    // $transit_quantity = SaleOrderDetails::where([['product_id', '=', $data['product_id']]])->sum('qty');
                    // if($transit_quantity > 0) {
                    //     $current_stock = $current_stock - $transit_quantity;
                    // }
                    // if($current_stock < 0) {
                    //     $current_stock = 0;
                    // }
        
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                }
                
                //if($current_stock > 0) {
                    
                    $binning_location_details = DB::table('binning_location_details')->select('location_id', 'zone_id', 'row_id', 'rack_id', 'plate_id', 'place_id')->where('product_id', $data['product_id'])->get()->toArray();
                    if(sizeof($binning_location_details) > 0) {
                        if(!empty($binning_location_details[0]->location_id)) {
                            $location = DB::table('location')->select('location_name')->where('location_id', $binning_location_details[0]->location_id)->get()->toArray();
                            if(sizeof($location) > 0) {
                                if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->zone_id)) {
                            $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $binning_location_details[0]->zone_id)->get()->toArray();
                            if(sizeof($zone_master) > 0) {
                                if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->row_id)) {
                            $row = DB::table('row')->select('row_name')->where('row_id', $binning_location_details[0]->row_id)->get()->toArray();
                            if(sizeof($row) > 0) {
                                if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->rack_id)) {
                            $rack = DB::table('rack')->select('rack_name')->where('rack_id', $binning_location_details[0]->rack_id)->get()->toArray();
                            if(sizeof($rack) > 0) {
                                if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->plate_id)) {
                            $plate = DB::table('plate')->select('plate_name')->where('plate_id', $binning_location_details[0]->plate_id)->get()->toArray();
                            if(sizeof($plate) > 0) {
                                if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                            }
                        }
                        if(!empty($binning_location_details[0]->place_id)) {
                            $place = DB::table('place')->select('place_name')->where('place_id', $binning_location_details[0]->place_id)->get()->toArray();
                            if(sizeof($place) > 0) {
                                if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                            }
                        }
                    }
                    
                    $qty_appr = '';
                    $SaleOrder = SaleOrder::select('is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                    if(sizeof($SaleOrder)>0) {
                        $SaleOrderDetails = SaleOrderDetails::select('qty_appr')->where([['sale_order_details_id', '=', $data['sale_order_details_id']], ['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                        if(sizeof($SaleOrderDetails) > 0) {
                            $qty_appr = $SaleOrderDetails[0]['qty_appr'];
                        }
                    }
                    $max_price = "";
                    $getMaxPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'desc')->limit(1)->get()->toArray();
                    if(sizeof($getMaxPrice) > 0) {
                        $max_price = $getMaxPrice[0]['product_price'];
                    }
                    $min_price = "";
                    $getMinPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'asc')->limit(1)->get()->toArray();
                    if(sizeof($getMinPrice) > 0) {
                        $min_price = $getMinPrice[0]['product_price'];
                    }
                    if($qty_appr > 0) {
                        array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'current_stock' => $current_stock, 'qty_appr'=> $qty_appr, 'max_price' => $max_price, 'min_price' => $min_price,'location_name'=>$location_name,'zone_name'=>$zone_name, 'row_name'=>$row_name, 'rack_name'=>$rack_name, 'plate_name'=>$plate_name, 'place_name'=>$place_name));
                    }
                //}
            }
        }
        $is_rejected=0;
        $is_approved=0;
        $SaleOrder = SaleOrder::select('is_rejected', 'is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrder)>0) {
            if(!empty($SaleOrder[0]['is_rejected'])) $is_rejected = $SaleOrder[0]['is_rejected'];
            if(!empty($SaleOrder[0]['is_approved'])) $is_approved = $SaleOrder[0]['is_approved'];
        }
        $reject_reason = '';
        if($is_rejected == "1") {
            $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
            if(sizeof($SaleOrderRejectReason) > 0) {
                $reject_reason = $SaleOrderRejectReason[0]['reason'];
            }
        }
        return \View::make("backend/sale_order/picking_slip_sale_order_details")->with([
            'products' => $returnData,
            'is_rejected' => $is_rejected,
            'reject_reason' => $reject_reason,
            'is_approved' => $is_approved,
            'sale_order_id' => $request->sale_order_id,
            'ordersatatus' => $request->ordersatatus
        ]);
    }
    
    public function get_approve_sale_order_details(Request $request){
        
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
        
        if(sizeof($SaleOrderDetails) > 0) {
            
            foreach($SaleOrderDetails as $data) {

                $current_stock = 0;
                $available_stock = 0;
                $pmpno = "";
                $part_name = "";
                $Products = Products::select('product_id', 'part_name_id', 'pmpno', 'current_stock', 'qty_on_order')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                
                if(sizeof($Products) > 0) {
                    
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    
                     if(!empty($Products[0]['pmpno'])) $available_stock = $Products[0]['current_stock'];
                     
                    $approve_quantity = SaleOrderDetails::where([['product_id', '=', $data['product_id']], ['sale_order_id', '!=', $request->sale_order_id]])->sum('qty_appr');
                    if($approve_quantity > 0) {
                        $available_stock = $available_stock - $approve_quantity;
                    }
                    
                    if($available_stock < 0) {
                        $available_stock = 0;
                    }
                        
                    if(!empty($Products[0]['part_name_id'])) {
                        
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                }
                    
                $max_price = "";
                $getMaxPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'desc')->limit(1)->get()->toArray();
                if(sizeof($getMaxPrice) > 0) {
                    $max_price = $getMaxPrice[0]['product_price'];
                }
                $min_price = "";
                $getMinPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'asc')->limit(1)->get()->toArray();
                if(sizeof($getMinPrice) > 0) {
                    $min_price = $getMinPrice[0]['product_price'];
                }
                
                $SaleOrder = SaleOrder::select('is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                if(sizeof($SaleOrder)>0) {
                    
                    if($SaleOrder[0]['is_approved'] > 0) {
                        if($data['qty_appr'] > 0) {
                            array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'current_stock' => $current_stock, 'available_stock' => $available_stock, 'qty_appr'=> $data['qty_appr'], 'max_price' => $max_price, 'min_price' => $min_price));
                        }
                    }else {
                
                        array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'current_stock' => $current_stock, 'available_stock' => $available_stock, 'qty_appr'=> "", 'max_price' => $max_price, 'min_price' => $min_price));
                    }
                }
            }
        }
        $is_rejected=0;
        $is_approved=0;
        $is_picking_approved=0;
        $SaleOrder = SaleOrder::select('is_rejected', 'picking_approved', 'is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrder)>0) {
            if(!empty($SaleOrder[0]['is_rejected'])) $is_rejected = $SaleOrder[0]['is_rejected'];
            if(!empty($SaleOrder[0]['picking_approved'])) $is_picking_approved = $SaleOrder[0]['picking_approved'];
            if(!empty($SaleOrder[0]['is_approved'])) $is_approved = $SaleOrder[0]['is_approved'];
        }
        $reject_reason = '';
        if($is_rejected == "1") {
            $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
            if(sizeof($SaleOrderRejectReason) > 0) {
                $reject_reason = $SaleOrderRejectReason[0]['reason'];
            }
        }
        $packingStatus = 0;
        $PackingSQL = Packing::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($PackingSQL) > 0) {
            $packingStatus = 1;
        }
        return \View::make("backend/sale_order/approve_sale_order_details")->with([
            'products' => $returnData,
            'is_rejected' => $is_rejected,
            'is_approved' => $is_approved,
            'reject_reason' => $reject_reason,
            'is_picking_approved' => $is_picking_approved,
            'sale_order_id' => $request->sale_order_id,
            'ordersatatus' => $request->ordersatatus,
            'packingStatus' => $packingStatus
        ]);
    }
    
    public function get_no_stock_sale_order_details(Request $request){
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
                
                if($data['available_qty'] < 1) {
                    $part_name = "";
                    $current_stock = 0;
                    $noStockQty = $data['qty'];
                    $Products = Products::select('product_id', 'part_name_id', 'pmpno', 'current_stock', 'qty_on_order')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        
                        if(!empty($Products[0]['current_stock'])) $current_stock = $Products[0]['current_stock'];
                        
                        $SaleOrderDetails4DeuQty = SaleOrderDetails::select('qty_due')->where([['sale_order_id', '=', $request->sale_order_id], ['product_id', '=', $data['product_id']], ['is_approved', '=', '1']])->get()->toArray();
                        
                        if(sizeof($SaleOrderDetails4DeuQty) > 0) {
                            
                            //if($SaleOrderDetails4DeuQty[0]['qty_due'] > 0) {
                                
                                $noStockQty =$SaleOrderDetails4DeuQty[0]['qty_due'];
                                
                            //}
                        }else {
                            
                            $approve_quantity = SaleOrderDetails::where([['product_id', '=', $data['product_id']]])->sum('qty_appr');
                            
                            if($approve_quantity > 0) {
                                
                                $current_stock = $current_stock - $approve_quantity;
                                $noStockQty = $data['qty'] - $current_stock;
                                
                            }else {
                                
                                $noStockQty = $data['qty'] - $current_stock;
                            }
                        }
                        
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                            if(!empty($PartName)) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                    }
                    if($noStockQty > 0) {
                        
                        $max_price = "";
                        $getMaxPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'desc')->limit(1)->get()->toArray();
                        if(sizeof($getMaxPrice) > 0) {
                            $max_price = $getMaxPrice[0]['product_price'];
                        }
                        $min_price = "";
                        $getMinPrice = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data['product_id']]])->orderBy('product_price', 'asc')->limit(1)->get()->toArray();
                        if(sizeof($getMinPrice) > 0) {
                            $min_price = $getMinPrice[0]['product_price'];
                        }
        
                        array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_price' => $data['product_price'], 'qty' => $noStockQty, 'current_stock' => $current_stock, 'part_name' => $part_name, 'pmpno' => $pmpno, 'min_price' => $min_price, 'max_price' => $max_price));
                    }
                }
            }
        }
        
        return \View::make("backend/sale_order/no_stock_sale_order_details")->with([
            'products' => $returnData,
            'sale_order_id' => $request->sale_order_id,
            'ordersatatus' => $request->ordersatatus
        ]);
    }
    
    public function approve_sale_order_management() {

        return \View::make("backend/sale_order/approve_sale_order_management")->with([
            'PartBrand' => PartBrand::select('part_brand_id','part_brand_name')->where([['status', '=', 1], ['part_brand_name', '!=', '']])->orderBy('part_brand_name', 'ASC')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status', 1)->orderBy('part_name', 'ASC')->get()->toArray(),
            'Clients' => Clients::select('client_id', 'customer_name')->where('delete_status', 0)->orderBy('customer_name', 'ASC')->get()->toArray(),
            //'PartBrand' => DB::table('sale_order_details as sod')->select('pb.part_brand_id', 'pb.part_brand_name')->join('products as p', 'p.product_id', '=', 'sod.product_id', 'left')->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id')->where('p.current_stock', '>', 0)->groupBy('pb.part_brand_id')->get()->toArray(),
            ]);
    }
    public function get_approve_sale_order(Request $request){
    	if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');

            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved', 'sale_order.slip_approved','sale_order.is_rejected','sale_order.order_status', 'clients.customer_name', 'clients.sponsor_name');
            
            if(!empty($request->filter_customer)) {
                $query->where('sale_order.client_id', '=', $request->filter_customer);
            }
            if(!empty($request->filter_from_date)) {
                
                if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                    
                    $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") BETWEEN "'.$request->filter_from_date.'" AND "'.$request->filter_to_date.'"');
                }else {
                    $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") = "'.$request->filter_from_date.'"');
                }
            }

            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order.sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order.sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order.sale_order_id', 'DESC');
            }

            $query->where('sale_order.order_status', '=', '1');
            $data_sale=$query->get()->toArray();
            $sale= new Collection;

            foreach($data_sale as $data_array){
                
                $status = "";
                $selectDeliveryManagement = DeliveryManagement::select('sale_order_id')->whereRaw('FIND_IN_SET('.$data_array->sale_order_id.' ,sale_order_id )')->get()->toArray();
                $ShippingDetails = ShippingDetails::where([['sale_order_id', '=', $data_array->sale_order_id]])->get()->toArray();
                
                $Packing = Packing::where([['sale_order_id', '=', $data_array->sale_order_id]])->get()->toArray();
                if(sizeof($selectDeliveryManagement) > 0) {
                    $status = '<span class="badge badge-success">Delivery</span>';
                }else if(sizeof($ShippingDetails) > 0) {
                    $status = '<span class="badge badge-success">Shipping</span>';
                }else if(sizeof($Packing) > 0) {
                    $status = '<span class="badge badge-success">Packed</span>';
                }else if($data_array->slip_approved == 1) {
                    $status = '<span class="badge badge-info">Invoice</span>';
                }else if($data_array->is_approved == 1) {
                    $status = '<span class="badge badge-warning">Approved</span>';
                }else {
                    $status = '<span class="badge badge-danger">Not Approved</span>';
                }
                
                $actions = '';
                $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" data-ordersatatus="StockOrder" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                if($data_array->is_approved == "0") {
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn approved-order" title="Click To Approve"  style="background-color:orange;border-color:orange"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                    //$actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn view-order-reject" title="Reject Order"><i class="fa fa-window-close" aria-hidden="true"></i></a> ';
                    
                }
                // $nextAppr = $this->getNestApprStatus($data_array->sale_order_id);
                // if($nextAppr) {
                //     $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn approved-order" title="Click To Approve"  style="background-color:orange;border-color:orange"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                // }
                if(!empty($request->filter_part_no)) {
                    $selectProduct = Products::select('product_id')->where([['pmpno', '=', $request->filter_part_no]])->get()->toArray();
                    if(sizeof($selectProduct) > 0) {
                        $product_id = $selectProduct[0]['product_id'];
                        $selectSaleOrderDetails = SaleOrderDetails::select('sale_order_id')->where([['product_id', '=', $product_id], ['sale_order_id', '=', $data_array->sale_order_id]])->get()->toArray();
                        if(sizeof($selectSaleOrderDetails) > 0) {
                            $grand_total = $this->calculateGrandTotal($data_array->sale_order_id);
                             $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions, 'status' => $status]);
                        }
                    }
                } else if(!empty($request->filter_part_brand)) {
                    
                    $selectSaleOrderDetails = SaleOrderDetails::select('sale_order_id')->where([['sale_order_id', '=', $data_array->sale_order_id]])->whereIn('product_id', [DB::raw("SELECT product_id FROM `products` WHERE `part_brand_id` = '".$request->filter_part_brand."' AND `current_stock` > 0")])->get()->toArray();
                    if(sizeof($selectSaleOrderDetails) > 0) {
                        $grand_total = $this->calculateGrandTotal($data_array->sale_order_id);
                         $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions, 'status' => $status]);
                    }
                        
                } else if(!empty($request->filter_part_name)) {
                    
                    $selectSaleOrderDetails = SaleOrderDetails::select('sale_order_id')->where([['sale_order_id', '=', $data_array->sale_order_id]])->whereIn('product_id', [DB::raw("SELECT product_id FROM `products` WHERE `part_name_id` = '".$request->filter_part_name."' AND `current_stock` > 0")])->get()->toArray();
                    if(sizeof($selectSaleOrderDetails) > 0) {
                        $grand_total = $this->calculateGrandTotal($data_array->sale_order_id);
                         $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions, 'status' => $status]);
                    }
                        
                } else{
                    $grand_total = $this->calculateGrandTotal($data_array->sale_order_id);
                    $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions, 'status' => $status]);
                }
                // }
                
            }
                $datatable_array=Datatables::of($sale)
                 ->filter(function ($instance) use ($request) {
   
                        if (!empty($request->input('search.value'))) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                //echo Str::lower($row['client_name'])."<br>";
                                if (Str::contains(Str::lower($row['company_name']), Str::lower($request->input('search.value')))){
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['client_name']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['order_id']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['created_at']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
   
                                return false;
                            });
                        }
   
                    })
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action', 'status'])
                ->toJson();
                //$data=(array)$datatable_array->getData();
                //print_r($data);
                //$data['data']=$x;
                //$data['recordsFiltered']=count($x);
                //$data['page']=($_POST['start']/$_POST['length'])+1;
                //$data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $datatable_array;
            
        }else{
            //
        }
    }
    
    function getNestApprStatus($sale_order_id) {
        $flag = 0;
        
        $SaleOrderDetails = SaleOrderDetails::select('product_id', 'qty_due')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderDetails) >0) {
            foreach($SaleOrderDetails as $data) {
                
                if($data['qty_due'] > 0) {
                    
                    $Products = Products::select('current_stock')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    
                    if(sizeof($Products) >0) {
                        
                        if($Products[0]['current_stock'] > 0 && $Products[0]['current_stock'] >= $data['qty_due']) {
                            $flag = 1;
                            break;
                        }
                    }
                }
            }
        }
        return $flag;
    }
    
    public function create_no_stock_order(Request $request) {
        $qry = SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->update(['order_status' => 1]);
        if($qry) {
            return response()->json(["status" => 1, "msg" => "Order crerate successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Order crerate faild!"]);
        }
    }

    //Get order details for approve
    public function get_sale_order_details_for_approve(Request $request){
        $sale_order_id = $request->sale_order_id;
        $query = DB::table('sale_order_details as cod');
        $query->join('products as p', 'cod.product_id', '=', 'p.product_id', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->select('cod.product_id','cod.sale_order_details_id','cod.order_line_no', 'p.pmpno', 'pn.part_name','cod.product_price','cod.qty');
        $query->where([['cod.sale_order_id', '=', $sale_order_id], ['cod.is_deleted', '=', '0']]);
        $data=$query->get();
        $returnData = [];
        if(sizeof($data) > 0) {
            foreach($data as $val) {
        
                $current_stock = 0;
                $available_stock = 0;
                $Products = Products::select('current_stock', 'qty_on_order')->where([['product_id', '=', $val->product_id], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
        
                    if(!empty($Products[0]['current_stock'])) $current_stock = $Products[0]['current_stock'];
                    // if(!empty($Products[0]['qty_on_order'])) {
                    //     $current_stock = $current_stock - $Products[0]['qty_on_order'];
                    // }
                    
                    $available_stock = $Products[0]['current_stock'];
                    
                    $approve_quantity = SaleOrderDetails::where([['product_id', '=', $val->product_id]])->sum('qty_appr');
                    if($approve_quantity > 0) {
                        $available_stock = $available_stock - $approve_quantity;
                    }
                    
                    if($available_stock < 0) {
                        $available_stock = 0;
                    }
                }
                $mad = $this->calculateMAD($val->product_id);
                
                //if($available_stock > 0) {
                    $apv_qty = $val->qty;
                    if($val->qty > $available_stock) {
                        $apv_qty =$available_stock;
                    }
                    array_push($returnData, ['product_id' => $val->product_id, 'sale_order_details_id' => $val->sale_order_details_id, 'order_line_no' => $val->order_line_no, 'pmpno' => $val->pmpno, 'part_name' => $val->part_name, 'product_price' => $val->product_price, 'qty' => $val->qty, 'current_stock' => $current_stock, 'available_stock' => $available_stock, 'apv_qty' => $apv_qty, 'sale_order_id' => $sale_order_id, 'mad' => $mad]);
                //}
            }
        }
        
        return \View::make("backend/sale_order/sale_order_approve")->with(array('products'=>$returnData));
    }
    public function calculateMAD($product_id) {
        $mad = 0;
        
        $today = date('Y-m-d',strtotime("-1 days"));
        $fromday = date('Y-m-d',strtotime("-365 days"));
        
        $query = DB::table('sale_order_details as sod');
        $query->select(DB::raw("SUM(sod.qty_appr) as total_qty"));
        $query->where([['product_id', '=', $product_id]]);
        $query->join('sale_order as so', 'so.sale_order_id', '=', 'sod.sale_order_id', 'left');
        $query->whereRaw('DATE_FORMAT(so.created_at,"%Y-%m-%d") BETWEEN "'.$fromday.'" AND "'.$today.'"');
        $selectData = $query->get()->toArray();
        if(sizeof($selectData) > 0) {
            if($selectData[0]->total_qty > 0) $mad = $selectData[0]->total_qty;
        }
        $cMad = 0;
        if($mad > 0) {
            $cMad = $mad/12;
            if($cMad > 0 && $cMad < 1) {
                $cMad = 1;
            }else {
                $cMad = round($cMad,1);
            }
        }
        
        return $cMad;
    }
    public function get_product_by_part_no(Request $request) {
        if ($request->ajax()) {
            $view = "";
            $query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->whereRaw('is_deleted != 1 and (p.pmpno LIKE "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%")');
            $query->limit(30);
            $Products = $query->get()->toArray();
            //Products::select('product_id', 'part_name', 'pmpno')->whereRaw('supplier_id = '.$request->supplier.' and is_deleted != 1 and (pmpno LIKE "%'.$request->part_no.'%" or  part_name LIKE "%'.$request->part_no.'%") and FIND_IN_SET('.$request->warehouse.' ,warehouse_id )')->limit('100')->get()->toArray();
            if(sizeof($Products) > 0) {
                $view = $view.'<ul class="list-group" style="position: absolute;">';
                foreach($Products as $data) {
                    $view = $view.'<li class="list-group-item"><a href="#" class="product-details" style="text-decoration: none" data-pmpno="'.$data->pmpno.'">'.$data->part_name.' ('.$data->pmpno.')</a></li>';
                }
                $view = $view.'</ul>';
                return response()->json(["status" => 1, "data" => $view]);
            }else {
                return response()->json(["status" => 0, "message" => "No record found."]);
            }
        }
    }
    
    // approve order
    public function approve_order(Request $request){
        
        $product_id = $request->product_id;
        $sale_order_id = $request->sale_order_id;
        $prev_qty= $request->prev_qty;
        $prev_product_tax = $request->prev_product_tax;
        $approve_qty = $request->approve_qty;
        $product_price = $request->product_price;
        $grand_total = 0;
        
        if(sizeof($request->lineItemCheck) > 0) {
            
            for($i=0; $i<sizeof($request->lineItemCheck); $i++) {
                
                if($request->current_stock[$request->lineItemCheck[$i]] > 0) {
                    
                    $sub_total =$approve_qty[$request->lineItemCheck[$i]]*$product_price[$request->lineItemCheck[$i]];
                    $grand_total +=$sub_total;
                    
                    $due_qty = $request->qty_ordered[$request->lineItemCheck[$i]] - $approve_qty[$request->lineItemCheck[$i]];
                    $available_qty = $request->current_stock[$request->lineItemCheck[$i]] - $approve_qty[$request->lineItemCheck[$i]];
                    SaleOrderDetails::where([['sale_order_details_id', '=', $request->sale_order_details_id[$request->lineItemCheck[$i]]], ['sale_order_id', '=', $sale_order_id]])->update(['qty_appr'=>$approve_qty[$request->lineItemCheck[$i]], 'qty_due' => $due_qty, 'available_qty' => $available_qty, 'product_price'=>$product_price[$request->lineItemCheck[$i]], 'is_approved' => 1]);
                }
            }
        }
        
        $query = DB::table('sale_order');
        $query->where('sale_order_id','=',$sale_order_id);
        $res=$query->update(array('sub_total'=>$sub_total,'grand_total'=>$grand_total,'updated_at'=>date('Y-m-d'),'is_approved'=>1));
        if($res) {
            $returnData = ["status" => 1, "msg" => "Order is approved successfully"];
        }else {
            $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
        }
        return response()->json($returnData);
    }
    // Reject
    public function reject_sale_order(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $data = new SaleOrderRejectReason;
            $data->sale_order_id = $request->order_id;
            $data->reason = $request->reason;
            $data->reason = $request->reason;
            $SaleOrderRejectReason = $data->save();
            if($SaleOrderRejectReason) {
                $saveData = SaleOrder::where('sale_order_id', $request->order_id)->update(['is_rejected' => "1"]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Reject successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Reject failed!"];
                }
            }else {
                $returnData = ["status" => 0, "msg" => "Reject failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function view_order_reject_form(Request $request) {
        return \View::make("backend/sale_order/view_order_reject_form")->with([
            'order_id' => $request->order_id
        ])->render();
    }
    
    public function delete_sale_order(Request $request) {
        if ($request->ajax()) {
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->delete();
            if($SaleOrderDetails) {
                SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->delete();
                return response()->json(["status" => 1, "msg" => "Delete successfully."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Delete failed!"]);
            }
        }
    }
    
    public function delete_sale_order_details(Request $request) {
        if ($request->ajax()) {
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_details_id', '=', $request->id]])->delete();
            if($SaleOrderDetails) {
                return response()->json(["status" => 1, "msg" => "Delete successfully."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Delete failed!"]);
            }
        }
    }
    
    public function delete_approve_sale_order_details(Request $request) {
        if ($request->ajax()) {
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_details_id', '=', $request->id]])->delete();
            if($SaleOrderDetails) {
                $selectDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                if(sizeof($selectDetails) > 0) {
                    return response()->json(["status" => 1, "msg" => "Delete successfully."]);
                }else {
                    SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->delete();
                    return response()->json(["status" => 1, "msg" => "Delete successfully."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete failed!"]);
            }
        }
        
    }
    
    // public function download_invoice(Request $request) {
    //     $id = $request->id;
    //     $pdf = \App::make('dompdf.wrapper');
    //     $pdf->loadHTML($this->convert_request_order_to_html($id));
    //     return $pdf->stream();
    // }
    function download_invoice(Request $request) {
        
        $id = $request->id;
        $returnData = [];
        $invoice_no = 'WMS-'.$id;
        SaleOrder::where([['sale_order_id', '=', $id]])->update(['invoice_date' => date('Y-m-d'), 'print_invoice'=>'1', 'invoice_no' => $invoice_no]);
        $query = DB::table('sale_order_details as so');
        $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
        $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.alternate_part_no', 'p.unit', 'p.pmrprc', 'wu.unit_name');
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
                    array_push($returnData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'alternate_part_no' => $data->alternate_part_no, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                }
            }
        }
        $vat_description = "";
        $vat_percentage = "";
        $ClientsData = [];
        $SaleOrder = SaleOrder::where([['sale_order_id', '=', $id]])->get()->toArray();
        if(sizeof($SaleOrder) > 0) {
            if(!empty($SaleOrder[0]['client_id'])) {
                $Clients = Clients::where([['client_id', '=', $SaleOrder[0]['client_id']]])->get()->toArray();
                if(sizeof($Clients) > 0) {
                    $ClientsData = $Clients;
                }
                
                if(!empty($SaleOrder[0]['vat_type_id'])) {
                
                    $selectVatType = VatType::select('*')->where([['vat_type_id', '=', $SaleOrder[0]['vat_type_id']]])->get()->toArray();
                    if(sizeof($selectVatType) > 0) {
                        
                        $vat_description = $selectVatType[0]['description'];
                        $vat_percentage = $selectVatType[0]['percentage'];
                    }
                }
            }
        }
        return view('backend.sale_order.sale_order_invoice')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $id,
            'vat_description' => $vat_description,
            'vat_percentage' => $vat_percentage
        ]);
    }
    
    function download_customer_invoice(Request $request) {
        
        $returnData = [];
        $orderIds = $request->orderIds;
        
        if(!empty($orderIds)) {
            
            $orderIdsIm = explode (",", $orderIds);
            
            foreach($orderIdsIm as $k=>$v) {
                
                $invoice_no = "WMS-".$orderIds;
                SaleOrder::where([['sale_order_id', '=', $v]])->update(['print_invoice'=>'1', 'is_approved' => '1', 'picking_approved' => '1', 'slip_approved' => '1', 'invoice_no' => $invoice_no]);
                $query = DB::table('sale_order_details as so');
                $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
                $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.unit', 'p.pmrprc', 'wu.unit_name');
                $query->where([['so.sale_order_id', '=', $v], ['so.is_deleted', '=', '0']]);
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
            }
        }
        $ClientsData = [];
        $Clients = Clients::where([['client_id', '=', $request->filter_customer]])->get()->toArray();
        if(sizeof($Clients) > 0) {
            $ClientsData = $Clients;
        }
        return view('backend.sale_order.sale_order_invoice')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $orderIds
        ]);
    }
    public static function numberTowords(float $amount)
    {
       $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
       // Check if there is any number after decimal
       $amt_hundred = null;
       $count_length = strlen($num);
       $x = 0;
       $string = array();
       $change_words = array(0 => 'Zero', 1 => 'One', 2 => 'Two',
         3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
         7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
         10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
         13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
         16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
         19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
         40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
         70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
      $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
      while( $x < $count_length ) {
           $get_divider = ($x == 2) ? 10 : 100;
           $amount = floor($num % $get_divider);
           $num = floor($num / $get_divider);
           $x += $get_divider == 10 ? 1 : 2;
           if ($amount) {
             $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
             $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
             $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
             '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
             '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
             }else $string[] = null;
           }
       $implode_to_Rupees = implode('', array_reverse($string));
       $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
       " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
       return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
    }
    // Picking Slip
    public function picking_slip() {
        return \View::make("backend/sale_order/picking_slip")->with([
            'Clients' => Clients::select('client_id', 'customer_name')->where('delete_status', 0)->orderBy('customer_name', 'ASC')->get()->toArray(),
            ]);
    }
    public function get_picking_order(Request $request){
        if ($request->ajax()) {
            DB::enableQueryLog();
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.gst', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved', 'sale_order.picking_approved', 'sale_order.slip_approved','sale_order.is_rejected','sale_order.print_picking_slip','sale_order.print_invoice', 'sale_order.invoice_no', 'clients.customer_name', 'clients.sponsor_name');
            $query->where('is_approved', '1');
            if(!empty($request->filter_customer)) {
                $query->where([['sale_order.client_id', '=', $request->filter_customer], ['sale_order.invoice_no', '=', NULL]]);
            }
            if($keyword)
            {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order_id', 'DESC');
            }
            $query->get();
//             $query = DB::getQueryLog();
// dd($query); exit();
            $datatable_array=Datatables::of($query)
                ->addColumn('order_id', function ($query) use($request) {
                    $order_id = $query->sale_order_id;
                    if(!empty($request->filter_customer)) {
                        $order_id = '<div class="form-check" style="    padding-left: 2.85rem;"><input class="form-check-input client-line-item-check" type="checkbox" id="check'.$query->sale_order_id.'" name="lineItemCheck[]" value="'.$query->sale_order_id.'"></div>  '.$query->sale_order_id;
                    }
                    return $order_id;
                })
                ->addColumn('client_name', function ($query) {
                    $customer_name = '';
                    if(!empty($query->customer_name)) {
                        $customer_name .= $query->customer_name;
                    }
                    return $customer_name;
                })
                ->addColumn('company_name', function ($query) {
                    $sponsor_name = '';
                    if(!empty($query->sponsor_name)) {
                        $sponsor_name .= $query->sponsor_name;
                    }
                    return $sponsor_name;
                })
                ->addColumn('grand_total', function ($query) {
                    $grand_total = 0;
                    if(!empty($query->grand_total)) {
                        $grand_total += $query->grand_total;
                    }
                    if(!empty($query->gst)) {
                        $grand_total += $query->gst;
                    }
                    $grand_total = round($grand_total,3);
                    return $grand_total;
                })
                ->addColumn('created_at', function ($query) {
                    $created_at = '';
                    if(!empty($query->created_at)) {
                        $created_at .= date('d M Y',strtotime($query->created_at));
                    }
                    return $created_at;
                })
                ->addColumn('merged_no', function ($query) {
                    $merged_no = '';
                    if(!empty($query->invoice_no)) {
                        $myRes = explode('-', $query->invoice_no );
                        $array4mNo = explode(",",$myRes[1]);
                        asort($array4mNo, SORT_NUMERIC);
                        $latest_array = array_count_values($array4mNo);
                        $out = array();
                        foreach ($latest_array as $k => $value) {
                            array_push($out, "$k");
                        }
                        $merged_no = implode(', ', $out);
                    }
                    return $merged_no;
                })
                ->addColumn('action', function ($query) {
                    $actions = '';
                    $actions .= '<a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    if($query->is_rejected == "1") {
                        $actions .= '<a href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn" title="Rejected"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                    }else {
                        if($query->picking_approved < 1) {
                            $actions .= '<a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn approved-order" title="Click To Approve"  style="background-color:orange;border-color:orange"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                            //$actions .= '<a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn view-order-reject" title="Reject Order"><i class="fa fa-window-close" aria-hidden="true"></i></a> ';
                            
                        }else {
                            if(!empty($query->invoice_no)) {
                                
                                $actions .= '<a href="javascript:void(0);" data-invoice_no="'.$query->invoice_no.'" data-client_id="'.$query->c_id.'" data-print_picking_slip="'.$query->print_picking_slip.'" class="print-merged-picking-slip btn btn-warning action-btn" title="Print Picking Slip" ><i class="fa fa-download" aria-hidden="true"></i> Print Picking Slip</a>';
                            }else {
                                
                                $actions .= '<a href="javascript:void(0);" data-sale-order-id="'.$query->sale_order_id.'" data-print_picking_slip="'.$query->print_picking_slip.'" class="print-picking-slip btn btn-warning action-btn" title="Print Picking Slip" ><i class="fa fa-download" aria-hidden="true"></i> Print Picking Slip</a>';
                            }
                        }
                        if($query->picking_approved == 1 && $query->slip_approved < 1) {
                            $actions .= ' <a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn approve-packing-slip" title="Click To Approve Packing Slip"  style="background-color:orange;border-color:orange"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                        }
                        if($query->picking_approved == 1 && $query->slip_approved == 1) {
                            if(!empty($query->invoice_no)) {
                                $actions .= ' <a href="javascript:void(0);" data-invoice_no="'.$query->invoice_no.'" data-client_id="'.$query->c_id.'" data-print_invoice="'.$query->print_invoice.'" class="download-merged-invoice btn btn-warning action-btn" title="Download Invoice" ><i class="fa fa-download" aria-hidden="true"></i> Print Invoice</a> ';
                            }else {
                            
                                $actions .= ' <a href="javascript:void(0);" data-sale-order-id="'.$query->sale_order_id.'" data-print_invoice="'.$query->print_invoice.'" class="download-invoice btn btn-warning action-btn" title="Print Invoice" ><i class="fa fa-download" aria-hidden="true"></i> Print Invoice</a> ';
                            }
                        }
                        if($query->print_picking_slip == 1 || $query->print_invoice == 1) {
                            if(Session::get('user_type') == "A" || Session::get('user_type') == "Admin") {
                                $actions .= '<a href="javascript:void(0);" data-sale-order-id="'.$query->sale_order_id.'" class="reset-print btn btn-success action-btn" title="Reset Print" >Reset Print</a>';
                            }
                        }                       
                    }
                    return $actions;
                })
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    public function before_picking_approve(Request $request) {
        //echo $request->sale_order_id;
        $returnData = [];
        $ProductLocation = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $Products = DB::table('products as p')->select('p.pmpno', 'pn.part_name')->join('part_name as pn', 'p.part_name_id', '=', 'pn.part_name_id', 'left')->where([['p.product_id', '=', $data['product_id']]])->get()->toArray();
                if(!empty($Products[0]->pmpno)) $pmpno = $Products[0]->pmpno;
                if(!empty($Products[0]->part_name)) $part_name = $Products[0]->part_name;
                $BinningLocationDetails = BinningLocationDetails::where([['product_id', '=', $data['product_id']]])->get()->toArray();
                $qty_appr = $data['qty_appr'];

                $location_name = "";
                $location_id = "";
                $zone_name = "";
                $zone_id = "";
                $row_name = "";
                $row_id = "";
                $rack_name = "";
                $rack_id = "";
                $plate_name = "";
                $plate_id = "";
                $place_name = "";
                $place_id = "";
                $binning_location_details_id = "";
                $binning_qty = "";
                $accept_qty = $qty_appr;
                foreach($BinningLocationDetails as $bld) {
                    if($qty_appr > 0) {
                        // if($qty_appr > $bld['quantity']) {
                        //     $accept_qty = $bld['quantity'];
                        // }else {
                        //     $accept_qty = $qty_appr;
                        // }
                        // $qty_appr = $qty_appr - $bld['quantity'];
                        //
                        if(!empty($bld['location_id'])) {
                            $location_id = $bld['location_id'];
                            $location = DB::table('location')->select('location_name')->where('location_id', $bld['location_id'])->get()->toArray();
                            if(sizeof($location) > 0) {
                                if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                            }
                        }
                        if(!empty($bld['zone_id'])) {
                            $zone_id = $bld['zone_id'];
                            $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $bld['zone_id'])->get()->toArray();
                            if(sizeof($zone_master) > 0) {
                                if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                            }
                        }
                        if(!empty($bld['row_id'])) {
                            $row_id = $bld['row_id'];
                            $row = DB::table('row')->select('row_name')->where('row_id', $bld['row_id'])->get()->toArray();
                            if(sizeof($row) > 0) {
                                if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                            }
                        }
                        if(!empty($bld['rack_id'])) {
                            $rack_id = $bld['rack_id'];
                            $rack = DB::table('rack')->select('rack_name')->where('rack_id', $bld['rack_id'])->get()->toArray();
                            if(sizeof($rack) > 0) {
                                if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                            }
                        }
                        if(!empty($bld['plate_id'])) {
                            $plate_id = $bld['plate_id'];
                            $plate = DB::table('plate')->select('plate_name')->where('plate_id', $bld['plate_id'])->get()->toArray();
                            if(sizeof($plate) > 0) {
                                if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                            }
                        }
                        if(!empty($bld['place_id'])) {
                            $place_id = $bld['place_id'];
                            $place = DB::table('place')->select('place_name')->where('place_id', $bld['place_id'])->get()->toArray();
                            if(sizeof($place) > 0) {
                                if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                            }
                        }
                        if(!empty($bld['binning_location_details_id'])) {
                            $binning_location_details_id = $bld['binning_location_details_id'];
                        }
                        if(!empty($bld['quantity'])) {
                            $binning_qty = $bld['quantity'];
                        }
                        //
                    }
                    //$returnData['binning_location_details_id'] = $bld['binning_location_details_id'];
                }
                if($qty_appr > 0) {
                    array_push($returnData, ['binning_location_details_id' => $binning_location_details_id, 'product_id' =>$data['product_id'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'binning_qty' => $binning_qty, 'quantity' => $accept_qty, 'location_id' => $location_id, 'location_name' => $location_name, 'zone_id' => $zone_id, 'zone_name' => $zone_name, 'row_id' => $row_id, 'row_name' => $row_name, 'rack_id' => $rack_id, 'rack_name' => $rack_name, 'plate_id' => $plate_id, 'plate_name' => $plate_name, 'place_id' => $place_id, 'place_name' => $place_name, 'product_price' =>$data['product_price']]);
                }
            }
        }
        return \View::make("backend/sale_order/approve_order_details")->with([
            'OrderDetails' => $returnData,
            'order_id' => $request->sale_order_id
        ]);
    }
    public function picking_approve(Request $request){
        
        $falg = 0;
        // if(sizeof($request->product_id) > 0) {
        //     for($i=0; $i < sizeof($request->product_id); $i++) {
                //$current_qty = $request->binning_qty[$i] - $request->quantity[$i];
                //BinningLocationDetails::where([['binning_location_details_id', '=', $request->binning_location_details_id[$i]]])->update(['quantity' => $current_qty]);
        //         $data = new SaleOrderPickingApproveDetails;
        //         $data->order_id = $request->order_id;
        //         $data->product_id = $request->product_id[$i];
        //         $data->quantity = $request->quantity[$i];
        //         $data->location_id = $request->location_id[$i];
        //         $data->zone_id = $request->zone_id[$i];
        //         $data->row_id = $request->row_id[$i];
        //         $data->rack_id = $request->rack_id[$i];
        //         $data->plate_id = $request->plate_id[$i];
        //         $data->place_id = $request->place_id[$i];
        //         $data->save();
        //         $falg++;
        //     }
        // }
        
        if(sizeof($request->product_id) > 0)
        {
            for($i=0; $i < sizeof($request->product_id); $i++)
            {
                Products::where('product_id',$request->product_id[$i])->update(array('current_stock' => DB::raw('current_stock - '.$request->quantity[$i])));
            
                $falg++;
            }
        }
        
        if($falg == sizeof($request->product_id)) {
            SaleOrder::where([['sale_order_id', '=', $request->order_id]])->update(['picking_approved'=> '1']);
            $returnData = ["status" => 1, "msg" => "Approved successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Something is wrong, please try again!"];
        }


        // $up_query = SaleOrder::where([['sale_order_id', '=', $request->order_id]])->update(['picking_approved'=> '1']);
        // if($up_query) {
        //     $returnData = ["status" => 1, "msg" => "Approved successful."];
        // }else {
        //     $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
        // }
        return response()->json($returnData);
    }
    
    public function reset_print(Request $request){
        
        $selectSaleOrder = SaleOrder::select('invoice_no')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        
        if(!empty($selectSaleOrder[0]['invoice_no'])) {
            
            $up_query = SaleOrder::where([['invoice_no', '=', $selectSaleOrder[0]['invoice_no']]])->update(['print_picking_slip'=> '0', 'print_invoice'=> '0']);
            
            if($up_query) {
                
                $returnData = ["status" => 1, "msg" => "Reset successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Reset faild! Semethning is wrong."];
            }
            
        } else {
            
            $up_query = SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->update(['print_picking_slip'=> '0', 'print_invoice'=> '0']);
            
            if($up_query) {
                
                $returnData = ["status" => 1, "msg" => "Reset successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Reset faild! Semethning is wrong."];
            }
            }
        return response()->json($returnData);
    }
    
    // public function print_picking_slip(Request $request) {
    //     $id = $request->id;
    //     $pdf = \App::make('dompdf.wrapper');
    //     $pdf->loadHTML($this->convert_picking_slip_to_html($id));
    //     return $pdf->stream();
    // }
    function print_picking_slip(Request $request) {
        $id = $request->id;
        $returnData = [];
        SaleOrder::where([['sale_order_id', '=', $id]])->update(['print_picking_slip'=>'1']);
        $query = DB::table('sale_order_details as so');
        $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
        $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->select('so.product_id', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.alternate_part_no', 'p.unit', 'p.pmrprc', 'wu.unit_name');
        $query->where([['so.sale_order_id', '=', $id]]);
        $SaleOrderDetails = $query->get()->toArray();
        
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
                
                $location_name = "";
                $zone_name = "";
                $row_name = "";
                $rack_name = "";
                $plate_name = "";
                $place_name = "";
                
                if($data->qty_appr > 0 ) {
                    
                    $BinningLocationDetails = BinningLocationDetails::where([['product_id', '=', $data->product_id]])->get()->toArray();
                    if(sizeof($BinningLocationDetails) > 0) {
                        if(!empty($BinningLocationDetails[0]['location_id'])) {
                            
                            $location = DB::table('location')->select('location_name')->where('location_id', $BinningLocationDetails[0]['location_id'])->get()->toArray();
                            if(sizeof($location) > 0) {
                                if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                            }
                        }
                        if(!empty($BinningLocationDetails[0]['zone_id'])) {
                            
                            $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $BinningLocationDetails[0]['zone_id'])->get()->toArray();
                            if(sizeof($zone_master) > 0) {
                                if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                            }
                        }
                        if(!empty($BinningLocationDetails[0]['row_id'])) {
                            
                            $row = DB::table('row')->select('row_name')->where('row_id', $BinningLocationDetails[0]['row_id'])->get()->toArray();
                            if(sizeof($row) > 0) {
                                if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                            }
                        }
                        if(!empty($BinningLocationDetails[0]['rack_id'])) {
                            
                            $rack = DB::table('rack')->select('rack_name')->where('rack_id', $BinningLocationDetails[0]['rack_id'])->get()->toArray();
                            if(sizeof($rack) > 0) {
                                if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                            }
                        }
                        if(!empty($BinningLocationDetails[0]['plate_id'])) {
                            
                            $plate = DB::table('plate')->select('plate_name')->where('plate_id', $BinningLocationDetails[0]['plate_id'])->get()->toArray();
                            if(sizeof($plate) > 0) {
                                if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                            }
                        }
                        if(!empty($BinningLocationDetails[0]['place_id'])) {
                            
                            $place = DB::table('place')->select('place_name')->where('place_id', $BinningLocationDetails[0]['place_id'])->get()->toArray();
                            if(sizeof($place) > 0) {
                                if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                            }
                        }
                    }
                    
                    array_push($returnData, array('product_id' => $data->product_id, 'quantity' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'alternate_part_no' => $data->alternate_part_no, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'zone_name' => $zone_name, 'location_name' => $location_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                }
            }
        }
        // echo "<pre>";
        // print_r($returnData); exit();
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
        return view('backend.sale_order.print_picking_slip')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $id
        ]);
    }
    
    function print_customer_picking_slip(Request $request) {
        
        $returnData = [];
        $orderIds = $request->orderIds;
        
        if(!empty($orderIds)) {
            
            $orderIdsIm = explode (",", $orderIds);
            
            foreach($orderIdsIm as $k=>$v) {
                
                $slip_no = "SLIP-".$orderIds;
                SaleOrder::where([['sale_order_id', '=', $v]])->update(['print_picking_slip'=>'1', 'slip_approved' => '1', 'slip_no' => $slip_no]);
                
                $query = DB::table('sale_order_details as so');
                $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
                $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->select('so.product_id', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.unit', 'p.pmrprc', 'wu.unit_name');
                $query->where([['so.sale_order_id', '=', $v]]);
                $SaleOrderDetails = $query->get()->toArray();
                
                if(sizeof($SaleOrderDetails) > 0) {
                    foreach($SaleOrderDetails as $data) {
                        
                        $location_name = "";
                        $zone_name = "";
                        $row_name = "";
                        $rack_name = "";
                        $plate_name = "";
                        $place_name = "";
                        
                        if($data->qty_appr > 0 ) {
                            
                            $BinningLocationDetails = BinningLocationDetails::where([['product_id', '=', $data->product_id]])->get()->toArray();
                            if(sizeof($BinningLocationDetails) > 0) {
                                if(!empty($BinningLocationDetails[0]['location_id'])) {
                                    
                                    $location = DB::table('location')->select('location_name')->where('location_id', $BinningLocationDetails[0]['location_id'])->get()->toArray();
                                    if(sizeof($location) > 0) {
                                        if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['zone_id'])) {
                                    
                                    $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $BinningLocationDetails[0]['zone_id'])->get()->toArray();
                                    if(sizeof($zone_master) > 0) {
                                        if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['row_id'])) {
                                    
                                    $row = DB::table('row')->select('row_name')->where('row_id', $BinningLocationDetails[0]['row_id'])->get()->toArray();
                                    if(sizeof($row) > 0) {
                                        if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['rack_id'])) {
                                    
                                    $rack = DB::table('rack')->select('rack_name')->where('rack_id', $BinningLocationDetails[0]['rack_id'])->get()->toArray();
                                    if(sizeof($rack) > 0) {
                                        if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['plate_id'])) {
                                    
                                    $plate = DB::table('plate')->select('plate_name')->where('plate_id', $BinningLocationDetails[0]['plate_id'])->get()->toArray();
                                    if(sizeof($plate) > 0) {
                                        if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['place_id'])) {
                                    
                                    $place = DB::table('place')->select('place_name')->where('place_id', $BinningLocationDetails[0]['place_id'])->get()->toArray();
                                    if(sizeof($place) > 0) {
                                        if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                                    }
                                }
                            }
                            
                            array_push($returnData, array('product_id' => $data->product_id, 'quantity' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'zone_name' => $zone_name, 'location_name' => $location_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                        }
                    }
                }
            }
        }
        
        $ClientsData = [];
        $Clients = Clients::where([['client_id', '=', $request->filter_customer]])->get()->toArray();
        if(sizeof($Clients) > 0) {
            $ClientsData = $Clients;
        }
        return view('backend.sale_order.print_picking_slip')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $orderIds
        ]);
    }
    public function approve_packing_slip(Request $request){
        $sale_order_id = $request->sale_order_id;

        // $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        // if(sizeof($SaleOrderDetails) > 0) {

        //     foreach($SaleOrderDetails as $sdata) {

        //         Products::where('product_id',$sdata['product_id'])->update(array('qty_on_order' => DB::raw('qty_on_order + '.$sdata['qty_appr'])));
        //     }
        // }
        //print_r($SaleOrderDetails); exit();

        $query = SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->update(['slip_approved'=>'1']);
        if($query) {
            $returnData = ["status" => 1, "msg" => "Approved successfully"];
        }else {
            $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
        }
        return response()->json($returnData);
    }
    // No Stock Order Management
    public function no_stock_order() {
        return \View::make("backend/sale_order/no_stock_order")->with([
            'Clients' => Clients::select('client_id', 'customer_name')->where('delete_status', 0)->orderBy('customer_name', 'ASC')->get()->toArray(),
            ]);
    }
    public function get_no_stock_order(Request $request){
    	if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');

            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected','sale_order.order_status', 'clients.customer_name', 'clients.sponsor_name');
            
            // if(!empty($request->client_id)) {
            //     $query->where('sale_order.client_id', '=', $request->client_id);
            // }
            if(!empty($request->filter_customer)) {
                $query->where('sale_order.client_id', '=', $request->filter_customer);
            }
            if(!empty($request->filter_from_date)) {
                
                if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                    
                    $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") BETWEEN "'.$request->filter_from_date.'" AND "'.$request->filter_to_date.'"');
                }else {
                    $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") = "'.$request->filter_from_date.'"');
                }
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order.sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order.sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order.sale_order_id', 'DESC');
            }

            $query->where('sale_order.order_status', '=', '1');
            $data_sale=$query->get()->toArray();
            $sale= new Collection;

            foreach($data_sale as $data_array){

                $product_status = $this->chcekProductStock4nostock($data_array->sale_order_id);
                if($product_status == 1) {
                    $actions = '';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" data-ordersatatus="StockOrder" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    
                    $SaleOrderTemplate = SaleOrderTemplate::where('sale_order_id', $data_array->sale_order_id)->get()->toArray();
                    if(sizeof($SaleOrderTemplate) > 0) {
                        //$actions .= '<a  href="javascript:void(0);" name="button" class="view-subbrand btn btn-warning download-order-template" data-template_name="'.$SaleOrderTemplate[0]['template_name'].'" title="Download Template" ><i class="fa fa-download" aria-hidden="true"></i></a> ';
                    }
                    if($data_array->order_status == 2) {
                        $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-primary action-btn edit-order-details" title="Edit Order"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    }
                    $grand_total = $this->calculateNoStockGrandTotal($data_array->sale_order_id);
                    $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions]);
                }
                
            }
                $datatable_array=Datatables::of($sale)
                 ->filter(function ($instance) use ($request) {
   
                        if (!empty($request->input('search.value'))) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                //echo Str::lower($row['client_name'])."<br>";
                                if (Str::contains(Str::lower($row['company_name']), Str::lower($request->input('search.value')))){
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['client_name']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['order_id']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
                                else if (Str::contains(Str::lower($row['created_at']), Str::lower($request->input('search.value')))) {
                                    return true;
                                }
   
                                return false;
                            });
                        }
   
                    })
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action'])
                ->toJson();
                //$data=(array)$datatable_array->getData();
                //print_r($data);
                //$data['data']=$x;
                //$data['recordsFiltered']=count($x);
                //$data['page']=($_POST['start']/$_POST['length'])+1;
                //$data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $datatable_array;
            
        }else{
            //
        }
    }
    
    function calculateNoStockGrandTotal($sale_order_id) {
        
        $grandtotal = 0;
        $selectData = DB::table('sale_order_details')->select('*')->where('sale_order_id', '=', $sale_order_id)->get()->toArray();
        
        if(sizeof($selectData) > 0) {
            
            foreach($selectData as $data) {
                
                $current_stock = 0;
                $qty = $data->qty;
                
                if($data->is_approved == '1') {
                    
                    $qty = $data->qty_due;
                }else {
                
                    $Products = Products::select('current_stock', 'qty_on_order')->where([['product_id', '=', $data->product_id], ['is_deleted', '=', '0']])->get()->toArray();
                    
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['current_stock'])) {
                            if($Products[0]['current_stock'] > $qty) {
                                $qty = 0;
                            }else {
                                $qty = $qty - $Products[0]['current_stock'];
                            }
                        }
                        
                    }
                    
                    $qty_appr = SaleOrderDetails::where([['product_id', '=', $data->product_id], ['sale_order_id', '=', $sale_order_id]])->sum('qty_appr');
                    
                    if($qty_appr > 0) {
                        $qty = $qty - $qty_appr;
                    }
                }
                
                $sub_total = $data->product_price * $qty;
                $grandtotal +=$sub_total;
            }
        }
        $grandtotal = round($grandtotal,3);
        return $grandtotal;
    }
    
    public function get_no_stock_order_19_10_2022(Request $request){
        if ($request->ajax()) {

            $data=[];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');

            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected', 'sale_order.reference_id', 'clients.customer_name', 'clients.sponsor_name');

            if(!empty($request->client_id)) {
                $query->where('sale_order.client_id', '=', $request->client_id);
            }

            if($keyword)
            {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }

            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order_id', 'DESC');
            }

            $query->where('sale_order.order_status', '=', '3');
            $data_sale=$query->get()->toArray();
            $sale= new Collection;

            foreach($data_sale as $data_array){

                    $actions = '';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" data-reference_id="' . $data_array->reference_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" data-ordersatatus="noStockOrder" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger action-btn view-order-reject" title="Reject Order"><i class="fa fa-window-close" aria-hidden="true"></i></a> ';
                    

                    $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $data_array->grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions]);
                
                
            }
            $datatable_array=Datatables::of($sale)
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    public static function chcekProductStock4Create($sale_order_id) {
        $flag = 1;
        $SaleOrderDetails = SaleOrderDetails::select('product_id', 'qty')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderDetails) >0) {
            foreach($SaleOrderDetails as $data) {
                $Products = Products::select('current_stock')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) >0) {
                    if($Products[0]['current_stock'] > 0 || $Products[0]['current_stock'] >= $data['qty']) {
                        $flag = 1;
                        break;
                    }
                }else {
                    $flag = 0;
                    break;
                }
            }
        }
        return $flag;
    }
    public static function chcekProductStock4nostock($sale_order_id) {
        $flag = 0;
        
        $SaleOrderDetails = SaleOrderDetails::select('product_id', 'qty','is_approved', 'qty_due', 'available_qty')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderDetails) >0) {
            foreach($SaleOrderDetails as $data) {
                
                if($data['is_approved'] == '1') {
                    if($data['available_qty'] > 0) {
                        $flag = 0;
                    }else if($data['qty_due'] > 0) {
                        $flag = 1;
                        break;
                    }
                }else {
                
                    $qty_appr = SaleOrderDetails::where([['product_id', '=', $data['product_id']]])->sum('qty_appr');
                    $Products = Products::select('current_stock')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) >0) {
                        $available_qty = $Products[0]['current_stock'];
                        if($qty_appr > 0) {
                            $available_qty = $available_qty - $qty_appr;
                        }
                        if($available_qty < 1 || $available_qty < $data['qty']) {
                            $flag = 1;
                            //break;
                        }
                    }else {
                        $flag = 0;
                        //break;
                    }
                }
            }
        }
        return $flag;
    }
    public function add_new_order(Request $request)
    {
        $previous_data=[];
        //$cart_data = $request->cookie('cart_data');
        //if(!empty($cart_data)) {
            //$res=$this->product_details_by_id($cart_data);
            // $cart_data_array=json_decode($cart_data,true);
            // foreach ($cart_data_array as $key => $value) {
            //     //$res=$this->product_details_by_id($value['product_id']);
            //     $res['qty']=$value['qty'];
            //     $previous_data[]=$res;
            // }
            //$previous_data[]=$res;
        //}
        //print_r($previous_data); exit();
       return \View::make("backend/sale_order/new_order_mamangement_form")->with([
            'customer_id' => Clients::where([['customer_stopsale', '!=', 'y'], ['delete_status', '=',0]])->get()->toArray(),
            'cart_datas'=>$previous_data,
            'gst_value' => WmsProductTaxes::where('tax_name', 'like', '%vat%')->get()->toArray(),
            'VatTypeData' => VatType::orderBy('description', 'ASC')->get()->toArray()
        // $clients = Clients::where('delete_status',0)->get();
        ])->render();
    }
    function product_details_by_id($cart_data) {
        $returnData = [];
        $data=json_decode($cart_data,true);
        if(sizeof($data)>0) {
            foreach($data as $key=>$val) {
                $pmpno = "";
                $part_name = "";
                $category_id = "";
                $category_name = "";
                $mrp = "";
                $Products = Products::select('pmpno', 'part_name_id', 'ct', 'pmrprc')->where([['product_id', '=', $val['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                if(!empty($Products)) {
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['ct'])) $category_id = $Products[0]['ct'];
                    if(!empty($Products[0]['pmrprc'])) $mrp = $Products[0]['pmrprc'];
                    $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($PartName)) {
                        if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                    }
                    $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $Products[0]['ct']], ['status', '=', '0']])->get()->toArray();
                    if(!empty($ProductCategories)) {
                        if(!empty($ProductCategories[0]['category_name'])) $category_name = $ProductCategories[0]['category_name'];
                    }
                }
                array_push($returnData, array('product_id' => $val['product_id'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'category_id' => $category_id, 'category_name' => $category_name, 'vat' => 0, 'pmrprc' => $mrp, 'qty' => $val['qty']));
            }
            return $returnData;
        }
    }
    public function sales_order_edit(Request $request)
    {
        $arrayData = [];
        $arrayData2 = [];
        if(!empty($request->sale_order_id)) {
            $arrayData = SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            if(sizeof($SaleOrderDetails) > 0) {
                foreach($SaleOrderDetails as $sd) {
                    $part_no = '';
                    $part_name = '';
                    $c_name = '';
                    $ct = '';
                    $products = Products::select('pmpno', 'ct', 'part_name_id')->where([['product_id', '=', $sd['product_id']]])->get()->toArray();
                    if(sizeof($products) > 0) {
                        $part_no = $products[0]['pmpno'];
                        $ct = $products[0]['ct'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            $part_name = $PartName[0]['part_name'];
                        }
                        $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $products[0]['ct']]])->get()->toArray();
                        if(sizeof($ProductCategories) > 0) {
                            $c_name = $ProductCategories[0]['category_name'];
                        }
                    }
                    array_push($arrayData2, array('product_id' => $sd['product_id'], 'part_no' => $part_no, 'part_name' => $part_name, 'c_name' => $c_name, 'ct' => $ct, 'pmrprc' => $sd['product_price'], 'current_stock' => '', 'qty' => $sd['qty']));
                }
            }
        }
        $previous_data=[];
        $cart_data = $request->cookie('cart_data');
        if($cart_data)
        {
            $cart_data_array=json_decode($cart_data,true);
            foreach ($cart_data_array as $key => $value) {
                $res=$this->product_details_by_id($value['product_id']);
                $res['qty']=$value['qty'];
                $previous_data[]=$res;
            }
        }
        
        $vatEditData = [];
        
        if(sizeof($arrayData) > 0) {
            if(!empty($arrayData[0]['vat_type_id'])) {
                $selectVatType = VatType::where([['vat_type_id', '=', $arrayData[0]['vat_type_id']]])->get()->toArray();
                if(sizeof($selectVatType) > 0) {
                    array_push($vatEditData, ['description' => $selectVatType[0]['description'], 'percentage' => $selectVatType[0]['percentage']]);
                }
            }
        }
        
        return \View::make("backend/sale_order/new_order_mamangement_form")->with([
            'customer_id' => Clients::where([['customer_stopsale', '!=', 'y'], ['delete_status', '=', 0]])->get()->toArray(),
            'cart_datas' => $previous_data,
            'SaleOrder' => $arrayData,
            'SaleOrderDetails' => $arrayData2,
            'VatTypeData' => VatType::orderBy('description', 'ASC')->get()->toArray(),
            'vatEditData' => $vatEditData
        // $clients = Clients::where('delete_status',0)->get();
        ])->render();
    }
    public function edit_new_order(Request $request)
    {
        $arrayData = [];
        $arrayData2 = [];
        if(!empty($request->sale_order_id)) {
            $arrayData = SaleOrder::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
            if(sizeof($SaleOrderDetails) > 0) {
                foreach($SaleOrderDetails as $sd) {
                    $part_no = '';
                    $part_name = '';
                    $c_name = '';
                    $ct = '';
                    $products = Products::select('pmpno', 'ct', 'part_name_id')->where([['product_id', '=', $sd['product_id']]])->get()->toArray();
                    if(sizeof($products) > 0) {
                        $part_no = $products[0]['pmpno'];
                        $ct = $products[0]['ct'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            $part_name = $PartName[0]['part_name'];
                        }
                        $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $products[0]['ct']]])->get()->toArray();
                        if(sizeof($ProductCategories) > 0) {
                            $c_name = $ProductCategories[0]['category_name'];
                        }
                    }
                    array_push($arrayData2, array('product_id' => $sd['product_id'], 'part_no' => $part_no, 'part_name' => $part_name, 'c_name' => $c_name, 'ct' => $ct, 'pmrprc' => $sd['product_price'], 'current_stock' => '', 'qty' => $sd['qty']));
                }
            }
        }
        $previous_data=[];
        $cart_data = $request->cookie('cart_data');
        if($cart_data)
        {
            $cart_data_array=json_decode($cart_data,true);
            foreach ($cart_data_array as $key => $value) {
                $res=$this->product_details_by_id($value['product_id']);
                $res['qty']=$value['qty'];
                $previous_data[]=$res;
            }
        }
       return \View::make("backend/sale_order/new_order_mamangement_form")->with([
            'customer_id' => Clients::where('delete_status',0)->get()->toArray(),
            'cart_datas' => $previous_data,
            'SaleOrder' => $arrayData,
            'SaleOrderDetails' => $arrayData2,
        // $clients = Clients::where('delete_status',0)->get();
        ])->render();
    }

    public function get_product_by_part_no_order(Request $request)
    {
        if ($request->ajax()) {

            $returnData = [];

            if(!empty($request->part_no)) {

                $ProductsData = [];
                $view = "";

                $query = DB::table('products as p');
                $query->select('p.product_id', 'p.pmpno', 'p.current_stock', 'p.qty_on_order', 'p.selling_price', 'p.lc_price', 'pn.part_name', 'pb.part_brand_name');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
                $query->whereRaw('p.is_deleted != 1 and (replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or p.pmpno like "%'.$request->part_no.'%")');
                $query->limit('100');
                $Products = $query->get()->toArray();

                if(sizeof($Products) > 0) {

                    $product_details = '\'product-details\'';
                    $view = $view.'<ul class="list-group">';

                    foreach($Products as $data) {

                        $alternate_no = [];
                        $alternate_noC = "";
                        $AlternatePartNo = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $data->product_id]])->get()->toArray();

                        if(sizeof($AlternatePartNo) > 0) {
                            foreach($AlternatePartNo as $alt) {
                                $alternate_no[] = "#".$alt['alternate_no'];
                            }
                            $alternate_noC = implode(',', $alternate_no);
                        }

                        $available_stock = $data->current_stock;
                        $approve_quantity = SaleOrderDetails::where([['product_id', '=', $data->product_id]])->sum('qty_appr');
                        if($approve_quantity > 0) {
                            $available_stock = $available_stock - $approve_quantity;
                        }
                        
                        if($available_stock < 0) {
                            $available_stock = 0;
                        }
                        $lp_amount = 0;
                        $getLP = OrderDetail::select('mrp')->where([['product_id', '=', $data->product_id]])->orderBy('order_detail_id', 'desc')->limit(1)->get()->toArray();
                        if(sizeof($getLP) > 0)
                        {
                            $lp_amount = $getLP[0]['mrp'];
                        }
                        
                        $ls_amount = 0;
                        $getLS = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data->product_id], ['client_id', '!=', $request->client_id]])->orderBy('sale_order_details_id', 'desc')->limit(1)->get()->toArray();
                        if(sizeof($getLS) > 0)
                        {
                            $ls_amount = $getLS[0]['product_price'];
                        }
                        $lc_amount = 0;
                        if(!empty($data->lc_price))
                        {
                            $lc_amount = $data->lc_price;
                        }
                        // $getLC = SaleOrderDetails::select('product_price')->where([['product_id', '=', $data->product_id], ['client_id', '=', $request->client_id]])->orderBy('sale_order_details_id', 'desc')->limit(1)->get()->toArray();
                        // if(sizeof($getLC) > 0)
                        // {
                        //     $lc_amount = $getLC[0]['product_price'];
                        // }
                        
                        $selling_price = 0;
                        if(!empty($data->selling_price))
                        {
                            $selling_price = $data->selling_price;
                        }
                        $view = $view.'<li class="list-group-item"><a href="#" class="product-details" style="text-decoration: none" data-pmpno="'.$data->pmpno.'" data-current_stock="'.$available_stock.'" data-product-id="'.$data->product_id.'" data-lp_amount="'.$lp_amount.'" data-ls_amount="'.$ls_amount.'" data-lc_amount="'.$lc_amount.'" data-selling_price="'.$selling_price.'">'.$data->part_name.' ('.$data->pmpno.') - '.$available_stock.' - '.$data->part_brand_name.'<br>'.$alternate_noC.'</a></li>';
                    }

                    $view = $view.'</ul>';
                    $returnData = array('status' => 1, 'data' => $view);

                }else {

                    $returnData = array('status' => 1, 'msg' => "No record found.");

                }
            }
            return response()->json($returnData);
        }
    }

    public function product_details(Request $request){
        $part_no = $request->part_no;

        $min_price = 0;
        $max_price = 0;
        $query = DB::table('products');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct', 'left');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name');
        $query->where('products.pmpno', '=', $part_no);
        $data=$query->get()->toArray();
        $model = new DB;
        $product_id = $data[0]->product_id;
        $available_stock = $this->available_stock($model, $product_id);
        $qry1 = DB::table('sale_order_details')->select(\DB::raw('MIN(NULLIF(product_price,0)) AS min_price, MAX(product_price) AS max_price'))->where(['product_id'=>$data[0]->product_id,'is_deleted'=>0],['qty_appr','!=',null])->get();
        if(sizeof($qry1)>0)
        {
            if($qry1[0]->min_price != "")
            $min_price = $qry1[0]->min_price;
            if($qry1[0]->max_price != "")
            $max_price = $qry1[0]->max_price;
        }
        $order_status = "";
        if(!empty(Session::get('order_status'))) {
            $order_status = Session::get('order_status');
        }
        $data_array[]=array('product_id'=>$data[0]->product_id,'pmpno'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=> round($data[0]->pmrprc,3),'current_stock'=>$available_stock,'min_price'=>$min_price,'max_price'=>$max_price, 'order_status' => $order_status);
        return response()->json($data_array);
    }
    function available_stock($model, $product_id)
    {
        $query = $model::table('products');
        $query->select('products.current_stock');
        $query->where('products.product_id', '=', $product_id );
        $data=$query->get();
        // $used_stock_query=$model::table('client_order_details');
        // $used_stock_query->select(DB::raw("SUM(qty) as total_qty"));
        // $used_stock_query->where('client_order_details.product_id', '=', $product_id);
        // $used_data=json_decode(json_encode($used_stock_query->get()->toArray()),TRUE);
        // if(sizeof($used_data)>0)
        // {
        //     $current_stock = $data[0]->current_stock-$used_data[0]['total_qty'];
        // }
        // else
        // {
            $current_stock = $data[0]->current_stock;
        //}
        $transit_quantity = SaleOrderDetails::where([['product_id', '=', $product_id]])->sum('qty');
        if($transit_quantity > 0) {
            $current_stock = $current_stock - $transit_quantity;
        }
        if($current_stock < 0) {
            $current_stock = 0;
        }
        return $current_stock;          
    }
    public function order_preview()
    {
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArrayWithAll($file);
        return \View::make("backend/sale_order/order_preview")->with([
            'products'=>$productArr['data'],
            'VatTypeData' => VatType::orderBy('description', 'ASC')->get()->toArray()
            ]);
    }
    function csvToArrayWithAll($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $product_details=$this->product_details_by_part_no($row[0]);
                    if(sizeof($product_details)>0 && $row[1]>0 && is_numeric($row[1])) {
                            $product_details['qty']=$row[1];
                            $sub_total+=($row[1]*$product_details['selling_price']);
                            $total_gst=0;
                            $grand_total+=($row[1]*$product_details['selling_price']);
                            $data[] = $product_details;
                    }else {
                        if(sizeof($product_details) >0 && is_numeric($row[1])) {
                            $data[] = array('product_id'=>$row[0],'qty'=>"Invalid quantity");
                        }else {
                            if(sizeof($product_details) >0 && $row[1] <= 0) {
                                $data[] = array('product_id'=>$row[0],'qty'=>0 . " Quantity sholud be atleast 1");
                            }else {
                                $data[] = array('product_id'=>$row[0],'qty'=>$row[1] ." This is wrong product. It will be skipped when upload.");
                            }
                        }
                    }
                }
            }
            fclose($handle);
        }
        return array('data'=>$data,'sub_total'=>$sub_total,'tax'=>$total_gst,'grand_total'=>$grand_total);
    }
    public function product_details_by_part_no($part_no){
        $data_array=[];
        $query = DB::table('products');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name' );
        $query->where('products.pmpno', '=', $part_no);
        $data=$query->get()->toArray();
        if(sizeof($data)>0) {
            $model = new DB;
            $available_stock = $this->available_stock_by_part_no($model, $part_no);
            $data_array=array('product_id'=>$data[0]->product_id,'part_no'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>$data[0]->pmrprc,'selling_price'=>$data[0]->selling_price,'current_stock'=>$available_stock);
        }
        return $data_array;
    }
    function available_stock_by_part_no($model, $part_no)
    {
        $query = $model::table('products');
        $query->select('products.current_stock');
        $query->where('products.pmpno', '=', $part_no);
        $data=$query->get();
        // $used_stock_query=$model::table('client_order_details');
        // $used_stock_query->select(DB::raw("SUM(qty) as total_qty"));
        // $used_stock_query->where('client_order_details.product_id', '=', $product_id);
        // $used_data=json_decode(json_encode($used_stock_query->get()->toArray()),TRUE);
        // if(sizeof($used_data)>0)
        // {
        //     $current_stock = $data[0]->current_stock-$used_data[0]['total_qty'];
        // }
        // else
        // {
            $current_stock = $data[0]->current_stock;
        //}
        return $current_stock;          
    }
    public function create_multiple_order(Request $request){
        $file = $_FILES['file']['tmp_name'];
        $client_data = $request->client;
        // echo $client_data; exit();
        $productArr = $this->csvToArray($file);
        if(sizeof($productArr['data'])>0) {
            $upimages = $request->file;
            $csv_file = rand() . '.' . $upimages->getClientOriginalExtension();
            $upimages->move(public_path('backend/file/upload_order_csv/'), $csv_file);
            //
            $sub_total = $productArr['sub_total'];
            $grand_total = $productArr['grand_total'];
            $tax = 0;
            $vat_percentage = $request->vat_percentage;
            //echo "sub_total: ".$sub_total." grand_total: ".$grand_total." vat_percentage: ".$vat_percentage; exit();
            if(is_numeric($vat_percentage)) {
                
                $tax = ($productArr['sub_total'] * $vat_percentage)/100;
                $grand_total +=$tax;
            }else {
                $tax = $vat_percentage;
            }
            if(is_numeric($tax)) {
                $tax = round($tax, 3);
            }
            else {
                $tax = 0;
            }
            //echo $tax; exit();
            $order_data = array('client_id'=>$client_data,'sub_total'=>$productArr['sub_total'],'gst'=> $tax,'grand_total'=> $grand_total, 'vat_type_id' => $request->vat_type_value, 'remarks'=>"",'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
            $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
            $data = new SaleOrderTemplate;
            $data->sale_order_id = $last_sale_order_id;
            $data->template_name = $csv_file;
            $data->save();
            foreach($productArr['data'] as $product) {
                $product_id = $product['product_id'];
                $mrp = $product['pmrprc'];
                $qty = $product['qty'];
                $gst_array = 0;
                $product_tax = 0;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id,'product_tax'=>$product_tax,'product_price'=>$mrp,'qty'=>$qty);
                SaleOrderDetails::insert($order_details);
            }
            if(!$last_sale_order_id) {
                $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
            }else {
                $returnData = ["status" => 1, "msg" => "Order is created successfully"];
            }
        }
        else {
            $returnData = ["status" => 0, "msg" => "Sorry! Order is failed"];
        }
        return response()->json($returnData);
    }
    function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false)
        {

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                {
                    $product_details=$this->product_details_by_part_no($row[0]);
                    if(sizeof($product_details)>0 && $row[1]>0 && is_numeric($row[1]))
                    {
                        //if($product_details['current_stock'] >= $row[1])
                        //{
                            $product_details['qty']=$row[1];
                            $sub_total+=($row[1]*$product_details['selling_price']);
                            $total_gst=0;
                            $grand_total+=($row[1]*$product_details['selling_price']);
                            $data[] = $product_details;
                        //}
                    }
                    
                    
                }
            }
            fclose($handle);
        }

        return array('data'=>$data,'sub_total'=>$sub_total,'tax'=>$total_gst,'grand_total'=>$grand_total);
    }
    public function create_order_1(Request $request)
    {
        if(!empty($request->hidden_sale_order_id)) {
            $order_status = 1;
            $msg = "Order create successful.";
            if($request->order_status == "SaveOrder") {
                $order_status = 2;
                $msg = "Order save successful.";
            }
            
            $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks, 'order_status'=> $order_status, 'vat_type_id' => $request->vat_type_value, 'updated_at'=>date('Y-m-d'));
            SaleOrder::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->update($order_data);
            SaleOrderDetails::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
            $product_id_array = $request->product_id;
            $flag = 0;
            for($i=0;$i<sizeof($request->category_id);$i++) {
                $mrp_array = $request->mrp;
                $qty_array = $request->qty;
                $gst_array = $request->gst;
                $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $order_details=array('sale_order_id'=>$request->hidden_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id_array[$i],'product_tax'=>$product_tax,'product_price'=>$mrp_array[$i],'qty'=>$qty_array[$i]);
                SaleOrderDetails::insert($order_details);
                $flag++;
            }
            if($flag == sizeof($request->category_id)) {
                $returnData = ["status" => 1, "msg" => $msg];
            }else {
                $returnData = ["status" => 0, "msg" => "Save faild! Something is wrong. Please try again."];
            }
            return response()->json($returnData);
        }else {
            $customer_off_msg_no = "";
            $customer_name = "";
            $customer_email_id = "";
            $ClientsData = Clients::select('customer_off_msg_no', 'customer_name', 'customer_email_id')->where([['client_id', '=', $request->client]])->get()->toArray();
            if(sizeof($ClientsData) > 0) {
                if(!empty($ClientsData[0]['customer_off_msg_no'])) $customer_off_msg_no = $ClientsData[0]['customer_off_msg_no'];
                if(!empty($ClientsData[0]['customer_name'])) $customer_name = $ClientsData[0]['customer_name'];
                if(!empty($ClientsData[0]['customer_email_id'])) $customer_email_id = $ClientsData[0]['customer_email_id'];
            }
            $api_key = "";
            $SmsApiKey = SmsApiKey::select('api_key')->where([['status', '=', '1']])->get()->toArray();
            if(sizeof($SmsApiKey) > 0) {
                if(!empty($SmsApiKey[0]['api_key'])) $api_key = $SmsApiKey[0]['api_key'];
            }
            $smtp_user = "";
            $smtp_pass = "";
            $smtp_port = "";
            $from_mail = "";
            $MailApiKey = MailApiKey::select('smtp_user', 'from_mail', 'from_name', 'from_mail')->where([['status', '=', '1']])->get()->toArray();
            if(sizeof($MailApiKey) > 0) {
                if(!empty($MailApiKey[0]['smtp_user'])) $smtp_user = $MailApiKey[0]['smtp_user'];
                if(!empty($MailApiKey[0]['smtp_pass'])) $smtp_pass = $MailApiKey[0]['smtp_pass'];
                if(!empty($MailApiKey[0]['smtp_port'])) $smtp_port = $MailApiKey[0]['smtp_port'];
                if(!empty($MailApiKey[0]['from_mail'])) $from_mail = $MailApiKey[0]['from_mail'];
            }
            $order_status = 1;
            $msg = "Order create successful.";
            if($request->order_status == "SaveOrder") {
                $order_status = 2;
                $msg = "Order save successful.";
            }
            //
            $StockOrderArray = [];
            $NoStockOrderArray = [];
            $s_sub_total = 0;
            $n_sub_total = 0;
            $s_tax = 0;
            $n_tax = 0;
            $s_grand_total = 0;
            $n_grand_total= 0;
            $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
            $max_order_line_no = $max_order_line_no[0]->olnm;
            for($i=0;$i<sizeof($request->category_id);$i++) {
                $max_order_line_no=$max_order_line_no+1;
                $Products = Products::select('current_stock')->where([['product_id', '=', $request['product_id'][$i]]])->get()->toArray();
                if(sizeof($Products) >0) {
                    if($Products[0]['current_stock'] < 1) {
                        $mrp_array = $request->mrp;
                        $qty_array = $request->qty;
                        $gst_array = $request->gst;
                        $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;

                        array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));

                        $n_sub_total += $qty_array[$i] * $mrp_array[$i];
                        $n_sub_total = round($n_sub_total,2);
                        $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                        $n_tax = round($n_tax,2);
                        $n_grand_total += $n_sub_total + $n_tax;
                        $n_grand_total = round($n_grand_total,2);
                    }else {
                        $mrp_array = $request->mrp;
                        $qty_array = $request->qty;
                        $gst_array = $request->gst;
                        $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;

                        array_push($StockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));

                        $s_sub_total += $qty_array[$i] * $mrp_array[$i];
                        $s_sub_total = round($s_sub_total,2);
                        $s_tax += ($s_sub_total * $request->hidden_tax_rate)/100;
                        $s_tax = round($s_tax,2);
                        $s_grand_total += $s_sub_total + $s_tax;
                        $s_grand_total = round($s_grand_total,2);
                    }
                }
            }

            print_r($NoStockOrderArray);
            print_r($StockOrderArray);
            exit();
            if(sizeof($NoStockOrderArray) > 0) {
                $order_data = array('client_id'=>$request->client,'sub_total'=>$n_sub_total,'gst'=>$n_tax,'grand_total'=>$n_grand_total,'remarks'=>$request->remarks, 'order_status' => "3", 'vat_type_id' => $request->vat_type_value , 'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                
                $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
                foreach ($NoStockOrderArray as $ndata) {
                    SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$ndata['order_line_no'],'product_id'=>$ndata['product_id'],'product_tax'=>$ndata['product_tax'],'product_price'=>$ndata['product_price'],'qty'=>$ndata['qty']));
                }
            }

            if(sizeof($StockOrderArray) > 0) {
                $order_data = array('client_id'=>$request->client,'sub_total'=>$s_sub_total,'gst'=>$s_tax,'grand_total'=>$s_grand_total,'remarks'=>$request->remarks, 'order_status' => $order_status,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                
                $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
                foreach ($StockOrderArray as $sdata) {
                    SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$sdata['order_line_no'],'product_id'=>$sdata['product_id'],'product_tax'=>$sdata['product_tax'],'product_price'=>$sdata['product_price'],'qty'=>$sdata['qty']));
                }
            }
            Cookie::queue(Cookie::forget('cart_data'));
            $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
            $returnData = ["status" => 1, "msg" => $msg];
            return response()->json($returnData);
            exit();
            //
            $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks, 'order_status' => $order_status,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
            //print_r($order_data); exit();
            $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
            $product_id_array = $request->product_id;
            for($i=0;$i<sizeof($request->category_id);$i++) {
                $mrp_array = $request->mrp;
                $qty_array = $request->qty;
                $gst_array = $request->gst;
                $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id_array[$i],'product_tax'=>$product_tax,'product_price'=>$mrp_array[$i],'qty'=>$qty_array[$i]);
                SaleOrderDetails::insert($order_details);
            }
            if(!$last_sale_order_id) {
                $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
            }else {
                Cookie::queue(Cookie::forget('cart_data'));
                $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
                //$this->submitMsg($customer_off_msg_no, $customer_name, $api_key);
                //$this->sendEmail("Order Create", $body, $customer_email_id, 'OMS');
                $returnData = ["status" => 1, "msg" => $msg];
            }
            return response()->json($returnData);
        }
    }
    public function create_order_18_10_2022(Request $request)
    {
        if(!empty($request->hidden_sale_order_id)) {
            if($request->order_status == "SaveOrder") {
                $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks, 'order_status'=> "2", 'updated_at'=>date('Y-m-d'));
                SaleOrder::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->update($order_data);
                SaleOrderDetails::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
                $product_id_array = $request->product_id;
                $flag = 0;
                for($i=0;$i<sizeof($request->category_id);$i++) {
                    $mrp_array = $request->mrp;
                    $qty_array = $request->qty;
                    $gst_array = $request->gst;
                    $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;
                    $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                    $order_details=array('sale_order_id'=>$request->hidden_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id_array[$i],'product_tax'=>$product_tax,'product_price'=>$mrp_array[$i],'qty'=>$qty_array[$i]);
                    SaleOrderDetails::insert($order_details);
                    $flag++;
                }
                if($flag == sizeof($request->category_id)) {
                    $returnData = ["status" => 1, "msg" => "Order save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save faild! Something is wrong. Please try again."];
                }
                return response()->json($returnData);
            }else {
                SaleOrder::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
                SaleOrderDetails::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
                $StockOrderArray = [];
                $NoStockOrderArray = [];
                $s_sub_total = 0;
                $n_sub_total = 0;
                $s_tax = 0;
                $n_tax = 0;
                $s_grand_total = 0;
                $n_grand_total= 0;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $max_order_line_no = $max_order_line_no[0]->olnm;
                for($i=0;$i<sizeof($request->category_id);$i++) {
                    $max_order_line_no=$max_order_line_no+1;
                    $Products = Products::select('current_stock')->where([['product_id', '=', $request['product_id'][$i]]])->get()->toArray();
                    if(sizeof($Products) >0) {
                        if($Products[0]['current_stock'] > 0) {
                            $mrp_array = $request->mrp;
                            $qty_array = $request->qty;
                            $gst_array = $request->gst;
                            $sQty = 0;
                            $nQty = 0;
                            if($qty_array[$i] > $Products[0]['current_stock']) {
                                $nQty = $qty_array[$i] - $Products[0]['current_stock'];
                                $sQty = $qty_array[$i] - $nQty;
                            }else {
                                $sQty = $qty_array[$i];
                            }
                            $product_tax = (($mrp_array[$i]*$sQty)*$gst_array[$i])/100;

                            array_push($StockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $sQty));

                            $s_sub_total += $sQty * $mrp_array[$i];
                            $s_sub_total = round($s_sub_total,2);
                            $s_tax += ($s_sub_total * $request->hidden_tax_rate)/100;
                            $s_tax = round($s_tax,2);
                            $s_grand_total += $s_sub_total + $s_tax;
                            $s_grand_total = round($s_grand_total,2);
                            if($nQty > 0) {
                                $max_order_line_no=$max_order_line_no+1;
                                $product_tax = (($mrp_array[$i]*$nQty)*$gst_array[$i])/100;
                                array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $nQty));
                                $n_sub_total += $nQty * $mrp_array[$i];
                                $n_sub_total = round($n_sub_total,2);
                                $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                                $n_tax = round($n_tax,2);
                                $n_grand_total += $n_sub_total + $n_tax;
                                $n_grand_total = round($n_grand_total,2);
                            }
                        }else {

                            $mrp_array = $request->mrp;
                            $qty_array = $request->qty;
                            $gst_array = $request->gst;
                            $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;

                            array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));

                            $n_sub_total += $qty_array[$i] * $mrp_array[$i];
                            $n_sub_total = round($n_sub_total,2);
                            $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                            $n_tax = round($n_tax,2);
                            $n_grand_total += $n_sub_total + $n_tax;
                            $n_grand_total = round($n_grand_total,2);
                        }
                    }
                }
                
                if(sizeof($StockOrderArray) > 0) {
                    
                    $order_data = array('client_id'=>$request->client,'sub_total'=>$s_sub_total,'gst'=>$s_tax,'grand_total'=>$s_grand_total,'remarks'=>$request->remarks, 'order_status' => '1','created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));

                    $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);

                    foreach ($StockOrderArray as $sdata) {

                        SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$sdata['order_line_no'],'product_id'=>$sdata['product_id'],'product_tax'=>$sdata['product_tax'],'product_price'=>$sdata['product_price'],'qty'=>$sdata['qty']));

                    }
                }
                if(sizeof($NoStockOrderArray) > 0) {
                    $order_data = array('client_id'=>$request->client,'sub_total'=>$n_sub_total,'gst'=>$n_tax,'grand_total'=>$n_grand_total,'remarks'=>$request->remarks, 'order_status' => "3",'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                    
                    $last_no_stock_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
                    foreach ($NoStockOrderArray as $ndata) {
                        SaleOrderDetails::insert(array('sale_order_id'=>$last_no_stock_sale_order_id,'order_line_no'=>$ndata['order_line_no'],'product_id'=>$ndata['product_id'],'product_tax'=>$ndata['product_tax'],'product_price'=>$ndata['product_price'],'qty'=>$ndata['qty']));
                    }
                }
            }
            $returnData = ["status" => 1, "msg" => "Order create successful."];
            return response()->json($returnData);
            //
            // if($flag == sizeof($request->category_id)) {
            //     $returnData = ["status" => 1, "msg" => $msg];
            // }else {
            //     $returnData = ["status" => 0, "msg" => "Save faild! Something is wrong. Please try again."];
            // }
            // return response()->json($returnData);
        }else {
            $customer_off_msg_no = "";
            $customer_name = "";
            $customer_email_id = "";
            $ClientsData = Clients::select('customer_off_msg_no', 'customer_name', 'customer_email_id')->where([['client_id', '=', $request->client]])->get()->toArray();
            if(sizeof($ClientsData) > 0) {
                if(!empty($ClientsData[0]['customer_off_msg_no'])) $customer_off_msg_no = $ClientsData[0]['customer_off_msg_no'];
                if(!empty($ClientsData[0]['customer_name'])) $customer_name = $ClientsData[0]['customer_name'];
                if(!empty($ClientsData[0]['customer_email_id'])) $customer_email_id = $ClientsData[0]['customer_email_id'];
            }
            $api_key = "";
            $SmsApiKey = SmsApiKey::select('api_key')->where([['status', '=', '1']])->get()->toArray();
            if(sizeof($SmsApiKey) > 0) {
                if(!empty($SmsApiKey[0]['api_key'])) $api_key = $SmsApiKey[0]['api_key'];
            }
            $smtp_user = "";
            $smtp_pass = "";
            $smtp_port = "";
            $from_mail = "";
            $MailApiKey = MailApiKey::select('smtp_user', 'from_mail', 'from_name', 'from_mail')->where([['status', '=', '1']])->get()->toArray();

            if(sizeof($MailApiKey) > 0) {

                if(!empty($MailApiKey[0]['smtp_user'])) $smtp_user = $MailApiKey[0]['smtp_user'];
                if(!empty($MailApiKey[0]['smtp_pass'])) $smtp_pass = $MailApiKey[0]['smtp_pass'];
                if(!empty($MailApiKey[0]['smtp_port'])) $smtp_port = $MailApiKey[0]['smtp_port'];
                if(!empty($MailApiKey[0]['from_mail'])) $from_mail = $MailApiKey[0]['from_mail'];
            }

            $order_status = 1;
            $msg = "Order create successful.";
            //echo $request->order_status; exit();
            if($request->order_status == "SaveOrder") {

                $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks, 'order_status' => "2",'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);

                $product_id_array = $request->product_id;

                for($i=0;$i<sizeof($request->category_id);$i++) {

                    $mrp_array = $request->mrp;
                    $qty_array = $request->qty;
                    $gst_array = $request->gst;
                    $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;
                    $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                    $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id_array[$i],'product_tax'=>$product_tax,'product_price'=>$mrp_array[$i],'qty'=>$qty_array[$i]);

                    SaleOrderDetails::insert($order_details);

                }

                if(!$last_sale_order_id) {

                    $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
                }else {

                    $returnData = ["status" => 1, "msg" => "Order save successful."];
                }

                return response()->json($returnData);

            }else {

                $StockOrderArray = [];
                $NoStockOrderArray = [];
                $s_sub_total = 0;
                $n_sub_total = 0;
                $s_tax = 0;
                $n_tax = 0;
                $s_grand_total = 0;
                $n_grand_total= 0;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $max_order_line_no = $max_order_line_no[0]->olnm;
                for($i=0;$i<sizeof($request->category_id);$i++) {
                    $max_order_line_no=$max_order_line_no+1;
                    $Products = Products::select('current_stock')->where([['product_id', '=', $request['product_id'][$i]]])->get()->toArray();
                    if(sizeof($Products) >0) {
                        
                        $available_stock = $Products[0]['current_stock'];
                        $transit_quantity = SaleOrderDetails::where([['product_id', '=', $request['product_id'][$i]]])->sum('qty');
                        if($transit_quantity > 0) {
                            $available_stock = $available_stock - $transit_quantity;
                        }
                        $stock = $available_stock - $request['qty'][$i];
                        // if($available_stock < 0) {
                        //     $stock = 0;
                        // }
                        
                        if($stock >= 0) {
                            $mrp_array = $request->mrp;
                            $qty_array = $request->qty;
                            $gst_array = $request->gst;
                            $sQty = 0;
                            $nQty = 0;
                            if($qty_array[$i] > $Products[0]['current_stock']) {
                                $nQty = $qty_array[$i] - $Products[0]['current_stock'];
                                $sQty = $qty_array[$i] - $nQty;
                            }else {
                                $sQty = $qty_array[$i];
                            }
                            $product_tax = (($mrp_array[$i]*$sQty)*$gst_array[$i])/100;

                            array_push($StockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $sQty));

                            $s_sub_total += $sQty * $mrp_array[$i];
                            $s_sub_total = round($s_sub_total,2);
                            $s_tax += ($s_sub_total * $request->hidden_tax_rate)/100;
                            $s_tax = round($s_tax,2);
                            $s_grand_total += $s_sub_total + $s_tax;
                            $s_grand_total = round($s_grand_total,2);
                            if($nQty > 0) {
                                $max_order_line_no=$max_order_line_no+1;
                                $product_tax = (($mrp_array[$i]*$nQty)*$gst_array[$i])/100;
                                array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $nQty));
                                $n_sub_total += $nQty * $mrp_array[$i];
                                $n_sub_total = round($n_sub_total,2);
                                $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                                $n_tax = round($n_tax,2);
                                $n_grand_total += $n_sub_total + $n_tax;
                                $n_grand_total = round($n_grand_total,2);
                            }
                        }else {

                            $mrp_array = $request->mrp;
                            $qty_array = $request->qty;
                            $gst_array = $request->gst;
                            $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;

                            array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));

                            $n_sub_total += $qty_array[$i] * $mrp_array[$i];
                            $n_sub_total = round($n_sub_total,2);
                            $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                            $n_tax = round($n_tax,2);
                            $n_grand_total += $n_sub_total + $n_tax;
                            $n_grand_total = round($n_grand_total,2);
                        }
                    }
                }
                
                $last_sale_order_id = NULL;
                if(sizeof($StockOrderArray) > 0) {

                    $order_data = array('client_id'=>$request->client,'sub_total'=>$s_sub_total,'gst'=>$s_tax,'grand_total'=>$s_grand_total,'remarks'=>$request->remarks, 'order_status' => $order_status,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));

                    $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);

                    foreach ($StockOrderArray as $sdata) {

                        SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$sdata['order_line_no'],'product_id'=>$sdata['product_id'],'product_tax'=>$sdata['product_tax'],'product_price'=>$sdata['product_price'],'qty'=>$sdata['qty']));

                    }
                }

                if(sizeof($NoStockOrderArray) > 0) {
                    $order_data = array('client_id'=>$request->client,'sub_total'=>$n_sub_total,'gst'=>$n_tax,'grand_total'=>$n_grand_total,'remarks'=>$request->remarks, 'order_status' => "3",'reference_id' => $last_sale_order_id,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                    
                    $last_no_stock_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
                    foreach ($NoStockOrderArray as $ndata) {
                        SaleOrderDetails::insert(array('sale_order_id'=>$last_no_stock_sale_order_id,'order_line_no'=>$ndata['order_line_no'],'product_id'=>$ndata['product_id'],'product_tax'=>$ndata['product_tax'],'product_price'=>$ndata['product_price'],'qty'=>$ndata['qty']));
                    }
                }
                Cookie::queue(Cookie::forget('cart_data'));
                $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
                $returnData = ["status" => 1, "msg" => $msg];
                return response()->json($returnData);
                exit();
            }
        }
    }
    public function create_order(Request $request)
    {
        //echo "here"; exit();
        
        if(!empty($request->hidden_sale_order_id)) {
                
            SaleOrder::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->update(['gst'=> $request->tax, 'grand_total'=>$request->expertSubTotalWithTax, 'vat_type_id' => $request->vat_type_value]);
            SaleOrderDetails::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
            
            $n_grand_total= 0;
            $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
            $max_order_line_no = $max_order_line_no[0]->olnm;
            $flag = 0;
            if(!empty($request->product_id) ) {
                for($i=0;$i<sizeof($request->product_id);$i++) {
                    
                    $max_order_line_no=$max_order_line_no+1;
                    
                    SaleOrderDetails::insert(array('sale_order_id'=> $request->hidden_sale_order_id, 'order_line_no' => $max_order_line_no, 'client_id'=>$request->client, 'product_id'=>$request->product_id[$i], 'product_price'=> $request->mrp[$i],'qty'=> $request->qty[$i]));
                    $flag++;
                }
                if($flag == sizeof($request->product_id)) {
                    $returnData = ["status" => 1, "msg" => "Order update successful."];
                    return response()->json($returnData);
                }else {
                    $returnData = ["status" => 1, "msg" => "Order update faild. Something is wrong"];
                    return response()->json($returnData);
                }
            }else {
                SaleOrder::where([['sale_order_id', '=', $request->hidden_sale_order_id]])->delete();
                $returnData = ["status" => 1, "msg" => "Order update faild. Something is wrong"];
                return response()->json($returnData);
            }
        }else {
            $customer_off_msg_no = "";
            $customer_name = "";
            $customer_email_id = "";
            $ClientsData = Clients::select('customer_off_msg_no', 'customer_name', 'customer_email_id')->where([['client_id', '=', $request->client]])->get()->toArray();
            if(sizeof($ClientsData) > 0) {
                if(!empty($ClientsData[0]['customer_off_msg_no'])) $customer_off_msg_no = $ClientsData[0]['customer_off_msg_no'];
                if(!empty($ClientsData[0]['customer_name'])) $customer_name = $ClientsData[0]['customer_name'];
                if(!empty($ClientsData[0]['customer_email_id'])) $customer_email_id = $ClientsData[0]['customer_email_id'];
            }
            $api_key = "";
            $SmsApiKey = SmsApiKey::select('api_key')->where([['status', '=', '1']])->get()->toArray();
            if(sizeof($SmsApiKey) > 0) {
                if(!empty($SmsApiKey[0]['api_key'])) $api_key = $SmsApiKey[0]['api_key'];
            }
            $smtp_user = "";
            $smtp_pass = "";
            $smtp_port = "";
            $from_mail = "";
            $MailApiKey = MailApiKey::select('smtp_user', 'from_mail', 'from_name', 'from_mail')->where([['status', '=', '1']])->get()->toArray();

            if(sizeof($MailApiKey) > 0) {

                if(!empty($MailApiKey[0]['smtp_user'])) $smtp_user = $MailApiKey[0]['smtp_user'];
                if(!empty($MailApiKey[0]['smtp_pass'])) $smtp_pass = $MailApiKey[0]['smtp_pass'];
                if(!empty($MailApiKey[0]['smtp_port'])) $smtp_port = $MailApiKey[0]['smtp_port'];
                if(!empty($MailApiKey[0]['from_mail'])) $from_mail = $MailApiKey[0]['from_mail'];
            }

            $order_status = 1;
            $msg = "Order create successful.";
            //echo $request->order_status; exit();
            if($request->order_status == "SaveOrder") {

                $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks, 'order_status' => "2", 'vat_type_id' => $request->vat_type_value ,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
                $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);

                $product_id_array = $request->product_id;

                for($i=0;$i<sizeof($request->category_id);$i++) {

                    $mrp_array = $request->mrp;
                    $qty_array = $request->qty;
                    $gst_array = $request->gst;
                    $product_tax = (($mrp_array[$i]*$qty_array[$i])*$gst_array[$i])/100;
                    $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                    $order_details=array('sale_order_id'=>$last_sale_order_id, 'client_id'=>$request->client, 'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id_array[$i],'product_tax'=>$product_tax,'product_price'=>$mrp_array[$i],'qty'=>$qty_array[$i]);

                    SaleOrderDetails::insert($order_details);

                }

                if(!$last_sale_order_id) {

                    $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
                }else {

                    $returnData = ["status" => 1, "msg" => "Order save successful."];
                }

                return response()->json($returnData);

            }else {
                
                $StockOrderArray = [];
                $s_sub_total = 0;
                $n_sub_total = 0;
                $s_tax = 0;
                $n_tax = 0;
                $s_grand_total = 0;
                $n_grand_total= 0;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $max_order_line_no = $max_order_line_no[0]->olnm;
                for($i=0;$i<sizeof($request->category_id);$i++) {
                    
                    $max_order_line_no=$max_order_line_no+1;
                    $mrp_array = $request->mrp;
                    $qty_array = $request->qty;
                    $gst_array = $request->gst;

                    array_push($StockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));

                    $s_grand_total += $qty_array[$i] * $mrp_array[$i];
                    // $s_sub_total = round($s_sub_total,3);
                    // $s_grand_total += $s_sub_total;
                    
                }
                
                $s_grand_total = round($s_grand_total,3);
                $last_sale_order_id = NULL;
                
                if(sizeof($StockOrderArray) > 0) {

                    $order_data = array('client_id' => $request->client, 'grand_total' => $s_grand_total, 'remarks' => $request->remarks, 'order_status' => $order_status, 'gst'=>$request->tax, 'vat_type_id' => $request->vat_type_value, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'));

                    $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);

                    foreach ($StockOrderArray as $sdata) {

                        SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id, 'client_id'=>$request->client,'order_line_no'=>$sdata['order_line_no'],'product_id'=>$sdata['product_id'],'product_price'=>$sdata['product_price'],'qty'=>$sdata['qty']));

                    }
                }
                Cookie::queue(Cookie::forget('cart_data'));
                $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
                $returnData = ["status" => 1, "msg" => $msg];
                return response()->json($returnData);
                exit();
            }
        }
    }
    // Save Order Management
    public function save_order() {
        return \View::make("backend/sale_order/save_order")->with(array());
    }
    public function save_order_list(Request $request){
        if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected', 'clients.customer_name', 'clients.sponsor_name');
            if(!empty($request->client_id)) {
                $query->where('sale_order.client_id', '=', $request->client_id);
            }
            if($keyword)
            {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('sale_order_id', 'DESC');
            }
            $query->where('sale_order.order_status', '=', '2');
            $data_sale=$query->get()->toArray();
            $sale= new Collection;
            foreach($data_sale as $data_array){
                //$product_status = $this->chcekProductStock($data_array->sale_order_id);
                //if($product_status == 0) {
                    $actions = '';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn view-order-details" data-ordersatatus="saveOrder" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    $actions .= '<a data-sale-order-id="' . $data_array->sale_order_id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-primary action-btn edit-save-order" title="Edit Order"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    $sale->push(['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $data_array->grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'action' => $actions]);
                //}
                
            }
            $datatable_array=Datatables::of($sale)
                ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    public function picking_slip_export() {
        $query = DB::table('sale_order')->join('clients', 'sale_order.client_id', '=', 'clients.client_id')->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved', 'sale_order.picking_approved', 'sale_order.slip_approved','sale_order.is_rejected','sale_order.print_picking_slip','sale_order.print_invoice', 'clients.customer_name', 'clients.sponsor_name')->where('is_approved', '1')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order Id');
        $sheet->setCellValue('B1', 'Client Name');
        $sheet->setCellValue('C1', 'Sponsor Name');
        $sheet->setCellValue('D1', 'Grand Total');
        $sheet->setCellValue('E1', 'Created On');
        
        $rows = 2;
        foreach($query as $td){
            
            $sheet->setCellValue('A' . $rows, $td->sale_order_id);
            $sheet->setCellValue('B' . $rows, $td->customer_name);
            $sheet->setCellValue('C' . $rows, $td->sponsor_name);
            $sheet->setCellValue('D' . $rows, $td->grand_total);
            $sheet->setCellValue('E' . $rows, date('d M Y',strtotime($td->created_at)));
            
            $rows++;
        }
        $fileName = "Picking_Slip.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function no_stock_order_export(Request $request) {
        $arrayData = [];
        //$query = DB::table('sale_order')->join('clients', 'sale_order.client_id', '=', 'clients.client_id')->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved', 'sale_order.picking_approved', 'sale_order.slip_approved','sale_order.is_rejected','sale_order.print_picking_slip','sale_order.print_invoice', 'clients.customer_name', 'clients.sponsor_name')->where('sale_order.order_status', '=', '3')->get()->toArray();
        $query = DB::table('sale_order');
        $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
        $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved','sale_order.is_rejected','sale_order.order_status', 'clients.customer_name', 'clients.sponsor_name');
        if(!empty($request->filter_customer)) {
            $query->where('sale_order.client_id', '=', $request->filter_customer);
        }
        if(!empty($request->filter_from_date)) {
            
            if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                
                $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") BETWEEN "'.$request->filter_from_date.'" AND "'.$request->filter_to_date.'"');
            }else {
                $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") = "'.$request->filter_from_date.'"');
            }
        }
        $query->where('sale_order.order_status', '=', '1');
        $data_sale=$query->get()->toArray();
        foreach($data_sale as $data_array){
            $product_status = $this->chcekProductStock4nostock($data_array->sale_order_id);
                if($product_status == 1) {
                array_push($arrayData, ['order_id' => $data_array->sale_order_id, 'client_name' => $data_array->customer_name, 'company_name' => $data_array->sponsor_name, 'grand_total'=> $data_array->grand_total, 'created_at' => date('d M Y',strtotime($data_array->created_at))]);
            }
            
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order Id');
        $sheet->setCellValue('B1', 'Client Name');
        $sheet->setCellValue('C1', 'Sponsor Name');
        $sheet->setCellValue('D1', 'Grand Total');
        $sheet->setCellValue('E1', 'Created On');
        $rows = 2;
        foreach($arrayData as $td){
            
            $sheet->setCellValue('A' . $rows, $td['order_id']);
            $sheet->setCellValue('B' . $rows, $td['client_name']);
            $sheet->setCellValue('C' . $rows, $td['company_name']);
            $sheet->setCellValue('D' . $rows, $td['grand_total']);
            $sheet->setCellValue('E' . $rows, $td['created_at']);
            
            $rows++;
        }
        $fileName = "No_Stock_Order.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function loss_of_sales_report_export() {
        
        $arrayData = [];
        $SaleOrderDetails = DB::table('sale_order_details')->where([['is_deleted', '=', '0']])->get()->toArray();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Customer');
        $sheet->setCellValue('B1', 'Item');
        $sheet->setCellValue('C1', 'Available quantity');
        $sheet->setCellValue('D1', 'Loss of sale(No stock)');
        $sheet->setCellValue('E1', 'Loss of Amount');
        $sheet->setCellValue('F1', 'Transit Quantity');
        $sheet->setCellValue('G1', 'Need to be order');
        
        $rows = 2;
        
        foreach($SaleOrderDetails as $val) {
            
            $pmpno = "";
            $available_stock = 0;
            
            $noStockQty = $val->qty;
            $Products = Products::select('product_id', 'part_name_id', 'pmpno', 'current_stock', 'qty_on_order')->where([['product_id', '=', $val->product_id], ['is_deleted', '=', '0']])->get()->toArray();
            if(sizeof($Products) > 0) {
                
                if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                
                if(!empty($Products[0]['current_stock'])) $available_stock = $Products[0]['current_stock'];
                
                $approve_quantity = SaleOrderDetails::where([['product_id', '=', $val->product_id]])->sum('qty_appr');
                if($val->qty_appr > 1) {
                    if($approve_quantity > 0) {
                        $available_stock = $available_stock - $approve_quantity;
                        $noStockQty = $val->qty - $val->qty_appr;
                    }else {
                        $noStockQty = $val->qty - $available_stock;
                    }
                }
                
                if($available_stock < 0) {
                    $available_stock = 0;
                }
            }
            if($noStockQty > 0 && $available_stock < $noStockQty) {
                
                $transit_quantity = OrderDetail::where([['product_id', '=', $val->product_id]])->sum('qty');
                $CheckInDetails = CheckInDetails::where([['product_id', '=', $val->product_id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($CheckInDetails) > 0) {
                    $transit_quantity = 0;
                }
                
                $need_to_be_order2 = $noStockQty - $available_stock ;
                
                $customer_name = "";
                $getSaleOrder = SaleOrder::select('client_id')->where([['sale_order_id', '=', $val->sale_order_id]])->get()->toArray();
                if(sizeof($getSaleOrder) > 0)
                {
                    $getClients = Clients::select('customer_name')->where([['client_id', '=', $getSaleOrder[0]['client_id']]])->get()->toArray();
                    if(sizeof($getClients) > 0) {
                        $customer_name = $getClients[0]['customer_name'];
                    }
                }
                
                $noStockAmount = $noStockQty * $val->product_price;
                
                $sheet->setCellValue('A' . $rows, $customer_name.' - '.$val->sale_order_details_id);
                $sheet->setCellValue('B' . $rows, $pmpno);
                $sheet->setCellValue('C' . $rows, $available_stock);
                $sheet->setCellValue('D' . $rows, $noStockQty);
                $sheet->setCellValue('E' . $rows, $noStockAmount);
                $sheet->setCellValue('F' . $rows, $transit_quantity);
                $sheet->setCellValue('G' . $rows, $need_to_be_order2);
                $rows++;
            }
            
            // $product_name = "";
            // $pmpno = "";
            // $available_stock = 0;
            // $selectProduct = Products::select('part_name_id', 'current_stock', 'qty_on_order', 'pmpno')->where([['product_id', '=', $val->product_id]])->get()->toArray();
            
            // if(sizeof($selectProduct) > 0) {
                
            //     if($selectProduct[0]['current_stock'] > 0) {
            //         $available_stock = $selectProduct[0]['current_stock'];
            //     }
                
            //     $qty_on_order = $selectProduct[0]['qty_on_order'];
                
            //     if($qty_on_order > 0 && $selectProduct[0]['current_stock'] > 0) {
            //         $available_stock = $selectProduct[0]['current_stock'] - $selectProduct[0]['qty_on_order'];
            //     }
                
            //     $pmpno = $selectProduct[0]['pmpno'];
                
            //     $selectPartName = PartName::select('part_name')->where([['part_name_id', '=', $selectProduct[0]['part_name_id']]])->get()->toArray();
                
            //     if(sizeof($selectPartName) > 0) {
            //         $product_name = $selectPartName[0]['part_name'];
            //     }
            // }
            
            // $transit_quantity = OrderDetail::where([['product_id', '=', $val->product_id]])->sum('qty');
            // $CheckInDetails = CheckInDetails::where([['product_id', '=', $val->product_id], ['status', '=', '1']])->get()->toArray();
            // if(sizeof($CheckInDetails) > 0) {
            //     $transit_quantity = 0;
            // }
            
            // $need_to_be_order2 = 0;
            // $need_to_be_order = ($transit_quantity + $val->no_sales_qty) - $available_stock;
            // if($need_to_be_order > 0) {
            //     $need_to_be_order2 = $need_to_be_order;
            // }
            
            // $sheet->setCellValue('A' . $rows, $pmpno);
            // $sheet->setCellValue('B' . $rows, $available_stock);
            // $sheet->setCellValue('C' . $rows, $val->no_sales_qty);
            // $sheet->setCellValue('D' . $rows, $transit_quantity);
            // $sheet->setCellValue('E' . $rows, $need_to_be_order2);
            
            //$rows++;
            
        }
        
        $fileName = "LOSS-OF-SALE-Report.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function save_order_export() {
        $query = DB::table('sale_order')->join('clients', 'sale_order.client_id', '=', 'clients.client_id')->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at','sale_order.is_approved', 'sale_order.picking_approved', 'sale_order.slip_approved','sale_order.is_rejected','sale_order.print_picking_slip','sale_order.print_invoice', 'clients.customer_name', 'clients.sponsor_name')->where('sale_order.order_status', '=', '2')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order Id');
        $sheet->setCellValue('B1', 'Client Name');
        $sheet->setCellValue('C1', 'Sponsor Name');
        $sheet->setCellValue('D1', 'Grand Total');
        $sheet->setCellValue('E1', 'Created On');
        
        $rows = 2;
        foreach($query as $td){
            
            $sheet->setCellValue('A' . $rows, $td->sale_order_id);
            $sheet->setCellValue('B' . $rows, $td->customer_name);
            $sheet->setCellValue('C' . $rows, $td->sponsor_name);
            $sheet->setCellValue('D' . $rows, $td->grand_total);
            $sheet->setCellValue('E' . $rows, date('d M Y',strtotime($td->created_at)));
            
            $rows++;
        }
        $fileName = "Save_Order.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    public function order_quantity_update_form(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.sale_order.order_quantity_update_form')->with([
                'SaleOrderDetails' => SaleOrderDetails::where([['sale_order_details_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    public function approve_quantity_update_form(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.sale_order.approve_quantity_update_form')->with([
                'SaleOrderDetails' => SaleOrderDetails::where([['sale_order_details_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function update_order_quantity(Request $request){

        $saveData = SaleOrderDetails::where('sale_order_details_id', $request->hidden_id)->update(array('qty'=>$request->qty));
        if($saveData) {
            $returnData = ["status" => 1, "msg" => "Update successful.", 'qty' => $request->qty];
        }else {
            $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    
    public function update_approved_order_quantity(Request $request){

        $saveData = SaleOrderDetails::where('sale_order_details_id', $request->hidden_id)->update(array('qty_appr'=>$request->qty));
        if($saveData) {
            $returnData = ["status" => 1, "msg" => "Update successful.", 'qty' => $request->qty, 'sale_order_id' => $request->hidden_id];
        }else {
            $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
        }
        return response()->json($returnData);
    }

    public function order_price_update_form(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.sale_order.order_price_update_form')->with([
                'SaleOrderDetails' => SaleOrderDetails::where([['sale_order_details_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function update_order_price(Request $request){

        $saveData = SaleOrderDetails::where('sale_order_details_id', $request->hidden_id)->update(array('product_price'=>$request->product_price));
        if($saveData) {
            $returnData = ["status" => 1, "msg" => "Update successful.", 'product_price' => $request->product_price];
        }else {
            $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    
    public function print_merged_invoice() {
        return \View::make("backend/sale_order/print_merged_invoice")->with([
            'Clients' => Clients::select('client_id', 'customer_name')->where('delete_status', 0)->orderBy('customer_name', 'ASC')->get()->toArray(),
            ]);
    }
    
    public function print_merged_invoice_list(Request $request){
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->select('invoice_no', 'client_id');
            $query->whereNotNull('invoice_no');
            $query->groupBy('invoice_no', 'client_id');
            //$query->where('is_approved', '1');
            if(!empty($request->filter_customer)) {
                $query->where([['client_id', '=', $request->filter_customer]]);
            }
            if($keyword)
            {
                $sql = "invoice_no like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('invoice_no', 'asc');
                else
                    $query->orderBy('invoice_no', 'desc');
            }
            else
            {
                $query->orderBy('invoice_no', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                ->addColumn('client_name', function ($query) {
                    $customer_name = '';
                    if(!empty($query->client_id)) {
                        $selectClient = Clients::select('customer_name')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                        if(sizeof($selectClient) > 0) {
                            $customer_name = $selectClient[0]['customer_name'];
                        }
                    }
                    return $customer_name;
                })
                ->addColumn('action', function ($query) {
                    $actions = '<a href="javascript:void(0);" data-invoice_no="'.$query->invoice_no.'" data-client_id="'.$query->client_id.'" class="download-merged-invoice btn btn-warning action-btn" title="Download Invoice" ><i class="fa fa-download" aria-hidden="true"></i> Print Invoice</a>';
                    return $actions;
                })
                ->rawColumns(['client_name', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    function download_merged_invoice(Request $request) {
        
        $returnData = [];
        $myRes = explode('-', $request->invoice_no );
        $ids = $myRes[1];
        $vat_percentage = "";
        $SelectData = SaleOrder::select('sale_order_id')->where([['invoice_no', '=', $request->invoice_no], ['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($SelectData) > 0) {
            foreach($SelectData as $data)
            {
                $SaleOrderData = SaleOrder::select('vat_type_id')->where([['sale_order_id', '=', $data['sale_order_id']]])->get()->toArray();
                if(sizeof($SaleOrderData) > 0)
                {
                    if(!empty($SaleOrderData[0]['vat_type_id']))
                    {
                        $VatTypeData = VatType::select('percentage')->where([['vat_type_id', '=', $SaleOrderData[0]['vat_type_id']]])->get()->toArray();
                        if(sizeof($VatTypeData) > 0)
                        {
                            $vat_percentage = $VatTypeData[0]['percentage'];
                        }
                    }
                }
                
                SaleOrder::where([['sale_order_id', '=', $data['sale_order_id']]])->update(['print_invoice'=>'1']);
                
                $query = DB::table('sale_order_details as so');
                $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
                $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.alternate_part_no', 'p.unit', 'p.pmrprc', 'wu.unit_name');
                $query->where([['so.sale_order_id', '=', $data['sale_order_id']], ['so.is_deleted', '=', '0']]);
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
                            array_push($returnData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'alternate_part_no' => $data->alternate_part_no, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                        }
                    }
                }
            }
        }
        
        $ClientsData = [];
        $Clients = Clients::where([['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($Clients) > 0) {
            $ClientsData = $Clients;
        }
        return view('backend.sale_order.sale_order_invoice')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $ids,
            'vat_percentage' => $vat_percentage
        ]);
    }
    
    function print_merged_picking_slip(Request $request) {
        $returnData = [];
        $myRes = explode('-', $request->invoice_no );
        $ids = $myRes[1];
        
        $SelectData = SaleOrder::select('sale_order_id')->where([['invoice_no', '=', $request->invoice_no], ['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($SelectData) > 0) {
            foreach($SelectData as $data) {
                
                SaleOrder::where([['sale_order_id', '=', $data['sale_order_id']]])->update(['print_picking_slip'=>'1']);
                
                $query = DB::table('sale_order_details as so');
                $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
                $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->select('so.product_id', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.alternate_part_no', 'p.unit', 'p.pmrprc', 'wu.unit_name');
                $query->where([['so.sale_order_id', '=', $data['sale_order_id']]]);
                $SaleOrderDetails = $query->get()->toArray();
                
                if(sizeof($SaleOrderDetails) > 0) {
                    foreach($SaleOrderDetails as $data) {
                        
                        $location_name = "";
                        $zone_name = "";
                        $row_name = "";
                        $rack_name = "";
                        $plate_name = "";
                        $place_name = "";
                        
                        if($data->qty_appr > 0 ) {
                            
                            $BinningLocationDetails = BinningLocationDetails::where([['product_id', '=', $data->product_id]])->get()->toArray();
                            if(sizeof($BinningLocationDetails) > 0) {
                                if(!empty($BinningLocationDetails[0]['location_id'])) {
                                    
                                    $location = DB::table('location')->select('location_name')->where('location_id', $BinningLocationDetails[0]['location_id'])->get()->toArray();
                                    if(sizeof($location) > 0) {
                                        if(!empty($location[0]->location_name)) $location_name = $location[0]->location_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['zone_id'])) {
                                    
                                    $zone_master = DB::table('zone_master')->select('zone_name')->where('zone_id', $BinningLocationDetails[0]['zone_id'])->get()->toArray();
                                    if(sizeof($zone_master) > 0) {
                                        if(!empty($zone_master[0]->zone_name)) $zone_name = $zone_master[0]->zone_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['row_id'])) {
                                    
                                    $row = DB::table('row')->select('row_name')->where('row_id', $BinningLocationDetails[0]['row_id'])->get()->toArray();
                                    if(sizeof($row) > 0) {
                                        if(!empty($row[0]->row_name)) $row_name = $row[0]->row_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['rack_id'])) {
                                    
                                    $rack = DB::table('rack')->select('rack_name')->where('rack_id', $BinningLocationDetails[0]['rack_id'])->get()->toArray();
                                    if(sizeof($rack) > 0) {
                                        if(!empty($rack[0]->rack_name)) $rack_name = $rack[0]->rack_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['plate_id'])) {
                                    
                                    $plate = DB::table('plate')->select('plate_name')->where('plate_id', $BinningLocationDetails[0]['plate_id'])->get()->toArray();
                                    if(sizeof($plate) > 0) {
                                        if(!empty($plate[0]->plate_name)) $plate_name = $plate[0]->plate_name;
                                    }
                                }
                                if(!empty($BinningLocationDetails[0]['place_id'])) {
                                    
                                    $place = DB::table('place')->select('place_name')->where('place_id', $BinningLocationDetails[0]['place_id'])->get()->toArray();
                                    if(sizeof($place) > 0) {
                                        if(!empty($place[0]->place_name)) $place_name = $place[0]->place_name;
                                    }
                                }
                            }
                            
                            array_push($returnData, array('product_id' => $data->product_id, 'quantity' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'alternate_part_no' => $data->alternate_part_no, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'zone_name' => $zone_name, 'location_name' => $location_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                        }
                    }
                }
            }
        }
        $ClientsData = [];
        $Clients = Clients::where([['client_id', '=', $request->client_id]])->get()->toArray();
        if(sizeof($Clients) > 0) {
            $ClientsData = $Clients;
        }
        return view('backend.sale_order.print_picking_slip')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'id' => $ids,
        ]);
    }
    
    // Outstanding
    public function sale_order_outstanding() {

        return \View::make("backend/sale_order/sale_order_outstanding")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ]);
    }
    
    public function list_sale_order_outstanding(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order as so');
            $query->select('so.sale_order_id', 'so.invoice_no', 'so.client_id', 'so.invoice_date', 'so.grand_total', 'so.gst', 'so.vat_type_id', 'c.customer_name as client_name');
            $query->join('clients as c', 'c.client_id', '=', 'so.client_id', 'left');
            //$query->groupBy('invoice_no', 'client_id');
            //$query->where('so.print_invoice', '1');
            $query->where([['so.order_status', '!=', '2'], ['so.print_invoice', '=', '1']]);
            if(!empty($request->filter_customer)) {
                $query->where([['so.client_id', '=', $request->filter_customer]]);
            }
            if($keyword)
            {
                $query->whereRaw("(so.invoice_no like '%$keyword%' or so.sale_order_id like '%$keyword%' or c.customer_name like '%$keyword%')");
                // $sql = "so.invoice_no like ?";
                // $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('so.sale_order_id', 'asc');
                else
                    $query->orderBy('so.sale_order_id', 'desc');
            }
            else
            {
                $query->orderBy('so.sale_order_id', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                ->addColumn('invoice_amount', function ($query) {
                    
                    $TotalGrandTotal = 0;
                    
                    if($query->grand_total > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->grand_total;
                    }
                    
                    if($query->gst > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->gst;
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $query->sale_order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $query->sale_order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    return $TotalGrandTotal;
                })
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->invoice_date)) {
                        
                        $date_of_invoice = $query->invoice_date;
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $TotalGrandTotal = 0;
                    
                    if($query->grand_total > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->grand_total;
                    }
                    
                    if($query->gst > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->gst;
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $query->sale_order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $query->sale_order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    $due_amount = $TotalGrandTotal;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_no]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        
                        $payamount = round($SalesPay_amount,3);
                        $due_amount = $TotalGrandTotal - $payamount;
                        //$due_amount = $payamount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    $due_amount = $query->grand_total;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_no]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        
                        $payamount = round($SalesPay_amount,3);
                        $due_amount = $query->grand_total - $payamount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }else {
                            $status = '<span class="badge badge-success">Paid</span>';
                        }
                    }
                    
                    return $status;
                })
                ->rawColumns(['client_name', 'status'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_outstanding_payment(Request $request)
    {
        
        return \View::make("backend/sale_order/outstanding_payment_form")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ])->render();
    }
    
    public function get_customer_invoice_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            $SaleOrderData = SaleOrder::select('sale_order_id', 'invoice_no', 'invoice_date', 'grand_total', 'gst', 'vat_type_id')->where([['client_id', '=', $request->client_id], ['print_invoice', '=', '1']])->get()->toArray();
            
            if(sizeof($SaleOrderData) > 0) {
                
                foreach($SaleOrderData as $data)
                {
                    $TotalGrandTotal = 0;
                    
                    if($data['grand_total'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['grand_total'];
                    }
                    
                    if($data['gst'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['gst'];
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $data['sale_order_id']]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $data['sale_order_id']], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data['vat_type_id']))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data['vat_type_id']]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    $dueAmount = $TotalGrandTotal;
                    $paymentStatus = "due";
                    
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $data['invoice_no']]])->sum('pay_amount');
                    
                    if(!empty($SalesPay_amount) > 0) {
                        
                        $dueAmount = $TotalGrandTotal - $SalesPay_amount;
                        $dueAmount = round($dueAmount, 3);
                        
                        $totalPay = round($SalesPay_amount, 3);
                        if($TotalGrandTotal == $totalPay) {
                            
                            $paymentStatus = 'paid';
                        }
                    }
                    
                    if($paymentStatus == 'due') {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('sale_order_id' => $data['sale_order_id'], 'invoice_no' => $data['invoice_no'], 'invoice_date' => $data['invoice_date'], 'grand_total' => $TotalGrandTotal, 'due_amount' => $dueAmount));
                    }
                }
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function save_outstanding_payment(Request $request) {
        
        $arrayData = [];
        $request->client_id;
        if(!empty($request->invoice_no)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->invoice_no); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new SalesReceipt;
                    $data->client_id = $request->client_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->payment_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->client_id = $request->client_id;
                    $data->payment_status = 'outstanding';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
        // echo "<pre>";
        // print_r($arrayData);
        // exit();
    }
    
    public function sale_order_outstanding_export(Request $request)
    {   
        
        $query = DB::table('sale_order as so');
        $query->select('so.sale_order_id','so.client_id', 'so.grand_total','so.invoice_no','so.invoice_date', 'c.customer_name');
        $query->join('clients as c', 'so.client_id', '=', 'c.client_id', 'left');
        if(!empty($request->filter_customer)) {
            $query->where([['so.client_id', '=', $request->filter_customer]]);
        }
        $query->where([['so.order_status', '!=', '2'], ['so.print_invoice', '=', '1']]);
        $query->orderBy('so.sale_order_id', 'desc');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Amount');
        $sheet->setCellValue('D1', 'Invoice Number');
        $sheet->setCellValue('E1', 'Due Amount');
        $sheet->setCellValue('F1', 'Status');
        
        $rows = 2;
        foreach($data as $td){
            
            $due_amount = $td->grand_total;
            $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $td->invoice_no]])->sum('pay_amount');
            
            if(!empty($SalesPay_amount)) {
            
                $payamount = round($SalesPay_amount,3);
                $due_amount = $td->grand_total - $payamount;
            }
            
            $status = 'Due';
            $due_amount = $td->grand_total;
            $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $td->invoice_no]])->sum('pay_amount');
            
            if(!empty($SalesPay_amount)) {
            
                $payamount = round($SalesPay_amount,3);
                $due_amount = $td->grand_total - $payamount;
            
                if($due_amount > 0) {
                    $status = 'Partial';
                }else {
                    $status = 'Paid';
                }
            }
            
            $sheet->setCellValue('A' . $rows, $td->customer_name);
            $sheet->setCellValue('B' . $rows, $td->invoice_date);
            $sheet->setCellValue('C' . $rows, $td->grand_total);
            $sheet->setCellValue('D' . $rows, $td->invoice_no);
            $sheet->setCellValue('E' . $rows, $due_amount);
            $sheet->setCellValue('F' . $rows, $status);
            $rows++;
        }
        $fileName = "Sale-Order-Outstanding.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Partial Outstanding
    public function sale_order_partial_outstanding() {

        return \View::make("backend/sale_order/sale_order_partial_outstanding")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
            ]);
    }
    
    public function list_sale_order_partial_outstanding(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sales_receipt as sr');
            $query->select('sr.invoice_number', 'sr.client_id', 'sr.invoice_date', 'sr.invoice_amount', 'sr.payment_date', 'c.customer_name as client_name');
            $query->join('clients as c', 'c.client_id', '=', 'sr.client_id', 'left');
            //$query->where('sr.payment_status', 'partial');
            if(!empty($request->filter_customer)) {
                $query->where([['sr.client_id', '=', $request->filter_customer]]);
            }
            if($keyword)
            {
                $query->whereRaw("(sr.invoice_number like '%$keyword%' or sr.invoice_date like '%$keyword%' or c.customer_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sr.payment_date', 'asc');
                else
                    $query->orderBy('sr.payment_date', 'desc');
            }
            else
            {
                $query->orderBy('sr.payment_date', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                // ->addColumn('client_name', function ($query) {
                //     $customer_name = '';
                //     if(!empty($query->client_id)) {
                //         $selectClient = Clients::select('customer_name')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                //         if(sizeof($selectClient) > 0) {
                //             $customer_name = $selectClient[0]['customer_name'];
                //         }
                //     }
                //     return $customer_name;
                // })
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->invoice_date)) {
                        
                        $date_of_invoice = $query->invoice_date;
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $due_amount = $query->invoice_amount;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_number]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        
                        $payamount = round($SalesPay_amount,3);
                        $due_amount = $query->invoice_amount - $payamount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    $due_amount = $query->invoice_amount;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_number]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        
                        $payamount = round($SalesPay_amount,3);
                        $due_amount = $query->invoice_amount - $payamount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }else {
                            $status = '<span class="badge badge-success">Paid</span>';
                        }
                    }
                    
                    return $status;
                })
                ->rawColumns(['client_name', 'status'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_partial_outstanding_payment(Request $request)
    {
        
        return \View::make("backend/sale_order/partial_outstanding_payment_form")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ])->render();
    }
    
    public function get_customer_partial_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            $SaleOrderData = SaleOrder::select('sale_order_id', 'invoice_no', 'invoice_date', 'grand_total', 'gst', 'vat_type_id')->where([['client_id', '=', $request->client_id], ['print_invoice', '=', '1']])->get()->toArray();
            
            if(sizeof($SaleOrderData) > 0) {
                
                foreach($SaleOrderData as $data) {
                    
                    $dueAmount = "0";
                    $TotalGrandTotal = 0;
                    
                    if($data['grand_total'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['grand_total'];
                    }
                    
                    if($data['gst'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['gst'];
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $data['sale_order_id']]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $data['sale_order_id']], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data['vat_type_id']))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data['vat_type_id']]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    $dueAmount = $TotalGrandTotal;
                    
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $data['invoice_no']]])->sum('pay_amount');
                    
                    if(!empty($SalesPay_amount) > 0) {
                        
                        $dueAmount = $TotalGrandTotal - $SalesPay_amount;
                        $dueAmount = round($dueAmount, 3);
                    }
                    if($dueAmount > 0) {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('sale_order_id' => $data['sale_order_id'], 'invoice_no' => $data['invoice_no'], 'invoice_date' => $data['invoice_date'], 'grand_total' => $TotalGrandTotal, 'due_amount' => $dueAmount));
                    }
                }
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function save_partial_outstanding_payment(Request $request) {
        
        $arrayData = [];
        $request->client_id;
        if(!empty($request->invoice_no)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->invoice_no); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new SalesReceipt;
                    $data->client_id = $request->client_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->payment_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->client_id = $request->client_id;
                    $data->payment_status = 'partial';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
        // echo "<pre>";
        // print_r($arrayData);
        // exit();
    }
    
    public function sale_order_partial_outstanding_export(Request $request)
    {   
        
        $query = DB::table('sales_receipt as sr');
        $query->select('sr.invoice_number','sr.client_id', 'sr.invoice_date','sr.invoice_amount', 'c.customer_name');
        $query->join('clients as c', 'sr.client_id', '=', 'c.client_id', 'left');
        if(!empty($request->filter_customer)) {
            $query->where([['sr.client_id', '=', $request->filter_customer]]);
        }
        //$query->where('sr.payment_status', 'partial');
        $query->orderBy('sr.invoice_number', 'desc');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Amount');
        $sheet->setCellValue('D1', 'Invoice Number');
        $sheet->setCellValue('E1', 'Due Amount');
        $sheet->setCellValue('F1', 'Status');
        
        $rows = 2;
        foreach($data as $td){
            
            $due_amount = $td->invoice_amount;
            $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $td->invoice_number]])->sum('pay_amount');
            
            if(!empty($SalesPay_amount)) {
            
                $payamount = round($SalesPay_amount,3);
                $due_amount = $td->invoice_amount - $payamount;
            }
            
            $status = 'Due';
            $due_amount = $td->invoice_amount;
            $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $td->invoice_number]])->sum('pay_amount');
            
            if(!empty($SalesPay_amount)) {
            
                $payamount = round($SalesPay_amount,3);
                $due_amount = $td->invoice_amount - $payamount;
            
                if($due_amount > 0) {
                    $status = 'Partial';
                }else {
                    $status = 'Paid';
                }
            }
            
            $sheet->setCellValue('A' . $rows, $td->customer_name);
            $sheet->setCellValue('B' . $rows, $td->invoice_date);
            $sheet->setCellValue('C' . $rows, $td->invoice_amount);
            $sheet->setCellValue('D' . $rows, $td->invoice_number);
            $sheet->setCellValue('E' . $rows, $due_amount);
            $sheet->setCellValue('F' . $rows, $status);
            $rows++;
        }
        $fileName = "Sale-Order-Partial-Outstanding.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Partial Receipt
    public function sale_order_receipt() {

        return \View::make("backend/sale_order/sale_order_receipt")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ]);
    }
    
    public function list_sale_order_receipt(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sales_receipt as sr');
            $query->select('sr.sales_receipt_id', 'sr.invoice_number', 'sr.client_id', 'sr.invoice_date', 'sr.invoice_amount', 'sr.pay_amount', 'c.customer_name as client_name');
            $query->join('clients as c', 'c.client_id', '=', 'sr.client_id', 'left');
            if(!empty($request->filter_customer)) {
                $query->where([['sr.client_id', '=', $request->filter_customer]]);
            }
            if($keyword)
            {
                $query->whereRaw("(sr.invoice_number like '%$keyword%' or sr.invoice_date like '%$keyword%' or c.customer_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('sr.payment_date', 'asc');
                else
                    $query->orderBy('sr.payment_date', 'desc');
            }
            else
            {
                $query->orderBy('sr.payment_date', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->invoice_date)) {
                        
                        $date_of_invoice = $query->invoice_date;
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $due_amount = $query->invoice_amount;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_number]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        $due_amount = $query->invoice_amount - $SalesPay_amount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    $due_amount = $query->invoice_amount;
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_number]])->sum('pay_amount');
                    if(!empty($SalesPay_amount)) {
                        $due_amount = $query->invoice_amount - $SalesPay_amount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }
                    }
                    
                    return $status;
                })
                ->addColumn('action', function ($query) {
                    
                    $action = '<a href="javascript:void(0);" data-sales_receipt_id="'.$query->sales_receipt_id.'" class="print-receipt-slip btn btn-warning action-btn" title="Print Picking Slip" ><i class="fa fa-download" aria-hidden="true"></i> Print Slip</a>';
                    
                    return $action;
                })
                
                ->rawColumns(['client_name', 'status', 'action'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_sale_order_receipt_payment(Request $request)
    {
        
        return \View::make("backend/sale_order/sale_order_receipt_payment_form")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ])->render();
    }
    
    public function get_customer_receipt_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            $SaleOrderData = SaleOrder::select('sale_order_id', 'invoice_no', 'invoice_date', 'grand_total', 'gst', 'vat_type_id')->where([['client_id', '=', $request->client_id], ['print_invoice', '=', '1']])->get()->toArray();
            
            if(sizeof($SaleOrderData) > 0) {
                
                foreach($SaleOrderData as $data) {
                    
                    $TotalGrandTotal = 0;
                    
                    if($data['grand_total'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['grand_total'];
                    }
                    
                    if($data['gst'] > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $data['gst'];
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $data['sale_order_id']]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $data['sale_order_id']], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data['vat_type_id']))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data['vat_type_id']]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    $dueAmount = $TotalGrandTotal;
                    $paymentStatus = 'due';
                    
                    $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $data['invoice_no']]])->sum('pay_amount');
                    
                    if(!empty($SalesPay_amount) > 0) {
                        
                        $dueAmount = $TotalGrandTotal - $SalesPay_amount;
                        $dueAmount = round($dueAmount, 3);
                        
                        $totalPay = round($SalesPay_amount, 3);
                        if($TotalGrandTotal == $totalPay) {
                            
                            $paymentStatus = 'paid';
                        }
                    }
                    if($paymentStatus == 'due') {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('sale_order_id' => $data['sale_order_id'], 'invoice_no' => $data['invoice_no'], 'invoice_date' => $data['invoice_date'], 'grand_total' => $TotalGrandTotal, 'due_amount' => $dueAmount));
                    }
                }
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function save_sale_order_receipt_payment(Request $request) {
        
        $arrayData = [];
        $request->client_id;
        if(!empty($request->invoice_no)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->invoice_no); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new SalesReceipt;
                    $data->client_id = $request->client_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->payment_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->client_id = $request->client_id;
                    $data->payment_status = 'receipt';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
        // echo "<pre>";
        // print_r($arrayData);
        // exit();
    }
    
    public function sale_order_receipt_export(Request $request)
    {   
        
        $query = DB::table('sales_receipt as sr');
        $query->select('sr.invoice_number','sr.pay_amount', 'sr.invoice_date','sr.invoice_amount', 'c.customer_name');
        $query->join('clients as c', 'sr.client_id', '=', 'c.client_id', 'left');
        if(!empty($request->filter_customer)) {
            $query->where([['sr.client_id', '=', $request->filter_customer]]);
        }
        //$query->where('sr.payment_status', 'partial');
        $query->orderBy('sr.invoice_number', 'desc');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Number');
        $sheet->setCellValue('D1', 'Invoice Amount');
        $sheet->setCellValue('E1', 'Pay');
        
        $rows = 2;
        foreach($data as $td){
            
            $sheet->setCellValue('A' . $rows, $td->customer_name);
            $sheet->setCellValue('B' . $rows, $td->invoice_date);
            $sheet->setCellValue('C' . $rows, $td->invoice_number);
            $sheet->setCellValue('D' . $rows, $td->invoice_amount);
            $sheet->setCellValue('E' . $rows, $td->pay_amount);
            $rows++;
        }
        $fileName = "Sale-Order-Receipt.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    function print_sale_order_receipt_slip(Request $request) {
        
        $id = $request->id;
        
        $query = DB::table('sales_receipt as sr');
        $query->join('clients as c', 'c.client_id', '=', 'sr.client_id', 'left');
        $query->select('sr.invoice_date', 'sr.invoice_number', 'sr.invoice_amount', 'sr.due_amount', 'sr.pay_amount', 'sr.payment_date', 'sr.pay_mode', 'c.customer_name');
        $query->where([['sr.sales_receipt_id', '=', $id]]);
        $receiptDetails = $query->get()->toArray();
        
        $totalDueAmount = "";
        if(sizeof($receiptDetails) > 0) {
            $totalDueAmount = $receiptDetails[0]->invoice_amount;
        }
        $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $receiptDetails[0]->invoice_number]])->sum('pay_amount');
        if(!empty($SalesPay_amount)) {
            
            $payamount = round($SalesPay_amount,3);
            $totalDueAmount = $receiptDetails[0]->invoice_amount - $payamount;
        }
        // return $due_amount;
        return view('backend.sale_order.print_sale_order_receipt_slip')->with([
            'ReceiptData' => $receiptDetails,
            'totalDueAmount' => $totalDueAmount,
        ]);
    }
    
    // Sales Report
    public function sale_order_sales_report() {

        return \View::make("backend/sale_order/sale_order_sales_report")->with([
            'ClientData' => Clients::where('delete_status',0)->get()->toArray(),
        ]);
    }
    
    public function list_sale_order_sales_report(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order as s');
            $query->select('s.client_id', 'c.customer_name');
            $query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            if(!empty($request->filter_customer)) {
                $query->where([['c.client_id', '=', $request->filter_customer]]);
            }
            if($keyword)
            {
                $query->whereRaw("(c.customer_name like '%$keyword%' or s.client_id like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('c.customer_name', 'ASC');
                else
                    $query->orderBy('c.customer_name', 'ASC');
            }
            else
            {
                $query->orderBy('c.customer_name', 'ASC');
            }
            $query->groupBy('s.client_id');
            $datatable_array=Datatables::of($query)
                
                
                ->addColumn('outstanding_amount', function ($query) {
                    
                    $outstanding_amount = 0;
                    
                    $selectSalesOrder = SaleOrder::select('sale_order_id', 'grand_total', 'gst', 'vat_type_id')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                    if(sizeof($selectSalesOrder) > 0)
                    {
                        foreach($selectSalesOrder as $saleData)
                        {
                            $TotalGrandTotal = 0;
                    
                            if($saleData['grand_total'] > 0)
                            {
                                $TotalGrandTotal = $TotalGrandTotal + $saleData['grand_total'];
                            }
                            
                            if($saleData['gst'] > 0)
                            {
                                $TotalGrandTotal = $TotalGrandTotal + $saleData['gst'];
                            }
                            
                            $TotalGrandTotal = round($TotalGrandTotal, 3);
                            $returnPrice = 0;
                            $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $saleData['sale_order_id']]])->get()->toArray();
                            if(sizeof($selectReturns) > 0)
                            {
                                $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                                if(sizeof($selectReturnDetails) > 0)
                                {
                                    foreach($selectReturnDetails as $rddata)
                                    {
                                        $qty = $rddata['received_quantity'];
                                        $returnProductPrice = 0;
                                        
                                        $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $saleData['sale_order_id']], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                        if(sizeof($selectOrderDetails) > 0)
                                        {
                                            $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                        }
                                        $qty *
                                        $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                                    }
                                }
                            }
                            
                            $returnVatPrice = 0;
                            if(!empty($query->vat_type_id))
                            {
                                $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $saleData['vat_type_id']]])->get()->toArray();
                                if(sizeof($selectVatDetails) > 0)
                                {
                                    $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                                }
                            }
                            $returnPrice = $returnPrice + $returnVatPrice;
                            $returnPrice = round($returnPrice, 3);
                            
                            $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                            $outstanding_amount = $outstanding_amount + $TotalGrandTotal;
                        }
                    }
                    
                    
                    return $outstanding_amount;
                })
                ->addColumn('partial_amount', function ($query) {
                    
                    $outstanding_amount = 0;
                    
                    $selectSalesOrder = SaleOrder::select('sale_order_id', 'grand_total', 'gst', 'vat_type_id')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                    if(sizeof($selectSalesOrder) > 0)
                    {
                        foreach($selectSalesOrder as $saleData)
                        {
                            $TotalGrandTotal = 0;
                    
                            if($saleData['grand_total'] > 0)
                            {
                                $TotalGrandTotal = $TotalGrandTotal + $saleData['grand_total'];
                            }
                            
                            if($saleData['gst'] > 0)
                            {
                                $TotalGrandTotal = $TotalGrandTotal + $saleData['gst'];
                            }
                            
                            $TotalGrandTotal = round($TotalGrandTotal, 3);
                            $returnPrice = 0;
                            $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $saleData['sale_order_id']]])->get()->toArray();
                            if(sizeof($selectReturns) > 0)
                            {
                                $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                                if(sizeof($selectReturnDetails) > 0)
                                {
                                    foreach($selectReturnDetails as $rddata)
                                    {
                                        $qty = $rddata['received_quantity'];
                                        $returnProductPrice = 0;
                                        
                                        $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $saleData['sale_order_id']], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                        if(sizeof($selectOrderDetails) > 0)
                                        {
                                            $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                        }
                                        $qty *
                                        $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                                    }
                                }
                            }
                            
                            $returnVatPrice = 0;
                            if(!empty($query->vat_type_id))
                            {
                                $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $saleData['vat_type_id']]])->get()->toArray();
                                if(sizeof($selectVatDetails) > 0)
                                {
                                    $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                                }
                            }
                            $returnPrice = $returnPrice + $returnVatPrice;
                            $returnPrice = round($returnPrice, 3);
                            
                            $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                            $outstanding_amount = $outstanding_amount + $TotalGrandTotal;
                        }
                    }
                    
                    $receipt_amount = SalesReceipt::where([['client_id', '=', $query->client_id]])->sum('pay_amount');
                    if($receipt_amount > 0)
                    {
                        $receipt_amount = round($receipt_amount,3);
                        $partial_amount = $outstanding_amount - $receipt_amount;
                    }else
                    {
                        $partial_amount = 0;
                    }
                    return $partial_amount;
                })
                ->addColumn('receipt_amount', function ($query) {
                    
                    $receipt_amount = SalesReceipt::where([['client_id', '=', $query->client_id]])->sum('pay_amount');
                    if($receipt_amount > 0)
                    {
                        $receipt_amount = round($receipt_amount,3);
                    }else
                    {
                        $receipt_amount = 0;
                    }
                    return $receipt_amount;
                })
                
                ->rawColumns([])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    function print_order_details(Request $request) {
        
        $returnData = [];
        $ClientsData = [];
        $vat_percentage = "";
        $SelectData = SaleOrder::select('client_id', 'vat_type_id')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($SelectData) > 0)
        {
            if(!empty($SelectData[0]['client_id']))
            {
                $Clients = Clients::where([['client_id', '=', $SelectData[0]['client_id']]])->get()->toArray();
                if(sizeof($Clients) > 0) {
                    $ClientsData = $Clients;
                }
            }
            if(!empty($SelectData[0]['vat_type_id']))
            {
                $VatTypeData = VatType::select('percentage')->where([['vat_type_id', '=', $SelectData[0]['vat_type_id']]])->get()->toArray();
                if(sizeof($VatTypeData) > 0)
                {
                    $vat_percentage = $VatTypeData[0]['percentage'];
                }
            }
                
            $query = DB::table('sale_order_details as so');
            $query->join('products as p', 'p.product_id', '=', 'so.product_id', 'left');
            $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('so.product_id', 'so.product_price', 'so.qty', 'so.qty_appr', 'pn.part_name', 'p.pmpno', 'p.alternate_part_no', 'p.unit', 'p.pmrprc', 'wu.unit_name');
            $query->where([['so.sale_order_id', '=', $request->sale_order_id], ['so.is_deleted', '=', '0']]);
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
                        array_push($returnData, array('product_price' => $data->product_price, 'qty' => $data->qty, 'qty_appr' => $data->qty_appr, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'alternate_part_no' => $data->alternate_part_no, 'unit' => $data->unit, 'pmrprc' => $data->pmrprc, 'unit_name' => $data->unit_name, 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name));
                    }
                }
            }
        }
        return view('backend.sale_order.print_sale_order_details')->with([
            'SaleOrderDetails' => $returnData,
            'clients_data' => $ClientsData,
            'sale_order_id' => $request->sale_order_id,
            'vat_percentage' => $vat_percentage
        ]);
    }
    
    
    
    
    
    
    
    
    
    
    
}