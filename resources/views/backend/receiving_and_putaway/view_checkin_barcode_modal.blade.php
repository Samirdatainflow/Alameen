{{ Form::open(array('id'=>'CheckInBarcodeForm', 'onsubmit'=>'return submitForm()')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-12">
            <input type="hidden" name="hidden_check_in_id" id="hidden_check_in_id" value="{{$check_in_id}}">
            <div class="position-relative form-group">
                <input type="text" class="form-control" id="barcode_no" name="order_id">
            </div>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}