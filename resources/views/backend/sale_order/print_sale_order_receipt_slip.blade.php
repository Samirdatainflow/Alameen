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
@php
$invoice_number = "";
$pay_amount = "";
$payment_date = "";
$customer_name = "";
$invoice_date = "";
$invoice_amount = "";

if(sizeof($ReceiptData) > 0) {

    if(!empty($ReceiptData[0]->invoice_number)) $invoice_number = $ReceiptData[0]->invoice_number;
    if(!empty($ReceiptData[0]->pay_amount)) $pay_amount = $ReceiptData[0]->pay_amount;
    if(!empty($ReceiptData[0]->payment_date)) $payment_date = $ReceiptData[0]->payment_date;
    if(!empty($ReceiptData[0]->customer_name)) $customer_name = $ReceiptData[0]->customer_name;
    if(!empty($ReceiptData[0]->invoice_date)) $invoice_date = $ReceiptData[0]->invoice_date;
    if(!empty($ReceiptData[0]->invoice_amount)) $invoice_amount = $ReceiptData[0]->invoice_amount;
}
$pay_amountWords = getIndianCurrency($pay_amount);
function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees : '') . $paise;
}
@endphp
<table border="0" width="100%">
	<tr>
		<td width="100%" style="text-align: center;">
			<p><h1><b>RECEIPT</b></h1></p>
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<table width="100%">
	<tr>
		<td style="height:50px;font-size:28px;width:60%"><strong>Invoice No:</strong> {{$invoice_number}}</td>
		<td style="height:50px;font-size:28px;"><strong>Payment Date:</strong> {{$payment_date}}</td>
	</tr>
	<tr>
		<td style="height:50px;font-size:28px"><strong>Amount Received:</strong> OMR {{$pay_amount}}</td>
		<td style="height:50px;font-size:28px;width:60%"><strong>Invoice Date:</strong> {{$invoice_date}}</td>
	</tr>
	<tr>
		<td style="height:50px;font-size:28px"><strong>Amount in words:</strong> {{$pay_amountWords}} Omani Rials Only</td>
		<td style="height:50px;font-size:28px;width:60%">&nbsp; </td>
	</tr>
</table>
<p>&nbsp;</p>
<table style="width: 100%;" class="table table-hover table-striped table-bordered">
	<tr>
		<td style="line-height:35px;font-size:30px; text-align:center"><strong>For</strong>:  {{$customer_name}} ................<br><br></td>
	</tr>		
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td style="width:30%;">
		    <span style="font-size:28px"><strong>Mode of Payment:</strong> </span><br>
		    @php
		    $paymentMode = ['Cash' => 'Cash', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer', 'Online Payment' => 'Online Payment'];
		    foreach($paymentMode as $k=>$v) {
		        $checked = "";
		        if(!empty($ReceiptData) > 0) {
                    if(!empty($ReceiptData[0]->pay_mode)) {
                        if($ReceiptData[0]->pay_mode == $v) $checked = 'checked';
                    }
                }
		    @endphp
		    <input type="checkbox" name="demo" value="{{$v}}" {{$checked}}>
		    <label for="{{$v}}" style="font-size:22px"> {{$k}}</label><br>
		    @php } @endphp
		</td>
		<td style="width:30%">&nbsp;</td>
		<td style="font-size:28px">
		    <strong>Amount Of Invoice:</strong> {{$invoice_amount}}<br><br>
		    <strong>Balance Due:</strong> {{$totalDueAmount}}
		</td>
	</tr>
</table>
<script type="text/javascript">
window.print();
window.onafterprint = function() {
    history.go(-1);
};
</script>