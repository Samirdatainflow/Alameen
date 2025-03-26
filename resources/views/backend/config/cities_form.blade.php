{{ Form::open(array('id'=>'configCitiesForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Cities Code *</label>
                @php
                $city_code = "";
                if(!empty($city_data[0]['city_code']))  {
                    $city_code = $city_data[0]['city_code'];
                }
                $hidden_id = "";
                if(!empty($city_data[0]['city_id']))  {
                    $hidden_id = $city_data[0]['city_id'];
                }
                @endphp
                <input name="city_code" id="city_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" placeholder="" type="text" class="form-control" value="{{$city_code}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>City Name *</label>
                @php
                $city_name = "";
                if(!empty($city_data[0]['city_name']))  {
                    $city_name = $city_data[0]['city_name'];
                }
                @endphp
                <input name="city_name" id="city_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="" type="text" class="form-control" value="{{$city_name}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>State *</label>
                <select class="form-control" name="state_id" id="state_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($state_id)) {
                        foreach($state_id as $urData){
                        $sel = "";
                        if(!empty($city_data[0]['state_id']))  {
                            
                            if($city_data[0]['state_id'] == $urData['state_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['state_id']}}" {{$sel}}>{{ $urData['state_name']}}</option>
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
                <label>Bulk Cities Create</label>
                <input type="file" id="cities_csv" name="cities_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="cities_csv" name="cities_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-cities" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}