{{ Form::open(array('id'=>'QuotationInvoiceForm')) }}
<div class="row" id="OderDetailsContent">
	<div class="col-md-12">
        <table style="width: 100%;" cellpadding="5" class="mb-3">
            <tr>
                <td width="20%">Order request id:</td>
                <td>{{$order_request_unique_id}}</td>
            </tr>
            <tr>
                <td width="20%">Order Date:</td>
                <td>{{date('d M,Y',strtotime($order_date))}}</td>
            </tr>
            <tr>
                <td width="20%">Created By:</td>
                <td>{{$created_by}}</td>
            </tr>
        </table>
		<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>Part Brand</th>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Unit</th>
                    <th>Manufacturer no</th>
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
                        <td>
                            @if($is_confirm != "1")
                            <a href="javascript:void(0)" class="delete-order-request-details" data-id="{{$data['order_request_details_id']}}"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>
                            @endif
                        </td>
        			</tr>
                    @php
                    $i++;
                    @endphp
            		@endforeach
            	@endif
            </tbody>
        </table>
        @php
        if(!empty($supplier_data)) {
        @endphp
        <table style="width: 100%;" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Supplier Name</th>
                    <th>Download Order</th>
                    <th>Received Quotation</th>
                    <th>Confirm</th>
                    <th>Upload Performa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            	@php
            	$i=1;
            	@endphp
        		@foreach($supplier_data as $data)
        			<tr>
                        <input type="hidden" name="supplier_id[]" value="{{$data['supplier_id']}}" />
                        <input type="hidden" name="order_quotation[]" value="{{$data['order_quotation']}}" />
                        <td>{{$i}}</td>
                        <td>{{$data['supplier_name']}}</td>
                        <td>
                            <a href="javascript:void(0)" type="button" class="btn btn-warning btn-sm generate-pdf" title="Download PDF" data-order_request_unique_id="{{$order_request_unique_id}}"><i class="fa fa-download"></i> Download & Print</a>
                        </td>
                        <td id="quotationTR{{$i}}">
                        	@php
        					if($data["order_quotation"] == "1") {
        					@endphp
        					<a href="javascript:void(0)" onclick="ViewQuotationFile('{{$data['order_quotation_file_extention']}}', '{{$data['order_quotation_file']}}')" class="badge badge-success">View File</a>
        					@php
	        				}else if($is_confirm !=1){
	        				@endphp
                        	<div class="form-group">
				                <input type="file" id="quotation_file{{$i}}" name="quotation_file" class="file-upload-default" accept=".jpg, .png, .pdf" />
				                <div class="input-group col-xs-12">
				                    <input type="text" class="form-control file-upload-info" id="quotation_file" name="quotation_file" disabled placeholder="Upload Quotation" />
				                    <span class="input-group-append">
				                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
				                    </span>
				                </div>
				            </div>
				            <button type="button" name="submit" class="btn-shadow btn btn-info upload-quotation" data-row-id="{{$i}}" data-supplier_id="{{$data['supplier_id']}}" data-order_request_unique_id="{{$order_request_unique_id}}" value="Submit"> Upload </button>
				            @php
					        }
					        @endphp
                        </td>
                        <td id="confirmTR{{$i}}">
                        	@php
        					if($data["quotation_is_confirm"] == "1") {
        					@endphp
                        	<span class="badge badge-success" title="Confirmed">Confirmed</span>
                        	@php
					        }else if($data["order_quotation"] == "1" && $is_confirm != "1") {
					        @endphp
					        	<a href="javascript:void(0)" onclick="ConfirmQuotation({{$i}}, {{$data['supplier_id']}}, {{$order_request_unique_id}})" class="badge badge-danger confirm-btn" title="Confirm it">Not Confirm</a>
					        @php
					        }else {
					        @endphp
					        <span class="badge badge-danger" title="No quotation Uploaded">Not Confirm</span>
					        @php
					        }
					        @endphp
                        </td>
        				<td id="performaInvoiceTR{{$i}}">
        					@php
        					if($data["performa_invoice"] == "1" && $data["quotation_is_confirm"] == "1") {
        					@endphp
	        					<a href="javascript:void(0)" onclick="ViewPerformaInvoiceFile('{{$data['performa_invoice_extention']}}', '{{$data['performa_invoice_file']}}')" class="badge badge-success">View File</a>
        					@php
        					}else if($data["quotation_is_confirm"] == "1") {
        					@endphp
	        					<div class="form-group">
					                <input type="file" id="performa_invoice{{$i}}" name="performa_invoice" class="file-upload-default" accept=".jpg, .png, .pdf" />
					                <div class="input-group col-xs-12">
					                    <input type="text" class="form-control file-upload-info" id="performa_invoice" name="performa_invoice" disabled placeholder="Upload Invoice" />
					                    <span class="input-group-append">
					                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
					                    </span>
					                </div>
					            </div>
					            <button type="button" name="submit" class="btn-shadow btn btn-info upload-performa-invoice" data-row-id="{{$i}}" data-supplier_id="{{$data['supplier_id']}}" data-order_request_unique_id="{{$order_request_unique_id}}" value="Submit"> Upload </button>
				            @php
					        }
					        @endphp
        				</td>
                        <td id="UploadPriceTR{{$i}}">
                            @php
                            if($data["order_quotation"] == "1") {
                                if($data["quotation_prices_upload"] == "1") {
                            @endphp
                                <button type="button" name="submit" class="btn-shadow btn btn-info view-quotation-price" data-row_id="{{$i}}" data-supplier_id="{{$data['supplier_id']}}" data-order_request_unique_id="{{$order_request_unique_id}}" value="Submit"><i class="fa fa-eye"></i> View Price </button>
                            @php
                                }else {
                                    @endphp
                                        <button type="button" name="submit" class="btn-shadow btn btn-info upload-quotation-price" data-row_id="{{$i}}" data-supplier_id="{{$data['supplier_id']}}" data-order_request_unique_id="{{$order_request_unique_id}}" value="Submit"> Upload Price </button>
                                    @php
                                }
                            }
                            @endphp
                        </td>
        			</tr>
                    @php
                    $i++;
                    @endphp
            	@endforeach
            </tbody>
        </table>
        @php
        }
        @endphp
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-right">
            {{-- <a href="javascript:void(0)" class="print-order-details"><button type="button" class="btn-shadow btn btn-info"> <i class="fa fa-print" aria-hidden="true"></i> Print </button></a> --}}
            <button type="button" name="submit" class="btn-shadow btn btn-info compare-price" data-order_request_unique_id="{{$order_request_unique_id}}"> Compare Price </button>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>
{{ Form::close() }}