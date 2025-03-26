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
    <style type="text/css">
      .brand {
          text-align: center;
      }
      .brand-option {
          height: 31px; width: 130px;
      }
      .reset{
        background-color: #3399ff;
      }
    </style>
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
            <div class="page-header flex-wrap">
              <div class="header-left">
                <button class="btn btn-primary mb-2 mb-md-0 mr-2"> Create new document </button>
                <button class="btn btn-outline-primary mb-2 mb-md-0"> Import documents </button>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card" style="background-color: #F4EFF3;">
                  <div class="card-body" style="padding: 32px 0px;">
                    <div class="brand">
                      <a href="<?php echo url('add-client');?>" class="btn btn-outline-secondary btn-fw butot"><i class="mdi mdi-plus-circle"></i>&nbsp;Add New Client</a><a href="<?php echo url('recover-client');?>" class="btn btn-outline-secondary btn-fw butot"><i class="mdi mdi-plus-circle"></i>&nbsp;Recover Client</a><a href="#" class="btn btn-outline-secondary btn-fw butot"><i class="mdi mdi-package-down"></i>&nbsp;Download</a>
                    </div>
                    <table id="example" class="table table-striped table-bordered" style="width: 1109px;">
                      <thead>
                        <tr>
                          <th>Company Name</th>
                          <th>Client Name</th>
                          <th>Mobile Phone</th>
                          <th>Phone</th>
                          <th>Email</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          <!-- FOOTER -->
          @include('Layout.footer')
          <!-- FOOTER -->
        </div>
      </div>
    </div>
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <script type="text/javascript">
     $(document).ready(function() {
        $('#example').DataTable();
    } );
   </script>
   <!-- SCRIPTS -->
  </body>
</html>