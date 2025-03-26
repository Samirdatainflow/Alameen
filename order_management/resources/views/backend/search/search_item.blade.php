<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Search Item</title>
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
          <div class="content-wrapper pb-0 p-20" style="max-width: 100%">
            <div class="page-header">
              <h3 class="page-title">Item Search</h3>
              <nav aria-label="breadcrumb">
              </nav>
            </div>
            <div class="row">
              <div class="col-md-6 text-left">
                  <input type="hidden" id="hidden_model_name">
                  <input type="hidden" id="hidden_category_name">
                  <input type="hidden" id="hidden_subcategory_name">
                  <input type="hidden" id="page_count" value="1">
                  <input type="hidden" id="hidden_car_manufacture">
                  <input type="hidden" id="hidden_model">
                  <input type="hidden" id="hidden_from_year">
                  <input type="hidden" id="hidden_from_month">
                  <input type="hidden" id="hidden_to_year">
                  <input type="hidden" id="hidden_to_month">
                  <input type="hidden" id="hidden_ct">
                  <input type="hidden" id="hidden_sct">
                  <h5 id="choose_items"></h5>
              </div>
            </div>
            <div class="row item-search">
              <div class="col-lg-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-header">
                    <p class="card-title" style="margin-bottom: 0px">Car Manufacture </p>
                  </div>
                  <div class="card-body max-height-400">
                    <div class="row">
                      <div class="col-md-12 ">
                        <ul class="list-group" id="car_manufacture_id">
                          @php
                          if(!empty($CarManufacture)) {
                            foreach($CarManufacture as $data){
                          @endphp
                            <li class="list-group-item car-manufacture" data-car_manufacture_id="{{$data['car_manufacture_id']}}" style="cursor: pointer;" > {{$data['car_manufacture']}} </li>
                          @php
                            }
                          }
                          @endphp
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-header">
                    <p class="card-title" style="margin-bottom: 0px">Car Model </p>
                  </div>
                  <div class="card-body max-height-400">
                    <div class="row">
                      <div class="col-md-12 ">
                        <!-- <input id="search_model" placeholder="Search Model..." type="text" class="form-control" style="border-radius: inherit;" autocomplete="off"> -->
                        <ul class="list-group" id="car_model_id"></ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card " style="display: block;">
                <div class="card">
                    <div class="card-header">
                        <p class="card-title" style="margin-bottom: 0px">From Year </p>
                    </div>
                    <div class="card-body max-height-400">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group" id="category_id">
                                    @php
                                    $fromYear=(int)date('Y');
                                    $toYear = "2000";
                                    for(; $fromYear >= $toYear; $fromYear--) {
                                        $sel = "";
                                        if(!empty($item_data[0]['from_year']))  {
                                            if($item_data[0]['from_year'] == $fromYear) $sel = 'selected="selected"';
                                        }
                                    @endphp
                                            <li data-fromyear="{{$fromYear}}" style="cursor: pointer" class="fromyear list-group-item">{{$fromYear}}</li>
                                    @php
                                    }
                                    @endphp
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card " style="display: block;">
                  <div class="card">
                      <div class="card-header">
                          <p class="card-title" style="margin-bottom: 0px">From Month </p>
                      </div>
                      <div class="card-body max-height-400">
                          <div class="row">
                              <div class="col-md-12">
                                  <ul class="list-group">
                                      @php
                                      $MonthArray = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
                                      foreach($MonthArray as $k=>$v) {
                                      @endphp
                                      <li data-frommonth="{{$k}}" style="cursor: pointer" class="frommonth list-group-item">{{$v}}</li>
                                      @php
                                      }
                                      @endphp
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card " style="display: block;">
                  <div class="card">
                      <div class="card-header">
                          <p class="card-title" style="margin-bottom: 0px">To Year </p>
                      </div>
                      <div class="card-body max-height-400">
                          <div class="row">
                              <div class="col-md-12">
                                  <ul class="list-group" id="category_id">
                                      @php
                                      $fromYear = date("Y",strtotime("+4 year"));
                                      $toYear = "2000";
                                      for(; $fromYear >= $toYear; $fromYear--) {
                                          $sel = "";
                                          if(!empty($item_data[0]['from_year']))  {
                                              if($item_data[0]['from_year'] == $fromYear) $sel = 'selected="selected"';
                                          }
                                      @endphp
                                              <li data-toyear="{{$fromYear}}" style="cursor: pointer" class="toyear list-group-item">{{$fromYear}}</li>
                                      @php
                                      }
                                      @endphp
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card " style="display: block;">
                  <div class="card">
                      <div class="card-header">
                          <p class="card-title" style="margin-bottom: 0px">To Month </p>
                      </div>
                      <div class="card-body max-height-400">
                          <div class="row">
                              <div class="col-md-12">
                                  <ul class="list-group" id="category_id">
                                      @php
                                      $MonthArray = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
                                      foreach($MonthArray as $k=>$v) {
                                      @endphp
                                      <li data-tomonth="{{$k}}" style="cursor: pointer" class="tomonth list-group-item">{{$v}}</li>
                                      @php
                                      }
                                      @endphp
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card category-card" style="display: block">
                <div class="card">
                  <div class="card-header">
                    <p class="card-title" style="margin-bottom: 0px">Category </p>
                  </div>
                  <div class="card-body max-height-400">
                    <div class="row">
                      <div class="col-md-12">
                        <ul class="list-group" id="category_id">
                          @php
                          foreach($ProductCategories as $cat){
                          @endphp
                            <li class="list-group-item category" data-category-id="{{$cat['category_id']}}" data-category_name="{{$cat['category_name']}}" style="cursor: pointer;" > {{$cat['category_name']}} </li>
                          @php
                            }
                          @endphp
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card subcategory-card" style="display: none">
                <div class="card">
                  <div class="card-header">
                    <p class="card-title" style="margin-bottom: 0px">Sub-Category </p>
                  </div>
                  <div class="card-body max-height-400">
                    <div class="row">
                      <div class="col-md-12 ">
                        <ul class="list-group">
                          <ul class="list-group" id="subcategory_id">
                          </ul>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 grid-margin stretch-card oem-card" style="display: none">
                <div class="card">
                  <div class="card-header">
	            	<p class="card-title" style="margin-bottom: 0px">OEM No </p>
	              </div>
                  <div class="card-body max-height-600">
                    
                    <div class="row">
                      <div class="col-md-12 ">
                        <ul class="list-group">
                          <ul class="list-group" id="oem_id">
                          </ul>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row all-item" style="display: none">
            	<div class="col-lg-12 grid-margin stretch-card">
                	<div class="card">
  		            	<div class="card-header">
  			            	<div class="row">
      						      <div class="col-md-9">
      						        <h4 class="card-title" style="margin-bottom: 0px;display: inline-block;line-height: 36px;">Item</h4>
      						      </div>
      						      <div class="col-md-3">
      						        <div class="float-right">
      						          <button class="btn btn-back back"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</button>
      						        </div>
      						      </div>
  			            	</div>
  		            	</div>
                  	<div class="card-body">
                  		<div class="row mb-4">
                  			<div class="col-md-12">
                  				<form class="example">
        									  <input type="text" class="filter-item-search" placeholder="Search by Part No/ Part Name.." name="search2">
        									  {{-- <button type="submit"><i class="fa fa-search"></i></button> --}}
        									</form>
                  			</div>
                  		</div>
                      <div class="row" id="listSearchItems"></div>
	                    <div class="row load-more" style="display: none">
                        <div class="col-md-12">
                          <div class="text-center">
                            <button class="btn btn-info oem"> Load More <i class="fa fa-caret-down" aria-hidden="true"></i></button>
                          </div>
                        </div> 
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
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
   <script src="{{URL::to('public/backend/js/page/search_item.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>