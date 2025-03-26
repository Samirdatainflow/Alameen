<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
class ClientsController extends Controller {
    public function clients() {
        return \View::make("backend/clients")->with(array());
    }
    public function add_client()
    {
    	return \View::make("backend/add_client")->with(array());
    }
    public function recover_client()
    {
    	return \View::make("backend/recover_client")->with(array());
    }
    
}