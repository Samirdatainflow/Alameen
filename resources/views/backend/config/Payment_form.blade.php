{{ Form::open(array('id'=>'configPaymentForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Payment Method *</label>
                @php
                $payment_method = "";
                if(!empty($payment_data[0]['payment_method']))  {
                    $payment_method = $payment_data[0]['payment_method'];
                }
                $hidden_id = "";
                if(!empty($payment_data[0]['payment_id']))  {
                    $hidden_id = $payment_data[0]['payment_id'];
                }
                @endphp
                <input name="payment_method" id="payment_method" placeholder="" type="text" class="form-control" value="{{$payment_method}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Payment Description *</label>
                @php
                $payment_description = "";
                if(!empty($payment_data[0]['payment_description']))  {
                    $payment_description = $payment_data[0]['payment_description'];
                }
                @endphp
                <input name="payment_description" id="payment_description" placeholder="" type="text" class="form-control" value="{{$payment_description}}">
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
                <label>Bulk Payment Create</label>
                <input type="file" id="payment_csv" name="payment_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="payment_csv" name="payment_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-payment" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}