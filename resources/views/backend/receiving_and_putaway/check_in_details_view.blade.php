{{ Form::open(array('id'=>'CheckInForm')) }}
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="hidden_id" value="{{$order_id}}">
            <input type="hidden" name="hidden_check_in_id" value="{{$check_in_id}}">
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
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                    @php
                    $i = 1;
                    if(!empty($check_in_data)) {
                        foreach($check_in_data as $data) {
                        @endphp
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$data['part_name']}}</td>
                            <td>{{$data['pmpno']}}</td>
                            <td>{{$data['price']}}</td>
                            <td>{{$data['quantity']}}</td>
                            <td>{{$data['good_quantity']}}</td>
                            <td>{{$data['shortage_quantity']}}</td>
                            <td>{{$data['excess_quantity']}}</td>
                            <td>{{$data['bad_quantity']}}</td>
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
    <br>
    <p class="text-right">
        {{-- <button type="submit" class="btn-shadow btn btn-info"> Update </button> --}}
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}