<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
class SettingsController extends Controller {
    public function settings() {
        return \View::make("backend/settings")->with(array());
    }
    
}