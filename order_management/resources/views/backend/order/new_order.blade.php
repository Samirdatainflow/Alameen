<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title> New Order</title>
    <!-- STYLESHEETS -->
    @include('layout.stylesheets')
    <!-- STYLESHEETS -->
    <style>
      input::-webkit-outer-spin-button,
      input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      /* Firefox */
      input[type=number] {
        -moz-appearance: textfield;
      }
      /*.stock,.row_total{
        vertical-align: middle !important;
      }*/
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
                <h3 class="page-title">New Order</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
              {{Form::open(array('id'=>'create_order'))}}
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="container">
                        <div class="row">
                          <div class="col-md-5">
                            <?php
                            if(!isset($_GET['cart']))
                            {
                            ?>
                            <button class="btn btn-primary bg-blue" type="button" id="download_template">Download Template</button>
                            <!-- <button class="btn btn-primary bg-blue" type="button" id="product_list_download">Download Item</button> -->
                            <?php
                            }
                            ?>
                          </div>
                        </div>

                        <div class="row mt-20" style="display: none">
                          <input type="hidden" name="client" id="client" value="{{Session::get('user_id')}}">
                          <!-- <div class="col-md-3">
                            <select class="form-control" name="client" id="client">
                              <option value="">Select Client*</option>
                              @foreach($clients as $client)
                              <option value="{{$client['client_id']}}">{{$client['customer_name']}}</option>
                              @endforeach
                            </select>
                          </div>-->
                          <div class="col-md-3" >
                            <select class="form-control">
                              <option>Select Category</option>
                              <option>a</option>
                              <option>a</option>
                            </select>
                          </div>
                        </div> 
                      </div>
                      </div>
                      
                        <div class="row">
                          <div class="col-md-12 mt-20">
                            <table class="table table-dark display nowrap" id="new_order" style="width:100%">
                              <thead>
                                <tr>
                                  <th style="width:200px">Part No.</th>
                                  <th style="width:200px">Name</th>
                                  <th>Category</th>
                                  <th>Price</th>
                                  <th style="display: none">Stock</th>
                                  <th style="width:100px">Quantity</th>
                                  <th>Total</th>
                                  <th>&nbsp;</th>
                                </tr>
                              </thead>
                              <tbody id="order_row">
                                <?php
                                $sub_total=0.00;
                                $total_tax=0.00;
                                $grand_total=0.00;
                                if(sizeof($cart_datas)>0 && isset($_GET['cart']))
                                {
                                  foreach ($cart_datas as $key => $cart_data) {
                                  $sub_total +=$cart_data['pmrprc']*$cart_data['qty'];
                                  $total_tax +=0;
                                  $grand_total +=($sub_total+$total_tax);
                                ?>
                                <tr>
                                  <td>
                                    <input type="hidden" name="product_id[]" class="form-control product_id" value="{{$cart_data['product_id']}}"><input type="text" name="part_no[]" class="form-control part_no" value="{{$cart_data['part_no']}}">
                                    {{-- <ul>
                                      <li>ALTERNATOR-REB</li>
                                      <li>ALTERNATOR</li>
                                    </ul> --}}
                                  </td>
                                  <td><input type="text" readonly="readonly" name="name[]" class="form-control name" value="{{$cart_data['part_name']}}"></td>
                                  <td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name" value="{{$cart_data['c_name']}}"><input type="hidden" name="category_id[]" class="form-control category_id" value="{{$cart_data['ct']}}"></td>
                                  <td><input type="text" readonly="readonly" name="gst[]" class="form-control gst" value="0"></td>
                                  <td><input type="text" name="mrp[]" class="form-control mrp" value="{{$cart_data['pmrprc']}}"></td>
                                  <td class="stock" style="display: none">{{$cart_data['current_stock']}}</td>
                                  <td><input type="number" min="1" name="qty[]" class="form-control qty" value="{{$cart_data['qty']}}"></td>
                                  <td class="row_total"></td>
                                  <td><button type="button" class="btn bg-blue text-white new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn bg-blue text-white trash" title="Trash"><i class="fa fa-trash"></i></button></td>
                                </tr>
                                <?php
                                  }
                                }
                                else
                                {
                                ?>
                                <tr>
                                  <td>
                                    <input type="hidden" name="product_id[]" class="form-control product_id"><input type="text" name="part_no[]" class="form-control part_no" autocomplete="off">
                                    {{-- <ul class="list-group">
                                      <li class="list-group-item"><a href="#" style="text-decoration: none"> ALTERNATOR-REB</a></li>
                                      <li class="list-group-item">ALTERNATOR</li>
                                    </ul> --}}
                                  </td>
                                  <td><input type="text" readonly="readonly" name="name[]" class="form-control name"></td>
                                  <td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name"><input type="hidden" name="category_id[]" class="form-control category_id"></td>
                                  <td><input type="text" name="mrp[]" class="form-control mrp"><p class="low_high"></p></td>
                                  <td class="stock" style="display: none"></td>
                                  <td><input type="number" min="1" name="qty[]" class="form-control qty"></td>
                                  <td class="row_total"></td>
                                  <td><button type="button" class="btn bg-blue text-white new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn bg-blue text-white trash" title="Trash"><i class="fa fa-trash"></i></button></td>
                                </tr>
                                <?php
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <?php
                        if(!isset($_GET['cart']))
                        {
                        ?>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Bulk Order Create</label>
                              <input type="file" id="product_csv" name="product_csv" class="file-upload-default" />
                              <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" id="product_csv" name="product_csv" disabled placeholder="Upload CSV" />
                                <span class="input-group-append">
                                  <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                              <button class="btn bg-blue text-white preview-multiple-order" type="button"> Preview </button>
                            </div>
                          </div>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="row">
                          <div class="col-md-6">
                              <label>Remarks</label>
                              <textarea class="form-control" rows="4" name="remarks"></textarea>
                          </div>
                          <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select VAT *</label>
                                    <select class="form-control" name="vat_type_value" id="vat_type_value">
                                        <option value="" data-description="Total Tax" data-percentage="">Select</option>
                                        @php
                                        if(!empty($VatTypeData)) {
                                            foreach($VatTypeData as $vattype) {
                                            
                                            $sel = '';
                                            if(!empty($SaleOrder)) {
                                                if($SaleOrder[0]['vat_type_id'] == $vattype['vat_type_id']) $sel = 'selected="selected"';
                                            }
                                            @endphp
                                            <option value="{{$vattype['vat_type_id']}}" data-percentage="{{$vattype['percentage']}}" data-description="{{$vattype['description']}}" {{$sel}}>{{$vattype['description']}}</option>
                                            @php
                                            }
                                        }
                                        @endphp
                                    </select>
                                </div>
                            <label>Summary</label>
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td>Sub-total</td>
                                        <td><span id="sub-total1">{{$sub_total}}</span><input type="hidden" id="sub-total" name="sub_total" value="{{$sub_total}}" style="border: 0px;background-color: transparent;"></td>
                                    </tr>
                                    <tr>
                                        <td>Total Vat</td>
                                        <td>
                                            <span id="tax1">0</span><input type="hidden" id="tax" name="tax" value="0" style="border: 0px;background-color: transparent;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Grand Total</td>
                                        <td id="randm"><span id="expertSubTotalWithTax1">{{$grand_total}}</span><input type="hidden" name="expertSubTotalWithTax" id="expertSubTotalWithTax" value="{{$grand_total}}" style="border: 0px;background-color: transparent;"></td>
                                    </tr>
                                      
                                </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="row mt-20">
                          <div class="col-md-12 text-center">
                            <button type="submit" class="btn bg-blue text-white"><i class="fa fa-check"></i> Create Order</button>
                          </div>
                        </div>
                      
                    </div>
                  </div>
                </div>
                
              </div>
            </div>
            {{Form::close()}}
            
          </div>
          <!-- FOOTER -->
          @include('layout.footer')
          <!-- FOOTER -->
        </div>
      </div>
    </div>
    <div class="modal fade " id="orderPreviewModal" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content ">
            <div class="modal-header">
               <h4 class="modal-title">Order Preview</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
             
            </div>
            <div class="modal-body" >
              <div class="row ">
                <div class="col-md-12 order_details">
                </div>
                
              </div>
              <div class="row">
                <div class="col-md-12 text-center">
                  <button type="button" class="btn bg-blue text-white create_mutiple_order_csv"><i class="fa fa-check"></i> Create Order</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <script src="{{URL::to('public/backend/js/page/order.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>