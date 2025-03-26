<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add Item</title>
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
            <div class="page-header flex-wrap">
              <div class="page-header">
              <h3 class="page-title">Add New Item</h3>
              <nav aria-label="breadcrumb">
                
              </nav>
            </div>
            </div>
            <div class="row"> 
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Add New Item</h4>
                    <form class="form-sample">
                      <p></p>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Item Name:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Item Name" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Vehicle:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Vehicle" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Brand:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Brand" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Model: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="Model" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Description: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="Item Description" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Description: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="Item Description" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>MRP: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="MRP" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>List Price: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="List Price" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>HSN: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="HSN" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>GST: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <!-- <label class="col-sm-3 col-form-label">Last Name</label>
                            <div class="col-sm-9"> -->
                              <input type="text" class="form-control" placeholder="GST" />
                            <!-- </div> -->
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>UOM: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="UOM" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Part No: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Part No" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Image: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="file" class="form-control" placeholder="Part No" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <!-- <label>Image: </label> -->
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <button type="submit" class="btn btn-primary btn-fw" style="width: 430px;"><i class="mdi mdi-check"></i>&nbsp;Save</button>&nbsp; &nbsp;
                              <button class="btn btn-outline-secondary btn-fw">Reset</button>
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                    </form>
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
   <!-- <script type="text/javascript">
     $(document).ready(function() {
        $('#example').DataTable();
    } );
   </script> -->
   <!-- SCRIPTS -->
  </body>
</html>