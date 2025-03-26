<h3 style="text-align: center;">Order Details</h3>
<table style="width: 100%;" cellpadding="5" class="mb-3">
    <tbody>
    	<tr>
            <td width="20%">Order id:</td>
            <td>{{$order_id}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-bordered" border="1" width="100%">
	<thead>
		<tr>
			<th style="height: 25px">Part Brand</th>
			<th>Part No</th>
			<th>Part Name</th>
			<th>Unit</th>
			<th>Manufacture No</th>
			<th>Qty</th>
		</tr>
	</thead>
	<tbody>
		@foreach($order_data as $data)
		<tr style="text-align: center;">
			<td style="height: 25px">{{$data['part_brand_name']}}</td>
			<td>{{$data['pmpno']}}</td>
			<td>{{$data['part_name']}}</td>
			<td>{{$data['unit_name']}}</td>
			<td>
				@php
				$output = "";
                if(!empty($data['manufacturing_no'])) {
                    foreach($data['manufacturing_no'] as $man) {
                    	$output.=$man['manufacturing_no'].", ";
                    }
                }
                $output = substr($output, 0, -2);
                echo $output;
                @endphp
			</td>
			<td>{{$data['qty']}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
  