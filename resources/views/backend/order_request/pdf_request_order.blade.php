<table width="100%" border="0">
	<thead>
		<tr>
			<th>Order ID</th>
			<th>Order Date</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>
				@php
				if(!empty($OrderRequest)) {
					if(!empty($OrderRequest[0]['order_request_id'])) echo "#".$OrderRequest[0]['order_request_id'];
				}
				@endphp
			</th>
			<th>
				@php
				if(!empty($OrderRequest)) {
					if(!empty($OrderRequest[0]['created_at'])) echo date('M d Y', strtotime($OrderRequest[0]['created_at']));
				}
				@endphp
			</th>
		</tr>
	</tbody>
</table>
<h3 style="text-align: center;">Order Details</h3>
<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%">
    <thead>
        <tr>
            <th style="height: 25px">Part Brand</th>
            <th>Part No</th>
            <th>Part Name</th>
            <th>Unit</th>
            <th>Manufacturer no</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
    	@php
    	$i=1;
    	@endphp
		@if(!empty($OrderRequestDetails) > 0)
			@foreach($OrderRequestDetails as $data)
			<tr style="text-align: center;">
                <td style="height: 25px">{{$data['part_brand_name']}}</td>
                <td>{{$data['pmpno']}}</td>
                <td>{{$data['part_name']}}</td>
                <td>{{$data['unit_name']}}</td>
                <td>
                    @php
                    if(!empty($data['manufacturing_no'])) {
                        foreach($data['manufacturing_no'] as $man) {
                        @endphp
                        #{{$man['manufacturing_no']}}
                        @php
                        }
                    }
                    @endphp
                </td>
				<td>{{$data['qty']}}</td>
			</tr>
            @php
            $i++;
            @endphp
    		@endforeach
    	@endif
    </tbody>
</table>