{{ Form::open(array('id'=>'orderQuantityUpdateForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Quantity *</label>
                @php
                $qty = "";
                if(!empty($SaleOrderDetails[0]['qty']))  {
                    $qty = $SaleOrderDetails[0]['qty'];
                }
                $hidden_id = "";
                if(!empty($SaleOrderDetails[0]['sale_order_details_id']))  {
                    $hidden_id = $SaleOrderDetails[0]['sale_order_details_id'];
                }
                @endphp
                <input name="qty" id="qty" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" placeholder="" type="text" class="form-control" value="{{$qty}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                 <input name="current_stock" id="current_stock" type="hidden">
                 <input name="sl" id="sl" type="hidden">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}