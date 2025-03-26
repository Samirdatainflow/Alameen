{{ Form::open(array('id'=>'GateEntryForm')) }}
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Transaction Type *</label>
                <select class="form-control" name="transaction_type">
                    <option value="">Select</option>
                    @php
                    $arrayData = array('Inbound' => 'Inbound', 'Outbound' => 'Outbound');
                    foreach($arrayData as $k=>$v) {
                        $sel = "";
                        if(!empty($gate_entry_data)) {
                            if(!empty($gate_entry_data[0]['transaction_type'])) {
                                if($gate_entry_data[0]['transaction_type'] == $v) $sel = 'selected="selected"';
                            }
                        }
                    @endphp
                        <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                    @php
                    }
                    @endphp
                </select>
                @php
                $hidden_id = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['gate_entry_id'])) $hidden_id = $gate_entry_data[0]['gate_entry_id'];
                }
                @endphp
                <input type="hidden" name="hidden_id" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Order ID *</label>
                @php
                //print_r($listOrderIds);
                $order_number = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['order_number'])) $order_number = $gate_entry_data[0]['order_number'];
                }
                @endphp
                <select class="form-control" name="order_number" id="order_number">
                    <option value="">Select</option>
                    @php
                    if(!empty($listOrderIds)) {
                        foreach($listOrderIds as $oid) {
                        @endphp
                        <option value="{{$oid->order_id}}">{{$oid->order_id}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
                {{-- <input id="order_number" name="order_number"  type="number" class="form-control" value="{{$order_number}}"> --}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Order date *</label>
                @php
                $order_date = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['order_date'])) $order_date = date('d/m/Y', strtotime($gate_entry_data[0]['order_date']));
                }
                @endphp
                <input name="order_date"  type="text" class="form-control datetimepicker" value="{{$order_date}}" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Vehicle Number *</label>
                @php
                $vehicle_no = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['vehicle_no'])) $vehicle_no = $gate_entry_data[0]['vehicle_no'];
                }
                @endphp
                <input id="vaichel_number" name="vehicle_no"  type="text" class="form-control" value="{{$vehicle_no}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Driver Name *</label>
                @php
                $driver_name = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['driver_name'])) $driver_name = $gate_entry_data[0]['driver_name'];
                }
                @endphp
                <input id="driver_name" name="driver_name"  type="text" class="form-control" value="{{$driver_name}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Contact *</label>
                @php
                $contact_no = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['contact_no'])) $contact_no = $gate_entry_data[0]['contact_no'];
                }
                @endphp
                <input id="contact" name="contact_no"  type="number" class="form-control" value="{{$contact_no}}">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Vehicle In/Out Date *</label>
                @php
                $vehicle_in_out_date = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['vehicle_in_out_date'])) $vehicle_in_out_date = date('d/m/Y', strtotime($gate_entry_data[0]['vehicle_in_out_date']));
                }
                @endphp
                <input id="vehicle_in_out_date" name="vehicle_in_out_date"  type="text" class="form-control datetimepicker" value="{{$vehicle_in_out_date}}" autocomplete="off">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Courier Date *</label>
                @php
                $courier_date = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['courier_date'])) $courier_date = date('d/m/Y', strtotime($gate_entry_data[0]['courier_date']));
                }
                @endphp
                <input id="courier_date" name="courier_date"  type="text" class="form-control datetimepicker" value="{{$courier_date}}" autocomplete="off">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Courier Number *</label>
                @php
                $courier_number = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['courier_number'])) $courier_number = $gate_entry_data[0]['courier_number'];
                }
                @endphp
                <input id="courier_number" name="courier_number"  type="Number" class="form-control" value="{{$courier_number}}" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Number Of Box *</label>
                @php
                $no_of_box = "";
                if(!empty($gate_entry_data)) {
                    if(!empty($gate_entry_data[0]['no_of_box'])) $no_of_box = $gate_entry_data[0]['no_of_box'];
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