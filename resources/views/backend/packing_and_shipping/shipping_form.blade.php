{{ Form::open(array('id'=>'ShippingForm')) }}
<div class="row" id="OrderRequestSection">
    <div class="col-md-4">
        <select class="form-control selectpicker" data-live-search="true" name="client_id" id="client_id" onchange="changeCustomer(this.value)">
            <option value="" selected="" disabled="">Select Customer</option>
            
        	@php
            if(!empty($customerData)) {
                foreach($customerData as $data){
            @endphp
                    <option value="{{$data['client_id']}}" >{{ $data['customer_name']}}</option>
            @php
                }
            }
            @endphp
            
        </select>
    </div>
    <div class="col-md-4">
        <div class="position-relative form-group">
            <select class="form-control selectpicker" data-live-search="true" name="order_id[]" id="order_id" multiple>
            <option value="" selected="" disabled="">Select Order ID's</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <button type="button" id="get_order_details" class="btn-shadow btn btn-info" value="Submit"> Load </button>
    </div>
</div>
<div id="OrderDetails"></div>
{{ Form::close() }}