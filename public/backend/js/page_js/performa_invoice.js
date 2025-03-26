// List
var PerformaInvoice = $('#PerformaInvoiceList').DataTable({
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
        "url": base_url+"/performa-invoice/list-performa-invoice",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_request_id', name: 'order_request_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_supplier").on('keyup input',function(){
    PerformaInvoice.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_supplier').val('');
    PerformaInvoice.draw();
})
$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addNew()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Invoice</button>');
// Add
function addNew() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/performa-invoice/add-invoice",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Upload Invoice");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-lg');
                performa_invoice_form();
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
function performa_invoice_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#PerformaInvoiceForm").validate({
        rules: {
            supplier_id: "required",
            order_request_id: "required",
            invoice_file: "required",
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "supplier_id") {
                error.appendTo(element.parent());
            }else if (element.attr("name") == "invoice_file") {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var formData = new FormData($('#PerformaInvoiceForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/performa-invoice/save-invoice",  
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
                        $('#PerformaInvoiceForm')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            PerformaInvoice.draw();
                        });
                    }else {
                        hideLoader();
                        swal("Warning!", res["msg"], "error");
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
    });
}
// Delete Order
$("body").on("click", "a.delete-performa-invoice", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
    swal({
        title: "Are you sure?",
        text: "You want to remove this!",
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
                url:base_url+"/performa-invoice/delete-invoice",  
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
                            PerformaInvoice.draw();
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
$('body').on('click', 'a.view-performa-invoice', function() {
	var obj = $(this);
	var id = obj.data("id");
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/performa-invoice/view-invoice",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("View Invoice Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-lg');
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
	setTimeout(function(){newWin.close();},10);
});
function checkAlreadyExistsProduct(_this, pmpno) {
  var last_tr=$('body #entryProductTbody tr').find('.entry-part-no');
  var r=0;
  $(last_tr).not(_this).each(function(){
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
  _this = $(this);
  if(!checkAlreadyExistsProduct(_this, pmpno)) {
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
          _this.parents('tr').find('.entry-product-name').val(res.data[0].part_name);
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
  window.open(base_url+"/public/backend/file/order_template.csv");
})