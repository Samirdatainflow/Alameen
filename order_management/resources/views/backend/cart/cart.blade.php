<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Cart</title>
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
                <h3 class="page-title">Cart</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                    <?php
                    if(sizeof($cart_datas)>0)
                      {
                    ?>
                      <div class="col-md-12">
                        <a href="<?php echo url('new-order?cart=true')?>" class="btn text-white btn-fw bg-blue"><i class="mdi mdi-plus-circle"></i>&nbsp;Create Cart Order ({{sizeof($cart_datas)}})</a>
                      </div>
                      <?php
                  		}
                      ?>
                      <div class="col-md-12 mt-20">
                        <table class="table table-dark display nowrap" id="new_order" style="width:100%">
                              <thead>
                                <tr>
                                  <th style="width:200px">Product ID</th>
                                  <th style="width:200px">Part No</th>
                                  <th style="width:200px">Name</th>
                                  <th>Category</th>
                                  <th>MRP</th>
                                  <th style="width:100px">Quantity</th>
                                  <th>Total</th>
                                  <th>&nbsp;</th>
                                </tr>
                              </thead>
                              <tbody id="order_row">
                                <?php
                                if(sizeof($cart_datas)>0)
                                {
                                  foreach ($cart_datas as $key => $cart_data) {
                                ?>
                                <tr>
                                  <td>{{$cart_data['product_id']}}</td>
                                  <td>{{$cart_data['pmpno']}}</td>
                                  <td>{{$cart_data['part_name']}}</td>
                                  <td>{{$cart_data['c_name']}}</td>
                                  <td>{{$cart_data['pmrprc']}}</td>
                                  <td><input type="number" min="1" name="qty[]" class="form-control qty" value="{{$cart_data['qty']}}"></td>
                                  <td class="row_total">{{$cart_data['pmrprc']*$cart_data['qty']}}</td>
                                  <td><button type="button" class="btn bg-blue text-white update" title="Update" data-product-id="{{$cart_data['product_id']}}"><i class="fa fa-edit"></i></button> <button type="button" class="btn bg-blue text-white trash" title="Trash" data-product-id="{{$cart_data['product_id']}}"><i class="fa fa-trash"></i></button></td>
                                </tr>
                                <?php
                                  }

                                }
                                else
                                {
                                ?>
                                <td colspan="7" align="center">Your Cart is empty</td>
                                <?php
                                }
                                ?>
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
   @include('layout.scripts')
   <script src="{{URL::to('public/backend/js/page/cart.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>