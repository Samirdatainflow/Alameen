{{ Form::open(array('id'=>'ConsignmentReceiptForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" id="inbound_order_no" name="inbound_order_no">
                    <option value="">Select Inbound Order No</option>
                    @php
                    if(!empty($listInboundNo)) {
                        foreach($listInboundNo as $data) {
                        @endphp
                        <option value="{{$data->order_number}}">{{$data->order_number}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" id="get_order_details" class="btn-shadow btn btn-info" value="Submit"> Load </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5 id="purchasedDate"></h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Product Name</th>
                        <th>Part No</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody"></tbody>
            </table>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="submit" class="btn-shadow btn btn-info"> Save </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}