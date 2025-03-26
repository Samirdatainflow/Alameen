<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Item</title>
    <!-- STYLESHEETS -->
    @include('layout.stylesheets')
    <!-- STYLESHEETS -->
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_horizontal-navbar.html -->
      <!-- HEADER -->
      @include('layout.header')
      <!-- HEADER -->
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
          <div class="content-wrapper pb-0">
              <div class="page-header">
                <h3 class="page-title">Item</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3">
                          <input name="product_name" id="product_name" placeholder="Product name" class="form-control">
                      </div>
                      <div class="col-md-3">
                          <select name="category" class="form-control" id="category">
                            <option value="" selected="" disabled="">Select category</option>
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
                      <div class="col-md-2">
                        <button type="submit" class="btn btn-success bg-blue" id="reset">Reset</button>
                      </div>
                      <div class="col-md-12 mt-20">
                          <table class="table table-dark display nowrap" id="item" style="width:100%">
                            <thead>
                              <tr>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Supplier</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Warehouse</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                              
                            </tbody>
                          </table>
                      </div>
                    </div>
                  </div>
                </div>
                
              </div>
            </div>
            
          </div>
          <!-- FOOTER -->
          @include('layout.footer')
          <!-- FOOTER -->
        </div>
      </div>
    </div>
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <script src="{{URL::to('public/backend/js/page/item.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>