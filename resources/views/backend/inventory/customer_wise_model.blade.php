<?php
if(sizeof($price_data)>0)
{
?>
<h5><?php echo sizeof($price_data)>0?$price_data[0]->part_name:"";?> (<?php echo sizeof($price_data)>0?$price_data[0]->pmpno:"";?>)</h5>
<table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Customer Code</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    
    <tbody>
        <?php 
        foreach ($price_data as $data) {
        ?>
        <tr>
            <td><?php echo $data->customer_id;?></td>
            <td><?php echo $data->product_price;?></td>
            <td><?php echo $data->qty_appr;?></td>
            <td><?php if($data->qty_appr > 0) { echo round($data->product_price * $data->qty_appr, 0);}?></td>
        </tr>
        <?php } ?>
    </tbody>
    

</table>
<?php
    }
    else
    {
        echo "<h5>No record found</h5>";
    }
?>