<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Part Brand</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->part_brand_name)) echo $item_data[0]->part_brand_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Part no</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->pmpno)) echo $item_data[0]->pmpno;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Part name</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->part_name)) echo $item_data[0]->part_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Car manufacture</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->car_manufacture)) echo $item_data[0]->car_manufacture;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3" style="display: none">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Car name</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->car_name)) echo $item_data[0]->car_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">From Year</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->from_year)) echo $item_data[0]->from_year;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">From month</h5>
		    <p class="card-text">
		    	@php
				$from_month = "";
		    	if(!empty($item_data[0]->from_month)) $from_month = $item_data[0]->from_month;
				switch ($from_month) {
				case "01":
					echo "January";
					break;
				case "02":
					echo "February";
					break;
				case "03":
					echo "March";
					break;
				case "04":
					echo "April";
					break;
				case "05":
					echo "May";
					break;
				case "06":
					echo "June";
					break;
				case "07":
					echo "July";
					break;
				case "08":
					echo "August";
					break;
				case "09":
					echo "September";
					break;
				case "10":
					echo "October";
					break;
				case "11":
					echo "November";
					break;
				case "12":
					echo "December";
					break;
				default:
					echo "";
				}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">To Year</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->to_year)) echo $item_data[0]->to_year;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">To Month</h5>
		    <p class="card-text">
		    	@php
				$to_month = "";
		    	if(!empty($item_data[0]->to_month)) $to_month = $item_data[0]->to_month;
				switch ($to_month) {
				case "01":
					echo "January";
					break;
				case "02":
					echo "February";
					break;
				case "03":
					echo "March";
					break;
				case "04":
					echo "April";
					break;
				case "05":
					echo "May";
					break;
				case "06":
					echo "June";
					break;
				case "07":
					echo "July";
					break;
				case "08":
					echo "August";
					break;
				case "09":
					echo "September";
					break;
				case "10":
					echo "October";
					break;
				case "11":
					echo "November";
					break;
				case "12":
					echo "December";
					break;
				default:
					echo "";
				}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Car Model</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($car_model_name)) echo $car_model_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Category </h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->category_name)) echo $item_data[0]->category_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Subcategory </h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->sub_category_name)) echo $item_data[0]->sub_category_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Group </h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->gr)) echo $item_data[0]->gr;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Engine</h5>
		    <p class="card-text">
		    	@php
		    	if(sizeof($Engine) > 0) {
		    		foreach($Engine as $edata) {
		    		@endphp
		    		<span class="badge badge-success remove-application-no" data-id="8">{{$edata['engine_name']}}</span>
		    		@php
		    		}
		    	}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Chassis / Model</h5>
		    <p class="card-text">
		    	@php
		    	if(sizeof($ChassisModel) > 0) {
		    		foreach($ChassisModel as $mdata) {
		    		@endphp
		    		<span class="badge badge-success remove-application-no" data-id="8">{{$mdata['chassis_model']}}</span>
		    		@php
		    		}
		    	}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Manufacturer No</h5>
		    <p class="card-text">
		    	@php
		    	if(sizeof($manufacturing_no) > 0) {
		    		foreach($manufacturing_no as $app) {
		    		@endphp
		    		<span class="badge badge-success remove-application-no" data-id="8">{{$app['manufacturing_no']}}</span>
		    		@php
		    		}
		    	}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Alternate Part</h5>
		    <p class="card-text">
		    	@php
		    	if(sizeof($alternate_no) > 0) {
		    		foreach($alternate_no as $alt) {
		    		@endphp
		    		<span class="badge badge-success remove-application-no" data-id="8">{{$alt['alternate_no']}}</span>
		    		@php
		    		}
		    	}
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Retail Price</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->pmrprc)) echo $item_data[0]->pmrprc;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Mark Up</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->mark_up)) echo $item_data[0]->mark_up;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">LC Price</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->lc_price)) echo $item_data[0]->lc_price;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">LC Price Date</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->lc_date)) echo date('M d Y', strtotime($item_data[0]->lc_date));
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Previous LC Price</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->prvious_lc_price)) echo $item_data[0]->prvious_lc_price;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Previous LC Price Date</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->prvious_lc_date)) echo date('M d Y', strtotime($item_data[0]->prvious_lc_date));
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Minimum Order Qty</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->moq)) echo $item_data[0]->moq;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3"></div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Country</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->country_name)) echo $item_data[0]->country_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Supplier</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->supplier_name)) echo $item_data[0]->supplier_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Supplier currency</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->supplier_currency)) echo $item_data[0]->supplier_currency;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Reorder Number</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->no_re_order)) echo $item_data[0]->no_re_order;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Stop Sale Quantity</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->stop_sale)) echo $item_data[0]->stop_sale;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Warehouses</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->warehouse_name)) echo $item_data[0]->warehouse_name;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Alert stock</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->stock_alert)) echo $item_data[0]->stock_alert;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Reserved Qty</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->reserved_qty)) echo $item_data[0]->reserved_qty;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Allocation Qty</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->allocation_qty)) echo $item_data[0]->allocation_qty;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Last Month Stock</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->last_month_stock)) echo $item_data[0]->last_month_stock;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Quantity In Transit</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->qty_in_transit)) echo $item_data[0]->qty_in_transit;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
		  <div class="card-body">
		    <h5 class="card-title">Quantity On Order</h5>
		    <p class="card-text">
		    	@php
		    	if(!empty($item_data[0]->qty_on_order)) echo $item_data[0]->qty_on_order;
		    	@endphp
		    </p>
		  </div>
		</div>
	</div>
	<p>&nbsp;</p>
</div>
<div class="row">
	<p>&nbsp;</p>
	<div class="col-md-12">
		<p class="text-right">
	        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close View </button>
	    </p>
	</div>
</div>