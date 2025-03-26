{{ Form::open(array('id'=>'configContriesForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Country Code *</label>
                @php
                $country_code = "";
                if(!empty($country_data[0]['country_code']))  {
                    $country_code = $country_data[0]['country_code'];
                }
                $hidden_id = "";
                if(!empty($country_data[0]['country_id']))  {
                    $hidden_id = $country_data[0]['country_id'];
                }
                @endphp
                <input name="country_code" id="country_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" placeholder="" type="text" class="form-control" value="{{$country_code}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Country Name *</label>
                @php
                $country_name = "";
                if(!empty($country_data[0]['country_name']))  {
                    $country_name = $country_data[0]['country_name'];
                }
                @endphp
                <input name="country_name" id="country_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="" type="text" class="form-control" value="{{$country_name}}">
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
                <label>Bulk Countries Create</label>
                <input type="file" id="countries_csv" name="countries_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="countries_csv" name="countries_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-countries" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}