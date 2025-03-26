{{ Form::open(array('id'=>'PackingForm')) }}
<div class="row" id="OrderRequestSection">
    <div class="col-md-4">
        <div class="position-relative form-group">
            <select class="form-control" id="order_id" name="order_id">
                <option value="">Select Order ID</option>
                @php
                if(!empty($SaleOrderData)) {
                    foreach($SaleOrderData as $data) {
                    @endphp
                    <option value="{{$data['sale_order_id']}}">{{$data['sale_order_id']}}</option>
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
<div id="OrderDetails"></div>
{{ Form::close() }}