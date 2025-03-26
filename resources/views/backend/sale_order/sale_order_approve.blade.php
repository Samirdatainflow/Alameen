{{Form::open(array("id"=>'sale_order_approve'))}}
<input type="hidden" name="sale_order_id" id="sale_order_id">
<input type="hidden" name="product_entry_count" id="product_entry_count" value="<?=sizeof($products)+1;?>">
<table class="table table-bordered" id="approve_table">
	<thead>
		<tr>
		    <th></th>
			<th>Product Name</th>
			<th>Part No</th>
			<th>Order Line ID</th>
			<th>Price</th>
			<th>Available Qty</th>
			<th>Qty Ordered</th>
			<th>Allocated Qty</th>
			<th></th>
		</tr>
	</thead>
	<tbody id="ListProductEntry">
		@php
		$i=0;
		foreach($products as $product) {
		$style='';
		
		if($product['qty'] > $product['available_stock']) {
		    $style= 'style=background:#ffeb3b;';
		}
		
		if($product['available_stock'] == 0) {
		    $style= 'style=background:#ff0000;color:#fff';
		}
		
		@endphp
		<tr id="orderDetailsTr{{$i}}" {{$style}}>
			<input type="hidden" name="sale_order_details_id[]" value="{{$product['sale_order_details_id']}}">
			<td>
			    <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="check{{$product['sale_order_details_id']}}" name="lineItemCheck[]" value="{{$i}}" checked>
                </div>
			</td>
			<td>{{$product['part_name']}}</td>
			<td>{{$product['pmpno']}}</td>
			<td>{{$product['order_line_no']}}</td>
			<td><input type="text" class="form-control" name="product_price[]" onkeyup="this.value=this.value.replace(/[^0-9.]/g, '')" style="width: 100px" value="{{$product['product_price']}}"></td>
			<td>{{$product['available_stock']}} <input type="hidden" name="current_stock[]" class="current-stock" value="{{$product['available_stock']}}"></td>
			<td>{{$product['qty']}} <input type="hidden" name="qty_ordered[]"  value="{{$product['qty']}}"></td>
			<td><input type="hidden" class="form-control product_id" name="product_id[]" value="{{$product['product_id']}}"><input type="hidden" class="form-control prev_qty" value="{{$product['qty']}}" name="prev_qty[]"><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control appr_qty" name="approve_qty[]" value="{{$product['apv_qty']}}" style="width: 100px"></td>
			<td><a href="javascript:void(0)" class="delete-sale-order-details" data-line-no="{{$i}}" data-sale_order_id="{{$product['sale_order_id']}}" data-id="{{$product['sale_order_details_id']}}"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a></td>
		</tr>
		@php
		$i++;
		}
		@endphp
	</tbody>
</table>
<p class="text-right"><button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Approve </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button></p>
{{Form::close()}}