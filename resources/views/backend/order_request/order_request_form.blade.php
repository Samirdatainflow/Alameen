<style>
    .list-group-item:hover, .list-group-item:focus {
        z-index: 1;
        text-decoration: none;
        background-color: aliceblue !important;
    }
    .list-group *:focus {
       color: #ff606d !important;
    }
</style>
{{ Form::open(array('id'=>'OrderRequestForm')) }}
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                @php
                $hidden_order_request_unique_id = "";
                if(!empty($order_request_unique_id)) {
                    $hidden_order_request_unique_id = $order_request_unique_id;
                }
                @endphp
                <input type="hidden" name="hidden_order_request_unique_id" value="{{$hidden_order_request_unique_id}}">
                <select class="form-control selectpicker" name="supplier[]" id="supplier" multiple data-live-search="true" title="Select Supplier*">
                   @php
                    //print_r($requestSupplier);
                    if(!empty($supplier_data)) {
                        foreach($supplier_data as $data) {
                            $sel = "";
                            if(!empty($requestSupplier)) {
                                if(in_array($data['supplier_id'], $requestSupplier)) $sel = 'selected="selected"';
                            }
                    @endphp
                    <option value="{{$data['supplier_id']}}" {{$sel}}>{{$data['full_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Part No</th>
                        <th>Part Brand</th>
                        <th>Part Name</th>
                        <th>Unit</th>
                        <th>Manufacture No</th>
                        <th>Quantity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                    @php
                    $product_entry_count = 1;
                    if(!empty($OrderRequestDetails)) {
                        $product_entry_count = sizeof($OrderRequestDetails);
                        foreach($OrderRequestDetails as $details) {
                    @endphp
                    <tr id="entryProductRow1">
                        <td>
                            <input type="text" class="form-control entry-part-no" name="entry_part_no[]" value="{{$details['part_no']}}" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-part-brand" name="" autocomplete="off" value="{{$details['part_brand_name']}}" readonly="readonly">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-product-name" name="entry_product_name[]" value="{{$details['part_name']}}" readonly="readonly">
                            <input type="hidden" class="form-control entry-product-id" name="entry_product[]" value="{{$details['product_id']}}">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-unit" name="" autocomplete="off" value="{{$details['unit_name']}}" readonly="readonly">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-manufacture-no" name="" autocomplete="off" value="{{$details['manufacturing_no']}}" readonly="readonly">
                        </td>
                        <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control entry-product-quantity" name="entry_product_quantity[]" value="{{$details['qty']}}"></td>
                        <td style="width: 12%;">
                            <a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
                        </td>
                    </tr>
                    @php
                        }
                    }else {
                    @endphp
                    <tr id="entryProductRow1">
                        <td>
                            <input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-part-brand" name="" autocomplete="off"  readonly="readonly">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly">
                            <input type="hidden" class="form-control entry-product-id" name="entry_product[]">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-unit" name="" autocomplete="off"  readonly="readonly">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-manufacture-no" name="" autocomplete="off"  readonly="readonly">
                        </td>
                        <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td>
                        <td style="width: 12%;">
                            <a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
                        </td>
                    </tr>
                    @php
                    }
                    @endphp
                    <tr id="ListProductEntry" style="display: none">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <input type="hidden" id="product_entry_count" value="{{$product_entry_count}}">
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
        <div class="col-md-6">
            <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Bulk Order Create</label>
                <input type="file" id="order_request_csv" name="order_request_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="order_request_csv" name="order_request_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-order" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" name="submit" class="btn-shadow btn btn-info" value="CreateOrder" id="CreateOrder"> Create Order Request </button>
        <button type="button" name="submit" class="btn-shadow btn btn-info" value="SaveOrder" id="SaveOrder"> Save Order Request </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}