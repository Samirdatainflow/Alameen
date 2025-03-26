{{ Form::open(array('id'=>'RackForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id" onchange="changeLocation(this.value)">
                    <option value="">select Location</option>
                    @php
                    if(!empty($Location)) {
                        foreach($Location as $data){
                        $sel = "";
                        if(!empty($rack_data[0]['location_id']))  {
                            if($rack_data[0]['location_id'] == $data['location_id']) $sel = 'selected="selected';
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
                if(!empty($rack_data))  {
                    if(!empty($rack_data[0]['rack_id'])) $hidden_id = $rack_data[0]['rack_id'];
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
                        if(!empty($rack_data[0]['zone_id']))  {
                            if($rack_data[0]['zone_id'] == $data['zone_id']) $sel = 'selected="selected';
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
                <select class="form-control" name="row_id" id="row_id">
                    <option value="">Select</option>
                    @php
                    if(!empty($RowData)) {
                        foreach($RowData as $data){
                        $sel = "";
                        if(!empty($rack_data[0]['row_id']))  {
                            if($rack_data[0]['row_id'] == $data['row_id']) $sel = 'selected="selected';
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
                <label>Rack Name *</label>
                @php
                $rack_name = "";
                if(!empty($rack_data))  {
                    if($rack_data[0]['rack_name']) $rack_name = $rack_data[0]['rack_name'];
                }
                @endphp
                <input name="rack_name" type="text" class="form-control" value="{{$rack_name}}">
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="rack_download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Rack Create</label>
                <input type="file" id="rack_csv" name="rack_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="rack_csv" name="rack_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-rack" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}