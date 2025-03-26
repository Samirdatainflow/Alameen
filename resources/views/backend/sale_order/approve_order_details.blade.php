{{ Form::open(array('id'=>'ApprovePickingForm')) }}
<input type="hidden" name="order_id" value="{{$order_id}}">
<table class="table table-bordered">
	<thead>
		<tr>
			<th>#SL</th>
			<th>Product Name</th>
			<th>Part No</th>
			<th>Allocated Qty</th>
			<th>Location</th>
			<th>Zone</th>
			<th>Row</th>
			<th>Rack</th>
			<th>Plate</th>
			<th>Place</th>
		</tr>
	</thead>
	<tbody>
		@php
		$r=1;
		@endphp
		@foreach($OrderDetails as $data)
		<tr>
			<td>{{$r}}</td>
			<td>{{$data['part_name']}}</td>
			<td>
				{{$data['pmpno']}}
				<input type="hidden" name="product_id[]" value="{{$data['product_id']}}">
				<input type="hidden" name="binning_location_details_id[]" value="{{$data['binning_location_details_id']}}">
			</td>
			<td>
				{{$data['quantity']}}
				<input type="hidden" name="binning_qty[]" value="{{$data['binning_qty']}}">
				<input type="hidden" name="quantity[]" value="{{$data['quantity']}}">
				<input type="hidden" name="product_price[]" value="{{$data['product_price']}}">
			</td>
			<td>
				{{$data['location_name'] }}
				<input type="hidden" name="location_id[]" value="{{$data['location_id']}}">
			</td>
			<td>
				{{$data['zone_name'] }}
				<input type="hidden" name="zone_id[]" value="{{$data['zone_id']}}">
			</td>
			<td>
				{{$data['row_name'] }}
				<input type="hidden" name="row_id[]" value="{{$data['row_id']}}">
			</td>
			<td>
				{{$data['rack_name'] }}
				<input type="hidden" name="rack_id[]" value="{{$data['rack_id']}}">
			</td>
			<td>
				{{$data['plate_name'] }}
				<input type="hidden" name="plate_id[]" value="{{$data['plate_id']}}">
			</td>
			<td>
				{{$data['place_name'] }}
				<input type="hidden" name="place_id[]" value="{{$data['place_id']}}">
			</td>
		</tr>
		@php
		$r++;
		@endphp
		@endforeach
	</tbody>
</table>
<p class="text-right">
    <button type="button" class="btn-shadow btn btn-info" id="ApprovePicking"> Approve </button>
    <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>
{{ Form::close() }}