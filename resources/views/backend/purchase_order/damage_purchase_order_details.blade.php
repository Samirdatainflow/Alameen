<div class="row" id="OderDetailsContent">
	<div class="col-md-12">
		<table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-striped table-bordered" border="1" width="100%">
            <thead>
                <tr>
                    <th>#SL</th>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Quantity</th>
                    <th>Damage Quantity</th>
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
        				<td>{{$data->quantity}}</td>
        				<td>{{$data->bad_quantity}}</td>
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
            <!-- <a href="javascript:void(0)" class="print-order-details"><button type="button" class="btn-shadow btn btn-info"> <i class="fa fa-print" aria-hidden="true"></i> Print </button></a> -->
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>