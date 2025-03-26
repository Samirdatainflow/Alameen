<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Suppliers;
use DB;
use DataTables;
use App\Countries;
use App\State;
use App\Cities;
use App\Group;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierManagementController extends Controller {
    public function supplier_management() {
        return \View::make("backend/supplier/supplier_management")->with(array());
    }
    // Modal Show
    public function add_supplier(){
        
        return \View::make("backend/supplier/supplier_form")->with([
            'countries'=> $countries = $this->countryFunction(),
            'group_data' => Group::where([['status', '=', '1']])->orderBy('group_id', 'desc')->get()->toArray()
        ])->render();
    }
    
    function countryFunction() {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    // Supplier Insert/Update
    public function save_supplier(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Suppliers::where([['supplier_code', '=', $request->supplier_code], ['full_name', '=', $request->full_name], ['business_title', '=', $request->business_title], ['mobile', '=', $request->mobile], ['phone', '=', $request->phone], ['address', '=', $request->address], ['city_id', '=', $request->city], ['state_id', '=', $request->state], ['zipcode', '=', $request->zipcode], ['country_id', '=', $request->country], ['email', '=', $request->email], ['supplier_id', '!=', $request->hidden_id], ['status', '=', '1'] ])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Supplier Code already exist. Please try with another Supplier Code."];
            }else {
                $group_ids = "";
                if(!empty($request->group_ids)) {
                    $group_ids = implode(',',$request->group_ids);
                }
                $saveData=Suppliers::where('supplier_id', $request->hidden_id)->update(array('supplier_code'=>$request->supplier_code,'full_name'=>$request->full_name,'business_title'=>$request->business_title, 'mobile'=>$request->mobile, 'phone'=>$request->phone, 'address'=>$request->address, 'city_id'=>$request->city, 'state_id'=>$request->state,'zipcode'=>$request->zipcode, 'country_id'=>$request->country, 'state_code'=>$request->state_code, 'country_code'=>$request->country_code, 'email'=>$request->email, 'group_ids' => $group_ids, 'cr_number' => $request->cr_number, 'vatin_number' => $request->vatin_number));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Supplier Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Supplier Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Suppliers::where([['supplier_code', '=', $request->supplier_code], ['email', '=', $request->email]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Supplier Code already exist. Please try with another Supplier Code."];
            }else {
                $group_ids = "";
                if(!empty($request->group_ids)) {
                    $group_ids = implode(',',$request->group_ids);
                }
                $saveData=Suppliers::insert(array('supplier_code'=>$request->supplier_code,'full_name'=>$request->full_name,'business_title'=>$request->business_title, 'mobile'=>$request->mobile, 'phone'=>$request->phone, 'address'=>$request->address, 'city_id'=>$request->city, 'state_id'=>$request->state,'zipcode'=>$request->zipcode, 'country_id'=>$request->country, 'state_code'=>$request->state_code, 'country_code'=>$request->country_code, 'email'=>$request->email, 'group_ids' => $group_ids, 'cr_number' => $request->cr_number, 'vatin_number' => $request->vatin_number, 'status'=> '1'));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Supplier Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => " Supplier Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Supplier DataTable
    public function list_supplier(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('suppliers as s');
            //$query->select('supplier_id','supplier_code', 'full_name', 'business_title', 'mobile', 'phone', 'address', 'city_id', 'state_id', 'zipcode', 'country_id', 'email', 'status');
            $query->select('s.*', 'c.country_name as country', 'st.state_name as state', 'ci.city_name as city');
            $query->join('countries as c', 'c.country_id', '=', 's.country_id', 'left');
            $query->join('state as st', 'st.state_id', '=', 's.state_id', 'left');
            $query->join('cities as ci', 'ci.city_id', '=', 's.city_id', 'left');
            if($keyword) {
                $query->whereRaw("(s.supplier_code like '%$keyword%' or s.full_name like '%$keyword%' or s.business_title like '%$keyword%' or ci.city_name like '%$keyword%' or st.state_name like '%$keyword%' or c.country_name)");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.full_name', 'asc');
                else
                    $query->orderBy('s.supplier_id', 'desc');
            }
            else
            {
                $query->orderBy('supplier_id', 'DESC');
            }
            $query->where([['s.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            // ->addColumn('country', function ($query) {
            //     $country = '';
            //     $country_array = Countries::where('country_id',$query->country_id)->get();
            //     if(sizeof($country_array)>0)
            //     {
            //         $country = $country_array[0]['country_name'];
            //     }
            //     return $country;
            // })
            // ->addColumn('state', function ($query) {
            //     $state = '';
            //     $state_array = State::where('state_id',$query->state_id)->get();
            //     if(sizeof($state_array)>0)
            //     {
            //         $state = $state_array[0]['state_name'];
            //     }
            //     return $state;
            // })
            // ->addColumn('city', function ($query) {
            //     $city = '';
            //     $city_array = Cities::where('city_id',$query->city_id)->get();
            //     if(sizeof($city_array)>0)
            //     {
            //         $city = $city_array[0]['city_name'];
            //     }
            //     return $city;
            // })
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                    // if($query->status == "1") {
                        $status .= '<a href="javascript:void(0)" class="supplier-change-status" data-id="'.$query->supplier_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="supplier-change-status" data-id="'.$query->supplier_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                // }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-supplier" data-id="'.$query->supplier_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Supplier"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-supplier" data-id="'.$query->supplier_id.'"><button type="button" class="btn btn-danger btn-sm" title="Edit Supplier"><i class="fa fa-trash"></i></button></a>';
                return $action;
            })
            ->rawColumns(['status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Supplier Delete
    public function delete_supplier(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Suppliers::where('supplier_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // Supplier Edit
    public function edit_supplier(Request $request) {
        if ($request->ajax()) {
            
            $stateData = [];
            $cityData = [];
            
            $supplier = Suppliers::where([['supplier_id', '=', $request->id]])->get()->toArray();
            
            if(sizeof($supplier) > 0) {
                
                if(!empty($supplier[0]['country_code'])) {
                    $stateData = $this->stateFunction($supplier[0]['country_code']);
                }
                
                if(!empty($supplier[0]['state_code'])) {
                    $cityData = $this->cityFunction($supplier[0]['country_code'], $supplier[0]['state_code']);
                }
            }
            
            $supplier = Suppliers::where([['supplier_id', '=', $request->id]])->get()->toArray();
            $html = view('backend.supplier.supplier_form')->with([
                'supplier_data' => $supplier,
                'countries' => $this->countryFunction(),
                'states'  => $stateData,
                'cities'  => $cityData,
                'group_data' => Group::where([['status', '=', '1']])->orderBy('group_id', 'desc')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    function stateFunction($country_id) {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries/'.$country_id.'/states',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    
    function cityFunction($country_id, $state_id) {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries/'.$country_id.'/states/'.$state_id.'/cities',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    
    // Supplier Change Status
    public function change_supplier_status(Request $request){
        $res=Suppliers::where('supplier_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful."];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    public function supplier_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/supplier/supplier_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    
                    $supplier_email_exist = 0;
                    $Suppliers = Suppliers::where([['email', '=', $row[3]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Suppliers) > 0) {
                        $supplier_email_exist = 1;
                    }
                    
                    $group_ids = "";
                    $groupIds = [];
                    if(!empty($row[11])) {
                        
                        $groupNamesArr = explode(',', $row[11]);
                        
                        if(sizeof($groupNamesArr) > 0) {
                            foreach($groupNamesArr as $k=>$v) {
                                
                                $name = ltrim($v);
                                $selectGroupId = Group::where('group_name','LIKE','%'.$name.'%')->get()->toArray();
                                if(sizeof($selectGroupId) > 0) {
                                    array_push($groupIds, $selectGroupId[0]['group_id']);
                                }
                            }
                        }
                    }
                    if(sizeof($groupIds) > 0) {
                        $group_ids = json_encode($groupIds);
                    }
                    
                    // $country_exist = '';
                    // $country_id = "";
                    // $countryArray = Countries::where('country_name',$row[7])->get();
                    // if(sizeof($countryArray)> 0 ) {
                        
                    //     $country_id = $countryArray[0]['country_id'];
                    //     $country_exist = '1';
                    // }
                    
                    // $state_exist = '';
                    // $state_id = '';
                    // $stateArray = State::where([['state_name', '=',$row[8]], ['country_id', '=', $country_id]])->get();
                    // if(sizeof($stateArray)> 0 ) {
                        
                    //     $state_id = $stateArray[0]['state_id'];
                    //     $state_exist = '1';
                    // }
                    
                    // $city_exist = '';
                    // $city_id = '';
                    // $citiesArray = Cities::where([['city_name', '=',$row[9]], ['state_id', '=', $state_id]])->get();
                    // if(sizeof($citiesArray)> 0 ) {
                        
                    //     $city_id = $citiesArray[0]['city_id'];
                    //     $city_exist = '1';
                    // }
                    
                    array_push($data, array('supplier_email_exist' => $supplier_email_exist, 'supplier_code' => $row[0], 'full_name' => $row[1], 'business_title' => $row[2], 'email' => $row[3], 'address' => $row[4], 'mobile' => $row[5], 'phone' => $row[6], 'country_name' => $row[7], 'state_name' => $row[8], 'city_name' => $row[9], 'zipcode' => $row[10], 'group_ids' => $group_ids));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_supplier_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['supplier_email_exist'] == "0") {
                $pdata = new Suppliers;
                $pdata->supplier_code = $data['supplier_code'];
                $pdata->full_name = $data['full_name'];
                $pdata->business_title = $data['business_title'];
                $pdata->mobile = $data['mobile'];
                $pdata->phone = $data['phone'];
                $pdata->address = $data['address'];
                $pdata->city_id = $data['city_id'];
                $pdata->state_id = $data['state_id'];
                $pdata->zipcode = $data['zipcode'];
                $pdata->country_id = $data['country_id'];
                $pdata->email = $data['email'];
                $pdata->group_ids = $data['group_ids'];
                $pdata->status = "1";
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
                    
                    $group_ids = "";
                    $groupIds = [];
                    if(!empty($row[11])) {
                        
                        $groupNamesArr = explode(',', $row[11]);
                        
                        if(sizeof($groupNamesArr) > 0) {
                            foreach($groupNamesArr as $k=>$v) {
                                
                                $name = ltrim($v);
                                $selectGroupId = Group::where('group_name','LIKE','%'.$name.'%')->get()->toArray();
                                if(sizeof($selectGroupId) > 0) {
                                    array_push($groupIds, $selectGroupId[0]['group_id']);
                                }
                            }
                        }
                    }
                    if(sizeof($groupIds) > 0) {
                        $group_ids = implode( ',', $groupIds );
                    }
                    
                    // $country_exist = '';
                    // $country_id = "";
                    // $countryArray = Countries::where('country_name',$row[7])->get();
                    // if(sizeof($countryArray)> 0 ) {
                        
                    //     $country_id = $countryArray[0]['country_id'];
                    //     $country_exist = '1';
                    // }
                    
                    // $state_exist = '';
                    // $state_id = '';
                    // $stateArray = State::where([['state_name', '=',$row[8]], ['country_id', '=', $country_id]])->get();
                    // if(sizeof($stateArray)> 0 ) {
                        
                    //     $state_id = $stateArray[0]['state_id'];
                    //     $state_exist = '1';
                    // }
                    
                    // $city_exist = '';
                    // $city_id = '';
                    // $citiesArray = Cities::where([['city_name', '=',$row[9]], ['state_id', '=', $state_id]])->get();
                    // if(sizeof($citiesArray)> 0 ) {
                        
                    //     $city_id = $citiesArray[0]['city_id'];
                    //     $city_exist = '1';
                    // }
                    
                    $supplier_email_exist = 0;
                    $Suppliers = Suppliers::where([['email', '=', $row[3]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Suppliers) > 0) {
                        $supplier_email_exist = 1;
                    }
                    
                    array_push($data, array('supplier_email_exist' => $supplier_email_exist, 'supplier_code' => $row[0], 'full_name' => $row[1], 'business_title' => $row[2], 'email' => $row[3], 'address' => $row[4], 'mobile' => $row[5], 'phone' => $row[6], 'country_name' => $row[7], 'state_name' => $row[8], 'city_name' => $row[9], 'zipcode' => $row[10], 'group_ids' => $group_ids, 'country_id' => $row[7], 'state_id' => $row[8], 'city_id' => $row[9]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function supplier_export(){
        $query = DB::table('suppliers')->select('*')->orderBy('supplier_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Supplier Code');
        $sheet->setCellValue('B1', 'Full Name');
        $sheet->setCellValue('C1', 'Business Title');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Address');
        $sheet->setCellValue('G1', 'City');
        $sheet->setCellValue('H1', 'State');
        $sheet->setCellValue('I1', 'Zip Code');
        $sheet->setCellValue('J1', 'Country');
        $sheet->setCellValue('K1', 'Email');
        $sheet->setCellValue('L1', 'Status');
        $rows = 2;
        foreach($query as $d2){
            $city_name = '';
            if(!empty($d2->city_id)) {
                $Cities = Cities::select('city_name')->where([['city_id', '=', $d2->city_id]])->get()->toArray();
                if(count($Cities) > 0) {
                    if(!empty($Cities[0]['city_name'])) $city_name = $Cities[0]['city_name'];
                }
            }
            $state_name = '';
            if(!empty($d2->state_id)) {
                $State = State::select('state_name')->where([['state_id', '=', $d2->state_id]])->get()->toArray();
                if(count($State) > 0) {
                    if(!empty($State[0]['state_name'])) $state_name = $State[0]['state_name'];
                }
            }
            $country_name = '';
            if(!empty($d2->country_id)) {
                $Countries = Countries::select('country_name')->where([['country_id', '=', $d2->country_id]])->get()->toArray();
                if(count($Countries) > 0) {
                    if(!empty($Countries[0]['country_name'])) $country_name = $Countries[0]['country_name'];
                }
            }
            $status = ($d2->status == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $d2->supplier_code);
            $sheet->setCellValue('B' . $rows, $d2->full_name);
            $sheet->setCellValue('C' . $rows, $d2->business_title);
            $sheet->setCellValue('D' . $rows, $d2->mobile);
            $sheet->setCellValue('E' . $rows, $d2->phone);
            $sheet->setCellValue('F' . $rows, $d2->address);
            $sheet->setCellValue('G' . $rows, $city_name);
            $sheet->setCellValue('H' . $rows, $state_name);
            $sheet->setCellValue('I' . $rows, $d2->zipcode);
            $sheet->setCellValue('J' . $rows, $country_name);
            $sheet->setCellValue('K' . $rows, $d2->email);
            $sheet->setCellValue('L' . $rows, $status);
            $rows++;
        }
        $fileName = "supplier.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}