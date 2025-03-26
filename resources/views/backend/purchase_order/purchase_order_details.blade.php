<div class="row" id="OderDetailsContent">
	<div class="col-md-12">
		<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            	@php
            	$i=1;
            	@endphp
        		@if(sizeof($order_data) > 0)
        			@foreach($order_data as $data)
        			<tr>
        				<td>{{$i}}</td>
                        <td>{{$data->part_name}}</td>
                        <td>{{$data->pmpno}}</td>
        				<td>{{$data->mrp}}</td>
        				<td>{{$data->qty}}</td>
                        <td><a href="javascript:void(0)" class="delete-order-details" data-id="{{$data->order_detail_id}}"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a></td>
        			</tr>
                    @php
                    $i++;
                    @endphp
            		@endforeach
            	@endif
            </tbody>
        </table>
	</div>
</div>
<div class="row">
    <div class="col-md-4 mb-3" id="invoiceSection">
        <div class="form-group">
            <input type="hidden" id="hidden_order_id" value="{{$order_id}}" />
            <input type="file" id="invoice" class="file-upload-default" accept=".jpg, .png, .pdf" />
            <div class="input-group col-xs-12">
                <input type="text" class="form-control file-upload-info" id="invoice_file" disabled placeholder="Upload Invoice" />
                <span class="input-group-append">
                    <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                </span>
            </div>
        </div>
        <div class="form-group">
            <input type="text" id="invoice_no" class="form-control" placeholder="Enter invoice no" value="{{$invoice_no}}" />
        </div>
        <button type="button" name="submit" class="btn-shadow btn btn-info upload-invoice" data-order_request_id="" data-supplier_id="" value="Submit"> Upload </button> 
    </div>
        <div class="col-md-12 mb-3">
	    	<div class="file_view">
                @php
                if(!empty($invoice_file)) {
                    $invoice_extention = substr($invoice_file, strrpos($invoice_file, '.' )+1);
                    $url = url('public/backend/images/purchase_invoice/')."/".$invoice_file;
    	    		if($invoice_extention == "pdf") {
    	    		@endphp
    	    		<iframe src="{{$url}}" style="width:100%; height:500px;" frameborder="0"></iframe>
    	    		@php
    	    		}else {
    	    		@endphp
    	    		<img src="{{$url}}" style="width:100%">
    	    		@php
    	    		}
                }
                @endphp
	        </div>
	    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-right">
            <a href="javascript:void(0)" class="print-order-details"><button type="button" class="btn-shadow btn btn-info"> <i class="fa fa-print" aria-hidden="true"></i> Print </button></a>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>