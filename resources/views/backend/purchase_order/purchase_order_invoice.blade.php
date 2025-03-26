<form id="OrderInvoiceForm">
    <input type="hidden" name="order_id" value="{{$order_id}}">
<div class="row" id="OderDetailsContent">
	<div class="col-md-12">
		<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
            	@php
            	$i=1;
                $subTotal = 0;
            	@endphp
        		@if(sizeof($order_data) > 0)
        			@foreach($order_data as $data)
        			<tr>
        				<td>{{$i}}</td>
                        <td>{{$data->part_name}}</td>
                        <td>{{$data->pmpno}}</td>
        				<td>{{$data->mrp}}</td>
        				<td>{{$data->qty}}</td>
        			</tr>
                    @php
                    $subTotal = $subTotal + ($data->mrp * $data->qty);
                    $i++;
                    @endphp
            		@endforeach
            	@endif
            </tbody>
        </table>
	</div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="row" id="ExpensesDetails">
            @php
            $grandTotal = $subTotal;
            $budget_expenses_no = 1;
            if(!empty($PurchaseOrderExpenses)) {
                $budget_expenses_no = sizeof($PurchaseOrderExpenses);
            @endphp
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4>Expenses Details</h4>
                <a href="javascript:void(0)" onclick="addExpenses()">
                    <i class="fa fa-plus-circle" style="position: absolute;left: 200px;top: 8px;font-size: 20px;"></i>
                </a>
            </div>
            @php
            $expe = 1;
            foreach($PurchaseOrderExpenses as $poe_data) {
                $grandTotal = $grandTotal + $poe_data['expenses_value'];
            @endphp
            <div class="col-md-12 col-sm-12 col-xs-12" id="ExpensesDiv<?=$expe?>">
                <div class="form-group" style="display: inline-flex;">
                    <select class="form-control expenses-id" style="margin: 0px 5px 0px 0px;" name="expenses_id[]">
                        <option value="">Select</option>
                        @php
                          foreach($ExpensesData as $edata) {
                          $sel = "";
                          if($edata['expenses_id'] == $poe_data['expenses_id']) $sel = 'selected="selected"';
                        @endphp
                        <option value="{{$edata['expenses_id']}}" {{$sel}}>{{$edata['expenses_description']}}</option>
                        @php
                        }
                        @endphp
                    </select>
                    <div class="">
                        <input type="number" class="form-control expenses-value" name="expenses_value[]" id="" placeholder="Enter value" value="{{$poe_data['expenses_value']}}">
                    </div>
                    <a href="javascript:void(0)" onclick="removeExpenses({{$expe}})">
                        <i class="fa fa-times-circle" style="position: absolute; top: 10px;padding-left: 15px;font-size: 20px"></i>
                    </a>
                </div>
            </div>
              @php
              $expe++;
              }
            }else if(!empty($ExpensesData)) {
            @endphp
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4>Expenses Details</h4>
                <a href="javascript:void(0)" onclick="addExpenses()">
                    <i class="fa fa-plus-circle" style="position: absolute;left: 200px;top: 8px;font-size: 20px;"></i>
                </a>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12" id="ExpensesDiv1">
                <div class="form-group" style="display: inline-flex;">
                    <select class="form-control expenses-id" style="margin: 0px 5px 0px 0px;" name="expenses_id[]">
                        <option value="">Select</option>
                        @php
                        foreach($ExpensesData as $edata) {
                        @endphp
                            <option value="{{$edata['expenses_id']}}">{{$edata['expenses_description']}}</option>
                            @php
                            }
                            @endphp
                    </select>
                    <div class="">
                        <input type="number" class="form-control expenses-value" name="expenses_value[]" id="" placeholder="Enter value">
                    </div>
                    <a href="javascript:void(0)" onclick="removeExpenses(1)">
                        <i class="fa fa-times-circle" style="position: absolute; top: 10px;padding-left: 15px;font-size: 20px;"></i>
                    </a>
                </div>
            </div>
            @php
            }
            @endphp
            <input type="hidden" name="" id="budget_expenses_no" value="{{$budget_expenses_no}}">
            <div id="nextExpenses"></div>
        </div>
    </div>
    <div class="col-md-6">
        <label>Summary</label>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td>Sub-total</td>
                    <td>
                        <span id="sub_total_show">{{$subTotal}}</span>
                        <input type="hidden" value="{{$subTotal}}" id="sub_total" name="sub_total" style="border: 0px;background-color: transparent;">
                    </td>
                </tr>
                <tr>
                    @php
				    $vatTitle = "";
				    $vatValue = "";
				    
				    if(isset($vat_percentage) && isset($vat_description)) {
				        
				        $vatTitle = $vat_description;
				        if($vat_percentage > 0 && $vat_percentage !== 'nill' && $vat_percentage !== 'Nill') {
				        
				            $vatValue = ($subTotal * $vat_percentage) / 100;
				            $vatValue = round($vatValue,3);
				            $grandTotal += $vatValue;
				        }else if($vat_percentage == 'nill' || $vat_percentage == 'Nill') {
				            $vatValue = "Nill";
				        }else if($vat_percentage == '0') {
				            $vatValue = "0";
				        }
				    }
				    @endphp
                    <td>{{$vatTitle}}</td>
                    <td>
                        <span id="total_tax_show">{{$vatValue}}</span>
                        <input type="hidden" id="tax" name="total_tax" style="border: 0px;background-color: transparent;">
                    </td>
                </tr>
                <tr>
                    <td>Grand Total</td>
                    <td id="randm">
                        <span id="grand_total_show">{{$grandTotal}}</span>
                        <input type="hidden" value="{{$grandTotal}}" name="grand_total" id="grand_total" style="border: 0px;background-color: transparent;">
                    </td>
                </tr>
            </tbody>
        </table>
      </div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-right">
            <a href="javascript:void(0)" class="save-purchase-order-invoice"><button type="button" class="btn-shadow btn btn-info"> <i class="fa fa-print" aria-hidden="true"></i> Save & Print </button></a>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>
</form>