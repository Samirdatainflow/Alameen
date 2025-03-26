<?php
namespace App\Http\Controllers\API\v1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Clients;
use DB;

class ProfileController extends Controller {
    
	public function get_profile_data(Request $request){
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        $qry = Clients::where([['client_id', '=', $request->user_id]])->get()->toArray();
        return response()->json(["status" => 1, 'data'=>$qry]);
	}

    public function update_profile_data(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'customer_name' =>'required',
            'sponsor_name' =>'required'
        ]);
         Clients::where('client_id', $request->user_id)
           ->update([
               'customer_name' => $request->customer_name,
               'sponsor_name'  => $request->sponsor_name
            ]);
        return response()->json(["status" => 1, 'msg'=>'Profile is updated successful']);
    }

    public function change_password(Request $request){
    	$validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'newPassword' => 'required'
        ]);
        Clients::where('client_id', $request->user_id)
	       ->update([
	           'password' => base64_encode($request->newPassword)
	        ]);
	    return response()->json(["status" => 1, 'msg' => 'Password is updated successfully']);
    }
}