{{ Form::open(array('id'=>'userPurchaseOrderForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select id="purchase_order_id" class="form-control">
                    <option value=""> Select Purchase Order ID</option>
                    @php
                    if(!empty($listOrderNos)) {
                        foreach($listOrderNos as $list) {
                    @endphp
                    <option value="{{$list->order_id}}">{{$list->order_id}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" id="get_purchase_order_details" class="btn-shadow btn btn-info" value="Submit"> Fetch Order </button>
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
                        <th>Product Name</th>
                        <th>Part No</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody"></tbody>
            </table>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" class="btn-shadow btn btn-cancel"> Close </button>
    </p>
{{ Form::close() }}