<div class="row">
	<div class="col-md-12">
		<table width="100%" border="0">
			<tr>
				<th>
					Order Request ID:
					@php
					if(!empty($quotation_data)) {
						if(!empty($quotation_data[0]['order_request_id'])) echo $quotation_data[0]['order_request_id'];
					}
					@endphp
				</th>
				<th class="text-right">
					Order Request Date:
					@php
					if(!empty($quotation_data)) {
						if(!empty($quotation_data[0]['created_at'])) echo date('M d Y', strtotime($quotation_data[0]['created_at']));
					}
					@endphp
				</th>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
        <h4 class="text-center">Quotation File</h4>
        <div class="text-center">
	        @php
			if(!empty($quotation_data)) {
				if(!empty($quotation_data[0]['quotation'])) {
					$extention = substr($quotation_data[0]['quotation'], strrpos($quotation_data[0]['quotation'], '.' )+1);
					if($extention == "pdf") {
					@endphp
					<iframe src="{{$quotation_data[0]['quotation']}}" style="width:100%; height:500px;" frameborder="0"></iframe>
					@php
					}else {
					@endphp
					<img src="{{$quotation_data[0]['quotation']}}">
					@php
					}
				}
			}
			@endphp
		</div>
		<p>&nbsp;</p>
		<h4 class="text-center">Order Details</h4>
		<table style="width: 100%;" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>Part Brand</th>
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
        		@if(!empty($quotation_data) > 0)
	        		@if(!empty($quotation_data[0]['order_data']) > 0)
	        			@foreach($quotation_data[0]['order_data'] as $data)
	        			<tr>
	                        <td>{{$data['part_brand_name']}}</td>
	                        <td>{{$data['pmpno']}}</td>
	                        <td>{{$data['part_name']}}</td>
	                        <td>{{$data['unit_name']}}</td>
	                        <td>
	                            @php
	                            if(!empty($data['manufacturing_no'])) {
	                                foreach($data['manufacturing_no'] as $man) {
	                                @endphp
	                                <a href="javascript::void(0)" class="badge badge-success remove-manufacturing-no" data-id="15">{{$man['manufacturing_no']}}</a>
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
            	@endif
            </tbody>
        </table>
        <p class="text-right">
	        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
	    </p>
	</div>
</div>