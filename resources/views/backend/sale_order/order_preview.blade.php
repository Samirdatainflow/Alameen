
<div class="row">
    <div class="col-md-12">
      <table class="table table-dark" style="width:100%">
        <thead>
          <tr>
            <th >Part No.</th>
            <th >Name</th>
            <th>Category</th>
            <th>MRP</th>
            <th>Stock</th>
            <th>Quantity</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
        	@foreach($products as $product)
          @if(sizeof($product)>0 && isset($product['part_no']))
          <tr>
            <td>{{$product['part_no']}}</td>
            <td>{{$product['part_name']}}</td>
            <td>{{$product['c_name']}}</td>
            <td>{{$product['selling_price']}}</td>
            <td>{{$product['current_stock']}}</td>
            <td>{{$product['qty']}}</td>
            <td>{{($product['qty']*$product['selling_price'])}}</td>
          </tr>
          @else
          <tr style="background-color: red">
            <td>{{$product['product_id']}}</td>
            <td colspan="7" align="center">{{$product['qty']}}</td>
          </tr>
          @endif
          @endforeach
        </tbody>
      </table>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Select VAT *</label>
            <select class="form-control" name="vat_type_value2" id="vat_type_value2">
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
    </div>
</div>
   <br>
  <p class="text-right">
      <button type="button" name="submit" class="btn-shadow btn btn-info create_mutiple_order_csv"> Create Order </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
  </p>
