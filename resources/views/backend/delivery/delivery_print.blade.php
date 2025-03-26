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
.delivery-challan td {
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    border-left: 0;
    border-right: 0;
}
</style>
<table border="0" width="100%">
    <tr>
        <!-- <td width="40%" style="text-align: right;">
            <p>GHK798797789800YU</p>
        </td>
        <td width="20%"></td>
        <td>
            <p>PARTS TAX INVOICE </p>
        </td> -->
        <td width="100%" style="text-align: right;">
            <span style="font-size: 20px; font-weight: 700;">DELIVERY CHALLAN</span><br>
            Delivery Challan# - DC - @php if(!empty($DeliveryData[0]['delivery_management_id'])){ echo $DeliveryData[0]['delivery_management_id']; } @endphp
        </td>
    </tr>
</table>
<br>
<table border="0" width="100%">
    <tr>
        <td style="width:30%">
            @if(sizeof($shipping_address) > 0)
            <span style="font-size: 20px; font-weight: 700;">Shipping Address</span>
            <br>{{$shipping_address[0]['address']}}
            @endif
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<table border="0" width="100%" class="delivery-challan">
    <tr style="border:0">
        <td>
            Delivery Challan # <br>
            DC - @php if(!empty($DeliveryData[0]['delivery_management_id'])){ echo $DeliveryData[0]['delivery_management_id']; } @endphp
        </td>
        <td>
            Order Date # <br>
            @php if(!empty($DeliveryData[0]['order_date'])){ echo date('d/m/Y', strtotime($DeliveryData[0]['order_date'])); } @endphp
        </td>
        <td>
            Shipping Date # <br>
            @php if(!empty($ShippingData[0]['created_at'])){ echo date('d/m/Y', strtotime($ShippingData[0]['created_at'])); } @endphp
        </td>
    </tr>
</table>
<br/>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr>
        <td style="width:30%">
            <span style="font-size: 20px; font-weight: 700;">Bill To:</span><br/>
            @php
            $customer_name = "";
            $customer_address = "";
            $customer_wa_no = "";
            if(!empty($clients_data[0]['customer_name'])) $customer_name = $clients_data[0]['customer_name'];
            if(!empty($clients_data[0]['customer_address'])) $customer_address = $clients_data[0]['customer_address'];
            if(!empty($clients_data[0]['customer_wa_no'])) $customer_wa_no = $clients_data[0]['customer_wa_no'];
            echo $customer_name."<br/>";
            echo $customer_address."<br/>";
            echo "Phone: ".$customer_wa_no."<br/>";
            if(!empty($clients_data[0]['vatin']))
            {
                echo "Customer VATIN: ".$clients_data[0]['vatin'];
            }
            @endphp
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<table width="100%" border="1" align="center" cellpadding="0" cellspacing="1">
            
            <tr style="background-color: #ccc">
                <td style="height: 35px; text-align: center;">SL No</td>
                <td style="text-align: center;">Item No</td>
                <td style="text-align: center;" >Item</td>
                <td style="text-align: center;">Unit</td>
                <td style="text-align: center;">QTY</td>
                <td style="text-align: center;">Rate</td>
                <td style="text-align: center;">Total Amount</td>
                <td style="text-align: center;">Gross Amount</td>
            </tr>
            @php
            $i=1;
            $total_product_price=0;
            if(sizeof($ProductData) > 0) {
                foreach($ProductData as $data) {
                    $pmrprc = 0;
                    $quantity = 0;
                    if(is_numeric($data['price'])) $pmrprc = $data['price'];
                    if(is_numeric($data['quantity'])) $quantity = $data['quantity'];
                    $total_product_price =($pmrprc*$quantity) 
                @endphp
            <tr class="product-details">
                <td style="text-align: center;">{{$i}}</td>
                <td style="text-align: center;">{{$data['pmpno']}}</td>
                <td style="text-align: center;">{{$data['part_name']}}</td>
                <td style="text-align: center;">{{$data['unit_name']}}</td>
                <td style="text-align: center;">{{$data['quantity']}}</td>
                <td style="text-align: center;">{{round(($data['price']),2)}}</td>
                <td style="text-align: center;">{{round(($total_product_price),2)}}</td>
                <td style="text-align: center;">{{round(($total_product_price),2)}}</td>
            </tr>
                @php
                $i++;
                }
            }
            @endphp
            <tr>
                @php
                $total_product_price=0;
                $total_invoice =0;
                $product_tax=0;
                    if(sizeof($ProductData) > 0) {
                        foreach($ProductData as $data){
                        $pmrprc = 0;
                        $quantity = 0;
                        if(is_numeric($data['price'])) $pmrprc = $data['price'];
                        if(is_numeric($data['quantity'])) $quantity = $data['quantity'];
                        $total_product_price =($pmrprc*$quantity);
                        $total_invoice +=($total_product_price);
                        }
                    }
                @endphp
                <td colspan="4" rowspan="3" valign="top" cellpadding="5" style="line-height:30px;">
                    &nbsp;{{App\Http\Controllers\SaleOrderManagementController::numberTowords(number_format((float)($total_invoice+$product_tax), 2, '.', ''))}}
                </td>
                <td colspan="2" style="line-height:30px; text-align:center">Invoice Amount</td>
                <td colspan="2" style="line-height:30px; text-align:center">{{round(($total_invoice),2)}}</td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:30px; text-align:center">VAT Amount</td>
                <td colspan="2" style="line-height:30px; text-align:center">{{round(($product_tax),2)}}</td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:30px; text-align:center"> Net Amount</td>
                <td colspan="2" style="line-height:30px; text-align:center">{{round(($total_invoice+$product_tax),2)}}</td>
            </tr>
        
        </table>
<br/><br/><br/>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td style="border-left: 0px; border-top: 0px">
            <p style="text-align: left;"></p>
            <p style="text-align: left;"><b>Stamp</b></p>
        </td>
        <td style="border-left: 0px; border-top: 0px">
            <p style="text-align: right;"></p>
            <p style="text-align: right;"><b>Prepared By</b></p>
        </td>
        <td style="border-left: 0px; border-top: 0px">
            <p style="text-align: right;"></p>
            <p style="text-align: right;"><b>Authorized By</b></p>
        </td>
    </tr>
</table>