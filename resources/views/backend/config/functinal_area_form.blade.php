{{ Form::open(array('id'=>'configFunctionAreaForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Function Area Name *</label>
                @php
                $function_area_name = ""; 
                if(!empty($functional_area_data[0]['function_area_name']))  {
                    $function_area_name = $functional_area_data[0]['function_area_name'];
                }
                $hidden_id = "";
                if(!empty($functional_area_data[0]['functional_area_id']))  {
                    $hidden_id = $functional_area_data[0]['functional_area_id'];
                }
                @endphp
                <input name="function_area_name" id="function_area_name" placeholder="" type="text" class="form-control" value="{{$function_area_name}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Warehouse *</label>
                <select class="form-control" name="warehouseid"  id="warehouseid">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($warehouse_id)) {
                        foreach($warehouse_id as $urData){
                        $sel = "";
                        if(!empty($functional_area_data[0]['warehouseid']))  {
                            if($functional_area_data[0]['warehouseid'] == $urData['warehouse_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['warehouse_id']}}" {{$sel}}>{{ $urData['name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
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
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Functional Area Create</label>
                <input type="file" id="functional_area_csv" name="functional_area_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="functional_area_csv" name="functional_area_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-functional-area" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}