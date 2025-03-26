{{ Form::open(array('id'=>'supplierForm')) }}
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Supplier Code *</label>
                @php
                $supplier_code = "";
                if(!empty($supplier_data[0]['supplier_code']))  {
                    $supplier_code = $supplier_data[0]['supplier_code'];
                }
                $hidden_id = "";
                if(!empty($supplier_data[0]['supplier_id']))  {
                    $hidden_id = $supplier_data[0]['supplier_id'];
                }
                @endphp
                <input name="supplier_code" id="supplier_code" placeholder="" type="text" class="form-control" value="{{$supplier_code}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Full Name *</label>
                @php
                $full_name = "";
                if(!empty($supplier_data[0]['full_name']))  {
                    $full_name = $supplier_data[0]['full_name'];
                    }
                @endphp
                <input name="full_name" id="full_name" placeholder="" type="text" class="form-control" value="{{$full_name}}">
                
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Business Title *</label>
                @php
                $business_title = "";
                if(!empty($supplier_data[0]['business_title']))  {
                    $business_title = $supplier_data[0]['business_title'];
                }
                @endphp
                <input name="business_title" id="business_title" placeholder="" type="text" class="form-control" value="{{$business_title}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Email *</label>
                @php
                $email = "";
                if(!empty($supplier_data[0]['email']))  {
                    $email = $supplier_data[0]['email'];
                }
                @endphp
                <input name="email" id="email" placeholder="" type="text" class="form-control" value="{{$email}}">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Address *</label>
                @php
                $address = "";
                if(!empty($supplier_data[0]['address']))  {
                    $address = $supplier_data[0]['address'];
                }
                @endphp
                <input name="address" id="address" placeholder="" type="text" class="form-control" value="{{$address}}">
                <!-- <textarea name="address" id="address" placeholder="Address" type="text" class="form-control" value="{{$address}}"></textarea> -->
            </div>
        </div>
        <div class="col-sm-6">
            <div class="position-relative form-group">
                <label>Mobile *</label>
                @php
                $mobile = "";
                if(!empty($supplier_data[0]['mobile']))  {
                    $mobile = $supplier_data[0]['mobile'];
                }
                @endphp
                <input name="mobile" id="mobile" placeholder="Enter Only Number" type="number" class="form-control" value="{{$mobile}}">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Phone *</label>
                @php
                $phone = "";
                if(!empty($supplier_data[0]['phone']))  {
                    $phone = $supplier_data[0]['phone'];
                }
                @endphp
                <input name="phone" id="phone" type="number" class="form-control" placeholder="Enter Only Number" value="{{$phone}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Country *</label>
               
                <select name="country" id="country" class="form-control selectpicker" data-live-search="true" title="Search Country">
                    @php
                    
                    $country_code = "";
                    if(!empty($supplier_data[0]['country_code']))  {
                        $country_code = $supplier_data[0]['country_code'];
                    }
                    if(!empty($countries))  {
                        foreach($countries as $country) {
                            $selected = "";
                            if(!empty($supplier_data[0]['country_id']))  {
                                if($supplier_data[0]['country_id'] == $country->name)
                                {
                                    $selected = "selected=selected";
                                }
                            }
                    @endphp
                    <option value="{{$country->name}}" data-country_code="{{$country->iso2}}" {{$selected}}>{{$country->name}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
                <input type="hidden" name="country_code" id="country_code" value="">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>State *</label>
                <select name="state" id="state" class="form-control selectpicker" data-live-search="true" title="Select State">
                    <!--<option value="">Select </option>-->
                    @php
                    
                    $state_code = "";
                    if(!empty($supplier_data[0]['state_code']))  {
                        $state_code = $supplier_data[0]['state_code'];
                    }
                    if(!empty($states))  {
                        foreach($states as $state) {
                            $selected="";
                            if(!empty($supplier_data[0]['state_id']))  {
                                if($supplier_data[0]['state_id'] == $state->name)
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
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>City *</label>
                <select name="city" id="city" class="form-control selectpicker" data-live-search="true" title="Select City">
                    <!--<option value="">Select </option>-->
                    @php
                    if(!empty($cities))  {
                        foreach($cities as $city) {
                            $selected="";
                            if(!empty($supplier_data[0]['city_id']))  {
                                if($supplier_data[0]['city_id'] == $city->name)
                                {
                                $selected="selected=selected";
                                }
                            }
                    @endphp
                    <option value="{{$city->name}}" {{$selected}}>{{$city->name}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Zipcode </label>
                @php
                $zipcode = "";
                if(!empty($supplier_data[0]['zipcode']))  {
                    $zipcode = $supplier_data[0]['zipcode'];
                }
                @endphp
                <input name="zipcode" id="zipcode" type="number" class="form-control" placeholder="" value="{{$zipcode}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group car-manufacture-search">
                <label for="">Group * </label>
                <select class="form-control selectpicker" name="group_ids[]" id="group_ids" data-live-search="true" multiple title="Select">
                    @php
                    if(!empty($group_data)) {
                        foreach($group_data as $gdata){
                            $sel = "";
                            if(!empty($supplier_data[0]['group_ids'])) {
                                if(in_array($gdata['group_id'],explode(',',$supplier_data[0]['group_ids']))) $sel = 'selected="selected'; 
                            }
                    @endphp
                    <option value="{{$gdata['group_id']}}" {{$sel}}>{{$gdata['group_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>CR Number </label>
                @php
                $cr_number = "";
                if(!empty($supplier_data[0]['cr_number']))  {
                    $cr_number = $supplier_data[0]['cr_number'];
                }
                @endphp
                <input name="cr_number" id="cr_number" placeholder="" type="text" class="form-control" value="{{$cr_number}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>VATIN Number</label>
                @php
                $vatin_number = "";
                if(!empty($supplier_data[0]['vatin_number']))  {
                    $vatin_number = $supplier_data[0]['vatin_number'];
                }
                @endphp
                <input name="vatin_number" id="vatin_number" placeholder="" type="text" class="form-control" value="{{$vatin_number}}">
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
                <label>Bulk Supplier Create</label>
                <input type="file" id="supplier_csv" name="supplier_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="supplier_csv" name="supplier_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-supplier" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}