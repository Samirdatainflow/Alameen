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
                </tr>
            </thead>
            <tbody id="entryProductTbody">
                @php
                $i = 1;
                if(!empty($PackingDetails)) {
                    foreach($PackingDetails as $data) {
                    @endphp
                    <tr>
                        <td>{{$i}}</td>
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
        <br>
        <p class="text-right">
            <input type="hidden" name="sales_order_id" id="sales_order_id">
            <button type="button" aria-haspopup="true" id="Export_Details" aria-expanded="false" class="btn-shadow btn btn-info export-excel" onclick="ExportDetailsTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>
            <button type="button" class="btn-shadow btn btn-info print-packing-slip"> Print Slip </button>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
    </div>
</div>