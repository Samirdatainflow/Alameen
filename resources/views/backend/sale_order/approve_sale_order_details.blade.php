<div style="overflow-x: scroll; margin-bottom: 20px;">
@php
if(!empty($reject_reason)) {
@endphp
<h5>Reject Reason</h5>
<p>{{$reject_reason}}</p>
@php
}
@endphp
<table class="table table-bordered" id="orderDetails">
	<thead>
		<tr>
			<th>Order Line ID</th>
			<th>Product Name</th>
			<th>Part No</th>
			<th>Price</th>
			<th>Min Price</th>
			<th>Max Price</th>
			<th>Qty</th>
			<th>Allocated Qty</th>
		</tr>
	</thead>
	<tbody>
		@php
		$sl =1;
		$r=0;
		@endphp
		@foreach($products as $product)
		<tr>
			<td>{{$sl}}</td>
			<td>{{$product['part_name']}}</td>
			<td>{{$product['pmpno']}}</td>
			<td>
				<span id="showPrice{{$sl}}">{{$product['product_price']}}</span>
			</td>
			<td>{{$product['min_price']}}</td>
			<td>{{$product['max_price']}}</td>
			<td>
				{{$product['qty']}}
				<input type="hidden" class="pmpno{{$r}}" name="pmpno" value="{{$product['pmpno']}}">
				<input type="hidden" class="qty{{$r}}" name="qty" value="{{$product['qty']}}">
				<input type="hidden" class="current_stock{{$r}}" name="current_stock" value="{{$product['available_stock']}}">
				
			</td>
			<td>
			    <span id="showQty{{$sl}}">{{$product['qty_appr']}}</span>
			 <!--   @if($packingStatus != "1" && $is_approved == 1)-->
				<!--<a href="javascript:void(0)" class="approved-quantity-update-form" data-sl="{{$sl}}" data-current_stock="{{$product['available_stock']}}" data-sale_order_details_id="{{$product['sale_order_details_id']}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>-->
				<!--@endif-->
			 </td>
		</tr>
		@php
		$sl++;
		$r++;
		@endphp
		@endforeach
	</tbody>
</table>
</div>
@php
if($ordersatatus == "noStockOrder") {
@endphp
<div class="col-md-12 text-right">
	<input type="hidden" id="sale_order_id" value="{{$sale_order_id}}">
	<button type="button" class="btn-shadow btn btn-info" id="CreateOrder"><i class="fa fa-check"></i> Create Order</button>
	<br/><br/>
</div>
@php
}
@endphp