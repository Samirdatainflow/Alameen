<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Products;
use App\WmsLots;
use DB;
use DataTables;
use App\PartName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LotsController extends Controller {

    public function lots() {
        return \View::make("backend/config/lots")->with(array());
    }
    // Lots Modal
    public function add_lots(){
        $query = DB::table('products as p');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
        $query->where([['is_deleted', '=', '0']]);
        $query->orderBy('p.product_id', 'desc');
        $Products = $query->get()->toArray();
    	return \View::make("backend/config/lots_form")->with([
            'product_id' => $Products
        ])->render();
    }
    // Insert/ Update
    public function save_lots(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=WmsLots::where([['lot_name', '=', $request->lot_name], ['product_id', '=', $request->product_id], ['qty', '=', $request->qty], ['lot_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Lots name already exist. Please try with another Lots name."];
            }else {
                $saveData=WmsLots::where('lot_id', $request->hidden_id)->update(array('lot_name'=>$request->lot_name,'product_id'=>$request->product_id, 'qty'=>$request->qty));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Lots Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => " Lots Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=WmsLots::where([['lot_name', '=', $request->lot_name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Lots name already exist. Please try with another Lots name."];
            }else {
            	$data = new WmsLots;
            	$data->lot_name = $request->lot_name;
            	$data->product_id = $request->product_id;
            	$data->qty = $request->qty;
            	$data->status = "1";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " Lots Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Lots Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // LotsDataTAble
    public function list_lots(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_lots');
            $query->select('*');
            if($keyword)
            {
                $sql = "lot_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('lot_name', 'asc');
                else
                    $query->orderBy('lot_id', 'desc');
            }
            else
            {
                $query->orderBy('lot_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('product_id', function ($query) {
                $product_id = '';
                if(!empty($query->product_id)) {
                    $Products = Products::select('part_name_id')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                    if(count($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(!empty($PartName)) {
                                if(!empty($PartName[0]['part_name'])) $product_id = $PartName[0]['part_name'];
                            }
                        }
                    }
                }
                return $product_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-lots" data-id="'.$query->lot_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Lots"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-lots" data-id="'.$query->lot_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Lots"><i class="fa fa-trash"></i></button></a>';
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
    // Edit Lots
    public function edit_lots(Request $request) {
        if ($request->ajax()) {
            $WmsLots = WmsLots::where([['lot_id', '=', $request->id]])->get()->toArray();
            $Products = [];
            if(sizeof($WmsLots) > 0) {
                if(!empty($WmsLots[0]['product_id'])) {
                    $query = DB::table('products as p');
                    $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                    $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
                    $query->where([['p.product_id', '=', $WmsLots[0]['product_id']], ['is_deleted', '=', '0']]);
                    $Products = $query->get()->toArray();
                }
            }
            $html = view('backend/config/lots_form')->with([
                'lots_data' => $WmsLots,
                'Products' => $Products  
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Lots
    public function delete_lots(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = WmsLots::where('lot_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function lots_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/lots_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $lot_name_exist = 0;
                    $WmsLots = WmsLots::where([['lot_name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($WmsLots) > 0) {
                        $lot_name_exist = 1;
                    }
                    $product_name = "";
                    $Products = DB::table('products as p')->select('p.product_id', 'p.pmpno', 'pn.part_name')->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left')->where('p.pmpno', 'like', '%'.$row[2].'%')->orWhere('pn.part_name', 'like', '%'.$row[2].'%')->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]->part_name)) {
                            $product_name .= $Products[0]->part_name;
                        }
                        if(!empty($Products[0]->pmpno)) {
                            $product_name .= " (".$Products[0]->pmpno.")";
                        }
                    }
                    array_push($data, array('lot_name_exist' => $lot_name_exist, 'lot_name' => $row[1], 'product_name' => $product_name, 'partno' => $row[2], 'quantity' => $row[3]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_lots_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['lot_name_exist'] == 0 && $data['product_name'] != "") {
                $pdata = new WmsLots;
                $pdata->lot_name = $data['lot_name'];
                $pdata->product_id = $data['product_id'];
                $pdata->qty = $data['quantity'];
                $pdata->status = 1;
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
                    $lot_name_exist = 0;
                    $WmsLots = WmsLots::where([['lot_name', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($WmsLots) > 0) {
                        $lot_name_exist = 1;
                    }
                    $product_name = "";
                    $product_id = "";
                    $Products = DB::table('products as p')->select('p.product_id', 'p.pmpno', 'pn.part_name')->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left')->where('p.pmpno', 'like', '%'.$row[2].'%')->orWhere('pn.part_name', 'like', '%'.$row[2].'%')->get()->toArray();
                    if(sizeof($Products) > 0) {
                        $product_id = $Products[0]->product_id;
                        if(!empty($Products[0]->part_name)) {
                            $product_name .= $Products[0]->part_name;
                        }
                        if(!empty($Products[0]->pmpno)) {
                            $product_name .= " (".$Products[0]->pmpno.")";
                        }
                    }
                    array_push($data, array('lot_name_exist' => $lot_name_exist, 'lot_name' => $row[1], 'product_name' => $product_name, 'product_id' => $product_id, 'partno' => $row[2], 'quantity' => $row[3]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function lots_export(){
        $query = WmsLots::OrderBy('lot_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Lot Name');
        $sheet->setCellValue('B1', 'Product Name');
        $sheet->setCellValue('C1', 'Quantity');
        $rows = 2;
        foreach($query as $td){
            $product_name = '';
            if(!empty($td['product_id'])) {
                $Products = Products::select('part_name_id')->where([['product_id', '=', $td['product_id']]])->get()->toArray();
                if(count($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $product_name = $PartName[0]['part_name'];
                        }
                    }
                }
            }
            $sheet->setCellValue('A' . $rows, $td['lot_name']);
            $sheet->setCellValue('B' . $rows, $product_name);
            $sheet->setCellValue('C' . $rows, $td['qty']);
            $rows++;
        }
        $fileName = "lot.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}