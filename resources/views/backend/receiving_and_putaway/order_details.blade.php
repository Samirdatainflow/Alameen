<table class="table table-bordered">
	<thead>
		<tr>
			<th>Part Brand</th>
			<th>Part No</th>
			<th>Part Name</th>
			<th>Unit</th>
			<th>Manufacture No</th>
			<th>Qty</th>
		</tr>
	</thead>
	<tbody>
		@foreach($order_data as $data)
		<tr>
			<td>{{$data['part_brand_name']}}</td>
			<td>{{$data['pmpno']}}</td>
			<td>{{$data['part_name']}}</td>
			<td>{{$data['unit_name']}}</td>
			<td>
				@php
				$output = "";
                if(!empty($data['manufacturing_no'])) {
                    foreach($data['manufacturing_no'] as $man) {
                    @endphp
                    	<span class="badge badge-success">{{$man['manufacturing_no']}}</span>
                    @php
                    }
                }
                @endphp
			</td>
			<td>{{$data['qty']}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<div class="row">
	@php
        if(!empty($invoice_file)) {
        $invoice_extention = substr($invoice_file, strrpos($invoice_file, '.' )+1);
        $url = url('public/backend/images/purchase_invoice/')."/".$invoice_file;
        @endphp
        <div class="col-md-12 mb-3">
	    	<div class="file_view">
	    		@php
	    		if($invoice_extention == "pdf")
	    		{
	    		@endphp
	    		<iframe src="{{$url}}" style="width:100%; height:500px;" frameborder="0"></iframe>
	    		@php
	    		}
	    		else
	    		{
	    		@endphp
	    		<img src="{{$url}}" style="width:100%">
	    		@php
	    		}
	    		@endphp
	        </div>
	    </div>
            
    @php
    }
    @endphp
</div>
  