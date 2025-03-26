{{ Form::open(array('id'=>'configExchangeRateForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Trading Date *</label>
                @php
                $trading_date = "";
                if(!empty($exchange_rate_data[0]['trading_date']))  {
                    $trading_date = $exchange_rate_data[0]['trading_date'];
                }
                $hidden_id = "";
                if(!empty($exchange_rate_data[0]['exchange_rate_id']))  {
                    $hidden_id = $exchange_rate_data[0]['exchange_rate_id'];
                }
                @endphp
                <input name="trading_date" id="trading_date" placeholder="" type="text" class="form-control datetimepicker" value="{{$trading_date}}" autocomplete="off">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Source Currency *</label>
                <select name="source_currency" id="source_currency" class="form-control">
                    <option value="">Select</option>
                    @php
                    if(!empty($Currency)) {
                        foreach($Currency as $data) {
                            $sel = "";
                            if(!empty($exchange_rate_data[0]['source_currency']))  {
                                if($exchange_rate_data[0]['source_currency'] == $data['currency_id']) $sel = 'selected="selected"';
                            }
                        @endphp
                        <option value="{{$data['currency_id']}}" {{$sel}}>{{$data['currency_code']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Target Currency *</label>
                <select name="target_currency" id="target_currency" class="form-control">
                    <option value="">Select</option>
                    @php
                    if(!empty($Currency)) {
                        foreach($Currency as $data) {
                            $sel = "";
                            if(!empty($exchange_rate_data[0]['target_currency']))  {
                                if($exchange_rate_data[0]['target_currency'] == $data['currency_id']) $sel = 'selected="selected"';
                            }
                        @endphp
                        <option value="{{$data['currency_id']}}" {{$sel}}>{{$data['currency_code']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Closing Rate *</label>
                @php
                $closing_rate = "";
                if(!empty($exchange_rate_data[0]['closing_rate']))  {
                    $closing_rate = $exchange_rate_data[0]['closing_rate'];
                }
                @endphp
                <input name="closing_rate" id="closing_rate" placeholder=" " type="text" class="form-control" value="{{$closing_rate}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Average Rate *</label>
                @php
                $average_rate = "";
                if(!empty($exchange_rate_data[0]['average_rate']))  {
                    $average_rate = $exchange_rate_data[0]['average_rate'];
                }
                @endphp
                <input name="average_rate" id="average_rate" placeholder="" type="text" class="form-control" value="{{$average_rate}}">
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
                <label>Bulk Exchange Rate Create</label>
                <input type="file" id="exchange_rate_csv" name="exchange_rate_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="exchange_rate_csv" name="exchange_rate_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-exchange-rate" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}