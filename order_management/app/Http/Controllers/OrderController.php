<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\SaleOrder;
use App\Clients;
use App\SaleOrderDetails;
use App\Products;
use Cookie;
use App\OrderReceived;
use App\SmsApiKey;
use App\MailApiKey;
use App\SaleOrderRejectReason;
use App\WmsSaleOrderAproved;
use App\PartName;
use App\SaleOrderTemplate;
use App\VatType;

class OrderController extends Controller {
    public function order(Request $request) {
        
        return \View::make("backend/order/order")->with(array());
    }
    public function new_order(Request $request) {
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
        $clients = Clients::where('delete_status',0)->get();
    	return \View::make("backend/order/new_order")->with([
    	    'clients'=>$clients,'cart_datas'=>$previous_data,
    	    'VatTypeData' => VatType::orderBy('description', 'ASC')->get()->toArray()
    	    ]);
    }
    public function get_order(Request $request){
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.grand_total','sale_order.discount','sale_order.created_at', 'sale_order.is_rejected', 'sale_order.is_approved', 'clients.customer_name', 'clients.sponsor_name');
            $query->where('sale_order.client_id', '=', Session::get('user_id'));
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
            $datatable_array=Datatables::of($query)
                ->addColumn('order_id', function ($query) {
                    $sale_order_id = '';
                    if(!empty($query->sale_order_id)) {
                        $sale_order_id .= $query->sale_order_id;
                    }
                    return $sale_order_id;
                })
                // ->addColumn('client_name', function ($query) {
                //     $customer_name = '';
                //     if(!empty($query->customer_name)) {
                //         $customer_name .= $query->customer_name;
                //     }
                //     return $customer_name;
                // })
                ->addColumn('reject_reason', function ($query) {
                    $reject_reason = '';
                    if(!empty($query->is_rejected == "1")) {
                        $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $query->sale_order_id)->get()->toArray();
                        if(sizeof($SaleOrderRejectReason) > 0) {
                            $reject_reason = $SaleOrderRejectReason[0]['reason'];
                        }
                    }
                    return $reject_reason;
                })
                ->addColumn('discount', function ($query) {
                    $discount = 0;
                    if(!empty($query->discount)) {
                        $discount = $query->discount;
                    }
                    return $discount;
                })
                ->addColumn('grand_total', function ($query) {
                    $grand_total = 0;
                    if(!empty($query->grand_total)) {
                        $grand_total = $query->grand_total;
                    }
                    return $grand_total;
                })
                ->addColumn('created_at', function ($query) {
                    $created_at = '';
                    if(!empty($query->created_at)) {
                        $created_at .= date('d M Y',strtotime($query->created_at));
                    }
                    return $created_at;
                })
                ->addColumn('option', function ($query) {
                    $actions = '';
                        $actions .= '<a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="btn btn-success action-btn view-order-details" title="View order details"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    if($query->is_rejected == "1")
                    {
                        $actions .= '<a href="javascript:void(0);" name="button" class="view-subbrand btn btn-danger rejected" title="Rejected"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                    }else if($query->is_approved == "1") {
                        $actions .= '<a href="javascript:void(0);" name="button" class="view-subbrand btn btn-success rejected" title="Approved"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a> ';
                    }
                    return $actions;
                })
                ->addColumn('remarks', function ($query) {
                    $remarks = "";
                    if($query->is_rejected == "1") {
                        $remarks = '<a data-sale-order-id="' . $query->sale_order_id . '" href="javascript:void(0);" name="button" class="btn btn-info action-btn view-reason" title="View reason"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }
                    return $remarks;
                })
                ->rawColumns(['order_id', 'discount', 'grand_total', 'created_at', 'option', 'remarks'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
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
        $available_stock = available_stock($model,$data[0]->product_id);
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
        $data_array[]=array('product_id'=>$data[0]->product_id,'pmpno'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>$data[0]->pmrprc,'current_stock'=>$available_stock,'min_price'=>$min_price,'max_price'=>$max_price, 'order_status' => $order_status);
        return response()->json($data_array);
    }
    
    public function create_order(Request $request){
        //echo "here"; exit();
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
        //$body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
        //$this->submitMsg($customer_off_msg_no, $customer_name, $api_key);
        // $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
        // $submitMsg = $this->sendEmail($customer_email_id, "Order Create", $body, $smtp_user, $smtp_pass, $smtp_port, $from_mail);
        //print_r($submitMsg); exit();
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
                    //$gst_array = $request->gst;
                    $product_tax = 0;
                    array_push($NoStockOrderArray, array('order_line_no' => $max_order_line_no, 'product_id' => $request['product_id'][$i], 'product_tax' => $product_tax, 'product_price' => $mrp_array[$i], 'qty' => $qty_array[$i]));
                    //$NoStockOrderArray['order_line_no'] = $max_order_line_no;
                    // $NoStockOrderArray['product_id'] = $request['product_id'][$i];
                    // $NoStockOrderArray['product_tax'] = $product_tax;
                    // $NoStockOrderArray['product_price'] = $mrp_array[$i];
                    // $NoStockOrderArray['qty'] = $qty_array[$i];
                    $n_sub_total += $qty_array[$i] * $mrp_array[$i];
                    $n_sub_total = round($n_sub_total,2);
                    $n_tax += ($n_sub_total * $request->hidden_tax_rate)/100;
                    $n_tax = round($n_tax,2);
                    $n_grand_total += $n_sub_total + $n_tax;
                    $n_grand_total = round($n_grand_total,2);
                }else {
                    $mrp_array = $request->mrp;
                    $qty_array = $request->qty;
                    //$gst_array = $request->gst;
                    $product_tax = 0;
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
        if(sizeof($NoStockOrderArray) > 0) {
            $order_data = array('client_id'=>$request->client,'sub_total'=> $request->sub_total,'gst'=> $request->tax,'grand_total'=> $request->expertSubTotalWithTax, 'vat_type_id' => $request->vat_type_value,'remarks'=>$request->remarks,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
            //print_r($order_data);
            $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
            foreach ($NoStockOrderArray as $ndata) {
                SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$ndata['order_line_no'],'product_id'=>$ndata['product_id'],'product_tax'=>$ndata['product_tax'],'product_price'=>$ndata['product_price'],'qty'=>$ndata['qty']));
            }
        }
        if(sizeof($StockOrderArray) > 0) {
            $order_data = array('client_id'=>$request->client,'sub_total'=> $request->sub_total,'gst'=> $request->tax,'grand_total'=> $request->expertSubTotalWithTax,'remarks'=>$request->remarks,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
            //print_r($order_data);
            $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
            foreach ($StockOrderArray as $sdata) {
                SaleOrderDetails::insert(array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>$sdata['order_line_no'],'product_id'=>$sdata['product_id'],'product_tax'=>$sdata['product_tax'],'product_price'=>$sdata['product_price'],'qty'=>$sdata['qty']));
            }
        }
        Cookie::queue(Cookie::forget('cart_data'));
        $body = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
        $returnData = ["status" => 1, "msg" => "Order is created successfully"];
        return response()->json($returnData);
        exit();
        //
        $order_data = array('client_id'=>$request->client,'sub_total'=>$request->sub_total,'gst'=>$request->tax,'grand_total'=>$request->expertSubTotalWithTax,'remarks'=>$request->remarks,'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
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
            $returnData = ["status" => 1, "msg" => "Order is created successfully"];
        }
        return response()->json($returnData);
    }
    function submitMsg($customer_off_msg_no, $customer_name, $api_key) {
        $apiKey = urlencode($api_key);
        // Message details
        //$numbers = array(918123456789, 918987654321);
        //$numbers = implode(',', $numbers);
        $sender = urlencode('DISTEC');
        $message = "Hi ".$customer_name.". Your order is successfully done. Thanks, OMS.";
     
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $customer_off_msg_no, "sender" => $sender, "message" => $message);
     
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Process your response here
        echo $response;
    }
    function SendMail($sub, $msg,$email, $company_name){
      $data = array('body_message'=>$msg);
      Mail::send('backend/tempalte/order_template', $data, function($message) use($email,$company_name){
         $message->to($email, $company_name)->subject
            ('Your order is successfully done. Thanks, OMS');
      });
    }
    function sendEmail($emails, $sub, $msg, $smtp_user, $smtp_pass, $smtp_port) {
        $this->email->initialize(array(
          'protocol' => 'smtp',
          'smtp_host' => 'smtp.sendgrid.net',
          'smtp_user' => $smtp_user,
          'smtp_pass' => $smtp_pass,
          'smtp_port' => $smtp_port,
          'crlf' => "\r\n",
          'newline' => "\r\n"
        ));
        $this->email->from($from_mail);
        $this->email->to($emails);
        $this->email->subject($sub);
        $this->email->message($msg);
        //$this->email->attach($file_path);
        if($this->email->send()) {
            echo "Mail send successfully to ".$reciverEmail;
        }else{
            $sent=false;
        }
        // foreach ($emails as $reciverEmail) {
        //     $this->email->from($from_mail);
        //     $this->email->to($reciverEmail);
        //     $this->email->subject($sub);
        //     $this->email->message($msg);
        //     $this->email->attach($file_path);
        //     if($this->email->send()) {
        //         echo "Mail send successfully to ".$reciverEmail;
        //     }else{
        //         $sent=false;
        //     }
        // }
        // echo $this->email->print_debugger();
    }
    public function get_sale_order_details(Request $request){
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $Products = Products::select('part_name_id', 'pmpno')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                }
                $qty_appr = '';
                $SaleOrder = SaleOrder::select('is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                if(sizeof($SaleOrder)>0) {
                    $WmsSaleOrderAproved = WmsSaleOrderAproved::select('qty_appr')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                    if(sizeof($WmsSaleOrderAproved) > 0) {
                        $qty_appr = $WmsSaleOrderAproved[0]['qty_appr'];
                    }
                }
                array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_tax' => $data['product_tax'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'qty_appr'=> $data['qty_appr']));
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
        $sales_order_template_name = "";
        $SaleOrderTemplate = SaleOrderTemplate::select('template_name')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
        if(sizeof($SaleOrderTemplate) > 0) {
            $sales_order_template_name = $SaleOrderTemplate[0]['template_name'];
        }
        return \View::make("backend/order/sale_order_details")->with(array('products' => $returnData,'is_rejected' => $is_rejected, 'reject_reason' => $reject_reason,'is_approved' => $is_approved, 'sales_order_template_name' => $sales_order_template_name));
    }
    public function view_reason(Request $request){
        $returnData = [];
        $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
        if(sizeof($SaleOrderRejectReason) > 0) {
            $returnData = ["status" => 1, "reject_reason" => $SaleOrderRejectReason[0]['reason']];
        }else {
            $returnData = ["status" => 0];
        }
        return response()->json($returnData);
    }
    public function create_multiple_order(Request $request){
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArray($file);
        if(sizeof($productArr['data'])>0) {
            $upimages = $request->file;
            $csv_file = rand() . '.' . $upimages->getClientOriginalExtension();
            $upimages->move(public_path('backend/file/upload_order_csv/'), $csv_file);
            //
            $order_data = array('client_id'=>$request->client,'sub_total'=>$productArr['sub_total'],'gst'=>$productArr['tax'],'grand_total'=>$productArr['grand_total'],'remarks'=>"",'created_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d'));
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
                            $sub_total+=($row[1]*$product_details['pmrprc']);
                            $total_gst=0;
                            $grand_total+=($row[1]*$product_details['pmrprc']);
                            $data[] = $product_details;
                        //}
                    }
                    
                    
                }
            }
            fclose($handle);
        }

        return array('data'=>$data,'sub_total'=>$sub_total,'tax'=>$total_gst,'grand_total'=>$grand_total);
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
	                        $sub_total+=($row[1]*$product_details['pmrprc']);
	                        $total_gst=0;
	                        $grand_total+=($row[1]*$product_details['pmrprc']);
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
    public function product_details_by_id($product_id){
        $data_array=[];
        $query = DB::table('products');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct', 'left');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name');
        $query->where('products.product_id', '=', $product_id);
        $data=$query->get()->toArray();
        if(sizeof($data)>0) {
            $model = new DB;
            $available_stock = available_stock($model,$product_id);
            $data_array=array('product_id'=>$data[0]->product_id,'part_no'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>$data[0]->pmrprc,'current_stock'=>$available_stock);
        }
        return $data_array;
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
            $available_stock = available_stock_by_part_no($model,$part_no);
            $data_array=array('product_id'=>$data[0]->product_id,'part_no'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>$data[0]->pmrprc,'current_stock'=>$available_stock);
        }
        return $data_array;
    }
    public function order_preview() {
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArrayWithAll($file);
        return \View::make("backend/order/order_preview")->with(array('products'=>$productArr['data']));
    }
    public function remove_order_item(Request $request) {
        $sale_order_details_id=$request->sale_order_details_id;
        $sale_order_id=$request->sale_order_id;
        $total=$request->total;
        $qry=SaleOrderDetails::where('sale_order_details_id',$sale_order_details_id)->update(array('is_deleted'=>1));
        if(!$qry)
        {
            $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
        }
        else
        {
            SaleOrder::where('sale_order_id',$sale_order_id)->update(array(
                'sub_total' => DB::raw('sub_total - '.$total),
                'grand_total' => DB::raw('grand_total - '.$total),
            ));
            $returnData = ["status" => 1, "msg" => "Item is deleted successfully"];
        }
        return response()->json($returnData);
    }
    // Reject
    public function reject_sale_order(Request $request) {
        // echo "HIII"; exit();
        $returnData = [];
        if ($request->ajax()) {
            $saveData = SaleOrder::where('sale_order_id', $request->id)->update(['is_rejected' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Reject successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Reject failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // Get Product By Part No
    public function get_product_by_part_no(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            if(!empty($request->part_no)) {
                $ProductsData = [];
                $view = "";
                $query = DB::table('products as p');
                $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                //$query->whereRaw('p.is_deleted != 1 and (replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%")');
                $query->whereRaw('p.is_deleted != 1 and (replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or p.pmpno like "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%")');
                //$query->where('p.pmpno', 'like', '%' . $request->part_no . '%');
                $query->limit('100');
                $Products = $query->get()->toArray();
                if(sizeof($Products) > 0) {
                    $product_details = '\'product-details\'';
                    $view = $view.'<ul class="list-group">';
                    foreach($Products as $data) {
                        $view = $view.'<li class="list-group-item"><a href="#" class="product-details" style="text-decoration: none" data-pmpno="'.$data->pmpno.'" data-product-id="'.$data->product_id.'">'.$data->part_name.' ('.$data->pmpno.')</a></li>';
                    }
                    $view = $view.'</ul>';
                    $returnData = array('status' => 1, 'data' => $view);
                }else {
                    $returnData = array('status' => 0, 'msg' => "No record found.");
                }
            }
            return response()->json($returnData);
        }
    }
}