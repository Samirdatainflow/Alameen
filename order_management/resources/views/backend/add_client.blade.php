<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add Client</title>
    <!-- STYLESHEETS -->
    @include('layout.stylesheets')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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
              <h3 class="page-title">Add Client</h3>
              <nav aria-label="breadcrumb">
                
              </nav>
            </div>
            </div>
            <div class="row"> 
              <div class="col-12 grid-margin">
                <div class="card" style="background-color: #eff5f5;">
                  <div class="card-body">
                    <h4 class="card-title">Add New Clients</h4>
                    <form class="form-sample">
                      <p></p>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Name:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client Name" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Company Name:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Company Name" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Phone:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Client Phone" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Mobile:</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client Mobile" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Email: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client Email" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Gst: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client GST Value" />
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
                            <input type="text" class="form-control" placeholder="Client's Address" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Password: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Password" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>BankName: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client Bank Name" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>IFSCCode: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                            <input type="text" class="form-control" placeholder="Client IFSCCode Value" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>State: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="State" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>PinCode: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="PinCode" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Account No: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Account Number" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>Select Brand: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <select class="form-control" >
                                <option>Select Brand</option>
                                <option>A</option>
                                <option>B</option>
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
                              <label>Credit Limit: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="text" class="form-control" placeholder="Credit Limit" />
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                              <label>SHOW ITEM LIST: </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group row">
                              <input type="checkbox" checked data-toggle="toggle">
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
   <!-- SCRIPTS -->

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  </body>
</html>