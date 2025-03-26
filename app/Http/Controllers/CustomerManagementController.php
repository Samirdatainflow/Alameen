<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Clients;
use DB;
use DataTables;
use URL;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ClientDocuments;

class CustomerManagementController extends Controller {
    public function customer_management() {
        return \View::make("backend/customer/customer_management")->with(array());
    }
    // Customer Modal
    public function add_customer_management(){
        return \View::make("backend/customer/customer_management_form")->with(array());
    }
    // Insert/Update
    public function save_customer_management(Request $request){
        if(!empty($request->hidden_id))
        {
            $selectData=Clients::where([['customer_id', '=', $request->customer_id], ['client_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Customer Id already exist. Please try with another Customer Id."];
            }else {
                if(!empty($request->customer_file)) {
                    //File::delete(public_path('backend/images/customer_file/' . $request->hidden_customer_file));
                    foreach($request->customer_file as $key => $file)
                    {
                        $upimages = $file;
                        $customer_file = rand() . '.' . $upimages->getClientOriginalExtension();
                        $upimages->move(public_path('backend/images/customer_file/'), $customer_file);
                        $upDocument = new ClientDocuments;
                        $upDocument->client_id = $request->hidden_id;
                        $upDocument->document_name = $customer_file;
                        $upDocument->save();
                    }
                }
                $saveData=Clients::where('client_id', $request->hidden_id)->update(['customer_id' => $request->customer_id, 'customer_name' => $request->customer_name, 'reg_no' => $request->reg_no, 'sponsor_name' => $request->sponsor_name, 'sponsor_category' => $request->sponsor_category, 'sponsor_place_of_work' => $request->sponsor_place_of_work, 'sponsor_id' => $request->sponsor_id, 'credit_appl_ind' => $request->credit_appl_ind, 'other_customer_code' => $request->other_customer_code, 'authorizer_name' => $request->authorizer_name, 'customer_email_id' => $request->customer_email_id, 'customer_wa_no' => $request->customer_wa_no, 'customer_off_msg_no' => $request->customer_off_msg_no, 'security_cheques_submitted' => $request->security_cheques_submitted, 'customer_credit_limit' => $request->customer_credit_limit, 'customer_credit_period' => $request->customer_credit_period, 'customer_discount_percent' => $request->customer_discount_percent, 'customer_payment_method' => $request->customer_payment_method, 'customer_region' => $request->customer_region, 'customer_teritory' => $request->customer_teritory, 'customer_area' => $request->customer_area, 'store_address' => $request->store_address,'delivery_address' => $request->delivery_address,'ho_address' => $request->ho_address, 'cr_address' => $request->cr_address, 'customer_stopsale' => $request->customer_stopsale, 'vatin' => $request->vatin]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Customer Details  Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Customer Details Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Clients::where(['customer_id' => $request->customer_id])->get()->toArray();
            if(count($selectData) > 0)
            {
                $returnData = ["status" => 0, "msg" => "Enter Customer Id already exist. Please try with another Customer Id."];
            }
            else
            {
                $data = new Clients;
                $data->customer_id = $request->customer_id;
                $data->customer_name = $request->customer_name;
                $data->password = base64_encode('123');
                $data->reg_no = $request->reg_no;
                $data->sponsor_name = $request->sponsor_name;
                $data->sponsor_category = $request->sponsor_category;
                $data->sponsor_place_of_work = $request->sponsor_place_of_work;
                $data->sponsor_id = $request->sponsor_id;
                $data->credit_appl_ind = $request->credit_appl_ind;
                $data->other_customer_code = $request->other_customer_code;
                $data->authorizer_name = $request->authorizer_name;
                $data->customer_email_id = $request->customer_email_id;
                $data->customer_wa_no = $request->customer_wa_no;
                $data->customer_off_msg_no = $request->customer_off_msg_no;
                $data->security_cheques_submitted = $request->security_cheques_submitted;
                //$data->application_required_ind = $request->application_required_ind;
                $data->customer_credit_limit = $request->customer_credit_limit;
                $data->customer_credit_period = $request->customer_credit_period;
                $data->customer_discount_percent = $request->customer_discount_percent;
                $data->customer_payment_method = $request->customer_payment_method;
                $data->customer_region = $request->customer_region;
                $data->customer_teritory = $request->customer_teritory;
                $data->customer_area = $request->customer_area;
                $data->store_address = $request->store_address;
                $data->delivery_address = $request->delivery_address;
                $data->ho_address = $request->ho_address;
                $data->cr_address = $request->cr_address;
                
                $customer_stopsale = 'n';
                if(!empty($request->customer_stopsale)) {
                    $customer_stopsale = $request->customer_stopsale;
                }
                $data->customer_stopsale = $customer_stopsale;
                $data->vatin = $request->vatin;
                $data->delete_status = "0";
                $saveData = $data->save();
                if($saveData) {
                    $client_id = $data->id;
                    if(!empty($request->customer_file)) {
                    //File::delete(public_path('backend/images/customer_file/' . $request->hidden_customer_file));
                        foreach($request->customer_file as $key => $file)
                        {
                            $upimages = $file;
                            $customer_file = rand() . '.' . $upimages->getClientOriginalExtension();
                            $upimages->move(public_path('backend/images/customer_file/'), $customer_file);
                            $upDocument = new ClientDocuments;
                            $upDocument->client_id = $client_id;
                            $upDocument->document_name = $customer_file;
                            $upDocument->save();
                        }
                    }
                    $returnData = ["status" => 1, "msg" => "Customer Details Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Customer Details Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // customer DataTAble
    public function list_customer_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('clients');
            $query->select('*');
            if($keyword)
            {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('customer_name', 'asc');
                else
                    $query->orderBy('client_id', 'desc');
            }
            else
            {
                $query->orderBy('client_id', 'DESC');
            }
            $query->where([['delete_status', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('create_order', function ($query) {
                $create_order = "";
                if($query->customer_stopsale != 'y') {
                    //$url = 'https://www.discover-tech.in/order_management/';
                    $url = URL::to('/').'/order_management/';
                    $create_order = '<a href="'.$url.'create-order?client_id='.$query->client_id.'" class="" target="_blank"><button type="button" class="btn btn-success btn-sm" title="Create Order"><i class="fa fa-pencil"></i> Create Order</button></a>';
                }
                return $create_order;
            })
            ->addColumn('action', function ($query) {
                $delete = "";
                
                $selectClientDocuments = ClientDocuments::where([['client_id', '=', $query->client_id]])->get()->toArray();
                if(sizeof($selectClientDocuments) > 0)
                {
                    $delete .= '<a href="javascript:void(0)" class="view-customer-documents" data-id="'.$query->client_id.'"><button type="button" class="btn btn-success btn-sm" title="View Customer Document"><i class="fa fa-eye"></i></button></a>';
                }
                $delete .= ' <a href="javascript:void(0)" class="edit-customer" data-id="'.$query->client_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Customer"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-customer" data-id="'.$query->client_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Customer"><i class="fa fa-trash"></i></button></a>';
                return $delete;
            })
            ->rawColumns(['create_order', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    //Edit Customer Details
    public function edit_customer_management(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/customer/customer_management_form')->with([
                'client_data' => Clients::where([['client_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Customer Details
    public function delete_customer_management(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Clients::where('client_id', $request->id)->update(['delete_status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function customer_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/customer/customer_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $customer_id_exist = 0;
                    $Clients = Clients::where([['customer_id', '=', $row[0]], ['delete_status', '=', '0']])->get()->toArray();
                    if(count($Clients) > 0) {
                        $customer_id_exist = 1;
                    }
                    array_push($data, array('customer_id_exist' => $customer_id_exist, 'customer_code' => $row[0], 'customer_name' => $row[1], 'reg_no' => $row[2], 'sponsor_name' => $row[3], 'sponsor_category' => $row[4], 'sponsor_place_of_work' => $row[5], 'sponsor_id' => $row[6], 'credit_appl_ind' => $row[7], 'other_customer_code' => $row[8], 'authorizer_name' => $row[9], 'customer_email_id' => $row[10], 'customer_wa_no' => $row[11], 'customer_off_msg_no' => $row[12], 'security_cheques_submitted' => $row[13], 'application_required_ind' => $row[14], 'customer_credit_limit' => $row[15], 'customer_credit_period' => $row[16], 'customer_discount_percent' => $row[17], 'customer_payment_method' => $row[18], 'customer_region' => $row[19], 'customer_teritory' => $row[20], 'customer_area' => $row[21], 'customer_stopsale' => $row[22], 'store_address' => $row[23], 'delivery_address' => $row[24], 'ho_address' => $row[25], 'cr_address' => $row[26]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_customer_bulk_csv(Request $request){
        
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            
            if($data['customer_id_exist'] == "") {
                
                $pdata = new Clients;
                $pdata->customer_id = $data['customer_code'];
                $pdata->customer_name = $data['customer_name'];
                $pdata->password = base64_encode('123');
                $pdata->reg_no = $data['reg_no'];
                $pdata->sponsor_name = $data['sponsor_name'];
                $pdata->sponsor_category = $data['sponsor_category'];
                $pdata->sponsor_place_of_work = $data['sponsor_place_of_work'];
                $pdata->sponsor_id = $data['sponsor_id'];
                $pdata->credit_appl_ind = $data['credit_appl_ind'];
                $pdata->other_customer_code = $data['other_customer_code'];
                $pdata->authorizer_name = $data['authorizer_name'];
                $pdata->customer_email_id = $data['customer_email_id'];
                $pdata->customer_wa_no = $data['customer_wa_no'];
                $pdata->customer_off_msg_no = $data['customer_off_msg_no'];
                $pdata->security_cheques_submitted = $data['security_cheques_submitted'];
                $pdata->application_required_ind = $data['application_required_ind'];
                $pdata->customer_credit_limit = $data['customer_credit_limit'];
                $pdata->customer_credit_period = $data['customer_credit_period'];
                $pdata->customer_discount_percent = $data['customer_discount_percent'];
                $pdata->customer_payment_method = $data['customer_payment_method'];
                $pdata->customer_region = $data['customer_region'];
                $pdata->customer_teritory = $data['customer_teritory'];
                $pdata->customer_area = $data['customer_area'];
                $pdata->store_address = $data['store_address'];
                $pdata->delivery_address = $data['delivery_address'];
                $pdata->ho_address = $data['ho_address'];
                $pdata->cr_address = $data['cr_address'];
                $pdata->customer_stopsale = $data['customer_stopsale'];
                $pdata->delete_status = "0";
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
                    
                    $customer_id_exist = "";
                    $Clients = Clients::where([['customer_id', '=', $row[0]], ['delete_status', '=', '0']])->get()->toArray();
                    if(count($Clients) > 0) {
                        $customer_id_exist = 1;
                    }
                    
                    $reg_no = ($row[2] === '') ? null : $row[2];
                    $sponsor_name = ($row[3] === '') ? null : $row[3];
                    $customer_email_id = ($row[10] === '') ? null : $row[10];
                    $customer_wa_no = ($row[11] === '') ? null : $row[11];
                    $customer_off_msg_no = ($row[12] === '') ? null : $row[12];
                    $customer_credit_limit = ($row[15] === '') ? null : $row[15];
                    $customer_credit_period = ($row[16] === '') ? null : $row[16];
                    $customer_discount_percent = ($row[17] === '') ? null : $row[17];
                    $customer_payment_method = ($row[18] === '') ? null : $row[18];
                    
                    $credit_appl_ind = "n";
                    if(!empty($row[7])) {
                        
                        if($row[7] == "Yes" || $row[7] == "Y") {
                            
                            $credit_appl_ind = "y";
                        }
                    }
                    
                    $security_cheques_submitted = "n";
                    if(!empty($row[13])) {
                        
                        if($row[13] == "Yes" || $row[13] == "Y") {
                            
                            $security_cheques_submitted = "y";
                        }
                    }
                    
                    $application_required_ind = "n";
                    if(!empty($row[14])) {
                        
                        if($row[14] == "Yes" || $row[14] == "Y") {
                            
                            $application_required_ind = "y";
                        }
                    }
                    
                    $customer_stopsale = "n";
                    if(!empty($row[22])) {
                        
                        if($row[22] == "Yes" || $row[22] == "Y") {
                            
                            $customer_stopsale = "y";
                        }
                    }
                    
                    array_push($data, array('customer_id_exist' => $customer_id_exist, 'customer_code' => $row[0], 'customer_name' => $row[1], 'reg_no' => $reg_no, 'sponsor_name' => $sponsor_name, 'sponsor_category' => $row[4], 'sponsor_place_of_work' => $row[5], 'sponsor_id' => $row[6], 'credit_appl_ind' => $credit_appl_ind, 'other_customer_code' => $row[8], 'authorizer_name' => $row[9], 'customer_email_id' => $customer_email_id, 'customer_wa_no' => $customer_wa_no, 'customer_off_msg_no' => $customer_off_msg_no, 'security_cheques_submitted' => $security_cheques_submitted, 'application_required_ind' => $application_required_ind, 'customer_credit_limit' => $customer_credit_limit, 'customer_credit_period' => $customer_credit_period, 'customer_discount_percent' => $customer_discount_percent, 'customer_payment_method' => $customer_payment_method, 'customer_region' => $row[19], 'customer_teritory' => $row[20], 'customer_area' => $row[21], 'customer_stopsale' => $customer_stopsale, 'store_address' => $row[23], 'delivery_address' => $row[24], 'ho_address' => $row[25], 'cr_address' => $row[26]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    // Export
    public function customer_management_export(){
        $query = DB::table('clients')
        ->select('*')
        ->where([['delete_status', '=', '0']]);
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Registration Number');
        $sheet->setCellValue('C1', 'Customer Email');
        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->customer_name);
            $sheet->setCellValue('B' . $rows, $empDetails->reg_no);
            $sheet->setCellValue('C' . $rows, $empDetails->customer_email_id);
            $rows++;
        }
        $fileName = "customer_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    public function viewCustomerDocuments(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/customer/customer_documents')->with([
                'ClientDocuments' => ClientDocuments::where([['client_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
}