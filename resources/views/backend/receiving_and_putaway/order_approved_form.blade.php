{{ Form::open(array('id'=>'ApprovedOrderForm')) }} 
    <div class="row">
            <div class="col-md-6">
                <div class="position-relative form-group">
                    @php
                    $order_id = "";
                    @endphp
                    @if(!empty($order_data))
                        @if(!empty($order_data[0]['order_id']))
                        @php
                        $order_id = $order_data[0]['order_id'];
                        @endphp
                        @endif
                    @endif
                    <input name="order_id" placeholder="ID" type="text" class="form-control" value="{{$order_id}}" readonly="readonly">
                    @php
                    $supplier_id = "";
                    @endphp
                    @if(!empty($order_data))
                        @if(!empty($order_data[0]['supplier_id']))
                        @php
                        $supplier_id = $order_data[0]['supplier_id'];
                        @endphp
                        @endif
                    @endif
                    <input name="supplier_id" type="hidden" value="{{$supplier_id}}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="position-relative form-group">
                    @php
                    $datetime = "";
                    @endphp
                    @if(!empty($order_data))
                        @if(!empty($order_data[0]['datetime']))
                        @php
                        $datetime = date('d-m-Y', strtotime($order_data[0]['datetime']));
                        @endphp
                        @endif
                    @endif
                    <input name="datetime" placeholder="Order Date" type="text" class="form-control" value="{{$datetime}}" readonly="readonly">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="position-relative form-group">
                    @php
                    $deliverydate = "";
                    @endphp
                    @if(!empty($order_data))
                        @if(!empty($order_data[0]['deliverydate']))
                        @php
                        $deliverydate = date('d-m-Y', strtotime($order_data[0]['deliverydate']));
                        @endphp
                        @endif
                    @endif
                    <input name="deliverydate" placeholder="Estimed Delivery Date" type="text" class="form-control" value="{{$deliverydate}}" readonly="readonly">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Quantity Confirmed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($order_detail_data))
                            @foreach($order_detail_data as $data)
                            <tr>
                                <td>{{$data->pmpno}}</td>
                                <td>{{$data->part_name}}</td>
                                <td>{{$data->qty}}</td>
                                <td>
                                    <input name="approved_warehouse_id[]" type="hidden" class="form-control" value="{{$data->warehouse_id}}">
                                    <input name="approved_product_id[]" type="hidden" class="form-control" value="{{$data->product_id}}">
                                    <input name="qty[]" type="hidden" class="form-control" value="{{$data->qty}}">
                                    <input name="approved_qty[]" placeholder="Enter Approved Qty" type="number" class="form-control" value="{{$data->qty}}">
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    <br>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}