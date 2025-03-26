{{ Form::open(array('id'=>'saveLocationForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Location name *</label>
                        @php
                        $location_name = "";
                        if(!empty($location_data[0]['location_name']))  {
                            $location_name = $location_data[0]['location_name'];
                        }
                        $hidden_id = "";
                        if(!empty($location_data[0]['location_id']))  {
                            $hidden_id = $location_data[0]['location_id'];
                        }
                        @endphp 
                        <input name="location_name" id="location_name" placeholder="Enter" type="text" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" class="form-control" value="{{$location_name}}">
                        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                    </div>
                    <div class="col-md-6">
                        <label>Location Type *</label>
                        @php
                        $location_type = "";
                        if(!empty($location_data[0]['location_type']))  {
                            $location_type = $location_data[0]['location_type'];
                        }
                        @endphp                
                        <input name="location_type" id="location_type" placeholder="Enter" type="text" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" class="form-control" value="{{$location_type}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Functional Area</label>
                        <select name="location_functional" id="location_functional" class="form-control">
                            <option value="">Select </option>
                            @foreach($functional_areas as $functional_area)
                            @php
                            $selected = "";
                            if(!empty($location_data[0]['location_functional']))  {
                                if($location_data[0]['location_functional'] == $functional_area['functional_area_id'])
                                {
                                    $selected = "selected=selected";
                                }
                            }
                            @endphp 
                            <option value="{{$functional_area['functional_area_id']}}" {{$selected}}>{{$functional_area['function_area_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" style="display: none">
                        <label>Location Zone</label>
                       <select name="location_zone" id="location_zone" class="form-control">
                            <option value="">Select </option>
                            @foreach($location_zones as $location_zone)
                            @php
                            $selected = "";
                            if(!empty($location_data[0]['location_zone']))  {
                                if($location_data[0]['location_zone'] == $location_zone['zone_id'])
                                {
                                    $selected = "selected=selected";
                                }
                            }
                            @endphp 
                            <option value="{{$location_zone['zone_id']}}" {{$selected}}>{{$location_zone['zone_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Location Load Type</label>
                        <select name="location_load_type" id="location_load_type" class="form-control">
                            <option value="">Select </option>
                            @foreach($location_loads as $location_load)
                            @php
                            $selected = "";
                            if(!empty($location_data[0]['location_load_type']))  {
                                if($location_data[0]['location_load_type'] == $location_load['unit_load_id'])
                                {
                                    $selected = "selected=selected";
                                }
                            }
                            @endphp 
                            <option value="{{$location_load['unit_load_id']}}" {{$selected}}>{{$location_load['unit_load_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Location capacity *</label>
                        @php
                        $location_capacity = "";
                        if(!empty($location_data[0]['location_capacity']))  {
                            $location_capacity = $location_data[0]['location_capacity'];
                        }
                        @endphp 
                        <input name="location_capacity" id="location_capacity" placeholder="Enter Capacity" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control" value="{{$location_capacity}}">
                    </div>
                    <div class="col-md-6">
                        <label>Order Index *</label>
                        @php
                        $order_index = "";
                        if(!empty($location_data[0]['order_index']))  {
                            $order_index = $location_data[0]['order_index'];
                        }
                        @endphp 
                       <input name="order_index" id="order_index" placeholder="Enter Only Number" type="number" onkeyup="this.value=this.value.replace(/^0+/, '')" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control" value="{{$order_index}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Warehouse *</label>
                        <select class="form-control" name="warehouse" id="warehouse">
                            <option disabled="" selected="">Select</option> 
                            @php
                            $selected="";
                            if(!empty($warehouses))  {
                                foreach($warehouses as $warehouse)
                                {
                                    if(!empty($location_data[0]['warehouseid']))  {
                                        if($location_data[0]['warehouseid'] == $warehouse['warehouse_id'])
                                        {
                                            $selected="selected=selected";
                                        }
                                        else
                                        {
                                            $selected="";
                                        }
                                    }
                            @endphp
                            <option value="{{$warehouse['warehouse_id']}}" {{$selected}}>{{$warehouse['name']}}</option> 
                            @php
                                }
                            }
                            @endphp 
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}