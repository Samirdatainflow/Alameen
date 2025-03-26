{{ Form::open(array('id'=>'RackForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id" onchange="changeLocation(this.value)">
                    <option value="">Select</option>
                    @php
                    if(!empty($Location)) {
                        foreach($Location as $data){
                        $sel = "";
                        if(!empty($place_data[0]['location_id']))  {
                            if($place_data[0]['location_id'] == $data['location_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['location_id']}}" {{$sel}}>{{ $data['location_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
                @php
                $hidden_id = "";
                if(!empty($place_data))  {
                    if(!empty($place_data[0]['place_id'])) $hidden_id = $place_data[0]['place_id'];
                }
                @endphp
                <input type="hidden" name="hidden_id" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Zone *</label>
                <select class="form-control" name="zone_id" id="zone_id" onchange="changeZone(this.value)">
                    <option value="">Select</option>
                    @php
                    if(!empty($ZoneMasterData)) {
                        foreach($ZoneMasterData as $data){
                        $sel = "";
                        if(!empty($place_data[0]['zone_id']))  {
                            if($place_data[0]['zone_id'] == $data['zone_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['zone_id']}}" {{$sel}}>{{ $data['zone_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Row *</label>
                <select class="form-control" name="row_id" id="row_id" onchange="changeRow(this.value)">
                    <option value="">Select</option>
                    @php
                    if(!empty($RowData)) {
                        foreach($RowData as $data){
                        $sel = "";
                        if(!empty($place_data[0]['row_id']))  {
                            if($place_data[0]['row_id'] == $data['row_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['row_id']}}" {{$sel}}>{{ $data['row_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Rack *</label>
                <select class="form-control" name="rack_id" id="rack_id" onchange="changeRack(this.value)">
                    <option value="">Select</option>
                    @php
                    if(!empty($RackData)) {
                        foreach($RackData as $data){
                        $sel = "";
                        if(!empty($place_data[0]['rack_id']))  {
                            if($place_data[0]['rack_id'] == $data['rack_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['rack_id']}}" {{$sel}}>{{ $data['rack_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Level *</label>
                <select class="form-control" name="plate_id" id="plate_id">
                    <option value="">Select</option>
                    @php
                    if(!empty($PlateData)) {
                        foreach($PlateData as $data){
                        $sel = "";
                        if(!empty($place_data[0]['plate_id']))  {
                            if($place_data[0]['plate_id'] == $data['plate_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['plate_id']}}" {{$sel}}>{{ $data['plate_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Position Name *</label>
                @php
                $place_name = "";
                if(!empty($place_data))  {
                    if(!empty($place_data[0]['place_name'])) $place_name = $place_data[0]['place_name'];
                }
                @endphp
                <input name="place_name" type="text" class="form-control" value="{{$place_name}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Max. Capacity *</label>
                @php
                $max_capacity = "";
                if(!empty($place_data))  {
                    if(!empty($place_data[0]['max_capacity'])) $max_capacity = $place_data[0]['max_capacity'];
                }
                @endphp
                <input name="max_capacity" type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control" value="{{$max_capacity}}">
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="place_download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Position Create</label>
                <input type="file" id="place_csv" name="place_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="place_csv" name="place_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-place" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}