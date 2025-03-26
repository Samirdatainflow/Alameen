{{ Form::open(array('id'=>'configRowForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id" id="location_id" onchange="changeLocation(this.value)">
                    <option value="" disabled="" selected="">Select </option>
                    @php
                    if(!empty($location_id)) {
                        foreach($location_id as $data){
                        $sel = "";
                        if(!empty($row_data[0]['location_id']))  {
                            if($row_data[0]['location_id'] == $data['location_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$data['location_id']}}" {{$sel}}>{{ $data['location_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Zone *</label>
                <select class="form-control" name="zone_id" id="zone_id">
                    <option value="" disabled="" selected="">Select </option>
                    @php
                    if(!empty($zone_id)) {
                        foreach($zone_id as $data){
                        $sel = "";
                        if(!empty($row_data[0]['zone_id']))  {
                            if($row_data[0]['zone_id'] == $data['zone_id']) $sel = 'selected="selected';
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
                <label>Row Name *</label>
                @php
                $row_name = "";
                if(!empty($row_data[0]['row_name']))  {
                    $row_name = $row_data[0]['row_name'];
                }
                $hidden_id = "";
                if(!empty($row_data[0]['row_id']))  {
                    $hidden_id = $row_data[0]['row_id'];
                }
                @endphp
                <input name="row_name" id="row_name" placeholder="" type="text" class="form-control" value="{{$row_name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_row_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Row Create</label>
                <input type="file" id="row_csv" name="row_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="row_csv" name="row_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-row" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}