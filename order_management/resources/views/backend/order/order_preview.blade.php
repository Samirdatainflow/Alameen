<table class="table table-dark" style="width:100%">
  <thead>
    <tr>
      <th >Part No.</th>
      <th >Name</th>
      <th>Category</th>
      <th>VAT</th>
      <th>MRP</th>
      <th>Stock</th>
      <th>Quantity</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
  	@foreach($products as $product)
    @if(sizeof($product)>0 && isset($product['part_no']))
    <tr>
      <td>{{$product['part_no']}}</td>
      <td>{{$product['part_name']}}</td>
      <td>{{$product['c_name']}}</td>
      <td>0</td>
      <td>{{$product['pmrprc']}}</td>
      <td>{{$product['current_stock']}}</td>
      <td>{{$product['qty']}}</td>
      <td>{{($product['qty']*$product['pmrprc'])}}</td>
    </tr>
    @else
    <tr style="background-color: red">
      <td>{{$product['product_id']}}</td>
      <td colspan="8" align="center">{{$product['qty']}}</td>
    </tr>
    @endif
    @endforeach
  </tbody>
</table>