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
                            <td><input type="hidden" name="product_id[]" value="{{$data['product_id']}}"><input type="text" class="form-control" value="{{$data['part_name']}}" readonly></td>
                            <td><input type="text" class="form-control" value="{{$data['pmpno']}}" readonly></td>
                            <td><input type="text" class="form-control" value="{{$data['price']}}" readonly></td>
                            <td><input type="number" class="form-control quantity" name="quantity" value="{{$data['quantity']}}" readonly></td>
                            <td><input type="number" class="form-control good-quantity" name="good_quantity[]" value="{{$data['good_quantity']}}"></td>
                            <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control shortage-quantity" name="shortage_quantity[]" value="{{$data['shortage_quantity']}}"></td>
                            <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control excess-quantity" name="excess_quantity[]" value="{{$data['excess_quantity']}}"></td>
                            <td><input type="number" class="form-control bad-quantity" name="bad_quantity[]" value="{{$data['bad_quantity']}}">
                                <input type="hidden" class="form-control supplier-id" name="supplier_id[]" value="{{$data['supplier_id']}}"></td>
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
        <button type="submit" class="btn-shadow btn btn-info"> Update </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}