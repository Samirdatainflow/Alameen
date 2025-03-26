{{ Form::open(array('id'=>'BinningTaskForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Order ID</label>
                <select class="form-control" id="order_id" name="order_id">
                    <option value="">Select</option>
                    @php
                    if(!empty($listOrderIds)) {
                        foreach($listOrderIds as $data) {
                        @endphp
                        <option value="{{$data->order_id}}">{{$data->order_id}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>User</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option value="">Select</option>
                    @php
                    if(!empty($Users)) {
                        foreach($Users as $data) {
                        @endphp
                        <option value="{{$data['user_id']}}">{{$data['first_name']}} {{$data['last_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="submit" class="btn-shadow btn btn-info"> Save </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}