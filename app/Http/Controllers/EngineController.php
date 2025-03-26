<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\Engine;

class EngineController extends Controller {
    // View
    public function engine(){
        return \View::make("backend/item/engine")->with([]);
    }
    // List
    public function list_engine(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('engine');
            $query->select('*');
            if($keyword) {
                $sql = "engine_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('engine_name', 'asc');
                else
                    $query->orderBy('engine_id', 'desc');
            }else {
                $query->orderBy('engine_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-engine" data-id="'.$query->engine_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-engine" data-id="'.$query->engine_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    // Add
    public function add_engine(Request $request){
        return \View::make("backend/item/engine_form")->with([])->render();
    }
    // Save
    public function save_engine(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = Engine::where([['engine_name', '=', $request->engine_name], ['engine_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData = Engine::where('engine_id', $request->hidden_id)->update(array('engine_name'=>$request->engine_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = Engine::where([['engine_name', '=', $request->engine_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new Engine;
            	$data->engine_name = $request->engine_name;
                $data->status = "1";
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Edit
    public function edit_engine(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/item/engine_form')->with([
          	'Engine' =>  Engine::where([['engine_id', '=', $request->id]])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_engine(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Engine::where('engine_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
}