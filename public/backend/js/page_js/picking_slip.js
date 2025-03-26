var saleOrderTable = $('#sale_order').DataTable({
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
        "url": base_url+"/get-picking-order",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'client_name', name: 'client_name', orderable: true, searchable: true},
        {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
        {data: 'merged_no', name: 'merged_no', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});

$("#filter_customer").on('change',function(){
    $('.show-print').css('display', 'block');
    saleOrderTable.draw();
});

$('body').on('click', 'a.rest-filter', function() {
    $('.show-print').css('display', 'none');
    $('#filter_customer').val('');
    saleOrderTable.draw();
});

$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_brand" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/picking-slip-export";
}
$("#sale_order").on('click','.view-order-details',function(){
    var sale_order_id= $(this).data('sale-order-id');
    $.ajax({
        url:base_url+"/get-sale-order-details-for-picking-slip",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        data:{sale_order_id:sale_order_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            $('.modal-title').text('').text("Order Details (#"+sale_order_id+")");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
        },
        error:function(){
            hideLoader();
        },
        complete:function(){
            hideLoader();
        }
    })
});
$("#sale_order").on('click',".approved-order",function(){
    var sale_order_id= $(this).data('sale-order-id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/before-picking-approve",  
        type: "POST",
        data:  {sale_order_id: sale_order_id},
        beforeSend:function(){  
        },  
        success:function(res){
            console.log(res);
            $('.modal-title').text('').text("Order Details");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
        },
        error: function(e) {
            swal("Opps!", "There is an error", "error");
        },
        complete: function(c) {
        }
    });
});
$(document).on('click', '#ApprovePicking', function(e){
    var form = $('#ApprovePickingForm');
    swal({
        title: "Are you sure?",
        text: "You want to approved this order.",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes.",
        cancelButtonText: "No!",
        confirmButtonClass: "btn btn-success mr-5",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: !1
    }).then((result) => {
        if (result.value) {
            var formData = form.serializeArray();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/picking-approve",  
                type: "POST",
                data:  formData,
                dataType:'json',
                beforeSend:function(){  
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $("#CommonModal").modal('hide');
                            saleOrderTable.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is Not Reject :)", "error")
        }
    });
});
// $("#sale_order").on('click',".approved-order",function(){
//     var sale_order_id= $(this).data('sale-order-id');
//     swal({
//         title: "Are you sure?",
//         text: "You want to approved this order.",
//         type: "warning",
//         showCancelButton: !0,
//         confirmButtonText: "Yes.",
//         cancelButtonText: "No!",
//         confirmButtonClass: "btn btn-success mr-5",
//         cancelButtonClass: "btn btn-danger",
//         buttonsStyling: !1
//     }).then((result) => {
//         if (result.value) {
//             $.ajax({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                 },
//                 url:base_url+"/picking-approve",  
//                 type: "POST",
//                 data:  {sale_order_id: sale_order_id},
//                 beforeSend:function(){  
//                 },  
//                 success:function(res){
//                     if(res["status"]) {
//                         swal({
//                             title: "Success!",
//                             text: res["msg"],
//                             type: "success"
//                         }).then(function() {
//                             saleOrderTable.draw();
//                         });
//                     }else {
//                         swal("Opps!", res["msg"], "error");
//                     }
//                 },
//                 error: function(e) {
//                     swal("Opps!", "There is an error", "error");
//                 },
//                 complete: function(c) {
//                 }
//             });
//         } else if (
//             result.dismiss === Swal.DismissReason.cancel
//         ) {
//             swal("Cancelled", "Data is Not Reject :)", "error")
//         }
//     });
// });
$('body').on('click', 'a.download-invoice', function() {
    var obj = $(this);
    var id = obj.data("sale-order-id");
    var print_invoice = obj.data("print_invoice");
    if(print_invoice == 1) {
        swal("Sorry!", "You don't have permission to print it.", "warning");
    }else {
        window.open(base_url+"/sale-order/download-invoice?id="+id, '_blank');
    }
});
$('body').on('click', 'a.print-picking-slip', function() {
    var obj = $(this);
    var id = obj.data("sale-order-id");
    var print_picking_slip = obj.data("print_picking_slip");
    if(print_picking_slip == 1) {
        swal("Sorry!", "You don't have permission to print it.", "warning");
    }else {
        //window.print(base_url+"/sale-order/print-picking-slip?id="+id);
        window.location.href = base_url+"/sale-order/print-picking-slip?id="+id;
    }
});
// Picking Slip Approved
$("#sale_order").on('click',".approve-packing-slip",function(){
    var sale_order_id= $(this).data('sale-order-id');
    swal({
        title: "Are you sure?",
        text: "You want to approved this picking slip.",
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
                url:base_url+"/sale-order/approve-packing-slip",  
                type: "POST",
                data:  {sale_order_id: sale_order_id},
                beforeSend:function(){  
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            saleOrderTable.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is Not Reject :)", "error")
        }
    });
});
$("body").on('click',".reset-print",function(){
    var sale_order_id= $(this).data('sale-order-id');
    swal({
        title: "Are you sure?",
        text: "You want to rest it.",
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
                url:base_url+"/sale-order/reset-print",  
                type: "POST",
                data:  {sale_order_id: sale_order_id},
                beforeSend:function(){  
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            saleOrderTable.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is Not Reject :)", "error")
        }
    });
});
$(document).on('click', '.print-customer-picking-slip', function() {
    
    var filter_customer = $('#filter_customer').val();
    var checkedVals = $('.client-line-item-check:checkbox:checked').map(function() {
        return this.value;
    }).get();
    var orderIds = checkedVals.join(",");
    if(orderIds == '') {
        swal("Sorry!", "Please select a order no!", "warning");
    }else {
        window.open(base_url+"/sale-order/print-customer-picking-slip?orderIds="+orderIds+'&filter_customer='+filter_customer, '_blank');
        //window.location.href = base_url+"/sale-order/print-customer-picking-slip?orderIds="+orderIds+'&filter_customer='+filter_customer;
    }
});

