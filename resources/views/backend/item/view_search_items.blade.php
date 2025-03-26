@php
if(!empty($product_data)) {
	foreach($product_data as $data) {
@endphp
<div class="col-md-3 mb-4">
   	<div class="card">
    	<div class="card-header">
    	  <p class="card-title" style="margin-bottom: 0px">
    	  	@php
    	  	if(!empty($data['part_name'])) echo $data['part_name'];
    	  	@endphp
    	  </p>
    	</div>
    	<div class="card-body">
    		<p>
    			<strong>Part No: </strong>
    			@php
    	  	if(!empty($data['pmpno'])) echo $data['pmpno'];
    	  	@endphp
    		</p>
    		<p>
    			<strong>Retail Price: </strong>
    			@php
    	  	if(!empty($data['pmrprc'])) echo $data['pmrprc'];
    	  	@endphp
    		</p>
    		<p>
    			<strong>Manufacturer No: </strong>
    			@php
    			$manufacturing_no = "";
    	  	if(!empty($data['manufacturing_no'])) {
    	  		foreach($data['manufacturing_no'] as $app) {
    	  			$manufacturing_no .= $app['manufacturing_no'].", ";
    	  		}
    	  	}
    	  	echo $manufacturing_no;
    	  	@endphp
    		</p>
    		<p>
    			<strong>Alternate Part No: </strong>
    			@php
    			$alternate_no = "";
    	  	if(!empty($data['alternate_no'])) {
    	  		foreach($data['alternate_no'] as $app) {
    	  			$alternate_no .= $app['alternate_no'].", ";
    	  		}
    	  	}
    	  	echo $alternate_no;
    	  	@endphp
    		</p>
            <div class="row cart-details">
                <div class="col-md-12">
                    <input type="number" class="form-control cart-qty" style="width: 150px;display:inline-block;height: 42px;margin-top: 0px;position: relative;top: 2px;" placeholder="Enter Quantity" min="1">
                    <a data-product-id="{{$data['product_id']}}" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn add-to-cart" title="Add to cart"><i class="fa fa-cart-plus" aria-hidden="true"></i></a>
                </div>
            </div>
    	</div>
    </div>
</div>
@php
	}
}
@endphp