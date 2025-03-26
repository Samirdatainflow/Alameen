@php
$i= 1;
if(!empty($CheckInDetails)) {
	foreach($CheckInDetails as $data) {
		$accep_quantity = $data['good_quantity'];
		$rest_quantity = "";
		if(!empty($data['remaining_capacity']) && $data['good_quantity'] > $data['remaining_capacity']) {
			$accep_quantity = $data['remaining_capacity'];
			$rest_quantity = $data['good_quantity'] - $data['remaining_capacity'];
		}
@endphp
		<tr>
			<td>
				<input type="hidden" class="location-details-id" name="binning_location_details_id[]" value="{{$data['binning_location_details_id']}}">
				<input type="hidden" class="product-id" name="product_id[]" value="{{$data['product_id']}}">
				<input type="text" class="form-control part-name" value="{{$data['part_name']}}" readonly>
			</td>
			<td><input type="text" class="form-control pmpno" value="{{$data['pmpno']}}" readonly></td>
			<td><input type="number" class="form-control quantity" name="quantity[]" value="{{$accep_quantity}}" readonly></td>
			<td>
				<select class="form-control location-id" name="location_id[]">
					<option value="">Select</option>
					@php
					if(!empty($Location)) {
						foreach($Location as $ldata) {
							$sel = "";
							if(!empty($data['location_id'])) {
								if($ldata['location_id'] == $data['location_id']) $sel='selected="selected"';
							}
					@endphp
						<option value="{{$ldata['location_id']}}" {{$sel}}>{{$ldata['location_name']}}</option>
					@php
						}
					}
					@endphp
				</select>
			</td>
			<td>
				<select class="form-control zone-id" name="zone_id[]">
					<option value="">Select</option>
					@php
					if(!empty($data['ZoneMaster'])) {
						foreach($data['ZoneMaster'] as $zone) {
							$sel = "";
							if(!empty($data['zone_id'])) {
								if($zone['zone_id'] == $data['zone_id']) $sel='selected="selected"';
							}
						@endphp
						<option value="{{$zone['zone_id']}}" {{$sel}}>{{$zone['zone_name']}}</option>
						@php
						}
					}
					@endphp
				</select>
			</td>
			<td>
				<select class="form-control row-id" name="row_id[]">
					<option value="">Select</option>
					@php
					if(!empty($data['RowData'])) {
						foreach($data['RowData'] as $row) {
							$sel = "";
							if(!empty($data['row_id'])) {
								if($row['row_id'] == $data['row_id']) $sel='selected="selected"';
							}
						@endphp
						<option value="{{$row['row_id']}}" {{$sel}}>{{$row['row_name']}}</option>
						@php
						}
					}
					@endphp
				</select>
			</td>
			<td>
				<select class="form-control rack-id" name="rack_id[]">
					<option value="">Select</option>
					@php
					if(!empty($data['RackData'])) {
						foreach($data['RackData'] as $rack) {
							$sel = "";
							if(!empty($data['rack_id'])) {
								if($rack['rack_id'] == $data['rack_id']) $sel='selected="selected"';
							}
						@endphp
						<option value="{{$rack['rack_id']}}" {{$sel}}>{{$rack['rack_name']}}</option>
						@php
						}
					}
					@endphp
				</select>
			</td>
			<td>
				<select class="form-control plate-id" name="plate_id[]">
					<option value="">Select</option>
					@php
					if(!empty($data['PlateData'])) {
						foreach($data['PlateData'] as $plate) {
							$sel = "";
							if(!empty($data['plate_id'])) {
								if($plate['plate_id'] == $data['plate_id']) $sel='selected="selected"';
							}
						@endphp
						<option value="{{$plate['plate_id']}}" {{$sel}}>{{$plate['plate_name']}}</option>
						@php
						}
					}
					@endphp
				</select>
			</td>
			<td>
				<select class="form-control place-id" name="place_id[]">
					<option value="">Select</option>
					@php
					if(!empty($data['PlaceData'])) {
						foreach($data['PlaceData'] as $place) {
							$sel = "";
							if(!empty($data['place_id'])) {
								if($place['place_id'] == $data['place_id']) $sel='selected="selected"';
							}
						@endphp
						<option value="{{$place['place_id']}}" {{$sel}}>{{$place['place_name']}}</option>
						@php
						}
					}
					@endphp
				</select>
				<input type="hidden" class="hidden-position" name="hidden_position" value="{{$data['place_id']}}">
			</td>
			<td>
				@php
				$max_capacity = "";
				if(!empty($data['max_capacity'])) $max_capacity = $data['max_capacity'];
				@endphp
				<span class="max-capacity">{{$max_capacity}}</span>
				<input type="hidden" name="max_capacity" class="hidden-max-capacity" value="{{$max_capacity}}">
			</td>
			<td>
				@php
				$remaining_capacity = "";
				if(!empty($data['remaining_capacity'])) $remaining_capacity = $data['remaining_capacity'];
				@endphp
				<span class="remaining-capacity">{{$remaining_capacity}}</span>
				<input type="hidden" name="remaining_capacity" value="{{$remaining_capacity}}">
			</td>
		</tr>
@php
		if(!empty($data['remaining_capacity']) && $data['good_quantity'] > $data['remaining_capacity']) {
		@endphp
			<tr>
				<td><input type="hidden" class="product-id" name="product_id[]" value="{{$data['product_id']}}"><input type="text" class="form-control" value="{{$data['part_name']}}" readonly></td>
				<td><input type="text" class="form-control" value="{{$data['pmpno']}}" readonly></td>
				<td><input type="number" class="form-control" name="quantity[]" value="{{$rest_quantity}}" readonly></td>
				<td>
					<select class="form-control location-id" name="location_id[]">
						<option value="">Select</option>
						@php
						if(!empty($Location)) {
							foreach($Location as $ldata) {
								$sel = "";
								// if(!empty($data['location_id'])) {
								// 	if($ldata['location_id'] == $data['location_id']) $sel='selected="selected"';
								// }
						@endphp
							<option value="{{$ldata['location_id']}}" {{$sel}}>{{$ldata['location_name']}}</option>
						@php
							}
						}
						@endphp
					</select>
				</td>
				<td>
					<select class="form-control zone-id" name="zone_id[]">
						<option value="">Select</option>
					</select>
				</td>
				<td>
					<select class="form-control row-id" name="row_id[]">
						<option value="">Select</option>
					</select>
				</td>
				<td>
					<select class="form-control rack-id" name="rack_id[]">
						<option value="">Select</option>
					</select>
				</td>
				<td>
					<select class="form-control plate-id" name="plate_id[]">
						<option value="">Select</option>
					</select>
				</td>
				<td>
					<select class="form-control place-id" name="place_id[]">
						<option value="">Select</option>
					</select>
					<input type="hidden" class="hidden-position" name="hidden_position" value="">
				</td>
				<td>
					<span class="max-capacity">
					</span>
				</td>
				<td>
					<span class="remaining-capacity">
					</span>
				</td>
			</tr>
		@php
		}
	}
}
@endphp