<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Clients;
class ProfileController extends Controller {
    public function profile() {
    	// Session::get('user_id');
    	$user_data=Clients::where(['client_id'=>Session::get('user_id')])->get();
        return \View::make("backend/profile/profile")->with(array(
        	'users_data' =>$user_data ));

    }
    public function change_pass(){
    	return \View::make("backend/change_pass")->with(array());
    }

    public function update_profile(Request $request){
    	$customer_name=$request->customer_name;
    	$sponsor_name=$request->sponsor_name;
    	Clients::where(['client_id'=>Session::get('user_id')])->update(array('customer_name'=>$customer_name,'sponsor_name'=>$sponsor_name));
    	$ret=array('status'=>1,'msg'=>'Profile is updated successfully');
    	return response()->json($ret);
    }

    public function check_current_password(Request $request){
    	$current_password=base64_encode($request->current_password);
    	$res=Clients::where(['client_id'=>Session::get('user_id'),'password'=>$current_password])->get();
    	if(sizeof($res)==0)
    	{
    		$ret=array('status'=>0,'msg'=>'Password is worng');
    	
    	}
    	else
    	{
    		$ret=array('status'=>1,'msg'=>'Password is matched');
    	}
    	return response()->json($ret);
    }

    public function update_password(Request $request){
    	$new_password=base64_encode($request->new_password);
    	$res=Clients::where(['client_id'=>Session::get('user_id')])->update(['password'=>$new_password]);
    	$ret=array('status'=>1,'msg'=>'Password is updated successfully');
    	return response()->json($ret);
    }

}