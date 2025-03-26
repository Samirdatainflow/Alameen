{{ Form::open(array('id'=>'userPurchaseOrderForm')) }}
    @php
    $display = 'display:flex';
    if(!empty($hidden_order_id)) {
        $display = 'display:none';
    }
    @endphp
    <div class="row" style="{{$display}}">
        <input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id">
        <div class="col-md-4">
            <label class="">Get Order From Order Request</label><br/>
            <label class="radio-inline">
                <input type="radio" class="order-request-radio" name="order_request_radio" value="yes"> Yes
            </label>
            <label class="radio-inline">
                <input type="radio" class="order-request-radio" name="order_request_radio" value="no" checked="true"> No
            </label>
        </div>
        <div class="col-md-4" id="fetchCartOrder">
            <label class="">Get Order From Cart</label><br/>
            <label class="radio-inline">
                <input type="radio" class="order-from-cart" name="order_from_cart" value="yes"> Yes
            </label>
            <label class="radio-inline">
                <input type="radio" class="order-from-cart" name="order_from_cart" value="no" checked="true"> No
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                @php
                $hidden_order_id2 = '';
                if(!empty($hidden_order_id)) {
                    $hidden_order_id2 = $hidden_order_id;
                }
                $estimated_delivery_date = '';
                $disabled = '';
                if(!empty($Orders)) {
                    $disabled = 'disabled';
                    if(!empty($Orders[0]['deliverydate'])) $estimated_delivery_date = date('d/m/Y', strtotime($Orders[0]['deliverydate']));
                }
                @endphp
                <input type="hidden" name="hidden_order_id" value="{{$hidden_order_id2}}">
                <input name="estimated_delivery_date" id="estimated_delivery_date" placeholder="Enter Estimated Delivery Date*" type="text" class="form-control datetimepicker" autocomplete="off" value="{{$estimated_delivery_date}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" name="warehouse" id="warehouse" {{$disabled}}>
                    <option value=""> Select Warehouse*</option>
                   @php
                      if(!empty($warehouses_data)) {
                        foreach($warehouses_data as $data) {
                            $sel = '';
                            if(!empty($Orders)) {
                                if(!empty($Orders[0]['warehouse_id'])) {
                                    if($Orders[0]['warehouse_id'] == $data['warehouse_id']) $sel = 'selected="selected"';
                                }
                            }
                    @endphp
                    <option value="{{$data['warehouse_id']}}" {{$sel}}>{{$data['name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" name="supplier" id="supplier" {{$disabled}}>
                    <option value=""> Select Supplier*</option>
                   @php
                      if(!empty($supplier_data)) {
                        foreach($supplier_data as $data) {
                            $sel = '';
                            if(!empty($Orders)) {
                                if(!empty($Orders[0]['supplier_id'])) {
                                    if($Orders[0]['supplier_id'] == $data['supplier_id']) $sel = 'selected="selected"';
                                }
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
    <div class="row" id="OrderRequestSection" style="display: none;">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <input type="text" id="order_request_id" name="order_request_id" class="form-control" placeholder="Enter Order Request ID">
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" id="get_order_request_id" class="btn-shadow btn btn-info" value="Submit"> Pre Order </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @php
            $tax_rate = 0;
            if(!empty($gst_value)) {
              if(!empty($gst_value[0]['tax_rate'])) $tax_rate = $gst_value[0]['tax_rate'];
            }
            @endphp
            <input type="hidden" name="hidden_tax_rate" id="hidden_tax_rate" value="{{$tax_rate}}">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Part No</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Supplier Price</th>
                        <!--<th>VAT</th>-->
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                    
                    @php
                    $flag = 1;
                    $product_entry_count = 1;
                    $remove = "";
                    if(!empty($OrderDetailArray)) {
                        foreach($OrderDetailArray as $od) {
                            $total_price = round($od['qty'] * $od['pmrprc'], 2);
                    @endphp
                            <tr id="entryProductRow{{$flag}}">
                                <td>
                                    <input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off" value="{{$od['pmpno']}}">
                                </td>
                                <td>
                                    <input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly" value="{{$od['part_name']}}">
                                    <input type="hidden" class="form-control entry-product-id" name="entry_product[]" value="{{$od['product_id']}}">
                                </td>
                                <td>
                                    <input type="text" class="form-control category_name" name="category_name[]" readonly="readonly" value="{{$od['c_name']}}">
                                    <input type="hidden" class="form-control category_id" name="category_id[]" value="{{$od['ct']}}">
                                </td>
                                <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control entry-product-quantity" name="entry_product_quantity[]" value="{{$od['qty']}}"></td>
                                <td><input type="text" class="form-control mrp" name="mrp[]" value="{{$od['pmrprc']}}"><span class="viewMRP"></span><span class="viewLPP"></span></td>
                                <!--<td><input type="text" class="form-control gst" name="gst[]" readonly="readonly" value="{{$od['gst']}}"></td>-->
                                <td class="total_price">{{$total_price}}</td>
                                <td style="width: 12%;">
                                    <a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
                                    @php
                                    if($flag > 1) {
                                    @endphp
                                        <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry{{$flag}}"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                                    @php
                                    }
                                    @endphp
                                </td>
                            </tr>
                    @php
                    $flag++;
                    }
                    }else {
                    @endphp
                    <tr id="entryProductRow1">
                        <td>
                            <input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly">
                            <input type="hidden" class="form-control entry-product-id" name="entry_product[]">
                        </td>
                        <td>
                            <input type="text" class="form-control category_name" name="category_name[]" readonly="readonly">
                            <input type="hidden" class="form-control category_id" name="category_id[]">
                        </td>
                        <td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td>
                        <td><input type="text" class="form-control mrp" name="mrp[]"><input type="hidden" class="form-control previous_lc_price" name="previous_lc_price[]"><span class="viewMRP"></span><br/><span class="viewLPP"></span></td>
                        <!--<td><input type="text" class="form-control gst" name="gst[]" readonly="readonly"></td>-->
                        <td class="total_price"></td>
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
                <input type="hidden" id="product_entry_count" value="{{$flag}}">
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
            <div class="form-group">
                <label>Bulk Order Create</label>
                <input type="file" id="product_csv" name="product_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_csv" name="product_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-order" type="button"> Preview </button>
            </div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    @php
                    $expenses_entry_count = 1;
                    @endphp
                    <button class="btn-shadow btn btn-info" id="add_expenses" type="button"> <i class="fa fa-plus" aria-hidden="true"></i> Add Expenses </button>
                    <table style="width: 100%;" class="" border="0">
                        <tbody id="entryExpensesTbody">
                            <tr id="ListExpensesEntry" style="display: none">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <input type="hidden" id="expenses_entry_count" value="{{$expenses_entry_count}}">
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks"></textarea>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Select VAT *</label>
                <select class="form-control" name="vat_type_value" id="vat_type_value">
                    <option value="" data-description="Total Tax" data-percentage="">Select</option>
                    @php
                    if(!empty($VatTypeData)) {
                        foreach($VatTypeData as $vattype) {
                        
                        $sel = '';
                        if(!empty($SaleOrder)) {
                            if($SaleOrder[0]['vat_type_id'] == $vattype['vat_type_id']) $sel = 'selected="selected"';
                        }
                        @endphp
                        <option value="{{$vattype['vat_type_id']}}" data-percentage="{{$vattype['percentage']}}" data-description="{{$vattype['description']}}" {{$sel}}>{{$vattype['description']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
            <label>Summary</label>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td>Sub-total</td>
                        <td>
                            <span id="sub_total_show"></span>
                            <input type="hidden" id="sub-total" name="sub_total" style="border: 0px;background-color: transparent;">
                        </td>
                    </tr>
                    <tr>
                        <td>Total Tax</td>
                        <td>
                            <span id="total_tax_show"></span>
                            <input type="hidden" id="tax" name="total_tax" style="border: 0px;background-color: transparent;">
                        </td>
                    </tr>
                    <tr>
                        <td>Total Expenses</td>
                        <td>
                            <span id="total_expense_show"></span>
                            <input type="hidden" id="total_expense" name="total_expense" style="border: 0px;background-color: transparent;">
                        </td>
                    </tr>
                    <tr>
                        <td>Grand Total</td>
                        <td id="randm">
                            <span id="grand_total_show"></span>
                            <input type="hidden" name="grand_total" id="grand_total" style="border: 0px;background-color: transparent;">
                        </td>
                    </tr>
                </tbody>
            </table>
          </div>
    </div>
    <br>
    <p class="text-right">
        <button type="button" name="submit" class="btn-shadow btn btn-info" value="CreateOrder" id="CreateOrder"> Create Purchase Order </button>
        <button type="button" name="submit" class="btn-shadow btn btn-info" value="SaveOrder" id="SaveOrder"> Save Purchase Order</button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}