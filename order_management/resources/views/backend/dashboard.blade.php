<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>
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
                <h3 class="page-title">Dashboard</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
            <!-- BAR -->
            <div class="row">
              <div class="col-xl-4 grid-margin">
                <div class="card stretch-card mb-3" style="background: linear-gradient(135deg, rgb(15, 240, 179) 0%, rgb(3, 110, 217) 100%);border:0px;color:#fff">
                  <div class="card-body d-flex flex-wrap justify-content-between">
                    <div>
                      <h4 class="font-weight-semibold mb-1 text-white"> Order Placed</h4>
                      <h6 class="text-muted"></h6>
                    </div>
                    <h3 class="font-weight-bold text-white">{{$SaleOrder}}</h3>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 grid-margin">
                <div class="card stretch-card mb-3" style="background: linear-gradient(135deg, rgb(240 88 15 / 82%) 0%, rgb(95 217 3 / 89%) 100%);border:0px;color:#fff">
                  <div class="card-body d-flex flex-wrap justify-content-between">
                    <div>
                      <h4 class="font-weight-semibold mb-1 text-white"> Order Approved</h4>
                      <h6 class="text-muted"></h6>
                    </div>
                    <h3 class="font-weight-bold text-white">{{$ApproveOrder}}</h3>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 grid-margin">
                <div class="card stretch-card mb-3" style="background: linear-gradient(135deg, #00f4ddf2 0%, #dc3545d9 100%);border:0px;color:#fff">
                  <div class="card-body d-flex flex-wrap justify-content-between">
                    <div>
                      <h4 class="font-weight-semibold mb-1 text-white"> Order Reject</h4>
                      <h6 class="text-muted"></h6>
                    </div>
                    <h3 class="font-weight-bold text-white">{{$RejectOrder}}</h3>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 grid-margin" >
                <div class="card stretch-card mb-3" style="background: linear-gradient(135deg, rgb(197, 108, 214) 0%, rgb(52, 37, 175) 100%);border:0px;color:#fff">
                  <div class="card-body d-flex flex-wrap justify-content-between">
                    <div>
                      <h4 class="font-weight-semibold mb-1 text-white"> Order Delivered</h4>
                      <h6 class="text-muted"></h6>
                    </div>
                    <h3 class="font-weight-bold text-white">{{$Deliveries}}</h3>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 grid-margin" >
                <div class="card stretch-card mb-3" style="background: linear-gradient(135deg, rgb(24, 78, 104) 0%, rgb(87, 202, 133) 100%);border:0px;color:#fff">
                  <div class="card-body d-flex flex-wrap justify-content-between">
                    <div>
                      <h4 class="font-weight-semibold mb-1 text-white"> Pending Shipment</h4>
                      <h6 class="text-muted"></h6>
                    </div>
                    <h3 class="font-weight-bold text-white">0</h3>
                  </div>
                </div>
              </div>
              <div class="col-xl-8 stretch-card grid-margin">
              </div>
            </div>
            <!-- BAR -->
            <!-- <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Area chart</h4>
                    <canvas id="areaChart" style="height: 340px; display: block; width: 1052px;"></canvas>
                  </div>
                </div>
              </div>
            </div> -->
          </div>
          <!-- BODY -->

          <!-- BODY -->
          <!-- FOOTER -->
          @include('layout.footer')
          <!-- FOOTER -->
        </div>
      </div>
    </div>
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <!-- SCRIPTS -->
  </body>
</html>