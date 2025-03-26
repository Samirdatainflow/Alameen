{{ Form::open(array('id'=>'configCurrencyForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Currency Code *</label>
                @php
                $currency_code = "";
                if(!empty($currency_data[0]['currency_code']))  {
                    $currency_code = $currency_data[0]['currency_code'];
                }
                $hidden_id = "";
                if(!empty($currency_data[0]['currency_id']))  {
                    $hidden_id = $currency_data[0]['currency_id'];
                }
                @endphp
                <input name="currency_code" id="currency_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" placeholder="" type="text" class="form-control" value="{{$currency_code}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Currency Description *</label>
                @php
                $currency_description = "";
                if(!empty($currency_data[0]['currency_description']))  {
                    $currency_description = $currency_data[0]['currency_description'];
                }
                @endphp
                <input name="currency_description" id="currency_description" placeholder="" type="text" class="form-control" value="{{$currency_description}}">
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
                <label>Bulk Currency Create</label>
                <input type="file" id="currency_csv" name="currency_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="currency_csv" name="currency_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-currency" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}