<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Order</title>
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
                <h3 class="page-title">Order</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <a href="<?php echo url('new-order')?>" class="btn text-white btn-fw bg-blue"><i class="mdi mdi-plus-circle"></i>&nbsp;Create New Order</a>
                      </div>
                      <div class="col-md-12 mt-20">
                        <table class="table table-dark display nowrap" id="order" style="width:100%">
                          <thead>
                            <tr>
                              <th>Order Id</th>
                              <th>Grand Total Cost</th>
                              <th>Created On</th>
                              <th>Options</th>
                              <th>Remarks</th>
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
   <div class="modal fade " id="orderDetailsModal" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content ">
            <div class="modal-header">
               <h4 class="modal-title">Add Product</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
             
            </div>
            <div class="modal-body" >
              <div class="row">
                
              </div>
            </div>
          </div>
        </div>
      </div>
   @include('layout.scripts')
   <script src="{{URL::to('public/backend/js/page/order.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>