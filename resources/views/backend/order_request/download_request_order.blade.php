<h3 style="text-align: center;">Compare Details</h3>
<table width="100%" border="0">
	<tr>
        <td>Order ID: @php
             echo "#".$_GET['id'];
            @endphp
        </td>
        <td>Date: @php
             echo date('d/m/Y');
            @endphp
        </td>
	</tr>
</table>
<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th style="height: 25px">Supplier</th>
            <th>Part No</th>
            <th>Part Name</th>
            <th>Unit</th>
            <th>Product Price</th>
            <th>Supplier Price</th>
            <th>Diff (%)</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
    	@php
    	$i=1;
    	@endphp
		@if(!empty($OrderRequest) > 0)
			@foreach($OrderRequest as $data)
			<tr style="text-align: center;">
                <td style="height: 25px">{{$data['supplier_name']}}</td>
                <td>{{$data['part_no']}}</td>
                <td>{{$data['part_name']}}</td>
                <td>{{$data['unit_name']}}</td>
				<td>{{$data['pmrprc']}}</td>
                <td>{{$data['quotation_prices']}}</td>
                @php
                    if($data['status'] == "high") {
                    @endphp
                    <td style="color: red"> + {{$data['persentage']}}</td>
                    @php
                    }else if($data['status'] == "low"){
                    @endphp
                    <td style="color: green"> - {{$data['persentage']}}</td>
                    @php
                    }else {
                    @endphp
                    <td> {{$data['persentage']}}</td>
                    @php
                    }
                @endphp
                <td>
                   @php
                    if($data['status'] == "high") {
                    @endphp
                    Get Better Price
                    @php
                    }else {
                    @endphp
                    
                    @php
                    }
                @endphp 
                </td>
			</tr>
            @php
            $i++;
            @endphp
    		@endforeach
    	@endif
    </tbody>
</table>