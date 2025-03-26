<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="hidden_id" id="hidden_id" value="{{$order_id}}">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Price</th>
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
                        <td><input type="text" class="form-control" value="{{$data['price']}}" readonly></td>
                        <td><input type="number" class="form-control bad-quantity" name="bad_quantity[]" value="{{$data['return_quantity']}}" readonly></td>
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
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <input type="file" name="return_files[]" id="return_files" class="file-upload-default" accept=".jpg, .png, .pdf" multiple="" />
            <div class="input-group col-xs-12">
                <input type="text" class="form-control file-upload-info" id="return_files" disabled placeholder="Upload Files" />
                <span class="input-group-append">
                    <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                </span>
            </div>
        </div>
        <button type="button" class="btn-shadow btn btn-info" onclick="UploadFiles()"> Upload </button>
    </div>
</div>
<br>
<p class="text-right">
    <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>