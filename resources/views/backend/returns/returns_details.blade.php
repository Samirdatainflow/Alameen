<div class="row returnDetails">
    <div class="col-md-12">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Part No</th>
                    <th>Name</th>
                    <th style="width: 120px;">Invoice Qty</th>
                    <th>Received Qty</th>
                    <th>Good Qty</th>
                    <th>Bad Qty</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="entryProductRow">
                @php
                if(!empty($deliverie_details)) {
                    foreach($deliverie_details as $data) {
                @endphp
                    <tr>
                        <td> {{$data['pmpno']}} </td>
                        <td>
                            {{$data['part_name']}}
                            <input type="hidden"name="product_id[]" value="{{$data['product_id']}}">
                            <input type="hidden" class="pmrprc" name="pmrprc[]" value="{{$data['pmrprc']}}">
                        </td>
                        <td>{{$data['qty']}} <input type="hidden" class="form-control quantity" name="qty[]" value="{{$data['qty']}}"></td>
                        <td><input type="number" class="form-control received-quantity" name="received_quantity[]"></td>
                        <td><input type="number" class="form-control good-quantity" name="good_quantity[]"></td>
                        <td><input type="number" class="form-control bad-quantity" name="bad_quantity[]"></td>
                        <td><input type="text" class="form-control remarks" name="remarks[]" placeholder="Enter Remarks"></td>
                    </tr>
                @php
                    }
                }
                @endphp
            </tbody>
            <input type="hidden" id="product_entry_count" value="1">
        </table>
    </div>
</div>
<div class="row returnDetails">
    <div class="col-md-12">
        <p class="text-right">
            <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
        </p>
    </div>
</div>