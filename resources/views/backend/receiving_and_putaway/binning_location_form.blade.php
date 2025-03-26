{{ Form::open(array('id'=>'BinningLocationForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" id="order_id" name="order_id">
                    <option value="">Select Order</option>
                    @php
                    if(!empty($listCheckIn)) {
                        foreach($listCheckIn as $data) {
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
            <table id="biningTable" style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px">Product Name</th>
                        <th style="width: 130px">Part No</th>
                        <th style="width: 90px">Quantity</th>
                        <th>Location</th>
                        <th>Zone</th>
                        <th>Row</th>
                        <th>Rack</th>
                        <th>Level</th>
                        <th>Position</th>
                        <th>Max Capacity</th>
                        <th>Remaining Capacity</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody"></tbody>
            </table>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" class="btn-shadow btn btn-info" id="ConfirmBinning"> Confirm </button>
        <button type="button" class="btn-shadow btn btn-info" id="SaveBinning"> Save </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}