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
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Part No</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                    @php
                    $o = 1;
                    if(sizeof($order_details) > 0) {
                        foreach($order_details as $o_data) {
                    @endphp
                    <tr>
                        <td>{{($o)}}</td>
                        <td><input type="hidden" name="sale_order_id[]" value="{{$o_data['sale_order_id']}}">{{($o_data['sale_order_id'])}}</td>
                        <td><input type="hidden" name="product_id[]" value="{{$o_data['product_id']}}"><input type="text" class="form-control" value="{{$o_data['part_name']}}" readonly></td><td><input type="text" class="form-control" value="{{$o_data['pmpno']}}" readonly></td><td><input type="text" class="form-control" name="price[]" value="{{$o_data['price']}}" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" value="{{$o_data['quantity']}}" readonly></td>
                    </tr>
                    @php
                        $o++;
                        }
                    }
                    @endphp
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
                <tr>
                    <td colspan="3" valign="top">
                        <table cellpadding="5">
                            @php
                            $client_id = "";
                            $customer_id = "";
                            $customer_name = "";
                            $customer_email_id = "";
                            $customer_off_msg_no = "";
                            if(!empty($clients_data)) $customer_id = $clients_data[0]['customer_id'];
                            if(!empty($clients_data)) $client_id = $clients_data[0]['client_id'];
                            if(!empty($clients_data)) $customer_name = $clients_data[0]['customer_name'];
                            if(!empty($clients_data)) $customer_email_id = $clients_data[0]['customer_email_id'];
                            if(!empty($clients_data)) $customer_off_msg_no = $clients_data[0]['customer_wa_no'];
                            @endphp
                            <input type="hidden" name="hidden_client_id" value="{{$client_id}}">
                            <tr>
                                <td>{{$customer_name}}</td>
                            </tr>
                            <tr>
                                <td>Mobile No: </td>
                                <td>{{$customer_off_msg_no}}</td>
                            </tr>
                            <tr>
                                <td>Email: </td>
                                <td>{{$customer_email_id}}</td>
                            </tr>
                            <tr>
                                <td>Customer VATIN: </td>
                                @php
                                $vatin = "";
                                if(!empty($clients_data)) {
                                    $vatin = $clients_data[0]['vatin'];
                                }
                                @endphp
                                <td>{{$vatin}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn-shadow btn btn-info add-shipping-address"> <i class="fa fa-plus"></i> Add Shipping Address </button>
        </div>
    </div>
    <br>
    @if(sizeof($shipping_address) > 0)
    <div class="row">
        <div class="col-md-12">
            <h3>Shipping Address</h3>
        </div>
    </div>
    <br/>
    @endif
    <div id="ShippingAddressSection">
        @php
        $shipping_address_count = 1;
        if(sizeof($shipping_address) > 0) {
        $shipping_address_count = sizeof($shipping_address);
        foreach($shipping_address as $k=>$v) {
        @endphp
        <div class="row" style="margin-bottom:10px" id="shippingAddrInput{{$k}}">
            <div class="col-md-1">
                <input type="radio" id="html" name="shipping_address_active" value="1" title="Set as Primary Address" style="position: absolute;top: 25px;width: 100%;height: 20px;" class="shipping-address-active">
                <input type="hidden" class="address-status" name="address_status[]" value="">
            </div>
            <div class="col-md-11">
                <input type="hidden" name="shipping_address_id[]" value="{{$v['shipping_address_id']}}">
                <textarea class="form-control" name="shipping_address[]" readonly="readonly">{{$v['address']}}</textarea>
            </div>
        </div>
        @php
            }
        }
        @endphp
        <input type="hidden" name="shipping_address_count" id="shipping_address_count" value="{{$shipping_address_count}}">
    </div>
    <br>
    <div class="row">
        <div class="col-md-12" style="text-align:right;">
            <p class="text-right">
                <button type="submit" class="btn-shadow btn btn-info"> Approved Shipping </button>
                <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
            </p>
        </div>
    </div>
