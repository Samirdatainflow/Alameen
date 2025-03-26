<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\ZoneMaster;
use DB;
use DataTables;
use App\Location;
use App\Row;

class StorageManagementController extends Controller {
    public function storage_management() {
        return \View::make("backend/storage_management/storage_management")->with(array());
    }
}