$('body').on('click', 'a.download-customer-invoice', function() {
    
    var filter_customer = $('#filter_customer').val();
    var checkedVals = $('.client-line-item-check:checkbox:checked').map(function() {
        return this.value;
    }).get();
    var orderIds = checkedVals.join(",");
    if(orderIds == '') {
        swal("Sorry!", "Please select a order no!", "warning");
    }else {
        window.location.href = base_url+"/sale-order/download-customer-invoice?orderIds="+orderIds+'&filter_customer='+filter_customer;
        //window.open(base_url+"/sale-order/download-customer-invoice?orderIds="+orderIds+'&filter_customer='+filter_customer, '_blank');
    }
});

$(window).bind("load", function() {
   $('#filter_customer').val('');
});

$('body').on('click', 'a.download-merged-invoice', function() {
    var obj = $(this);
    var invoice_no = obj.data("invoice_no");
    var client_id = obj.data("client_id");
    var print_invoice = obj.data("print_invoice");
    if(print_invoice == 1) {
        swal("Sorry!", "You don't have permission to print it.", "warning");
    }else {
        window.open(base_url+"/sale-order/download-merged-invoice?invoice_no="+invoice_no+'&client_id='+client_id, '_blank');
    }
    
});

$('body').on('click', 'a.print-merged-picking-slip', function() {
    var obj = $(this);
    var invoice_no = obj.data("invoice_no");
    var client_id = obj.data("client_id");
    var print_picking_slip = obj.data("print_picking_slip");
    if(print_picking_slip == 1) {
        swal("Sorry!", "You don't have permission to print it.", "warning");
    }else {
        window.location.href = base_url+"/sale-order/print-merged-picking-slip?invoice_no="+invoice_no+'&client_id='+client_id;
    }
});