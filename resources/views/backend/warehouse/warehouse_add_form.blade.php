{{ Form::open(array('id'=>'saveWarehouseForm')) }}
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Name *</label>
                @php
                $name = "";
                if(!empty($warehouse_data[0]['name']))  {
                    $name = $warehouse_data[0]['name'];
                }
                $hidden_id = "";
                if(!empty($warehouse_data[0]['warehouse_id']))  {
                    $hidden_id = $warehouse_data[0]['warehouse_id'];
                }
                @endphp
                <input name="name" id="name" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]/g, '')" placeholder="Enter warehouse name" type="text" class="form-control" value="{{$name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Address *</label>
                @php
                $address = "";
                if(!empty($warehouse_data[0]['address']))  {
                    $address = $warehouse_data[0]['address'];
                }
                @endphp
                <input name="address" id="address" placeholder="Address (Street/Block)" type="text" class="form-control" value="{{$address}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Country *</label>
                <select name="country" id="country" class="form-control selectpicker" data-live-search="true" title="Select Country">
                    <!--<option value="">Select</option>-->
                    @foreach($countries as $country)
                    @php
                    $selected = "";
                    if(!empty($warehouse_data[0]['country_id']))  {
                        if($warehouse_data[0]['country_id'] == $country->name)
                        {
                            $selected = "selected=selected";
                        }
                    }
                    
                    $country_code = "";
                    if(!empty($warehouse_data[0]['country_code']))  {
                        $country_code = $warehouse_data[0]['country_code'];
                    }
                    @endphp
                    <option value="{{$country->name}}" data-country_code="{{$country->iso2}}" {{$selected}}>{{$country->name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="country_code" id="country_code" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>State *</label>
                <select name="state" id="state" class="form-control selectpicker" data-live-search="true" title="Select State">
                    @php
                    $state_code = "";
                    if(!empty($warehouse_data[0]['state_code']))  {
                        $state_code = $warehouse_data[0]['state_code'];
                    }
                    
                    if(!empty($states))  {
                    
                        foreach($states as $state) {
                        
                            if(!empty($warehouse_data[0]['state_id']) && !empty($state->name))  {
                            
                                $selected="";
                                if($warehouse_data[0]['state_id'] == $state->name)
                                {
                                    $selected="selected=selected";
                                }
                            }
                    @endphp
                    <option value="{{$state->name}}" {{$selected}}>{{$state->name}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
                <input type="hidden" name="state_code" id="state_code" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>City *</label>
                <select name="city" id="city" class="form-control selectpicker" data-live-search="true" title="Select City">
                    @php
                    if(!empty($warehouse_data[0]['city_id']))  {
                    @endphp
                    @foreach($cities as $city)
                    @php
                    $selected="";
                    if($warehouse_data[0]['city_id'] == $city->name)
                    {
                        $selected="selected=selected";
                    }
                    @endphp
                    <option value="{{$city->name}}" {{$selected}}>{{$city->name}}</option>
                    @endforeach
                    @php
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Manager Name *</label>
                @php
                $manager_name = "";
                if(!empty($warehouse_data[0]['manager']))  {
                    $manager_name = $warehouse_data[0]['manager'];
                }
                @endphp
                <input name="manager_name" id="manager_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" type="text" class="form-control" placeholder="" value="{{$manager_name}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Manager Contact Number</label>
                @php
                $manager_c_number = "";
                if(!empty($warehouse_data[0]['contact']))  {
                    $manager_c_number = $warehouse_data[0]['contact'];
                }
                @endphp
                <input name="manager_c_number" id="manager_c_number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" class="form-control" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" placeholder="Enter Number Only" value="{{$manager_c_number}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Surface Area of the warehouse (m&sup2;) *</label>
                @php
                $warehouse_area = "";
                if(!empty($warehouse_data[0]['surface']))  {
                    $warehouse_area = $warehouse_data[0]['surface'];
                }
                @endphp
                <input name="warehouse_area" id="warehouse_area" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter Number Only" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$warehouse_area}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Volume of the warehouse (m&sup3;) *</label>
                @php
                $warehouse_volume = "";
                if(!empty($warehouse_data[0]['volume']))  {
                    $warehouse_volume = $warehouse_data[0]['volume'];
                }
                @endphp
                <input name="warehouse_volume" id="warehouse_volume" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter Number Only" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$warehouse_volume}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Volume of the Free Zone (m&sup3;) *</label>
                @php
                $free_zone_volume = "";
                if(!empty($warehouse_data[0]['freezone']))  {
                    $free_zone_volume = $warehouse_data[0]['freezone'];
                }
                @endphp
                <input name="free_zone_volume" id="free_zone_volume" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" onkeyup="this.value=this.value.replace(/^0+/, '')" placeholder="Enter Number Only" type="number" class="form-control" value="{{$free_zone_volume}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Total area of warehouse</label>
                @php
                $total_area_of_warehouse = "";
                if(!empty($warehouse_data[0]['total_area_of_warehouse']))  {
                    $total_area_of_warehouse = $warehouse_data[0]['total_area_of_warehouse'];
                }
                @endphp
                <input name="total_area_of_warehouse" id="total_area_of_warehouse" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" class="form-control" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" placeholder="Enter Total area of warehouse" value="{{$total_area_of_warehouse}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of ground floor*</label>
                @php
                $ground_floor = "";
                if(!empty($warehouse_data[0]['ground_floor']))  {
                    $ground_floor = $warehouse_data[0]['ground_floor'];
                }
                @endphp
                <input name="ground_floor" id="ground_floor" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter ground floor" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$ground_floor}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of mezzanine floor *</label>
                @php
                $mezzanine_floor = "";
                if(!empty($warehouse_data[0]['mezzanine_floor']))  {
                    $mezzanine_floor = $warehouse_data[0]['mezzanine_floor'];
                }
                @endphp
                <input name="mezzanine_floor" id="mezzanine_floor" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter mezzanine floor" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$mezzanine_floor}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of first floor *</label>
                @php
                $first_floor = "";
                if(!empty($warehouse_data[0]['first_floor']))  {
                    $first_floor = $warehouse_data[0]['first_floor'];
                }
                @endphp
                <input name="first_floor" id="first_floor" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter first floor" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$first_floor}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of Racks and Bins *</label>
                @php
                $racks_and_bins = "";
                if(!empty($warehouse_data[0]['racks_and_bins']))  {
                    $racks_and_bins = $warehouse_data[0]['racks_and_bins'];
                }
                @endphp
                <input name="racks_and_bins" id="racks_and_bins" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter Racks and Bins" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$racks_and_bins}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of pallets *</label>
                @php
                $pallets = "";
                if(!empty($warehouse_data[0]['pallets']))  {
                    $pallets = $warehouse_data[0]['pallets'];
                }
                @endphp
                <input name="pallets" id="pallets" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter pallets" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$pallets}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of inbound /check area *</label>
                @php
                $inbound_check_area = "";
                if(!empty($warehouse_data[0]['inbound_check_area']))  {
                    $inbound_check_area = $warehouse_data[0]['inbound_check_area'];
                }
                @endphp
                <input name="inbound_check_area" id="inbound_check_area" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter inbound /check area" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$inbound_check_area}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of Outbound / check area *</label>
                @php
                $outbound_check_area = "";
                if(!empty($warehouse_data[0]['outbound_check_area']))  {
                    $outbound_check_area = $warehouse_data[0]['outbound_check_area'];
                }
                @endphp
                <input name="outbound_check_area" id="outbound_check_area" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter Outbound / check area" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$outbound_check_area}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of work area *</label>
                @php
                $work_area = "";
                if(!empty($warehouse_data[0]['work_area']))  {
                    $work_area = $warehouse_data[0]['work_area'];
                }
                @endphp
                <input name="work_area" id="work_area" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter work area" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$work_area}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of office *</label>
                @php
                $area_of_office = "";
                if(!empty($warehouse_data[0]['area_of_office']))  {
                    $area_of_office = $warehouse_data[0]['area_of_office'];
                }
                @endphp
                <input name="area_of_office" id="area_of_office" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter office" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$area_of_office}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of accommodation *</label>
                @php
                $accommodation = "";
                if(!empty($warehouse_data[0]['accommodation']))  {
                    $accommodation = $warehouse_data[0]['accommodation'];
                }
                @endphp
                <input name="accommodation" id="accommodation" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter accommodation" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$accommodation}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Area of security office *</label>
                @php
                $security_office = "";
                if(!empty($warehouse_data[0]['security_office']))  {
                    $security_office = $warehouse_data[0]['security_office'];
                }
                @endphp
                <input name="security_office" id="security_office" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter security office" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" class="form-control" value="{{$security_office}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Bulk Warehouse Create</label>
                <input type="file" id="warehouse_csv" name="warehouse_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="warehouse_csv" name="warehouse_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-warehouse" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right"><button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p>
{{ Form::close() }}