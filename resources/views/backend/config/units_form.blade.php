{{ Form::open(array('id'=>'configUnitsForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Unit Name *</label>
                @php
                $unit_name = "";
                if(!empty($units_data[0]['unit_name']))  {
                    $unit_name = $units_data[0]['unit_name'];
                }
                $hidden_id = "";
                if(!empty($units_data[0]['unit_id']))  {
                    $hidden_id = $units_data[0]['unit_id'];
                }
                @endphp
                <input name="unit_name" id="unit_name" placeholder="" type="text" class="form-control" value="{{$unit_name}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Unit Type *</label>
                @php
                $unit_type = "";
                if(!empty($units_data[0]['unit_type']))  {
                    $unit_type = $units_data[0]['unit_type'];
                }
                @endphp
                <input name="unit_type" id="unit_type" placeholder="" type="text" class="form-control" value="{{$unit_type}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Base Factor *</label>
                @php
                $base_factor = "";
                if(!empty($units_data[0]['base_factor']))  {
                    $base_factor = $units_data[0]['base_factor'];
                }
                @endphp
                <input name="base_factor" id="base_factor" placeholder="" type="text" class="form-control" value="{{$base_factor}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Base Measurement Unit *</label>
                @php
                $base_measurement_unit = "";
                if(!empty($units_data[0]['base_measurement_unit']))  {
                    $base_measurement_unit = $units_data[0]['base_measurement_unit'];
                }
                @endphp
                <input name="base_measurement_unit" id="base_measurement_unit" placeholder="" type="text" class="form-control" value="{{$base_measurement_unit}}">
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
                <label>Bulk Unit Create</label>
                <input type="file" id="unit_csv" name="unit_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="unit_csv" name="unit_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-unit" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}