<style>
table{
border-collapse:collapse;
}
.product-details td {
  border-top: 0;
  border-bottom: 0;
  border-right: 1px solid #000;
}
.product-details td:last-child {
  border-right: 0;
}
@page {
    margin: 20px 10px 50px 10px !important;
}
</style>
<div class="row">
	<div class="col-md-12">
		<table border="0" width="100%">
			<tr>
				<!-- <td width="40%" style="text-align: right;">
					<p>GHK798797789800YU</p>
				</td>
				<td width="20%"></td>
				<td>
					<p>PARTS TAX INVOICE </p>
				</td> -->
				<td width="100%" style="text-align: center;">
					<p><h1><b>PICKING SLIP</b></h1></p>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
			<tr>
				<td colspan="3" valign="top">
					<table cellpadding="5">
						@php
						$customer_id = "";
						$customer_name = "";
						$customer_email_id = "";
						$customer_wa_no = "";
						$vatin = "";
						if(!empty($clients_data)) $customer_id = $clients_data[0]['customer_id'];
						if(!empty($clients_data)) $customer_name = $clients_data[0]['customer_name'];
						if(!empty($clients_data)) $customer_email_id = $clients_data[0]['customer_email_id'];
						if(!empty($clients_data)) $customer_wa_no = $clients_data[0]['customer_wa_no'];
						if(!empty($clients_data)) $vatin = $clients_data[0]['vatin'];
						@endphp
						<tr>
							<td>{{$customer_name}}</td>
						</tr>
						<tr>
							<td>Mobile No: </td>
							<td>{{$customer_wa_no}}</td>
						</tr>
						<tr>
							<td>Email: </td>
							<td>{{$customer_email_id}}</td>
						</tr>
						<tr>
							<td>Customer VATIN: </td>
							<td>{{$vatin}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 15px">
	<div class="col-md-12">
		{{-- <table width="100%" border="1" align="center" cellpadding="0" cellspacing="1"> --}}
		<table style="width: 100%;" class="table table-hover table-striped table-bordered">
			<tr style="background-color: #ccc">
				<td style="height: 35px; text-align: center;">SL</td>
				<td style="text-align: center;">Part No</td>
				<td style="text-align: center;">Alternate Part No</td>
				<td style="text-align: center;" >Part Name</td>
				<td style="text-align: center;">Allocated Qty</td>
				<td style="text-align: center;">Location</td>
			</tr>
			@php
			$i=1;
			$total_product_price=0;
			if(sizeof($SaleOrderDetails) > 0) {
				foreach($SaleOrderDetails as $data) {
				$total_product_price =($data['pmrprc']*$data['quantity']) 
				@endphp
			<tr class="product-details">
				<td style="text-align: center;">{{$i}}</td>
				<td style="text-align: center;">{{$data['pmpno']}}</td>
				<td style="text-align: center;">{{$data['alternate_part_no']}}</td>
				<td style="text-align: center;">{{$data['part_name']}}</td>
				<td style="text-align: center;">{{$data['quantity']}}</td>
				<td style="text-align: center;">Location: {{$data['location_name']}}, Zone: {{$data['zone_name']}}, Row: {{$data['row_name']}}, Rack: {{$data['rack_name']}}, Plate: {{$data['plate_name']}}, Place: {{$data['place_name']}}</td>
			</tr>
				@php
				$i++;
				}
			}
			@endphp		
		</table>
	</div>
</div>
<div class="row" style="display:none">
	<div class="col-md-12">
		<table width="100%" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" style="border-right: 0px; border-bottom: 0px">
					<p style="margin-left: 10px"><?=wordwrap("I/ We Accepted this invoice correct and all item Received item Good Condition",50,"<br>\n")?></p>
				</td>
				<td colspan="3" style="border-left: 0px; border-bottom: 0px">&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" style="border-right: 0px; border-top: 0px">
					<p>&nbsp;&nbsp;&nbsp;Customer Signature</p>
					&nbsp;
					<p>&nbsp;&nbsp;&nbsp;Stamp:</p>
				</td>
				<td colspan="3" style="border-left: 0px; border-bottom: 0px">&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" style="border-left: 0px; border-right: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2" style="border-left: 0px; border-right: 0px;">
					<p></p>
					<p>
					<b>Prepared By</b>
					</p>
					<p></p>
				</td>
				<td colspan="2" style="border-left: 0px; border-top: 0px">
					<p style="text-align: right;"></p>
					<p style="text-align: right;"><b>Authorized By</b></p>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table width="100%" align="center" cellpadding="0" cellspacing="1" style="position: absolute;bottom: 0">
			<tr>
				<td colspan="2" style="border-right: 0px; border-bottom: 0px">
					<p><?php echo date('d/m/Y');?>&nbsp;<?php echo date('H:i:s');?></p>
				</td>
				<td colspan="2" style="border-right: 0px; border-bottom: 0px">
					<p style="text-align: center;">VATIN: </p>
				</td>
				<td colspan="2" style="border-left: 0px; border-bottom: 0px">&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			
		</table>
	</div>
</div>
<script type="text/javascript">
window.print();
window.onafterprint = function() {
    history.go(-1);
};
</script>