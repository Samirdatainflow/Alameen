<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Item List</title>
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    @include('layout.stylesheets')
    <!-- STYLESHEETS -->
    <style type="text/css">
      #item td{
        vertical-align: middle !important;
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
              <div class="page-header">
                <h3 class="page-title">Item list</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3">
                        <select class="form-control" id="filter_car_manufacture">
                            <option value="">Select Car Manufacture</option>
                            @if(!empty($car_manufacture))
                                @foreach($car_manufacture as $data)
                                <option value="{{$data['car_manufacture_id']}}">{{$data['car_manufacture']}}</option>
                                @endforeach
                            @endif
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select class="form-control" id="filter_car_model">
                            <option value="">Select Car Model</option>
                            @if(!empty($car_model))
                                @foreach($car_model as $data)
                                <option value="{{$data['brand_id']}}">{{$data['brand_name']}}</option>
                                @endforeach
                            @endif
                        </select>
                      </div>
                      <div class="col-md-3" style="display: none">
                        <select class="form-control" id="filter_from_year">
                            <option value="">Select From Year</option>
                            @php
                            $fromYear=(int)date('Y');
                            $toYear = "2000";
                            for(; $fromYear >= $toYear; $fromYear--) {
                                $sel = "";
                                if(!empty($item_data[0]['from_year']))  {
                                    if($item_data[0]['from_year'] == $fromYear) $sel = 'selected="selected"';
                                }
                            @endphp
                            <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                            @php
                            }
                            @endphp
                        </select>
                      </div>
                      <div class="col-md-3" style="display: none">
                        <select class="form-control" id="filter_from_month">
                            <option value="">Select From Month</option>
                            @php
                            for($i = 0; $i <= 12; ++$i) {
                                $time = strtotime(sprintf('+%d months', $i));
                                $Monthdecimalvalue = date('m', $time);
                                $MonthName = date('F', $time);
                                $sel = "";
                                if(!empty($item_data[0]['from_month']))  {
                                    if($item_data[0]['from_month'] == $Monthdecimalvalue) $sel = 'selected="selected"';
                                }
                                printf('<option value="%s" '.$sel.'>%s</option>', $Monthdecimalvalue, $MonthName);
                            }
                            @endphp
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select name="category_id" class="form-control" id="category_id">
                          <option value="" selected="">Select category</option>
                          @php
                          if(!empty($ProductCategories)) {
                          foreach($ProductCategories as $data) {
                          @endphp
                            <option value="{{$data['category_id']}}">{{$data['category_name']}}</option>
                          @php
                            }
                          }
                          @endphp
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select name="sub_category_id" class="form-control" id="sub_category_id">
                          <option value="" selected="" >Select Sub Category</option>
                        </select>
                      </div>
                    </div><br>
                    <div class="row">
                      <div class="col-md-3" style="display: none">
                        <select class="form-control" id="filter_to_year">
                            <option value="">Select To Year</option>
                            @php
                            $fromYear = date("Y",strtotime("+4 year"));
                            $toYear = "2000";
                            for(; $fromYear >= $toYear; $fromYear--) {
                                $sel = "";
                                if(!empty($item_data[0]['to_year']))  {
                                    if($item_data[0]['to_year'] == $fromYear) $sel = 'selected="selected"';
                                }
                            @endphp
                            <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                            @php
                            }
                            @endphp
                        </select>
                      </div>
                      <div class="col-md-3" style="display: none">
                        <select class="form-control" id="filter_to_month">
                            <option value="">Select To Month</option>
                            @php
                            for($i = 0; $i <= 12; ++$i) {
                                $time = strtotime(sprintf('+%d months', $i));
                                $Monthdecimalvalue = date('m', $time);
                                $MonthName = date('F', $time);
                                $sel = "";
                                if(!empty($item_data[0]['to_month']))  {
                                    if($item_data[0]['to_month'] == $Monthdecimalvalue) $sel = 'selected="selected"';
                                }
                            printf('<option value="%s" '.$sel.'>%s</option>', $Monthdecimalvalue, $MonthName);
                            }
                            @endphp
                        </select>
                      </div>
                      
                      
                    </div><br/>
                    <div class="row">
                      <div class="col-md-3">
                        <select name="product_name" id="product_name" class="form-control">
                          <option value="">Select Part Name</option>
                          @php
                          if(!empty($PartName)) {
                            foreach($PartName as $pndata) {
                            @endphp
                            <option value="{{$pndata['part_name_id']}}">{{$pndata['part_name']}}</option>
                            @php
                            }
                          }
                          @endphp
                        </select>
                      </div>
                      <div class="col-md-3">
                        <input name="part_no" id="part_no" placeholder="Part No" class="form-control">
                      </div>
                      <div class="col-md-3">
                        <select class="form-control" id="filter_part_brand">
                            <option value="">Select Part Brand</option>
                            @if(!empty($PartBrand))
                                @foreach($PartBrand as $data)
                                <option value="{{$data['part_brand_id']}}">{{$data['part_brand_name']}}</option>
                                @endforeach
                            @endif
                        </select>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-success bg-blue" id="reset">Reset</button>
                      </div>
                    </div>                   
                      
                      <div class="col-md-12 mt-20">
                        <div class="row">
                          <div class="col-md-7">
                            <!-- <button type="button" class="btn btn-success bg-blue" id="AddNewProduct"><i class="fa fa-plus-circle"></i> Add New Product</button>
                            <button type="button" class="btn btn-success bg-blue" id="reset"><i class="fa fa-plane" aria-hidden="true"></i> CSV Upload</button> -->
                            <!-- <button type="button" class="btn btn-success bg-blue" id="product_list_download"><i class="fa fa-download" aria-hidden="true"></i> Full Product List Download</button> -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12 mt-20">
                          <table class="table table-dark display nowrap" id="item" style="width:100%">
                            <thead>
                              <tr>
                                <th>Product ID</th>
                                <th>Part No.</th>
                                <th>Name</th>
                                <th>Supplier</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <!-- <th>Warehouse</th> -->
                                {{-- <th>Stock</th> --}}
                                <th>Add To Cart</th>
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
     <div class="modal fade " id="productAddModal" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content " style="width: 1020px;">
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
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
   <script src="{{URL::to('public/backend/js/page/item_list.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>