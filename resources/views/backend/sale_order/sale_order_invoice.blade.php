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
					<p><h1><b>TAX INVOICE</b></h1></p>
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
				<td colspan="3">
					<table cellpadding="5">
						<tr>
							<td>Invoice No: </td>
							<td>WMS-{{ $id }}</td>
						</tr>
						<tr>
							<td>Invoice Date:</td>
							<td><?php echo date('d/m/Y')?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 15px">
	<div class="col-md-12">
		<table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
			
			<tr style="background-color: #ccc">
				<td style="height: 35px; text-align: center;">SL No</td>
				<td style="text-align: center;">Part No</td>
				<td style="text-align: center;">Alternate Part No</td>
				<td style="text-align: center;" >Part Name</td>
				<td style="text-align: center;">Unit</td>
				<td style="text-align: center;">Qty</td>
				<td style="text-align: center;">Amount</td>
				<td style="text-align: center;">Total Amount</td>
			</tr>
			@php
			$i=1;
			$total_product_price=0;
			if(sizeof($SaleOrderDetails) > 0) {
				foreach($SaleOrderDetails as $data) {
				$total_product_price =($data['product_price']*$data['qty_appr']) 
				@endphp
			<tr class="product-details">
				<td style="text-align: center;">{{$i}}</td>
				<td style="text-align: center;">{{$data['pmpno']}}</td>
				<td style="text-align: center;">{{$data['alternate_part_no']}}</td>
				<td style="text-align: center;">{{$data['part_name']}}</td>
				<td style="text-align: center;">{{$data['unit_name']}}</td>
				<td style="text-align: center;">{{$data['qty_appr']}}</td>
				<td style="text-align: center;">{{round(($data['product_price']),2)}}</td>
				<td style="text-align: right;">{{round(($total_product_price),2)}}</td>
			</tr>
				@php
				$i++;
				}
			}
			@endphp
			<tr>
				@php
				//print_r($SaleOrderDetails); exit();
				$total_product_price=0;
				$total_invoice =0;
				$product_tax=0;
					if(sizeof($SaleOrderDetails) > 0) {
						foreach($SaleOrderDetails as $data){
						$total_product_price =($data['product_price']*$data['qty_appr']);
						$total_invoice +=($total_product_price);
						}
					}
				@endphp
				<td colspan="3" valign="top" >
					&nbsp;{{App\Http\Controllers\SaleOrderManagementController::numberTowords(number_format((float)($total_invoice), 2, '.', ''))}}
				</td>
				<td colspan="5">
					<table cellspacing="0"  width="100%">						
						<tr>
							<td style="border-bottom: 1px solid #000">Invoice Amount</td>
							<td style="border-bottom: 1px solid #000" align="right">{{round(($total_invoice),2)}}</td>
						</tr>
						<tr>
						    @php
						    $taxTotal = 0;
						    if($vat_percentage != 0 && $vat_percentage != 'Nil')
                            {
                                $taxTotal = ($total_invoice * $vat_percentage)/100;
                                $taxTotal = round(($taxTotal),3);
                                $total_invoice +=$taxTotal;
                            }
						    @endphp
							<td style="border-bottom: 1px solid #000">Tax amount</td>
							<td style="border-bottom: 1px solid #000" align="right">{{$taxTotal}}</td>
						</tr>
						<tr>
							<td> Net Amount</td>
							<td align="right">{{round(($total_invoice),3)}}</td>
						</tr>
					</table>
				</td>
			</tr>
		
		</table>
	</div>
</div>
<div class="row"><div class="col-md-12"><p>&nbsp;</p><p>&nbsp;</p></div></div>
<div class="row">
	<div class="col-md-12">
		<table width="100%" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" style="border-right: 0px; border-bottom: 0px">
					<p style="margin-left: 10px"><?=wordwrap("I/ We Accepted this invoice correct and all item Received item Good Condition",50,"<br>\n")?></p>
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