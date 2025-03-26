<p><strong>Return Date:</strong> @php if(!empty($Returns[0]['return_date'])) echo $Returns[0]['return_date']; @endphp </p>
<h3>Return Details</h3>
<table style="width: 100%;" id="returnTable" class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Part No</th>
            <th>Name</th>
            <th>Invoice Qty</th>
            <th>Received Qty</th>
            <th>Good Qty</th>
            <th>Bad Qty</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    	@php
    	if(!empty($ReturnDetail)) {
    		foreach ($ReturnDetail as $data) {
    	@endphp
    	<tr>
    		<td>{{$data['pmpno']}}</td>
    		<td>{{$data['part_name']}}</td>
    		<td>{{$data['qty']}}</td>
    		<td>{{$data['received_quantity']}}</td>
    		<td>{{$data['good_quantity']}}</td>
    		<td>{{$data['bad_quantity']}}</td>
    		<td>{{$data['remarks']}}</td>
    	</tr>
    	@php
    		}
    	}
    	@endphp
    </tbody>
</table>