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
                    <p><h1><b>PURCHASE INVOICE</b></h1></p>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
            <tr>
                <td valign="top">
                    <table cellpadding="5">
                        @php
                        $supplier_name = "";
                        $email = "";
                        $phone = "";
                        
                        if(!empty($order_data)) $supplier_name = $order_data[0]['full_name'];
                        if(!empty($order_data)) $email = $order_data[0]['email'];
                        if(!empty($order_data)) $phone = $order_data[0]['phone'];
                        @endphp
                        <tr>
                            <td>{{$supplier_name}}</td>
                        </tr>
                        <tr>
                            <td>Tel No: </td>
                            <td>{{$phone}}</td>
                        </tr>
                        <tr>
                            <td>Email: </td>
                            <td>{{$email}}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellpadding="5">
                        <tr>
                            @php
                            $invoice_no = "";
                            if(!empty($order_data)) {
                                $invoice_no = $order_data[0]['invoice_no'];
                            }
                            @endphp
                            <td>Invoice No: </td>
                            <td>{{$invoice_no}}</td>
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
    @php
    $subTotal=0;
    $GrandTotal=0;
    
    if(!empty($PurchaseOrderExpenses)) {
    @endphp
    <h3 style="margin:0">Expense Details</h3>
    <table width="50%">
    @php
        foreach ($PurchaseOrderExpenses as $pex) {
        $GrandTotal = $GrandTotal + $pex->expenses_value;
        @endphp
        <tr>
            <td width="70%">{{$pex->expenses_description}}</td>
            <td width="30%">{{$pex->expenses_value}}</td>
        </tr>
        @php
        }
        @endphp
    </table>
    @php
    }
    @endphp
</div>
<div class="row" style="margin-top: 15px">
    <div class="col-md-12">
        <table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
            
            <tr style="background-color: #ccc">
                <td style="height: 35px; text-align: center;">SL No</td>
                <td style="text-align: center;">Part Name</td>
                <td style="text-align: center;" >Part No</td>
                <td style="text-align: center;">Price</td>
                <td style="text-align: center;">Quantity</td>
            </tr>
            @php
            $i=1;
            if(sizeof($order_data) > 0) {
                foreach($order_data as $data) {
                    $subTotal +=($data['mrp'] * $data['qty']);

            @endphp
            <tr class="product-details">
                <td style="text-align: center;">{{$i}}</td>
                <td style="text-align: center;">{{$data['part_name']}}</td>
                <td style="text-align: center;">{{$data['pmpno']}}</td>
                <td style="text-align: center;">{{$data['mrp']}}</td>
                <td style="text-align: center;">{{$data['qty']}}</td>
            </tr>
                @php
                $i++;
                }
                
                $GrandTotal += $subTotal;
            }
            @endphp
            <tr>
                @php
                $vatTitle = "";
                $vatValue = "";
                
                if(isset($vat_percentage) && isset($vat_description)) {
                    
                    $vatTitle = $vat_description;
                    if($vat_percentage > 0 && $vat_percentage !== 'nill' && $vat_percentage !== 'Nill') {
                    
                        $vatValue = ($subTotal * $vat_percentage) / 100;
                        $vatValue = round($vatValue,3);
                        $GrandTotal += $vatValue;
                    }else if($vat_percentage == 'nill' || $vat_percentage == 'Nill') {
                        $vatValue = "Nill";
                    }else if($vat_percentage == '0') {
                        $vatValue = "0";
                    }
                }
                @endphp
                <td colspan="3" valign="top" >
                    &nbsp;{{App\Http\Controllers\PurchaseOrderManagementController::numberTowords(number_format((float)($GrandTotal), 2, '.', ''))}}
                </td>
                <td colspan="2">
                    <table cellspacing="0"  width="100%">                       
                        <tr>
                            <td style="border-bottom: 1px solid #000">Invoice Amount</td>
                            <td style="border-bottom: 1px solid #000" align="center">{{round(($subTotal),2)}}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #000">{{$vatTitle}}</td>
                            <td style="border-bottom: 1px solid #000" align="center">{{$vatValue}}</td>
                        </tr>
                        <tr>
                            <td> Net Amount</td>
                            <td align="center">{{round(($GrandTotal),2)}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        
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