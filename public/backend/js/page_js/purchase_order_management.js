// MOdal Show
function purchase_order_form_1() {
    $("#CommonModal").find("#userPurchaseOrderForm").validate({
        rules: {
            estimated_delivery_date: "required",
            warehouse: "required",
            supplier: "required",
            product: "required"
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('input');
            var x=0;
            $(last_tr).each(function(){
                if($(this).val()=="" && !$(this).hasClass('entry-product-name') && !$(this).hasClass('category_name') && !$(this).hasClass('gst')) {
                    x=1;
                }
            })
            if(x==1) {
                swal("Warning!", "Enter data first", "error");
            }else {
                var formData = new FormData($('#userPurchaseOrderForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/save-purchase-order-management",  
                    type: "POST",
                    data:  formData,
                    contentType: false,
                    cache: false,
                    processData:false, 
                    dataType:"json", 
                    beforeSend:function(){  
                        showLoader();
                    },  
                    success:function(res){
                        if(res["status"]) {
                            hideLoader();
                            $('#userPurchaseOrderForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#CommonModal').modal('hide');
                                PurchaseOrderTable.draw();
                            });
                        }else {
                            hideLoader();
                            swal("Opps!", res["msg"], "error");
                        }
                    },
                    error: function(e) {
                        hideLoader();
                        swal("Opps!", "There is an error", "error");
                    },
                    complete: function(c) {
                        hideLoader();
                    }
                });
            }
        }
    });
}
var PurchaseOrderTable = $('#PurchaseOrderList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-purchase-order-management",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
          data.filter_part_brand=$("#filter_part_brand").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'deliverydate', name: 'deliverydate', orderable: false, searchable: false},
        {data: 'item', name: 'item', orderable: false, searchable: false},
        {data: 'barcode', name: 'barcode', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        //{data: 'approved_status', name: 'approved_status', orderable: false, searchable: false},
        //{data: 'received_status', name: 'received_status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
var SavePurchaseOrderList = $('#SavePurchaseOrderList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-save-purchase-order-management",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'deliverydate', name: 'deliverydate', orderable: false, searchable: false},
        {data: 'item', name: 'item', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        //{data: 'approved_status', name: 'approved_status', orderable: false, searchable: false},
        //{data: 'received_status', name: 'received_status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_supplier").on('keyup input',function(){
    PurchaseOrderTable.draw();
});
$("#filter_part_brand").on('change',function(){
    PurchaseOrderTable.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_supplier').val('');
    $('#filter_part_brand').val('');
    PurchaseOrderTable.draw();
})
$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addPurchaseOrder()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Purchase Order</button> <button type="button" aria-haspopup="true" onclick="ExportPurchaseOrder()" aria-expanded="false" class="btn-shadow btn btn-info" title="Export"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');

$('#SavePurchaseOrderList_wrapper div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addPurchaseOrder()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Purchase Order</button> <button type="button" aria-haspopup="true" onclick="ExportSavePurchaseOrder()" aria-expanded="false" class="btn-shadow btn btn-info" title="Export"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportPurchaseOrder() {
    window.location.href = base_url+"/purchase-order-export";
}

function ExportSavePurchaseOrder() {
    window.location.href = base_url+"/save-purchase-order-export";
}
// Add Order form
function addPurchaseOrder() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/add-purchase-order-management",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Purchase Order");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $('.datetimepicker').datepicker({
                    format : 'dd/mm/yyyy',
                    todayHighlight: true,
                    autoclose: true,
                });
                $(".modal-dialog").addClass('modal-xl');
                //$("#order_date").attr('readonly', true);
                //purchase_order_form();
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
}
// Create Request Order
$('body').on('click', '#CreateOrder', function(){
    var form = $('#userPurchaseOrderForm');
    var estimated_delivery_date = $('#estimated_delivery_date').val();
    var warehouse = $('#warehouse').val();
    var supplier = $('#supplier').val();
    if(estimated_delivery_date == '') {
        swal("Sorry!", "Please Enter Estimated Delivery Date!", "error");
    }else if(warehouse == '') {
        swal("Sorry!", "Please Select Warehouse!", "error");
    }else if(supplier == '') {
        swal("Sorry!", "Please Select Supplier!", "error");
    }else if($("#vat_type_value").val() == "" || $("#vat_type_value").val() == null) {
          swal("Warning!", "Please select VAT", "error");
    }else {
        var last_tr = $('body #entryProductTbody tr').find('input');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="" && !$(this).hasClass('entry-product-name') && !$(this).hasClass('category_name') && !$(this).hasClass('gst') && !$(this).hasClass('previous_lc_price')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Sorry!", "Enter data first", "error");
        }else {
            swal({
                title: "Are you sure?",
                text: "You want to create this order request!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes',
                cancelButtonText: "No",
            }).then(function(isConfirm) {
                if (isConfirm && isConfirm.value) {
                    var formData = form.serializeArray();
                    formData.push({ name: "orders_status", value: "CreateOrder" });
                    $.ajax({
                        url : base_url+"/save-purchase-order-management", 
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        dataType:'json',
                        beforeSend:function(){  
                          $("#loader").css("display","block");
                        },
                        success: function(res){
                            if(res['status']) {
                                swal({
                                    title: 'Success',
                                    text: res['msg'],
                                    icon: 'success',
                                    type:'success',
                                }).then(function() {
                                window.location.href=base_url+"/"+res.return_url;
                            });
                        }
                        else {
                            swal("Warning!", res['msg'], "error");
                        }
                    },
                    error:function(error){
                      swal("Warning!", "Sorry! There is an error", "error");
                    },
                    complete:function(){
                      $("#loader").css("display","none");
                    }
                  });
                }
            });
        }
    }
});
// Save Request Order
$('body').on('click', '#SaveOrder', function(){
    var form = $('#userPurchaseOrderForm');
    var estimated_delivery_date = $('#estimated_delivery_date').val();
    var warehouse = $('#warehouse').val();
    var supplier = $('#supplier').val();
    if(estimated_delivery_date == '') {
        swal("Sorry!", "Please Enter Estimated Delivery Date!", "error");
    }else if(warehouse == '') {
        swal("Sorry!", "Please Select Warehouse!", "error");
    }else if(supplier == '') {
        swal("Sorry!", "Please Select Supplier!", "error");
    }else if($("#vat_type_value").val() == "" || $("#vat_type_value").val() == null) {
          swal("Warning!", "Please select VAT", "error");
    }else {
        var last_tr = $('body #entryProductTbody tr').find('input');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="" && !$(this).hasClass('entry-product-name') && !$(this).hasClass('category_name') && !$(this).hasClass('gst') && !$(this).hasClass('previous_lc_price')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Sorry!", "Enter data first", "error");
        }else {
            swal({
                title: "Are you sure?",
                text: "You want to save this order request!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes',
                cancelButtonText: "No",
            }).then(function(isConfirm) {
                if (isConfirm && isConfirm.value) {
                    var formData = form.serializeArray();
                    formData.push({ name: "orders_status", value: "SaveOrder" });
                    $.ajax({
                        url : base_url+"/save-purchase-order-management", 
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        dataType:'json',
                        beforeSend:function(){  
                          $("#loader").css("display","block");
                        },
                        success: function(res){
                            if(res['status']) {
                                swal({
                                    title: 'Success',
                                    text: res['msg'],
                                    icon: 'success',
                                    type:'success',
                                }).then(function() {
                                window.location.href=base_url+"/"+res.return_url;
                            });
                        }
                        else {
                            swal("Warning!", res['msg'], "error");
                        }
                    },
                    error:function(error){
                      swal("Warning!", "Sorry! There is an error", "error");
                    },
                    complete:function(){
                      $("#loader").css("display","none");
                    }
                  });
                }
            });
        }
    }            
});
// Edit Purchased Order
$('body').on('click', 'a.edit-purchase-order', function() {
    var order_id = $(this).data('id')
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/add-purchase-order-management",
        type:'post',
        dataType:'JSON',
        data: {order_id:order_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Purchase Order");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $('.datetimepicker').datepicker({
                    format : 'dd/mm/yyyy',
                    todayHighlight: true,
                    autoclose: true,
                });
                $(".modal-dialog").addClass('modal-xl');
                //$("#order_date").attr('readonly', true);
                //purchase_order_form();
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
//
$('body').on('click', '.order-request-radio', function() {
    var val = $(this).val();
    if(val == "yes") {
        $('#OrderRequestSection').css('display', 'flex');
        $('#fetchCartOrder').css('display', 'none');
    }else {
        $('#fetchCartOrder').css('display', 'block');
        $('#OrderRequestSection').css('display', 'none');
        $('#entryProductTbody').html("");
        $('#entryProductTbody').append('<tr id="entryProductRow1"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly"><input type="hidden" class="form-control category_id" name="category_id[]"></td><td><input type="text" class="form-control gst" name="gst[]" readonly="readonly"></td><td><input type="text" class="form-control mrp" name="mrp[]"><span class="viewMRP"></span></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td><td class="total_price"></td><td style="width: 12%;"><a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a></td></tr><tr id="ListProductEntry" style="display: none"><td></td><td></td><td></td><td></td></tr>');
        $('#hidden_supplier_id').val('');
        $('#supplier').val('');
        $('#order_request_id').val('');
    }
});
// Fetch order from cart
$('body').on('click', '.order-from-cart', function() {
    var val = $(this).val();
    if(val == "yes") {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/purchase-order/list-order-from-cart",  
            type: "POST",
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                hideLoader();
                //console.log(res);
                if(res["status"]) {
                    $('#entryProductTbody').html('');
                    var fetchData = "";
                    for(var i=0; i<res.data.length; i++) {
                        var mrp = res.data[i].mrp;
                        var qty = res.data[i].qty;
                        var gst = res.data[i].vat;
                        var net_total = (((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100)+(parseFloat(mrp)*parseFloat(qty));
                        net_total = net_total.toFixed(2);
                        fetchData += '<tr id="entryProductRow'+i+'"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off" value="'+res.data[i].pmpno+'"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly" value="'+res.data[i].part_name+'"><input type="hidden" class="form-control entry-product-id" name="entry_product[]" value="'+res.data[i].product_id+'"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly" value="'+res.data[i].category_name+'"><input type="hidden" class="form-control category_id" name="category_id[]" value="'+res.data[i].category_id+'"></td><td><input type="text" class="form-control gst" name="gst[]" readonly="readonly" value="'+res.data[i].vat+'"></td><td><input type="text" class="form-control mrp" name="mrp[]" readonly="readonly" value="'+res.data[i].mrp+'"></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]" value="'+res.data[i].qty+'"></td><td class="total_price">'+net_total+'</td><td style="width: 12%;"> <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry('+i+')"><i class="fa fa-window-close" aria-hidden="true"></i></button></td></tr>';
                    }
                    $('#entryProductTbody').append(fetchData);
                    final_calculation();
                }else {
                    swal("Opps!", res["msg"], "error");
                }
            },
            error: function(e) {
                swal("Opps!", "There is an error", "error");
            },
            complete: function(c) {
                //
            }
        });
    }else {
        $('#entryProductTbody').html('');
        $('#entryProductTbody').append('<tr id="entryProductRow1"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly"><input type="hidden" class="form-control category_id" name="category_id[]"></td><td><input type="text" class="form-control gst" name="gst[]" readonly="readonly"></td><td><input type="text" class="form-control mrp" name="mrp[]" readonly="readonly"></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td><td class="total_price"></td><td style="width: 12%;"><a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a></td></tr><tr id="ListProductEntry" style="display: none"><td></td><td></td><td></td><td></td></tr>');
        $('#hidden_supplier_id').val('');
    }
});
//
$('body').on('click', '#get_order_request_id', function() {
    var order_request_id = $('#order_request_id').val();
    if(order_request_id == "") {
        swal("Warning!", "Please enter a request order id.", "warning");
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/purchase-order/get-order-request-details",
            type:'post',
            dataType:'JSON',
            data: {order_request_id:order_request_id},
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                console.log(res);
                if(res['status']) {
                    hideLoader();
                    $('#entryProductTbody').html("");
                    for(var i=0; i< res.data.length; i++) {
                        var product_entry_count = i + 1;
                        var mrp = res.data[i].supplier_price;
                        var qty = res.data[i].qty;
                        var gst = res.data[i].vat;
                        var net_total = (((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100)+(parseFloat(mrp)*parseFloat(qty));
                        net_total = net_total.toFixed(2);
                        $(this).parents('tr').find('.total_price').html(net_total);
                        $('#entryProductTbody').append('<tr id="entryProductRow'+product_entry_count+'"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off" value="'+res.data[i].pmpno+'"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly" value="'+res.data[i].part_name+'"><input type="hidden" class="form-control entry-product-id" name="entry_product[]" value="'+res.data[i].product_id+'"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly" value="'+res.data[i].category_name+'"><input type="hidden" class="form-control category_id" name="category_id[]" value="'+res.data[i].category_id+'"></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]" value="'+res.data[i].qty+'"></td><td><input type="text" class="form-control mrp" name="mrp[]" value="'+res.data[i].supplier_price+'"></td><td><input type="text" class="form-control gst" name="gst[]" readonly="readonly" value="'+res.data[i].vat+'"></td><td class="total_price">'+net_total+'</td><td style="width: 12%;"> <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry('+product_entry_count+')"><i class="fa fa-window-close" aria-hidden="true"></i></button></td></tr>');
                    }
                    $('#hidden_supplier_id').val(res.supplier_id);
                    $('#supplier').val(res.supplier_id);
                    final_calculation();
                }else {
                    hideLoader();
                    $('#entryProductTbody').html("");
                    $('#entryProductTbody').append('<tr id="entryProductRow1"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly"><input type="hidden" class="form-control category_id" name="category_id[]"></td><td><input type="text" class="form-control gst" name="gst[]" readonly="readonly"></td><td><input type="text" class="form-control mrp" name="mrp[]" readonly="readonly"></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td><td class="total_price"></td><td style="width: 12%;"><a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a></td></tr><tr id="ListProductEntry" style="display: none"><td></td><td></td><td></td><td></td></tr>');
                    swal({title: "Sorry!", text: res.msg, type: "warning"});
                    $('#hidden_supplier_id').val('');
                }
            },
            error:function(){
                hideLoader();
                swal({title: "Sorry!", text: "There is an error", type: "error"});
            },
            complete:function(){
                hideLoader();
            }
        });
    }
});
// Add product entry
$('body').on('click', 'a.add-entry-product', function() {
    var last_tr = $('body #entryProductTbody tr').find('input');
    var x=0;
    $(last_tr).each(function(){
        if($(this).val()=="" && !$(this).hasClass('entry-product-name') && !$(this).hasClass('category_name') && !$(this).hasClass('gst') && !$(this).hasClass('mrp') && !$(this).hasClass('previous_lc_price')) {
            x=1;
        }
    })
    if(x==1) {
        swal("Warning!", "Enter data first", "error");
    }else {
        var product_entry_count = $('#product_entry_count').val();
        $('#ListProductEntry').before('<tr id="entryProductRow'+product_entry_count+'"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="text" class="form-control category_name" name="category_name[]" readonly="readonly"><input type="hidden" class="form-control category_id" name="category_id[]"></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td><td><input type="text" class="form-control mrp" name="mrp[]"><span class="viewMRP"></span></td><td class="total_price"></td><td style="width: 12%;"><a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a> <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry('+product_entry_count+')"><i class="fa fa-window-close" aria-hidden="true"></i></button></td></tr>');
    }
});
// Rwemove product entry
function removeProductEntry(id) {
    $('#entryProductRow'+id).remove();
    final_calculation();
}
// Delete Order
$("body").on("click", "a.delete-purchase-order", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
    swal({
        title: "Are you sure?",
        text: "You want to remove this order!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes.",
        cancelButtonText: "No!",
        confirmButtonClass: "btn btn-success mr-5",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: !1
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/delete-purchase-order",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            PurchaseOrderTable.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    //
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});
// View Order Details
$('body').on('click', 'a.view-order-details', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-purchase-order-details",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("View Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-xl');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
// Print Order Details
$('body').on('click', 'a.print-order-details', function() {
    var divToPrint=document.getElementById('OderDetailsContent');
    var newWin=window.open('','Print-Window');
    newWin.document.open();
    newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
    newWin.document.close();
    //setTimeout(function(){newWin.close();},10);
});
function checkAlreadyExistsProduct(_this, pmpno) {
  var parentTr = $(_this).parents("tr");
  var last_tr=$('body #entryProductTbody tr').not(parentTr).find('.entry-part-no');
  var r=0;
  $(last_tr).each(function(){
    if($(this).val() == pmpno)
    {
      r=1;
      
    }
  });
  return r;
}
// List product by Part No
$("body").on('keyup input', '#entryProductTbody .entry-part-no', function(element){
    var _this = $(this);
    var supplier = $('#supplier').val();
    var warehouse = $('#warehouse').val();
    var part_no = $(this).val();
    if(warehouse == "") {
        swal("Warning!", "Please select a Warehouse.", "error");
        $(this).val('');
    }else if(supplier == "") {
        swal("Warning!", "Please select a Supplier.", "error");
        $(this).val('');
    }else if(part_no != "") {
        $.ajax({
            url : base_url+"/purchase_order/get-product-by-part-no", 
            type: 'POST',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {part_no: part_no, supplier:supplier, warehouse:warehouse},
            dataType:'json',
            beforeSend:function(){  
                //showLoader();
            },
            success: function(res){
                if(res['status']) {
                    hideLoader();
                    _this.parents('td').find('.list-group').remove();
                    _this.parents('td').find('.entry-part-no').after(res.data);
                }else {
                    _this.parents('td').find('.list-group').remove();
                    //hideLoader();
                }
            },
            error:function(error){
                //hideLoader();
                swal("Warning!", "Sorry! There is an error", "error");
            },
            complete:function(){
                //hideLoader();
            }
        });
    }else {
        _this.parents('td').find('.list-group').remove();
        _this.parents('tr').find('.entry-part-no').val('');
        _this.parents('tr').find('.entry-product-name').val('');
        _this.parents('tr').find('.entry-product-id').val('');
        _this.parents('tr').find('.entry-product-id').val('');
        _this.parents('tr').find('.category_name').val('');
        _this.parents('tr').find('.category_id').val('');
        _this.parents('tr').find('.gst').val('');
        _this.parents('tr').find('.mrp').val('');
    }
});
$("body").on("click",'#entryProductTbody .product-details',function(){
  var product_entry_count = $('#product_entry_count').val();
  var pmpno = $(this).data('pmpno');
  _this = $(this);
  if(!checkAlreadyExistsProduct(_this, pmpno)) {
    $.ajax({
      url : base_url+"/purchase_order/get-product-details", 
      type: 'POST',
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {part_no:pmpno, product_entry_count:product_entry_count},
      dataType:'json',
      beforeSend:function(){  
        //showLoader();
      },
      success: function(res){
          console.log("res",res);
        if(res.data.length > 0) {
            hideLoader();
          _this.parents('tr').find('.entry-part-no').val(res.data[0].pmpno);
          _this.parents('tr').find('.entry-product-name').val(res.data[0].part_name);
          _this.parents('tr').find('.entry-product-id').val(res.data[0].product_id);
          _this.parents('tr').find('.entry-product-id').val(res.data[0].product_id);
          _this.parents('tr').find('.category_name').val(res.data[0].c_name);
          _this.parents('tr').find('.category_id').val(res.data[0].ct);
          _this.parents('tr').find('.mrp').val(res.data[0].selling_price);
          _this.parents('tr').find('.gst').val(0);
          _this.parents('tr').find('.viewMRP').text("MRP: "+res.data[0].pmrprc);
          if(res.data[0].last_po_price > 0) {
              _this.parents('tr').find('.viewLPP').text("LPP: "+res.data[0].last_po_price);
              _this.parents('tr').find('.previous_lc_price').val(res.data[0].last_po_price);
          }
          $('#product_entry_count').val(res.product_entry_count);
          _this.parents('td').find('.list-group').remove();
        }else {
            //hideLoader();
          _this.parents('tr').find('.entry-part-no').val('');
          _this.parents('tr').find('.entry-product-name').val("");
          _this.parents('tr').find('.entry-product-id').val("");
        }
        
      },
      error:function(error){
          //hideLoader();
        swal("Warning!", "Sorry! There is an error", "error");
      },
      complete:function(){
        //hideLoader();
      }
    });
  }else {
    swal("Warning!", "Sorry! You have already added this product", "error");
  }
});
// Calculate Product Price By Quantity
$('body').on('keyup paste','#entryProductTbody .entry-product-quantity, #entryProductTbody .mrp',function(){
    var qty = $(this).parents('tr').find('.entry-product-quantity').val();
    var net_total = 0;
    var cal_gst = 0;
    //var tax_rate = $('#hidden_tax_rate').val();
    if(qty != ""){
        var mrp = 0;
        if($(this).parents('tr').find('.mrp').val() > 0 && $(this).parents('tr').find('.mrp').val() != NaN) {
            mrp = $(this).parents('tr').find('.mrp').val();
        }
        var gst = 0;
        // if($(this).parents('tr').find('.gst').val() > 0 && $(this).parents('tr').find('.gst').val() != NaN) {
        //     gst = $(this).parents('tr').find('.gst').val();
        // }
        //net_total = (((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100)+(parseFloat(mrp)*parseFloat(qty));
        net_total = parseFloat(mrp) * parseFloat(qty);
        //net_total = net_total.toFixed(3);
        //cal_gst = (net_total * tax_rate) / 100;
        //cal_gst = cal_gst.toFixed(3);
        //net_total = parseFloat(net_total) + parseFloat(cal_gst);
        net_total = net_total.toFixed(3);
    }
    $(this).parents('tr').find('.total_price').html(net_total);
    $(this).parents('tr').find('.gst').val(cal_gst);
    final_calculation();
});
function final_calculation(){
    var sub_total=0;
    var total_tax=0;
    var grand_total=0;
    
    $('#entryProductTbody tr').each(function(){
        var mrp = "0";
        if(!isNaN(parseFloat($(this).find('.mrp').val()))) {
            mrp = parseFloat($(this).find('.mrp').val());
        }
        var qty = "0";
        if(!isNaN(parseFloat($(this).find('.entry-product-quantity').val()))) {
            qty = $(this).find('.entry-product-quantity').val()==""?0:parseFloat($(this).find('.entry-product-quantity').val());
        }
        var csub_total =(mrp*qty);
        sub_total += parseFloat(csub_total);
    });
    
    // Expenses calculation
    var totalExpenses = 0;
    
    var ExpensesDetails=$('#CommonModal #entryExpensesTbody').find('.expenses-value');
    $(ExpensesDetails).each(function(){
        
        var expenses_value = $(this).val();
        var expenses_vat = $(this).parents('tr').find('.expenses-vat').val();
        
        if(expenses_value !== NaN && expenses_value > 0){
        
            totalExpenses += parseFloat(expenses_value);
            
            if(expenses_vat > 0 && expenses_vat !== NaN)
            {
                var calExpenseVat = (expenses_value * parseFloat(expenses_vat))/100;
                totalExpenses += calExpenseVat;
            }
        }
    });
    
    if(totalExpenses > 0)
    {
        totalExpenses = totalExpenses.toFixed(3);
        $('#total_expense_show').text(totalExpenses);
        $('#total_expense').val(totalExpenses);
    }
    
    sub_total = sub_total.toFixed(3);
    grand_total = sub_total;
    grand_total = parseFloat(grand_total) + parseFloat(totalExpenses);
    
    $("#sub-total").val(sub_total);
    $("#sub_total_show").text(sub_total);
    $("#grand_total").val(grand_total);
    $("#grand_total_show").text(grand_total);
}

$('body').on('change', '#vat_type_value', function() {
        
    var vat = $(this).find(':selected').data('percentage');
    var sub_total = $('#sub-total').val();
    
    if(isNaN(vat)){
        $('#total_tax_show').text(vat);
        $('#tax').val(vat);
        
        $('#grand_total_show').text(sub_total);
        $('#grand_total').val(sub_total);
    }else {
        
        var totalTax = (sub_total * vat)/100;
        
        totalTax = totalTax.toFixed(3);
        $('#total_tax_show').text(totalTax);
        $('#tax').val(totalTax);
        
        // Expenses calculation
        var totalExpenses = 0;
        
        var ExpensesDetails=$('#CommonModal #entryExpensesTbody').find('.expenses-value');
        $(ExpensesDetails).each(function(){
            
            var expenses_value = $(this).val();
            var expenses_vat = $(this).parents('tr').find('.expenses-vat').val();
            
            if(expenses_value !== NaN && expenses_value > 0){
            
                totalExpenses += parseFloat(expenses_value);
                
                if(expenses_vat > 0 && expenses_vat !== NaN)
                {
                    var calExpenseVat = (expenses_value * parseFloat(expenses_vat))/100;
                    totalExpenses += calExpenseVat;
                }
            }
        });
        
        if(totalExpenses > 0)
        {
            totalExpenses = totalExpenses.toFixed(3);
            $('#total_expense_show').text(totalExpenses);
            $('#total_expense').val(totalExpenses);
        }
        
        var grandTotal = parseFloat(sub_total) + parseFloat(totalTax) + parseFloat(totalExpenses);
        $('#grand_total_show').text(grandTotal);
        $('#grand_total').val(grandTotal);
    }
});
// 
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
//
$('body').on('click', '.preview-multiple-order', function(){
    var estimated_delivery_date = $('#estimated_delivery_date').val();
    var warehouse = $('#warehouse').val();
    var supplier = $('#supplier').val();
    if(estimated_delivery_date == "") {
        swal("Warning!", "Please Enter Estimated Delivery Date", "error");
    }else if(warehouse == "") {
        swal("Warning!", "Please Select Warehouse", "error");
    }else if(supplier == "") {
        swal("Warning!", "Please Select Supplier", "error");
    }else {
        var file_data = $('#product_csv').prop('files')[0];
        if(file_data) {  
            var form_data = new FormData();                  
            form_data.append('file', file_data);
            form_data.append('estimated_delivery_date', estimated_delivery_date);
            form_data.append('warehouse', warehouse);
            form_data.append('supplier', supplier);
            $.ajax({
                url: base_url+"/purchase_order/order-preview", 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'html',  
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                type: 'post',
                beforeSend:function(){
                    showLoader();
                },
                success:function(res){
                    hideLoader();
                    $('.modal-title').text('').text("Order Preview");
                    $("#OrderPreviewModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $(".order_details").html(res);
                },
                error:function(){
                    hideLoader();
                },
                complete:function(){
                    hideLoader();
                }
            });
        }else {
            swal("Warning!", "Please select file", "error");
        }
    }
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
// Create Multiple Order (CSV Upload)
$('body').on('click', '.create_mutiple_order_csv', function(){
    if($("#client").val() == "") {
        swal("Warning!", "Please select client", "error");
    }else {
        var estimated_delivery_date = $('#estimated_delivery_date').val();
        var warehouse = $('#warehouse').val();
        var supplier = $('#supplier').val();
        var file_data = $('#product_csv').prop('files')[0];
        if(file_data) {
            var form_data = new FormData();                  
            form_data.append('file', file_data);
            form_data.append('estimated_delivery_date', estimated_delivery_date);
            form_data.append('warehouse', warehouse);
            form_data.append('supplier', supplier);
            //form_data.append('client', $("#client").val());                        
            $.ajax({
                url: base_url+"/purchase_order/create-multiple-order", 
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',  
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                type: 'post',
                beforeSend:function(){
                    showLoader();
                },
                success:function(res){
                    // hideLoader();
                    // console.log(res);
                    if(res['status']) {
                        hideLoader();
                        swal({
                            title: 'Success',
                            text: res['msg'],
                            icon: 'success',
                            type:'success',
                        }).then(function() {
                            window.location.reload();
                        });
                    }else {
                        hideLoader();
                        swal({
                            title: 'Warning',
                            text: res['msg'],
                            icon: 'error',
                            type:'error',
                        }).then(function() {
                            window.location.reload();
                        });
                    }
                },
                error:function(){
                    hideLoader();
                },
                complete:function(){
                    hideLoader();
                }
            });
        }else {
            swal("Warning!", "Please select file", "error");
        }
    }
});
// Delete Order Details
$("body").on("click", "a.delete-order-details", function(e) {
    _this = $(this);
    var obj = $(this);
    var id = obj.data("id");
    swal({
        title: "Are you sure?",
        text: "You want to remove this order!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes.",
        cancelButtonText: "No!",
        confirmButtonClass: "btn btn-success mr-5",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: !1
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/purchase-order/delete-order-details",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            _this.parents('tr').remove();
                            PurchaseOrderTable.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    //
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/order_template.csv");
})
// Upload Invoice
$('body').on('click', '.upload-invoice', function() {
    var obj = $(this);
    var hidden_order_id = $('#hidden_order_id').val();
    var invoice_no = $('#invoice_no').val();
    var invoice = $('#invoice').prop('files')[0];
    if(invoice == undefined || invoice == "") {
        swal("Warning!", "Please Select A File", "error");
    }else if(invoice_no == undefined || invoice_no == "") {
        swal("Warning!", "Please Enter invoice no", "error");
    }else {
        var form_data = new FormData();                  
        form_data.append('hidden_order_id', hidden_order_id);
        form_data.append('invoice', invoice);
        form_data.append('invoice_no', invoice_no);
        $.ajax({
            url: base_url+"/purchase-order/upload-invoice", 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'JSON',  
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                hideLoader();
                if(res["status"]) {
                    hideLoader();
                    swal({
                        title: "Success!",
                        text: res["msg"],
                        type: "success"
                    }).then(function() {
                        $('#invoice').val("");
                        $('#invoice_file').val("");
                        ViewInvoiceFile(res.invoice_extention, res.invoice_file)
                    });
                }else {
                    hideLoader();
                    swal("Opps!", res["msg"], "error");
                }
            },
            error:function(){
                hideLoader();
            },
            complete:function(){
                hideLoader();
            }
        });
    }
});
// View Invoice
function ViewInvoiceFile(extention, file) {
    var html = "";
    if(extention == "pdf") {
        html = '<iframe src="'+file+'" style="width:100%; height:500px;" frameborder="0"></iframe>';
    }else {
        html = '<img src="'+file+'" style="width:100%">'
    }
    $(".file_view").html(html);
}
$('body').on('click', '.btn-cancel', function() {
    $('#CommonModal').modal('hide');
    PurchaseOrderTable.draw()
});
// Order Invoice
$('body').on('click', 'a.purchase-order-invoice', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/purchase-order-invoice",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-xl');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
function addExpenses() {
    var budget_expenses_no = $('#budget_expenses_no').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/purchase-order-add-expenses",
        type:'post',
        dataType:'HTML',
        data: {budget_expenses_no:budget_expenses_no},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('#CommonModal #nextExpenses').before(res);
            $('#CommonModal #budget_expenses_no').val(parseInt(budget_expenses_no) + 1);
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
}
function removeExpenses(id) {
    $('#CommonModal #ExpensesDiv'+id).remove();
    var sub_total = $('#CommonModal #sub_total').val();
    var CountexpensesValue = 0;
    var ExpensesDetails=$('#CommonModal #ExpensesDetails').find('.expenses-value');
    $(ExpensesDetails).each(function(){
        var expensesValue = $(this).val();
        if(expensesValue > 0) {
            CountexpensesValue = parseInt(CountexpensesValue) + parseInt(expensesValue);
        }
    });
    var grand_total = parseInt(sub_total) + parseInt(CountexpensesValue)
    $("#CommonModal #grand_total_show").text(grand_total);
    $('#CommonModal #grand_total').val(grand_total);
}
// $('body').on('keyup input', '.expenses-value', function() {
//     var sub_total = $('#CommonModal #sub_total').val();
//     var CountexpensesValue = 0;
//     var ExpensesDetails=$('#CommonModal #ExpensesDetails').find('.expenses-value');
//     $(ExpensesDetails).each(function(){
//         var expensesValue = $(this).val();
//         if(expensesValue > 0) {
//             CountexpensesValue = parseInt(CountexpensesValue) + parseInt(expensesValue);
//         }
//     });
//     var grand_total = parseInt(sub_total) + parseInt(CountexpensesValue)
//     $("#CommonModal #grand_total_show").text(grand_total);
//     $('#CommonModal #grand_total').val(grand_total);
// });
$('body').on('change', '.expenses-id', function() {
    _this = $(this);
    _expenses_id = $(this).val();
    var ExpensesDetails=$('#CommonModal #ExpensesDetails').find('.expenses-id');
    var x=0;
    $(ExpensesDetails).not(_this).each(function(){
        if($(this).val()== _expenses_id) {
            x=1;
        }
    })
    if(x==1) {
        _this.val('');
        swal("Sorry!", "You have already added this Expense!", "error");
    }
});
$('body').on('click', '.save-purchase-order-invoice', function(){
    var form = $('#OrderInvoiceForm');
    var ExpensesDetails=$('#CommonModal #ExpensesDetails').find('input,select');
    var x=0;
    if(ExpensesDetails.length > 0) {
        $(ExpensesDetails).each(function(){
            if($(this).val()=="") {
                x=1;
            }
        });
    }
    if(x==1) {
        swal("Sorry!", "Enter data first", "error");
    }else {
        var formData = form.serializeArray();
        formData.push({ name: "orders_status", value: "CreateOrder" });
        $.ajax({
            url : base_url+"/save-purchase-order-invoice", 
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            dataType:'json',
            beforeSend:function(){  
              $("#loader").css("display","block");
            },
            success: function(res){
                if(res['status']) {
                    window.open(base_url+"/"+res.return_url+"?order_id="+res.order_id);
                }
                else {
                    swal("Warning!", res['msg'], "error");
                }
            },
            error:function(error){
              swal("Warning!", "Sorry! There is an error", "error");
            },
            complete:function(){
              $("#loader").css("display","none");
            }
        });
    }
});
// Excess Purchase Order
var ExcessPurchaseOrderList = $('#ExcessPurchaseOrderList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-excess-purchase-order",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#excess_filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'deliverydate', name: 'deliverydate', orderable: false, searchable: false},
        {data: 'excess_quantity', name: 'excess_quantity', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false}
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#excess_filter_supplier").on('keyup input',function(){
    ExcessPurchaseOrderList.draw();
});
$('#ExcessResetFilter').on('click', function(){
    $('#excess_filter_supplier').val('');
    ExcessPurchaseOrderList.draw();
})
$('body').on('click', 'a.view-excess-order-details', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-excess-purchase-order-details",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-xl');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
// Damage Purchase Order
var DamagePurchaseOrderList = $('#DamagePurchaseOrderList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-damage-purchase-order",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#damage_filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'deliverydate', name: 'deliverydate', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false}
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#damage_filter_supplier").on('keyup input',function(){
    DamagePurchaseOrderList.draw();
});
$('#DamageResetFilter').on('click', function(){
    $('#damage_filter_supplier').val('');
    DamagePurchaseOrderList.draw();
})
$('body').on('click', 'a.view-damage-order-details', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-damage-purchase-order-details",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-xl');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
// Shortage Purchase Order
var ShortagePurchaseOrderList = $('#ShortagePurchaseOrderList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-shortage-purchase-order",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#shortage_filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'deliverydate', name: 'deliverydate', orderable: false, searchable: false},
        {data: 'shortage_quantity', name: 'shortage_quantity', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false}
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#shortage_filter_supplier").on('keyup input',function(){
    ShortagePurchaseOrderList.draw();
});
$('#ShortageResetFilter').on('click', function(){
    $('#shortage_filter_supplier').val('');
    ShortagePurchaseOrderList.draw();
})
$('body').on('click', 'a.view-shortage-order-details', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-shortage-purchase-order-details",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-xl');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});

// Add Expenses
$('body').on('click', '#add_expenses', function() {
    var last_tr = $('body #entryExpensesTbody tr').find('input');
    var x=0;
    $(last_tr).each(function(){
        if($(this).val()=="" && !$(this).hasClass('expenses-vat')) {
            x=1;
        }
    })
    if(x==1) {
        swal("Warning!", "Enter data first", "error");
    }else {
        var expenses_entry_count = $('#expenses_entry_count').val();
        $('#ListExpensesEntry').before('<tr id="entryExpensesRow'+expenses_entry_count+'"><td><input type="text" class="form-control entry-expenses-description" name="expenses_description[]" autocomplete="off" placeholder="Description"></td><td><input type="number" class="form-control expenses-value" name="expenses_value[]" placeholder="Value"></td><td><input type="number" class="form-control expenses-vat" name="expenses_vat[]" placeholder="Vat"></td><td style="width: 12%;"> <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeExpensesEntry('+expenses_entry_count+')"><i class="fa fa-window-close" aria-hidden="true"></i></button></td></tr>');
        $('#expenses_entry_count').val(parseInt(expenses_entry_count)+1);
    }
});
function removeExpensesEntry(id) {
    $('#entryExpensesRow'+id).remove();
    final_calculation();
}

$('body').on('keyup paste','#entryExpensesTbody .expenses-value, #entryExpensesTbody .expenses-vat',function(){
    
    final_calculation();
    
});

