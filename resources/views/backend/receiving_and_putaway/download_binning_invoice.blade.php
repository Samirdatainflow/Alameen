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
					<p><h1><b>BINNING INVOICE</b></h1></p>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 15px">
	<div class="col-md-12">
		<table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
			
			<tr style="background-color: #ccc">
				<td style="height: 35px; text-align: center;">Product Name</td>
				<td style="text-align: center;">Part No</td>
				<td style="text-align: center;" >Quantity</td>
				<td style="text-align: center;">Location</td>
				<td style="text-align: center;">Zone</td>
				<td style="text-align: center;">Row</td>
				<td style="text-align: center;">Rack</td>
				<td style="text-align: center;">Plate</td>
				<td style="text-align: center;">Place</td>
				<td style="text-align: center;">Total Amount</td>
			</tr>
			@php
			//print_r($BinningDetails); exit();
			$total_product_price=0;
			if(sizeof($BinningDetails) > 0) {
				foreach($BinningDetails as $data) {
				$total_product_price = round($data['price']*$data['quantity'], 2);
			@endphp
			<tr class="product-details">
				<td style="text-align: center;" valign="center">{{$data['part_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['pmpno']}}</td>
				<td style="text-align: center;" valign="center">{{$data['quantity']}}</td>
				<td style="text-align: center;" valign="center">{{$data['location_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['zone_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['row_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['rack_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['plate_name']}}</td>
				<td style="text-align: center;" valign="center">{{$data['place_name']}}</td>
				<td style="text-align: center;" valign="center">{{$total_product_price}}</td>
			</tr>
				@php
				}
			}
			@endphp
		</table>
	</div>
</div>
<div class="row">
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
					<b>Prepered By</b>
					</p>
					<p></p>
				</td>
				<td colspan="2" style="border-left: 0px; border-top: 0px">
					<p style="text-align: right;"></p>
					<p style="text-align: right;"><b>Athorized By</b></p>
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

</script>