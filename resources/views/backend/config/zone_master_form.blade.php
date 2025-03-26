{{ Form::open(array('id'=>'configZoneMasterForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Zone Name *</label>
                @php
                $zone_name = "";
                if(!empty($zone_data[0]['zone_name']))  {
                    $zone_name = $zone_data[0]['zone_name'];
                }
                $hidden_id = "";
                if(!empty($zone_data[0]['zone_id']))  {
                    $hidden_id = $zone_data[0]['zone_id'];
                }
                @endphp
                <input name="zone_name" id="zone_name" placeholder="" type="text" class="form-control" value="{{$zone_name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($Location)) {
                        foreach($Location as $data){
                        $sel = "";
                        if(!empty($zone_data[0]['location_id']))  {
                            if($zone_data[0]['location_id'] == $data['location_id']) $sel = 'selected="selected';
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
    </div>
    <div class="row csv-upload">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Zone Master Create</label>
                <input type="file" id="zone_master_csv" name="zone_master_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="zone_master_csv" name="zone_master_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-zone-master" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}