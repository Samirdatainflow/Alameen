// List
var OrderRequest = $('#OrderRequestList').DataTable({
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
        "url": base_url+"/order-request/list-order-request",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_request_unique_id', name: 'order_request_unique_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'created_by', name: 'created_by', orderable: false, searchable: false},
        {data: 'item', name: 'item', orderable: false, searchable: false},
        {data: 'total_supplier', name: 'total_supplier', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
var SaveOrderRequestList = $('#SaveOrderRequestList').DataTable({
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
        "url": base_url+"/order-request/list-save-order-request",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_request_unique_id', name: 'order_request_unique_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'created_by', name: 'created_by', orderable: false, searchable: false},
        {data: 'item', name: 'item', orderable: false, searchable: false},
        {data: 'total_supplier', name: 'total_supplier', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_supplier").on('keyup input',function(){
    OrderRequest.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_supplier').val('');
    OrderRequest.draw();
})
$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addNew()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add New Order Request</button> <button type="button" aria-haspopup="true" onclick="ExportTable()" aria-expanded="false" class="btn-shadow btn btn-info" title="Export"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');
$('#SaveOrderRequestList_wrapper div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addNew()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add New Order Request</button> <button type="button" aria-haspopup="true" onclick="ExportSaveOrder()" aria-expanded="false" class="btn-shadow btn btn-info" title="ExportSaveOrder"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/order-request-export";
}
function ExportSaveOrder() {
    window.location.href = base_url+"/save-order-request-export";
}
// Add
function addNew() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/add-order-request",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Order Request");
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
                $('.selectpicker').selectpicker().change(function(){
                    $(this).valid()
                });
                //order_request_form();
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
// function order_request_form() {
//     $('.selectpicker').selectpicker().change(function(){
//         $(this).valid()
//     });
//     $("#CommonModal").find("#OrderRequestForm").validate({
//         rules: {
//             'supplier[]': "required",
//         },
//         submitHandler: function() {
//             var val = $("#CommonModal").find("#OrderRequestForm input[type=submit][clicked=true]").val();
//             console.log(val);
//             var last_tr = $('body #entryProductTbody tr').find('input');
//             var x=0;
//             $(last_tr).each(function(){
//                 if($(this).val()=="" && !$(this).hasClass('entry-manufacture-no')) {
//                     x=1;
//                 }
//             })
//             if(x==1) {
//                 swal("Warning!", "Enter data first", "error");
//             }else {
//                 var formData = new FormData($('#OrderRequestForm')[0]);
//                 $.ajax({
//                     headers: {
//                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                     },
//                     url:base_url+"/order-request/save-order-request",  
//                     type: "POST",
//                     data:  formData,
//                     contentType: false,
//                     cache: false,
//                     processData:false, 
//                     dataType:"json", 
//                     beforeSend:function(){  
//                         showLoader();
//                     },  
//                     success:function(res){
//                         if(res["status"]) {
//                             hideLoader();
//                             $('#OrderRequestForm')[0].reset();
//                             swal({
//                                 title: "Success!",
//                                 text: res["msg"],
//                                 type: "success"
//                             }).then(function() {
//                                 $('#CommonModal').modal('hide');
//                                 OrderRequest.draw();
//                             });
//                         }else {
//                             hideLoader();
//                             swal("Opps!", res["msg"], "error");
//                         }
//                     },
//                     error: function(e) {
//                         hideLoader();
//                         swal("Opps!", "There is an error", "error");
//                     },
//                     complete: function(c) {
//                         hideLoader();
//                     }
//                 });
//             }
//         }
//     });
// }
// Create Request Order
$('body').on('click', '#CreateOrder', function(){
    var form = $('#OrderRequestForm');
    var supplier = $('#supplier').val();
    if(supplier.length > 0) {
        var last_tr = $('body #entryProductTbody tr').find('input');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="" && !$(this).hasClass('entry-manufacture-no') && !$(this).hasClass('entry-unit')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Warning!", "Enter data first", "error");
        }else {
            //console.log("here"); return false;
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
                    formData.push({ name: "order_request_status", value: "CreateOrder" });
                    $.ajax({
                        url : base_url+"/order-request/save-order-request", 
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
                                window.location.href=base_url+"/order-request";
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
    }else {
        swal("Warning!", "Please select supplier.", "error");
    }            
});
// Save Request Order
$('body').on('click', '#SaveOrder', function(){
    var form = $('#OrderRequestForm');
    var supplier = $('#supplier').val();
    if(supplier.length > 0) {
        var last_tr = $('body #entryProductTbody tr').find('input');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="" && !$(this).hasClass('entry-manufacture-no')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Warning!", "Enter data first", "error");
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
                    formData.push({ name: "order_request_status", value: "SaveOrder" });
                    $.ajax({
                        url : base_url+"/order-request/save-order-request", 
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
    }else {
        swal("Warning!", "Please select supplier.", "error");
    }            
});
// Edit Save Order
$('body').on('click', 'a.edit-save-request-order', function() {
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/add-order-request",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Order Request");
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
                $('.selectpicker').selectpicker().change(function(){
                    $(this).valid()
                });
                //order_request_form();
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
$('#button2').click(function(){
    // $("#myform").validate({
    //     ....
    // });
});
// Add product entry
$('body').on('click', 'a.add-entry-product', function() {
    var last_tr = $('body #entryProductTbody tr').find('input');
    var x=0;
    $(last_tr).each(function(){
        console.log()
        if($(this).val()=="" && !$(this).hasClass('entry-manufacture-no')) {
            x=1;
        }
    })
    if(x==1) {
        swal("Warning!", "Enter data first", "error");
    }else {
        var product_entry_count = $('#product_entry_count').val();
        $('#ListProductEntry').before('<tr id="entryProductRow'+product_entry_count+'"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-part-brand" name="" autocomplete="off" readonly="readonly"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="text" class="form-control entry-unit" name="" autocomplete="off"  readonly="readonly"><td><input type="text" class="form-control entry-manufacture-no" name="" autocomplete="off"  readonly="readonly"></td></td><td><input type="number" class="form-control entry-product-quantity" name="entry_product_quantity[]"></td><td style="width: 12%;"><a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-danger btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a> <button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry('+product_entry_count+')"><i class="fa fa-window-close" aria-hidden="true"></i></button></td></tr>');
    }
});
// Rwemove product entry
function removeProductEntry(id) {
	$('#entryProductRow'+id).remove();
	//final_calculation();
}
// Delete Order
$("body").on("click", "a.delete-request-order", function(e) {                   
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
                url:base_url+"/order-request/delete-request-order",  
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
                            OrderRequest.draw();
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
// PDF
$('body').on('click', 'a.generate-pdf', function() {
    var obj = $(this);
    var id = obj.data("order_request_unique_id");
    window.open(base_url+"/order-request/pdf-request-order?id="+id, '_blank');
})
// download-pdf
$('body').on('click', 'a.download-pdf', function() {
    // alert("hi");
    var hidden_request_id = $("#hidden_request_id").val();
    // var obj = $(this);
    // var id = ("hidden_request_id");
    window.open(base_url+"/order-request/download-request-order?id="+hidden_request_id, '_blank');
})
// View Order Details
$('body').on('click', 'a.view-request-order-details', function() {
	var obj = $(this);
	var id = obj.data("id");
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/view-request-order-details",
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
                //quotation_invoice_form();
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
// Upload Quotation
$('body').on('click', '.upload-quotation', function() {
    var obj = $(this);
    var row_id = obj.data("row-id");
    var order_request_unique_id = obj.data("order_request_unique_id");
    var supplier_id = obj.data("supplier_id");
    var quotation_file = $('#quotation_file'+row_id).prop('files')[0];
    if(quotation_file == undefined || quotation_file == "") {
        swal("Warning!", "Please Select A File", "error");
    }else {
        var form_data = new FormData();                  
        form_data.append('supplier_id', supplier_id);
        form_data.append('order_request_unique_id', order_request_unique_id);
        form_data.append('quotation_file', quotation_file);
        $.ajax({
            url: base_url+"/order-request/save-quotation", 
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
                        $('#quotationTR'+row_id).html("");
                        $('#quotationTR'+row_id).html('<a href="javascript:void(0)" onclick="ViewQuotationFile(\''+res.order_quotation_file_extention+'\', \''+res.order_quotation_file+'\')" class="badge badge-success">View File</a>');
                        $('#confirmTR'+row_id).html("");
                        $('#confirmTR'+row_id).html('<a href="javascript:void(0)" onclick="ConfirmQuotation('+row_id+', '+supplier_id+', '+order_request_unique_id+')" class="badge badge-danger confirm-btn" title="Confirm it">Not Confirm</a>');
                        $('#UploadPriceTR'+row_id).html('<button type="button" name="submit" class="btn-shadow btn btn-info upload-quotation-price" data-row_id="'+row_id+'" data-supplier_id="'+supplier_id+'" data-order_request_unique_id="'+order_request_unique_id+'" value="Submit"> Upload Price </button>');
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
// Upload Performa Invoice
$('body').on('click', '.upload-performa-invoice', function() {
    var obj = $(this);
    var row_id = obj.data("row-id");
    var order_request_unique_id = obj.data("order_request_unique_id");
    var supplier_id = obj.data("supplier_id");
    var performa_invoice = $('#performa_invoice'+row_id).prop('files')[0];
    if(performa_invoice == undefined || performa_invoice == "") {
        swal("Warning!", "Please Select A File", "error");
    }else {
        var form_data = new FormData();                  
        form_data.append('order_request_unique_id', order_request_unique_id);
        form_data.append('supplier_id', supplier_id);
        form_data.append('performa_invoice', performa_invoice);
        $.ajax({
            url: base_url+"/order-request/upload-performa-invoice", 
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
                        $('#performaInvoiceTR'+row_id).html("");
                        $('#performaInvoiceTR'+row_id).html('<a href="javascript:void(0)" onclick="ViewQuotationFile(\''+res.performa_invoice_extention+'\', \''+res.performa_invoice_file+'\')" class="badge badge-success">View File</a>');
                        $('.file-upload-info').prop('disabled', true);
                        $('.upload-quotation').prop('disabled', true);
                        $('.file-upload-browse').prop('disabled', true);
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
function quotation_invoice_form() {
    $("#CommonModal").find("#QuotationInvoiceForm").validate({
        submitHandler: function() {
            var last_tr = $('body #CommonModal #QuotationInvoiceForm').find('input[type=file]');
            var x=0;
            $(last_tr).each(function(){
                if($(this).val()=="") {
                    x=1;
                }
            })
            if(x==1) {
                swal("Warning!", "Enter data first", "error");
            }else {
                var formData = new FormData($('#QuotationInvoiceForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/order-request/save-quotation-invoices",  
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
                            $('#QuotationInvoiceForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#CommonModal').modal('hide');
                                QuotationOrder.draw();
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
function ViewQuotationFile(extention, file) {
    var html = "";
    if(extention == "pdf") {
        html = '<div class="row"><iframe src="'+file+'" style="width:100%; height:500px;" frameborder="0"></iframe></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>';
    }else {
        html = '<div class="row"><div class="col-md-12 text-center"><img src="'+file+'" style="width:100%"></div></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>'
    }
    $('.location-modal-title').text('').text("View File");
    $("#LocationModal").modal({
        backdrop: 'static',
        keyboard: false
    });
    $("#LocationformContent").html(html);
    $(".location-dailog").addClass('modal-lg');
}
function ConfirmQuotation(row_id, supplier_id, order_request_unique_id) {
    swal({
        title: "Are you sure?",
        text: "You want to confirm it!",
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
                url:base_url+"/order-request/confirm-order-request",  
                type: "POST",
                data:  {order_request_unique_id:order_request_unique_id, supplier_id:supplier_id},
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
                            //$("#CommonModal").modal('hide');
                            $('.confirm-btn').css('pointer-events', 'none');
                            $('.file-upload-info').prop('disabled', true);
                            $('.upload-quotation').prop('disabled', true);
                            $('.file-upload-browse').prop('disabled', true);
                            $('#performaInvoiceTR'+row_id).html("");
                            $('#performaInvoiceTR'+row_id).html('<div class="form-group"><input type="file" id="performa_invoice'+row_id+'" name="performa_invoice" class="file-upload-default" accept=".jpg, .png, .pdf" /><div class="input-group col-xs-12"><input type="text" class="form-control file-upload-info" id="performa_invoice" name="performa_invoice" disabled placeholder="Upload Invoice" /><span class="input-group-append"><button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button></span></div></div><button type="button" name="submit" class="btn-shadow btn btn-info upload-performa-invoice" data-row-id="'+row_id+'" data-supplier_id="'+supplier_id+'" data-order_request_unique_id="'+order_request_unique_id+'" value="Submit"> Upload </button>');
                            $('#confirmTR'+row_id).html("");
                            $('#confirmTR'+row_id).html('<span class="badge badge-success" title="Confirm">Confirmed</span>');

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
}
function ViewPerformaInvoiceFile(extention, file) {
    var html = "";
    if(extention == "pdf") {
        html = '<div class="row"><iframe src="'+file+'" style="width:100%; height:500px;" frameborder="0"></iframe></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>';
    }else {
        html = '<div class="row"><div class="col-md-12 text-center"><img src="'+file+'" style="width:100%"></div></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>'
    }
    $('.location-modal-title').text('').text("View File");
    $("#LocationModal").modal({
        backdrop: 'static',
        keyboard: false
    });
    $("#LocationformContent").html(html);
    $(".location-dailog").addClass('modal-lg');
}
// Print Order Details
$('body').on('click', 'a.print-order-details', function() {
	var divToPrint=document.getElementById('OderDetailsContent');
	var newWin=window.open('','Print-Window');
	newWin.document.open();
	newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
	newWin.document.close();
	setTimeout(function(){newWin.close();},10);
});
function checkAlreadyExistsProduct(_this, product_id) {
    var parentTr = $(_this).parents("tr");
  var last_tr=$('body #entryProductTbody tr').not(parentTr).find('.entry-product-id');
  var r=0;
  $(last_tr).not(_this).each(function(){
    if($(this).val() == product_id)
    {
      r=1;
      
    }
  });
  return r;
}
// List product by Part No
$("body").on('keyup input', '#entryProductTbody .entry-part-no', function(element){
    var _this = $(this);
    var part_no = $(this).val();
    if(part_no != "") {
        $.ajax({
            url : base_url+"/order-request/get-product-by-part-no", 
            type: 'POST',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {part_no: part_no},
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
    }
});
$("body").on("click",'#entryProductTbody .product-details',function(){
  var product_entry_count = $('#product_entry_count').val();
  var pmpno = $(this).data('pmpno');
  var product_id = $(this).data('product-id');
  _this = $(this);
  if(!checkAlreadyExistsProduct(_this, product_id)) {
    $.ajax({
      url : base_url+"/order-request/get-product-details", 
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
        if(res.data.length > 0) {
            hideLoader();
          _this.parents('tr').find('.entry-part-no').val(res.data[0].pmpno);
          _this.parents('tr').find('.entry-part-brand').val(res.data[0].part_brand_name);
          _this.parents('tr').find('.entry-product-name').val(res.data[0].part_name);
          _this.parents('tr').find('.entry-unit').val(res.data[0].unit_name);
          _this.parents('tr').find('.entry-manufacture-no').val(res.data[0].manufacturing_no);
          _this.parents('tr').find('.entry-product-id').val(res.data[0].product_id);
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
// $('body').on('keyup paste','#entryProductTbody .entry-product-quantity',function(){
//     var mrp = $(this).parents('tr').find('.mrp').val();
//     var qty = $(this).parents('tr').find('.entry-product-quantity').val();
//     var gst = $(this).parents('tr').find('.gst').val();
//     var net_total = (((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100)+(parseFloat(mrp)*parseFloat(qty));
//     $(this).parents('tr').find('.total_price').html(net_total);
//     final_calculation();
// });
// function final_calculation(){
//     var sub_total=0;
//     var total_tax=0;
//     var grand_total=0;
//     $('#entryProductTbody tr').each(function(){
//         var mrp = "0";
//         if(!isNaN(parseFloat($(this).find('.mrp').val()))) {
//             mrp = parseFloat($(this).find('.mrp').val());
//         }
//         var qty = "0";
//         if(!isNaN(parseFloat($(this).find('.entry-product-quantity').val()))) {
//             qty = $(this).find('.entry-product-quantity').val()==""?0:parseFloat($(this).find('.entry-product-quantity').val());
//         }
//         var gst = "0";
//         if(!isNaN(parseFloat($(this).find('.gst').val()))) {
//             gst = parseFloat($(this).find('.gst').val());
//         }
//         sub_total +=(mrp*qty);
//         total_tax +=((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100;
//     });
//     grand_total = sub_total+total_tax;
//     $("#sub-total").val(sub_total.toFixed(2));
//     $("#tax").val(total_tax.toFixed(2));
//     $("#grand_total").val(grand_total.toFixed(2));
//     $("#sub_total_show").html(sub_total.toFixed(2));
//     $("#total_tax_show").html(total_tax.toFixed(2));
//     $("#grand_total_show").html(grand_total.toFixed(2));
// }
// 
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
//
$('body').on('click', '.preview-multiple-order', function(){
	var supplier = $('#supplier').val();
	if(supplier.length > 0) {
	    var file_data = $('#order_request_csv').prop('files')[0];
	    if(file_data) {  
	        var form_data = new FormData();                  
	        form_data.append('file', file_data);
	        form_data.append('supplier', supplier);
	        $.ajax({
	            url: base_url+"/order-request/order-preview", 
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
	}else {
		swal("Warning!", "Please select supplier", "warning");
	}
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
// Create Multiple Order (CSV Upload)
$('body').on('click', '.create_mutiple_order_csv', function(){
	var supplier = $('#supplier').val();
    var file_data = $('#order_request_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        form_data.append('supplier', supplier);                       
        $.ajax({
            url: base_url+"/order-request/create-multiple-order", 
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
});
// Delete Order Details
$("body").on("click", "a.delete-order-request-details", function(e) {
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
                url:base_url+"/order-request/delete-order-request-details",  
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
                            OrderRequest.draw();
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
  window.open(base_url+"/public/backend/file/order_request.csv");
});
// Upload Price
$('body').on('click', '.upload-quotation-price', function() {
    var obj = $(this);
    var supplier_id = obj.data("supplier_id");
    var order_request_unique_id = obj.data("order_request_unique_id");
    var row_id = obj.data("row_id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/view-order-details-4-price",
        type:'post',
        dataType:'JSON',
        data: {supplier_id: supplier_id, order_request_unique_id: order_request_unique_id, row_id: row_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#OrderPreviewModal .modal-title').text('').text("Upload Price");
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#OrderPreviewModal #formContent").html(res['message']);
                $("#OrderPreviewModal .modal-dialog").addClass('modal-xl');
                quotation_price_form();
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
function quotation_price_form() {
    $("#OrderPreviewModal").find("#QuotationPriceForm").validate({
        submitHandler: function() {
            var last_tr = $('body #OrderPreviewModal #QuotationPriceForm').find('input[type=number]');
            var x=0;
            $(last_tr).each(function(){
                if($(this).val()=="") {
                    x=1;
                }
            })
            if(x==1) {
                swal("Warning!", "Enter data first", "error");
            }else {
                var formData = new FormData($('#QuotationPriceForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/order-request/save-quotation-prices",  
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
                            $('#QuotationPriceForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#OrderPreviewModal').modal('hide');
                                $('#UploadPriceTR'+res["row_id"]).html("").append('<button type="button" name="submit" class="btn-shadow btn btn-info view-quotation-price" data-row_id="1" data-supplier_id="'+res["supplier_id"]+'" data-order_request_unique_id="'+res["order_request_unique_id"]+'" value="Submit"><i class="fa fa-eye"></i> View Price </button>')
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
// View Upload Price
$('body').on('click', '.view-quotation-price', function() {
    var obj = $(this);
    var supplier_id = obj.data("supplier_id");
    var order_request_unique_id = obj.data("order_request_unique_id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/view-quotation-price",
        type:'post',
        dataType:'JSON',
        data: {supplier_id: supplier_id, order_request_unique_id: order_request_unique_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#OrderPreviewModal .modal-title').text('').text("Upload Price Details");
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#OrderPreviewModal #formContent").html(res['message']);
                $("#OrderPreviewModal .modal-dialog").addClass('modal-xl');
                quotation_price_form();
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
// Compare Price
$('body').on('click', '.compare-price', function() {
    var obj = $(this);
    var order_request_unique_id = obj.data("order_request_unique_id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-request/compare-price",
        type:'post',
        dataType:'JSON',
        data: {order_request_unique_id: order_request_unique_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#OrderPreviewModal .modal-title').text('').text("Compare Price");
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#OrderPreviewModal #formContent").html(res['message']);
                $("#hidden_request_id").val(order_request_unique_id);
                $("#OrderPreviewModal .modal-dialog").addClass('modal-xl');
                quotation_price_form();
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