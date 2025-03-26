<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Settings</title>
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
              <h3 class="page-title">Settings</h3>
              <nav aria-label="breadcrumb">
                
              </nav>
            </div>
            </div>
            <div class="row"> 
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Settings</h4>
                    <form class="form-sample">
                      <p></p>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Database:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <a href="#" class="btn btn-danger">&nbsp;EXPORT</a>
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Brand Name:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Brand Name" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Email:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Email" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Phone: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="phone Number" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Address: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <textarea type="text" class="form-control"></textarea>
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Default Currency: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <select style="height: 30px; width: 160px;">
                              <option>Indian Rupees</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Invoice Footer Massage: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Invoice Footer Massage" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Background: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="selected-box">
                                  <div class="selected-icon">
                                    <img src="#" width="48" height="48">
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-8">
                                <button type="submit" class="btn btn-outline-secondary btn-fw" style="width: 430px;"><i class="mdi mdi-package-down"></i>&nbsp;Upload Background</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Default Currency: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="selected-icon">
                                  <img src="#" width="48" height="48">&nbsp;&nbsp;
                                  <button type="submit" class="btn btn-outline-secondary btn-fw" style="width: 300px;"><i class="mdi mdi-package-down"></i>&nbsp;Upload Background</button>
                                </div>
                              </div>
                            </div>
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