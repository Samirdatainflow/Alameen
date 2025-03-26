<table class="table table-bordered">
	<thead>
		<tr>
			<th>Order Line ID</th>
			<th>Product Name</th>
			<th>Part No</th>
			<th>Price</th>
			<th>Qty</th>
			@if($is_approved == "1")
				<th>Qty Approve</th>
			@endif
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@php
		$r=1;
		@endphp
		@foreach($products as $product)
		<tr>
			<td>{{$r}}</td>
			<td>{{$product['part_name']}}</td>
			<td>{{$product['pmpno']}}</td>
			<td>{{$product['product_price']}}</td>
			<td>{{$product['qty']}}</td>
			@if($is_approved == "1")
				<td>{{$product['qty_appr']}}</td>
			@endif
			<td>
				@if($is_rejected == "0" && $is_approved == "0")
				<a data-sale-order-details-id="{{$product['sale_order_details_id']}}" data-sale-order-id="{{$product['sale_order_id']}}" data-total="{{($product['product_price']*$product['qty'])}}" href="javascript:void(0);" name="button" class="btn btn-danger delete-item"  title="Delete item"><i class="fa fa-trash" aria-hidden="true"></i></a>
				@endif
			</td>
		</tr>
		@php
		$r++;
		@endphp
		@endforeach
	</tbody>
</table>
@php
if(!empty($sales_order_template_name)) {
@endphp
<button class="btn btn-primary bg-blue download-order-template" data-template_name="{{$sales_order_template_name}}" type="button">Download Order File</button>
@php
}
@endphp