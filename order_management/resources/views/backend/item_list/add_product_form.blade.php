{{ Form::open(array('class'=>'forms-sample', 'id'=>'AddProductForm')) }}
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
        <label for="Barcode">Barcode</label>
         <input type="text" class="form-control" id="Barcode" placeholder="Barcode">
      </div>
      <div class="form-group">
        <label for="City">Choose Supplier</label>
        <select class="form-control">
          <option>Choose Supplier</option>
          @php
          if(!empty($suppliers)) {
            foreach($suppliers as $suppliers)
            {
          @endphp
          <option value="{{$suppliers['supplier_id']}}">{{$suppliers['full_name']}}</option>
          @php
            }
          }
          @endphp
        </select>
      </div>
      <div class="form-group">
        <label for="City">Choose Catagory</label>
        <select class="form-control">
          <option>Choose Catagory</option>
          <?php
            foreach($product_categories as $product_category)
            {
          ?>
          <option value="{{$product_category['category_id']}}">{{$product_category['category_name']}}</option>
          <?php 
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="productSellingPrice">Product Selling Price</label>
        <input type="number" class="form-control" id="productSellingPrice" placeholder="Product Selling Price">
      </div>
      <div class="form-group">
        <label for="vatRate">VAT Rate</label>
        <input type="number" class="form-control" id="vatRate" placeholder="VAT Rate">
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <label for="ProductName">Product Name</label>
        <input type="text" class="form-control" id="ProductName" placeholder="Product Name">
      </div>
      <div class="form-group">
        <label for="City">Choose Product Unit</label>
        <select class="form-control">
          <option>Choose Product Unit</option>
          <option>Unit</option>
          <option>Box</option>
          <option>Kg</option>
          <option>Cm</option>
          <option>Liter</option>
        </select>
      </div>
      <div class="form-group">
        <label for="ProductCost">Product Cost</label>
        <input type="number" class="form-control" id="ProductCost" placeholder="Product Cost">
      </div>
       <div class="form-group">
          <label for="alertQuantity">Alert Quantity</label>
          <input type="text" class="form-control" id="alertQuantity" placeholder="Alert Quantity">
        </div>
    </div>
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="card" style="background-color: #ccc">
        <div class="card-body">
          <h5 class="card-title">Product Dimensions ( Important for storage calculations )</h5>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="Lenght">Lenght</label>
                  <input type="text" class="form-control" id="Lenght" placeholder="Lenght">
                </div>
                <div class="form-group">
                  <label for="height">Height</label>
                  <input type="text" class="form-control" id="height" placeholder="Height">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="Width">Width</label>
                  <input type="text" class="form-control" id="Width" placeholder="Width">
                </div>
                <div class="form-group">
                  <label for="weight">Weight</label>
                  <input type="text" class="form-control" id="weight" placeholder="Weight">
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
 <div class="container mt-2">
  <div class="row">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary mr-2 bg-blue"> Submit </button>
      <button class="btn btn-light">Cancel</button>
    </div>
  </div>
</div>
{{ Form::close() }}