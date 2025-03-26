{{ Form::open(array('id'=>'CheckInForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <input type="hidden" name="hidden_warehouse_id" id="hidden_warehouse_id" value="">
            <div class="position-relative form-group">
                <select class="form-control" id="order_id" name="order_id">
                    <option value="">Select Order</option>
                    @php
                    if(!empty($listbiningAdvice)) {
                        foreach($listbiningAdvice as $data) {
                        @endphp
                        <option value="{{$data->order_id}}">{{$data->order_id}}</option>
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
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Good Quantity</th>
                        <th>Shortage Quantity</th>
                        <th>Excess Quantity</th>
                        <th>Bad Quantity</th>
                        <th>Lot</th>
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