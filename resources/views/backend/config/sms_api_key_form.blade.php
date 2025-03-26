{{ Form::open(array('id'=>'configSmsApiKeyForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                @php
                $api_key = "";
                if(!empty($api_data[0]['api_key']))  {
                    $api_key = $api_data[0]['api_key'];
                }
                $hidden_id = "";
                if(!empty($api_data[0]['id']))  {
                    $hidden_id = $api_data[0]['id'];
                }
                @endphp
                <input name="api_key" id="api_key" placeholder="Enter Sms Api Key *" type="text" class="form-control" value="{{$api_key}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}