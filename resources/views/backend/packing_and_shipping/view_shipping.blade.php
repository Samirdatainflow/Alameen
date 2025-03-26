<div class="row">
    <div class="col-md-12">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Sale Order ID</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody id="entryProductTbody">
                @php
                $i = 1;
                if(!empty($ShippingData)) {
                    foreach($ShippingData as $data) {
                    @endphp
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$data['sale_order_id']}}</td>
                        <td><input type="text" class="form-control" value="{{$data['part_name']}}" readonly></td>
                        <td><input type="text" class="form-control" value="{{$data['pmpno']}}" readonly></td>
                        <td><input type="text" class="form-control" value="{{$data['price']}}" readonly></td>
                        <td><input type="number" class="form-control quantity" name="quantity" value="{{$data['quantity']}}" readonly></td>
                    </tr>
                    @php
                    $i++;
                    }
                }
                @endphp
            </tbody>
        </table>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-md-12">
        <h3>Customer Details</h3>
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
                        if(!empty($clients_data)) $customer_off_msg_no = $clients_data[0]['customer_off_msg_no'];
                        @endphp
                        <input type="hidden" name="hidden_client_id" value="{{$client_id}}">
                        <tr>
                            <td>{{$customer_name}}</td>
                        </tr>
                        <tr>
                            <td>Tel No: </td>
                            <td>{{$customer_off_msg_no}}</td>
                        </tr>
                        <tr>
                            <td>Fax No: </td>
                            <td></td>
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
@if(sizeof($shipping_address) > 0)
<div class="row">
    <div class="col-md-12">
        <h3>Shipping Address</h3>
        @foreach($shipping_address as $addr)
        <div style="margin-bottom:15px"><textarea class="form-control" readonly="readonly">{{$addr['address']}}</textarea></div>
        @endforeach
    </div>
</div>
<br/>
@endif
<div class="row">
    <div class="col-md-12">
        <p class="text-right">
            <input type="hidden" name="sales_order_id" id="sales_order_id">
            {{-- <button type="button" class="btn-shadow btn btn-info print-packing-slip"> Print Slip </button> --}}
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
    </div>
</div>