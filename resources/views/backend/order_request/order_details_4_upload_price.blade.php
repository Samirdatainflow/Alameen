{{ Form::open(array('id'=>'QuotationPriceForm')) }}
<div class="row" id="OderDetailsContent">
    <input type="hidden" name="order_request_unique_id" value="{{$order_request_unique_id}}">
    <input type="hidden" name="supplier_id" value="{{$supplier_id}}">
    <input type="hidden" name="row_id" id="row_id" value="{{$row_id}}">
	<div class="col-md-12">
		<table style="width: 100%;" id="OrderDetailsList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>Part Brand</th>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Unit</th>
                    <th>Manufacturer no</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
            	@php
            	$i=1;
            	@endphp
        		@if(sizeof($order_data) > 0)
        			@foreach($order_data as $data)
        			<tr>
                        <td>{{$data['part_brand_name']}} <input type="hidden" name="product_id[]" value="{{$data['product_id']}}"><input type="hidden" name="order_request_details_id[]" value="{{$data['order_request_details_id']}}"></td>
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
        				<td><input type="number" name="quotation_price[]" class="form-control" placeholder="Price"></td>
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
	<div class="col-md-12">
		<p class="text-right">
            {{-- <a href="javascript:void(0)" class="print-order-details"><button type="button" class="btn-shadow btn btn-info"> <i class="fa fa-print" aria-hidden="true"></i> Print </button></a> --}}
            <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Upload </button>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>
{{ Form::close() }}