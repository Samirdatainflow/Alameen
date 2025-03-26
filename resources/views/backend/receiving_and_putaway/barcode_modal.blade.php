{{ Form::open(array('id'=>'downloadForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <input type="hidden" id="order_id" value="{{$order_id}}">
                <input type="hidden" id="barcode_number" value="{{$barcode_number}}">
                <select class="form-control" id="download_no">
                    @php
                    $listNo = $selectQty;
                    for($i=1; $i<= $listNo; $i++) {
                    @endphp
                    <option value="{{$i}}">{{$i}}</option>
                    @php } @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" id="download_barcode" class="btn-shadow btn btn-info" value="Submit"> Download </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5 id="purchasedDate"></h5>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" class="btn-shadow btn btn-cancel"> Close </button>
    </p>
{{ Form::close() }}