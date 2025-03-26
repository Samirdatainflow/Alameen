{{ Form::open(array('id'=>'previewOrderForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th >Part No.</th>
            <th >Name</th>
            <th>Category</th>
            <th>VAT</th>
            <th>MRP</th>
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
    </div>
  </div>
  <br>
  <p class="text-right">
      <button type="button" name="submit" class="btn-shadow btn btn-info create_mutiple_order_csv" value="Submit"> Create Order </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
  </p>
{{ Form::close() }}