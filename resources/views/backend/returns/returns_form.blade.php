{{ Form::open(array('id'=>'returnsForm')) }}
    <div class="row first-part">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" id="return_type" name="return_type">
                    <option value="">Select Return Type</option>
                    <option value="Party Goods Return">Party Goods Return</option>
                    <option value="Battery Defective Goods Return">Battery Defective Goods Return</option>
                    <option value="Defective Goods Return">Defective Goods Return</option>
                    <option value="Excess Short Report Count">Excess Short Report Count</option>
                </select>
            </div>
        </div>
        <div class="col-md-4" id="DeliveryIdSection" style="display:none">
            <div class="position-relative form-group">
                <select class="form-control" id="delivery_id" name="delivery_id">
                    <option value="">Select Delivery ID</option>
                    @php
                    if(!empty($listDeliveryIds)) {
                    foreach($listDeliveryIds as $dids) {
                    @endphp
                    <option value="{{$dids->delivery_management_id}}">{{$dids->delivery_management_id}}</option>
                    @php
                    }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4" id="SaleOrderIdSection" style="display:none">
            <div class="position-relative form-group">
                <select class="form-control" id="sale_order_id" name="sale_order_id">
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="deliverie_details"></div>
    <div class="row first-part" id="findDetails">
        <div class="col-md-12">
            <p class="text-right">
                <button type="button" id="find_details" class="btn-shadow btn btn-info" value="Submit"> Submit & Find Details </button>
                <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
            </p>
        </div>
    </div>
{{ Form::close() }}