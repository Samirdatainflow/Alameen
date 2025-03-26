{{ Form::open(array('id'=>'CreateNewPayment')) }}
    <div class="row">
        @php
        $hidden_sale_order_id = "";
        if(!empty($SaleOrder)) {
          if(!empty($SaleOrder[0]['sale_order_id'])) $hidden_sale_order_id = $SaleOrder[0]['sale_order_id'];
        }
        @endphp
        <input type="hidden" name="hidden_sale_order_id" id="hidden_sale_order_id" value="{{$hidden_sale_order_id}}">
       
        <div class="col-md-4">
            <select class="form-control selectpicker" data-live-search="true" name="supplier_id" id="supplier_id" required="">
                <option value="" selected="" disabled="">Select Supplier *</option>
            	@php
                if(!empty($SupplierData)) {
                    foreach($SupplierData as $supp){
                    $sel = "";
                    if(!empty($SaleOrder)) {
                        if(!empty($SaleOrder[0]['supplier_id'])) {
                          if($SaleOrder[0]['supplier_id'] == $supp['supplier_id']) $sel='selected="selected"';
                        }
                    }
                @endphp
                <option value="{{$supp['supplier_id']}}" {{$sel}}>{{ $supp['full_name']}}</option>
                @php
                    }
                }
                @endphp
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" id="get_customer_invoice_details" class="btn-shadow btn btn-info valid" value="Submit" aria-invalid="false"> Load </button>
        </div>
        <div class="col-md-3 hide-amount-field" style="display:none;position: relative;top: -24px;">
            <lable>Total Invoice Due Amount</lable>
            <input type="number" class="form-control" id="outstanding_total_invoice_amount" name="outstanding_total_invoice_amount" readonly>
        </div>
        <div class="col-md-3 hide-amount-field" style="display:none;position: relative;top: -24px;">
            <lable>Enter Payment Amount</lable>
            <input type="number" class="form-control" id="outstanding_balance_amount" name="outstanding_balance_amount" placeholder="Enter Amount">
        </div>
    </div>
    <div class="row">
        <p>&nbsp;</p>
    </div>
    <div class="row" style="display:block" id="customerInvoices">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Invoice Date </th>
                        <th>Invoice Number</th>
                        <th>Invoice Amount</th>
                        <th>Due Amount</th>
                        <th>Pay</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody"></tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <p>&nbsp;</p>
    </div>
    <div class="row hide-section" style="display:none">
        <div class="col-md-3">
        	<label>Pay Mode *</label>
            <select class="form-control" name="payment_mode" id="payment_mode" required="">
                <option value="" selected="" disabled="">Select Payment Mode *</option>
            	@php
            	$PaymentModeArray = ['Cash' => 'Cash', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer', 'Online Payment' => 'Online Payment'];
            	
                foreach($PaymentModeArray as $k=>$v){
                    $sel = "";
                    
                @endphp
                <option value="{{$v}}" {{$sel}}>{{ $k}}</option>
                @php
                }
                @endphp
            </select>
        </div>
        <div class="col-md-3">
        	<label>Reference Number *</label>
            <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="Reference Number" autocomplete="off">
        </div>
        <div class="col-md-3">
        	<label>Payment Date *</label>
            <input type="text" class="form-control datepicker" name="payment_date" id="payment_date" placeholder="YY/MM/DD" autocomplete="off">
        </div>
        <div class="col-md-3">
        	<label>Remarks </label>
        	<textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks"></textarea>
        </div>
    </div>
    <div class="row hide-section" style="display:none">
        <p>&nbsp;</p>
        <div class="col-md-12 text-right">
            <button type="submit" class="btn-shadow btn btn-info" id="UpdateOrder"><i class="fa fa-check"></i> Save</button>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </div>
        <p>&nbsp;</p>
    </div>
 {{ Form::close() }}