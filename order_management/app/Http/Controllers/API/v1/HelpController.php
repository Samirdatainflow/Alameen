<?php
namespace App\Http\Controllers\API\v1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Clients;
use DB;

class HelpController extends Controller {
    public function help_request(){
	    $data = array('name'=>"Virat Gandhi");
	   
	      Mail::send(['text'=>'mail'], $data, function($message) {
	         $message->to('abc@gmail.com', 'Tutorials Point')->subject
	            ('Laravel Basic Testing Mail');
	         $message->from('xyz@gmail.com','Virat Gandhi');
	      });
	      echo "Basic Email Sent. Check your inbox.";
    }
   
	
}