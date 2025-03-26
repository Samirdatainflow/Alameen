{{ Form::open(array('id'=>'configStateForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>State Code *</label>
                @php
                $state_code = "";
                if(!empty($state_data[0]['state_code']))  {
                    $state_code = $state_data[0]['state_code'];
                }
                $hidden_id = "";
                if(!empty($state_data[0]['state_id']))  {
                    $hidden_id = $state_data[0]['state_id'];
                }
                @endphp
                <input name="state_code" id="state_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" placeholder=""  type="text" class="form-control" value="{{$state_code}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>State Name *</label>
                @php
                $state_name = "";
                if(!empty($state_data[0]['state_name']))  {
                    $state_name = $state_data[0]['state_name'];
                }
                @endphp
                <input name="state_name" id="state_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="" type="text" class="form-control" value="{{$state_name}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Country *</label>
                <select class="form-control" name="country_id" id="country_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($country_id)) {
                        foreach($country_id as $urData){
                        $sel = "";
                        if(!empty($state_data[0]['country_id']))  {
                            
                            if($state_data[0]['country_id'] == $urData['country_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['country_id']}}" {{$sel}}>{{ $urData['country_name']}}</option>
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
                <label>Bulk State Create</label>
                <input type="file" id="state_csv" name="state_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="state_csv" name="state_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-state" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}