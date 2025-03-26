{{ Form::open(array('id'=>'orderPriceUpdateForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Product Price *</label>
                @php
                $product_price = "";
                if(!empty($SaleOrderDetails[0]['product_price']))  {
                    $product_price = $SaleOrderDetails[0]['product_price'];
                }
                $hidden_id = "";
                if(!empty($SaleOrderDetails[0]['sale_order_details_id']))  {
                    $hidden_id = $SaleOrderDetails[0]['sale_order_details_id'];
                }
                @endphp
                <input name="product_price" id="product_price" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" placeholder="" type="text" class="form-control" value="{{$product_price}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                 <input name="sl" id="sl" type="hidden">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}