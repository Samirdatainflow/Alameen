{{ Form::open(array('id'=>'CarNameForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group csv-upload">
                <label>Car Manufacture</label>
                @php
                $hidden_id = "";
                if(!empty($CarName[0]['car_name_id']))  {
                    $hidden_id = $CarName[0]['car_name_id'];
                }
                @endphp 
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                <select name="car_manufacture_id" class="form-control selectpicker" data-live-search="true" title="Select Car Manufacture" onchange="getCarModel(this.value)">
                    @php
                    if(!empty($CarManufacture) > 0) {
                        foreach($CarManufacture as $data) {
                            $sel = "";
                            if(!empty($CarName[0]['car_manufacture_id'])) {
                                if($CarName[0]['car_manufacture_id'] == $data['car_manufacture_id']) $sel='selected="selected"';
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
    </div>
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group csv-upload">
                <label>Car Model</label>
                <select name="brand_id" id="brand_id" class="form-control selectpicker" data-live-search="true" title="Select Car Model">
                    @php
                    if(!empty($CarModel) > 0) {
                        foreach($CarModel as $model) {
                            $sel = "";
                            if(!empty($CarName[0]['brand_id'])) {
                                if($CarName[0]['brand_id'] == $model['brand_id']) $sel='selected="selected"';
                            }
                        @endphp
                        <option value="{{$model['brand_id']}}" {{$sel}}>{{$model['brand_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                @php
                $car_name = "";
                if(!empty($CarName[0]['car_name']))  {
                    $car_name = $CarName[0]['car_name'];
                }
                @endphp
                <input name="car_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="Enter Car Name *" type="text" class="form-control" value="{{$car_name}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}