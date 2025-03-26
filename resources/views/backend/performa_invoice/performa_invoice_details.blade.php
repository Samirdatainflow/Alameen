<div class="row">
	<div class="col-md-12">
		<table width="100%" border="0">
			<tr>
				<th>
					Order Request ID:
					@php
					if(!empty($performa_invoice)) {
						if(!empty($performa_invoice[0]['order_request_id'])) echo $performa_invoice[0]['order_request_id'];
					}
					@endphp
					<br>
					Supplier Name:
					@php
					if(!empty($performa_invoice)) {
						if(!empty($performa_invoice[0]['supplier_name'])) echo $performa_invoice[0]['supplier_name'];
					}
					@endphp
				</th>
				<th class="text-right">
					Order Request Date:
					@php
					if(!empty($performa_invoice)) {
						if(!empty($performa_invoice[0]['order_request_date'])) echo date('M d Y', strtotime($performa_invoice[0]['order_request_date']));
					}
					@endphp
				</th>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
        <h4 class="text-center">Invoice</h4>
        <div class="text-center">
	        @php
			if(!empty($performa_invoice)) {
				if(!empty($performa_invoice[0]['invoice_file'])) {
					$extention = substr($performa_invoice[0]['invoice_file'], strrpos($performa_invoice[0]['invoice_file'], '.' )+1);
					if($extention == "pdf") {
					@endphp
					<iframe src="{{$performa_invoice[0]['invoice_file']}}" style="width:100%; height:500px;" frameborder="0"></iframe>
					@php
					}else {
					@endphp
					<img src="{{$performa_invoice[0]['invoice_file']}}" style="width: 100%">
					@php
					}
				}
			}
			@endphp
		</div>
		<p>&nbsp;</p>
        <p class="text-right">
	        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
	    </p>
	</div>
</div>