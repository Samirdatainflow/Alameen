{{ Form::open(array('id'=>'userBrandForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group csv-upload">
                <label>Car Manufacture</label>
                @php
                $hidden_id = "";
                if(!empty($brand_data[0]['brand_id']))  {
                    $hidden_id = $brand_data[0]['brand_id'];
                }
                @endphp
                <input name="hidden_id" id="hidden_id" type="hidden" value="{{$hidden_id}}">
                <select name="car_manufacture_id" class="form-control">
                    <option value="">Select </option>
                    @php
                    if(sizeof($CarManufacture) > 0) {
                        foreach($CarManufacture as $data) {
                            $sel = "";
                            if(!empty($brand_data[0]['car_manufacture_id'])) {
                                if($brand_data[0]['car_manufacture_id'] == $data['car_manufacture_id']) $sel='selected="selected"';
                            }
                        @endphp
                        <option value="{{$data['car_manufacture_id']}}" {{$sel}}>{{$data['car_manufacture']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Model Name *</label>
                @php
                $Brand_name = "";
                if(!empty($brand_data[0]['brand_name']))  {
                    $Brand_name = $brand_data[0]['brand_name'];
                }
                @endphp
                <input name="Brand_name" id="Brand_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="" type="text" class="form-control" value="{{$Brand_name}}">
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
                <label>Bulk Model Create</label>
                <input type="file" id="car_model_csv" name="car_model_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="car_model_csv" name="car_model_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-car-model" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}