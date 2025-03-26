{{ Form::open(array('id'=>'QuotationPriceForm')) }}
<div class="row" id="OderDetailsContent">
	<div class="col-md-12">
        <input type="hidden" name="hidden_request_id" value="" id="hidden_request_id">
		<table style="width: 100%;" id="OrderDetailsList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Part Brand</th>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Unit</th>
                    <th>Manufacturer no</th>
                    <th>Product Price</th>
                    <th>Supplier Price</th>
                    <th>Diff (%)</th>
                </tr>
            </thead>
            <tbody>
            	@php
            	$i=1;
        		if(sizeof($compare_data) > 0) {
        			foreach($compare_data as $data) {
                @endphp
    			<tr>
                    <td>{{$data['supplier_name']}}</td>
                    <td>{{$data['part_brand']}}</td>
                    <td>{{$data['part_no']}}</td>
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
    			</tr>
                @php
                $i++;
                    }
                }
                @endphp
            </tbody>
        </table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-right">
            <a href="javascript:void(0)" type="button" class="btn btn-warning btn-sm download-pdf" title="Download PDF" >Download <i class="fa fa-download"></i></a>
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>
{{ Form::close() }}