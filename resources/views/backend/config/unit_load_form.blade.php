{{ Form::open(array('id'=>'configUnitLoadForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Unit Load Type *</label>
                @php
                $unit_load_type = "";
                if(!empty($unit_load_data[0]['unit_load_type']))  {
                    $unit_load_type = $unit_load_data[0]['unit_load_type'];
                }
                $hidden_id = "";
                if(!empty($unit_load_data[0]['unit_load_id']))  {
                    $hidden_id = $unit_load_data[0]['unit_load_id'];
                }
                @endphp
                <input name="unit_load_type" id="unit_load_type" placeholder="" type="text" class="form-control" value="{{$unit_load_type}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12" style="display: none">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id" id="location_id">
                    <option disabled="" selected=""> Select </option>
                    @php
                    if(!empty($location_id)) {
                        foreach($location_id as $urData){
                        $sel = "";
                        if(!empty($unit_load_data[0]['location_id']))  {
                            if($unit_load_data[0]['location_id'] == $urData['location_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['location_id']}}" {{$sel}}>{{ $urData['location_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Unit *</label>
                <select class="form-control" name="stock_unit" id="stock_unit">
                    <option disabled="" selected=""> Select </option>
                    @php
                    if(!empty($unit_id)) {
                        foreach($unit_id as $urData){
                        $sel = "";
                        if(!empty($unit_load_data[0]['stock_unit']))  {
                            if($unit_load_data[0]['stock_unit'] == $urData['unit_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['unit_id']}}" {{$sel}}>{{ $urData['unit_name']}}</option>
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
                <label>Bulk Unit Load Create</label>
                <input type="file" id="unit_load_csv" name="unit_load_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="unit_load_csv" name="unit_load_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-unit-load" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}