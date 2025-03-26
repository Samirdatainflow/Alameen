<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="hidden_id" id="hidden_id" value="{{$order_id}}">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Return Quantity</th>
                </tr>
            </thead>
            <tbody id="entryProductTbody">
                @php
                $i = 1;
                if(!empty($order_return_data)) {
                    foreach($order_return_data as $data) {
                    @endphp
                    <tr>
                        <td>{{$i}}</td>
                        <td><input type="hidden" name="product_id[]" value="{{$data['product_id']}}'"><input type="text" class="form-control" value="{{$data['part_name']}}" readonly></td>
                        <td><input type="text" class="form-control" value="{{$data['pmpno']}}" readonly></td>
                        <td><input type="number" class="form-control bad_quantity" name="bad_quantity" value="{{$data['bad_quantity']}}" readonly></td>
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
</div>
<br>
<p class="text-right">
    <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>