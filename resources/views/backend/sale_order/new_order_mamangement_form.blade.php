{{ Form::open(array('id'=>'CreateNewOrderForm')) }}
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  	<div class="container">
	                    <div class="row">
	                      <div class="col-md-5">
	                        <?php
	                        if(!isset($_GET['cart']))
	                        {
	                        ?>
	                        <button class="btn-shadow btn btn-info" type="button" id="download_template">Download Template</button>
	                        <!-- <button class="btn btn-primary bg-blue" type="button" id="product_list_download">Download Item</button> -->
	                        <?php
	                            }
	                        ?>
	                      </div>
	                    </div>
	                    <br>

	                    <div class="row mt-20" >
                        @php
                        $hidden_sale_order_id = "";
                        if(!empty($SaleOrder)) {
                          if(!empty($SaleOrder[0]['sale_order_id'])) $hidden_sale_order_id = $SaleOrder[0]['sale_order_id'];
                        }
                        @endphp
	                      <input type="hidden" name="hidden_sale_order_id" id="hidden_sale_order_id" value="{{$hidden_sale_order_id}}">
	                        <div class="col-md-3">
	                        	<label>Customer</label>
		                        <select class="form-control selectpicker" data-live-search="true" name="client" id="client" required="">
		                            <option value="" selected="" disabled="">Select Customer</option>
	                            	@php
				                    if(!empty($customer_id)) {
				                        foreach($customer_id as $urData){
                                  $sel = "";
                                  if(!empty($SaleOrder)) {
                                    if(!empty($SaleOrder[0]['client_id'])) {
                                      if($SaleOrder[0]['client_id'] == $urData['client_id']) $sel='selected="selected"';
                                    }
                                  }
				                    @endphp
	                                <option value="{{$urData['client_id']}}" {{$sel}}>{{ $urData['customer_name']}}</option>
	                                @php
				                        }
				                    }
				                    @endphp
		                        </select>
	                        </div>
	                      <!-- <div class="col-md-3" >
	                        <select class="form-control">
	                          <option>Select Category</option>
	                          <option>a</option>
	                          <option>a</option>
	                        </select>
	                      </div> -->
	                    </div> 
	                    <br>
                  	</div>
                  </div>
                  
                    <div class="row">
                      <div class="col-md-12 mt-20">
                        <table class="table table-dark display nowrap" id="new_order" style="width:100%">
                          <thead>
                            <tr>
                              <th style="width:230px">Part No.</th>
                              <th style="width:230px">Name</th>
                              <th style="width: 150px;">Category</th>
                              <th style="width: 130px;">Price</th>
                              <th style="display: none">Stock</th>
                              <th style="width:104px">Quantity</th>
                              <!--<th style="width: 90px;">VAT</th>-->
                              <th>Total</th>
                              <th>&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody id="order_row">
                            @php
                            $tax_rate = 0;
                            if(!empty($gst_value)) {
                              if(!empty($gst_value[0]['tax_rate'])) $tax_rate = $gst_value[0]['tax_rate'];
                            }
                            @endphp
                            <input type="hidden" name="hidden_tax_rate" id="hidden_tax_rate" value="{{$tax_rate}}">
                            <?php
                            $sub_total=0.00;
                            $total_tax=0.00;
                            $grand_total=0.00;
                            //print_r($cart_datas); exit();
                            if(sizeof($cart_datas)>0)
                            {
                              foreach ($cart_datas as $cart_data) {
                                //echo $cart_data['product_id']; exit();
                              $grand_total +=$cart_data['pmrprc']*$cart_data['qty'];
                              //$total_tax +=0;
                              //$grand_total +=round(($sub_total+$total_tax), 3);
                            ?>
                            <tr>
                              <td>
                                <input type="hidden" name="product_id[]" class="form-control product_id" value="{{$cart_data['product_id']}}"><input type="text" name="part_no[]" class="form-control part_no" value="{{$cart_data['part_no']}}">
                                {{-- <ul>
                                  <li>ALTERNATOR-REB</li>
                                  <li>ALTERNATOR</li>
                                </ul> --}}
                              </td>
                              <td><input type="text" readonly="readonly" name="name[]" class="form-control name" value="{{$cart_data['part_name']}}"></td>
                                <td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name" value="{{$cart_data['c_name']}}"><input type="hidden" name="category_id[]" class="form-control category_id" value="{{$cart_data['ct']}}"></td>
                                <td><input type="text" name="mrp[]" class="form-control mrp" value="{{$cart_data['pmrprc']}}"></td>
                                <td class="stock" style="display: none">{{$cart_data['current_stock']}}</td>
                                <td><input type="number" min="1" max="1000" name="qty[]" class="form-control qty" value="{{$cart_data['qty']}}"></td>
                                <!--<td><input type="text" readonly="readonly" name="gst[]" class="form-control gst" value="0"></td>-->
                                <td class="row_total"></td>
                                <td><button type="button" class="btn-shadow btn btn-info new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn-shadow btn btn-info trash" title="Trash"><i class="fa fa-trash"></i></button></td>
                            </tr>
                            <?php
                                  }
                                  $grand_total = round($grand_total,3);
                                }
                                else if(!empty($SaleOrderDetails)) {
                                  foreach($SaleOrderDetails as $sdetails) {
                                      $netTotal = $sdetails['pmrprc']*$sdetails['qty'];
                                      $netTotal = round($netTotal,3);
                                      $sub_total +=$sdetails['pmrprc']*$sdetails['qty'];
                                    $grand_total +=$sdetails['pmrprc']*$sdetails['qty'];
                                    // $total_tax +=0;
                                    // $grand_total +=round(($sub_total+$total_tax), 3);
                                ?>
                                <tr>
                                  <td>
                                    <input type="hidden" name="product_id[]" class="form-control product_id" value="{{$sdetails['product_id']}}"><input type="text" name="part_no[]" class="form-control part_no" value="{{$sdetails['part_no']}}">
                                  </td>
                                  <td><input type="text" readonly="readonly" name="name[]" class="form-control name" value="{{$sdetails['part_name']}}"></td>
                                    <td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name" value="{{$sdetails['c_name']}}"><input type="hidden" name="category_id[]" class="form-control category_id" value="{{$sdetails['ct']}}"></td>
                                    <td><input type="text" name="mrp[]" class="form-control mrp" value="{{$sdetails['pmrprc']}}"></td>
                                    <td class="stock" style="display: none">{{$sdetails['current_stock']}}</td>
                                    <td><input type="number" min="1" maxlength="1000" name="qty[]" class="form-control qty" value="{{$sdetails['qty']}}"></td>
                                    <!--<td><input type="text" readonly="readonly" name="gst[]" class="form-control gst" value="0"></td>-->
                                    <td class="row_total"><?=$netTotal?></td>
                                    <td><button type="button" class="btn-shadow btn btn-info new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn-shadow btn btn-info trash" title="Trash"><i class="fa fa-trash"></i></button></td>
                                </tr>
                                <?php
                                  }
                                  $grand_total = round($grand_total,3);
                                }
                                else
                                {
                            ?>
                            <tr>
                              <td>
                                <input type="hidden" name="product_id[]" class="form-control product_id"><input type="text" name="part_no[]" class="form-control part_no" autocomplete="off">
                                {{-- <ul class="list-group">
                                      <li class="list-group-item"><a href="#" style="text-decoration: none"> ALTERNATOR-REB</a></li>
                                      <li class="list-group-item">ALTERNATOR</li>
                                    </ul> --}}
                              </td>
                              <td><input type="text" readonly="readonly" name="name[]" class="form-control name"></td>
                              <td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name"><input type="hidden" name="category_id[]" class="form-control category_id"></td>
                              <td><input type="text" name="mrp[]" class="form-control mrp">
                              <div class="pre-price-details"></div>
                              <input type="hidden" class="last-lp-price">
                              <input type="hidden" class="hidden-lc-price">
                              	<!-- <p class="low_high"></p> -->
                              </td>
                              <td class="stock" style="display: none"></td>
                              <td><input type="number" min="1" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="1000" name="qty[]" class="form-control qty">
                                <div class="avl-qty-details"></div>
                              </td>
                              <!--<td><input type="text" readonly="readonly" name="gst[]" class="form-control gst"></td>-->
                              <td class="row_total"></td>
                              <td><button type="button" class="btn-shadow btn btn-info new_row" title="Add New Row"><i class="fa fa-plus"></i></button> 
                              	<!-- <button type="button" class="btn-shadow btn btn-info trash" title="Trash"><i class="fa fa-trash"></i></button> -->
                              </td>
                            </tr>
                            <?php
                                }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <?php
                        if(!isset($_GET['cart']))
                        {
                    ?>
                    <div class="row">
                      <div class="col-md-6">
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
                          <button class="btn-shadow btn btn-info preview-multiple-order" id="preview" type="button"> Preview </button>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div id="displayMessage"></div>
                      </div>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="row">
                      <div class="col-md-6">
                          <label>Remarks</label>
                          <textarea class="form-control" rows="4" name="remarks"></textarea>
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
                                    <td><span id="sub-total1">{{$sub_total}}</span><input type="hidden" id="sub-total" name="sub_total" value="{{$sub_total}}" style="border: 0px;background-color: transparent;"></td>
                                </tr>
                                <tr>
                                    @php
                                    $gst = 0;
                                    if(!empty($SaleOrder)) {
                                    
                                        if(!empty($SaleOrder[0]['gst'])) {
                                        
                                            $gst = $SaleOrder[0]['gst'];
                                            
                                            if(is_numeric($gst)) {
                                                $grand_total += $gst;
                                            }
                                        }
                                    }
                                    @endphp
                                    <td>Total VAT</td>
                                    <td>
                                        <span id="tax1">{{$gst}}</span><input type="hidden" id="tax" name="tax" value="{{$gst}}" style="border: 0px;background-color: transparent;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Grand Total</td>
                                    <td id="randm"><span id="expertSubTotalWithTax1">{{$grand_total}}</span><input type="hidden" name="expertSubTotalWithTax" id="expertSubTotalWithTax" value="{{$grand_total}}" style="border: 0px;background-color: transparent;"></td>
                                </tr>
                                  
                            </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-12 text-right">
                            <?php
                            if(!empty($SaleOrderDetails)) {
                            ?>
                            <button type="button" class="btn-shadow btn btn-info" id="UpdateOrder"><i class="fa fa-check"></i> Update Order</button>
                            <?php }else { ?>
                            <button type="button" class="btn-shadow btn btn-info" id="CreateOrder"><i class="fa fa-check"></i> Create Order</button>
                            <?php } ?>
                        <!--<button type="button" class="btn-shadow btn btn-info" id="SaveOrder"><i class="fa fa-check"></i> Save Order</button>-->
                        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
                      </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
 {{ Form::close() }}