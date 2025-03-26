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
			
			<!--@if($is_approved == "1")-->
			<!--	<th>Qty Approve</th>-->
			<!--@endif-->
			<!--<th>Location</th>-->
			<!--<th>Zone</th>-->
			<!--<th>Row</th>-->
			<!--<th>Rack</th>-->
			<!--<th>Plate</th>-->
			<!--<th>Place</th>-->
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
				<!--@if($is_approved != "1")-->
				<!--<a href="javascript:void(0)" class="order-price-update-form" data-sl="{{$sl}}" data-sale_order_details_id="{{$product['sale_order_details_id']}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>-->
				<!--@endif-->
			</td>
			<td>{{$product['min_price']}}</td>
			<td>{{$product['max_price']}}</td>
			<td>
				<span id="showQty{{$sl}}">{{$product['qty']}}</span>
				<input type="hidden" class="pmpno{{$r}}" name="pmpno" value="{{$product['pmpno']}}">
				<input type="hidden" class="qty{{$r}}" name="qty" value="{{$product['qty']}}">
				<input type="hidden" class="current_stock{{$r}}" name="current_stock" value="{{$product['current_stock']}}">
				<!--@if($is_approved != "1")-->
				<!--<a href="javascript:void(0)" class="order-quantity-update-form" data-sl="{{$sl}}" data-current_stock="{{$product['current_stock']}}" data-sale_order_details_id="{{$product['sale_order_details_id']}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>-->
				<!--@endif-->
			</td>
			
			<!--@if($is_approved == "1")-->
			<!--	<td>{{$product['qty_appr']}}</td>-->
			<!--@endif-->
			<!--<td>{{$product['location_name'] }}</td>-->
			<!--<td>{{$product['zone_name'] }}</td>-->
			<!--<td>{{$product['row_name'] }}</td>-->
			<!--<td>{{$product['rack_name'] }}</td>-->
			<!--<td>{{$product['plate_name'] }}</td>-->
			<!--<td>{{$product['place_name'] }}</td>-->
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