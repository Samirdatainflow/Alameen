{{ Form::open(array('id'=>'DeliveryManagementForm')) }}
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Shipping ID *</label>
                @php
                $shipping_id = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['shipping_id'])) $shipping_id = $delivery_management_data[0]['shipping_id'];
                }
                @endphp
                <select id="shipping_id" name="shipping_id" class="form-control">
                    <option value="">Select Shipping ID</option>
                    @php
                    if(!empty($ShippingData))
                    {
                        foreach($ShippingData as $sid)
                        {
                            $sel = "";
                            if(!empty($delivery_management_data)) {
                                if(!empty($delivery_management_data[0]['shipping_id']))
                                {
                                    if($delivery_management_data[0]['shipping_id'] == $sid['shipping_id']) $sel = 'selected="selected"';
                                }
                            }
                        @endphp
                        <option value="{{$sid['shipping_id']}}" {{$sel}}>{{$sid['shipping_id']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Order ID *</label>
                @php
                $sale_order_id = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['sale_order_id'])) $sale_order_id = $delivery_management_data[0]['sale_order_id'];
                }
                @endphp
                <input id="sale_order_id" name="sale_order_id"  type="text" class="form-control" value="{{$sale_order_id}}" readonly="readonly">
                @php
                $hidden_id = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['delivery_management_id'])) $hidden_id = $delivery_management_data[0]['delivery_management_id'];
                }
                @endphp
                <input type="hidden" name="hidden_id" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Order date *</label>
                @php
                $order_date = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['order_date'])) $order_date = date('d/m/Y', strtotime($delivery_management_data[0]['order_date']));
                }
                @endphp
                <input name="order_date" id="order_date" type="text" class="form-control datetimepicker" value="{{$order_date}}" autocomplete="off" readonly="readonly">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Vehicle Number </label>
                @php
                $vehicle_no = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['vehicle_no'])) $vehicle_no = $delivery_management_data[0]['vehicle_no'];
                }
                @endphp
                <input id="vaichel_number" name="vehicle_no"  type="text" class="form-control" value="{{$vehicle_no}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Driver Name </label>
                @php
                $driver_name = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['driver_name'])) $driver_name = $delivery_management_data[0]['driver_name'];
                }
                @endphp
                <input id="driver_name" name="driver_name"  type="text" class="form-control" value="{{$driver_name}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Contact </label>
                @php
                $contact_no = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['contact_no'])) $contact_no = $delivery_management_data[0]['contact_no'];
                }
                @endphp
                <input id="contact" name="contact_no"  type="text" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="20" class="form-control" value="{{$contact_no}}">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Vehicle In/Out Date </label>
                @php
                $vehicle_in_out_date = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['vehicle_in_out_date'])) $vehicle_in_out_date = date('d/m/Y', strtotime($delivery_management_data[0]['vehicle_in_out_date']));
                }
                @endphp
                <input id="vehicle_in_out_date" name="vehicle_in_out_date"  type="text" class="form-control datetimepicker" value="{{$vehicle_in_out_date}}" autocomplete="off">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Courier Company </label>
                <select class="form-control" name="courier_company_id">
                    <option value="">Select</option>
                    @php
                    if(!empty($courier_company)) {
                        foreach($courier_company as $com) {
                            $sel = "";
                            if(!empty($delivery_management_data)) {
                                if($delivery_management_data[0]['courier_company_id'] == $com['courier_company_id']) $sel = 'selected="selected"';
                            }
                        @endphp
                        <option value="{{$com['courier_company_id']}}" {{$sel}}>{{$com['company_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Courier Date </label>
                @php
                $courier_date = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['courier_date'])) $courier_date = date('d/m/Y', strtotime($delivery_management_data[0]['courier_date']));
                }
                @endphp
                <input id="courier_date" name="courier_date"  type="text" class="form-control datetimepicker" value="{{$courier_date}}" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Courier Number </label>
                @php
                $courier_number = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['courier_number'])) $courier_number = $delivery_management_data[0]['courier_number'];
                }
                @endphp
                <input id="courier_number" name="courier_number"  type="Number" class="form-control" value="{{$courier_number}}" autocomplete="off">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Number Of Box </label>
                @php
                $no_of_box = "";
                if(!empty($delivery_management_data)) {
                    if(!empty($delivery_management_data[0]['no_of_box'])) $no_of_box = $delivery_management_data[0]['no_of_box'];
                }
                @endphp
                <input id="box_number" name="no_of_box" placeholder="" type="number" class="form-control" value="{{$no_of_box}}">
            </div>
        </div>
    </div>

   <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}