var ReceivingAndPutaway = $('#ReceivingAndPutawayList').DataTable({
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
        "url": base_url+"/list-receiving-order",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        //{data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        //{data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'invoice_no', name: 'invoice_no', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'download_binning_advice', name: 'download_binning_advice', orderable: false, searchable: false}
        // {data: 'received_status', name: 'received_status', orderable: false, searchable: false}
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addNew()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Bining Advice</button>');
function addNew() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/bining-advice/add-bining-advice",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Bining Advice");
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
                bining_advice_form();
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
function bining_advice_form() {
    //
}
$('body').on('click', '#get_purchase_order_details', function() {
    var purchase_order_id = $('#purchase_order_id').val();
    if(purchase_order_id == "") {
        swal({title: "Sorry!", text: "Please enter a purchase order id", type: "warning"});
        $('#purchase_order_id').focus();
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/bining-advice/get-purchase-order-detals",
            type:'post',
            dataType:'JSON',
            data: {order_id:purchase_order_id},
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                if(res['status']) {
                    hideLoader();
                    $('#entryProductTbody').html("");
                    if(res.purchased_date != "") {
                        $('#purchasedDate').html("Order Date: "+res.purchased_date);
                    }
                    var listData = "";
                    for(var i=0; i<res.data.length; i++) {
                        var readonly = '';
                        var add_button = '<button type="button" name="submit" class="btn-shadow btn btn-info save-bining-advice" value="Submit"> Save </button>';
                        if(res.data[i].approved == 1) {
                            readonly = 'readonly';
                            add_button = '<button type="button" name="submit" class="btn-shadow btn btn-danger delete-bining-advice" value="Submit"><i class="fa fa-trash"></i></button>';
                        }
                        listData +='<tr id="biningAdvice'+res.data[i].order_detail_id+'"><td><input type="text" class="form-control" name="" id="" value="'+res.data[i].part_name+'" readonly="readonly"><input type="hidden" class="order-detail-id" value="'+res.data[i].order_detail_id+'"><input type="hidden" class="order-id" value="'+res.data[i].order_id+'"></td><td><input type="text" class="form-control" name="" id="" value="'+res.data[i].pmpno+'" readonly="readonly"><input type="hidden" class="product-id" value="'+res.data[i].product_id+'"></td><td><input type="text" class="form-control" name="" id="" value="'+res.data[i].mrp+'" readonly="readonly"></td><td><input type="number" class="form-control qty" value="'+res.data[i].qty_appr+'" '+readonly+'><input type="hidden" class="supplier_id" value="'+res.data[i].supplier_id+'"></td><td>'+add_button+'</td></tr>'
                    }
                    $('#entryProductTbody').append(listData);
                }else {
                    hideLoader();
                    $('#purchasedDate').html("");
                    $('#entryProductTbody').html("");
                    swal({title: "Sorry!", text: res.msg, type: "warning"});
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
// Save Bining Advice
$('body').on('click', '.save-bining-advice', function() {
    _this = $(this);
    var product_id = _this.parents("tr").find(".product-id").val();
    var order_detail_id = _this.parents("tr").find(".order-detail-id").val();
    var order_id = _this.parents("tr").find(".order-id").val();
    var qty = _this.parents("tr").find(".qty").val();
    var supplier_id = _this.parents("tr").find(".supplier_id").val();
    if(qty == "") {
        swal({title: "Sorry!", text: "Please enter quantity", type: "warning"});
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/bining-advice/save-bining-advice",
            type:'post',
            dataType:'JSON',
            data: {product_id:product_id, order_detail_id:order_detail_id, order_id:order_id, qty:qty, supplier_id:supplier_id},
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                if(res['status']) {
                    hideLoader();
                    _this.prop('disabled', true);
                    swal({title: "Success!", text: res.msg, type: "success"});
                }else {
                    hideLoader();
                    swal({title: "Sorry!", text: res.msg, type: "warning"});
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
})
// Delete Bining Advice
$('body').on('click', '.delete-bining-advice', function() {
    _this = $(this);
    var order_detail_id = _this.parents("tr").find(".order-detail-id").val();
    var order_id = _this.parents("tr").find(".order-id").val();
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
                url:base_url+"/bining-advice/delete-bining-advice",  
                type: "POST",
                data:  {order_detail_id: order_detail_id, order_id:order_id},
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
                            $('#biningAdvice'+order_detail_id).remove();
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
})
// Get Order for Approved
$('body').on('click', 'a.order-approved', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-order-with-detals",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Confirmed Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-lg');
                approved_order_form();
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
// Save Approved
function approved_order_form() {
    $("#CommonModal").find("#ApprovedOrderForm").validate({
        rules: {
            order_id: "required",
            datetime: "required",
            deliverydate: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#ApprovedOrderForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/approved-receiving-order",  
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
                        $('#ApprovedOrderForm')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            ReceivingAndPutaway.draw();
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
    });
}
// Get Order for Received
$('body').on('click', 'a.order-received', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-order-with-detals",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Received Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-lg');
                received_order_form();
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
// Save Approved
function received_order_form() {
    $("#CommonModal").find("#ApprovedOrderForm").validate({
        rules: {
            order_id: "required",
            datetime: "required",
            deliverydate: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#ApprovedOrderForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/received-receiving-order",  
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
                        $('#ApprovedOrderForm')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            ReceivingAndPutaway.draw();
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
    });
}
// View Order Details
$('body').on('click', 'a.view-order-details', function() {
	var obj = $(this);
	var id = obj.data("id");
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/receiving-order/view-order-details",
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
// PDF
$('body').on('click', 'a.download-pdf', function() {
    var obj = $(this);
    var id = obj.data("id");
    window.open(base_url+"/bining-advice/pdf-bining-advice?id="+id, '_blank');
});
$('body').on('click', '.btn-cancel', function() {
    $('#CommonModal').modal('hide');
    ReceivingAndPutaway.draw();
    //window.location.reload();
});

$('body').on('click', 'a.download-barcode-modal', function() {
    
    var obj = $(this);
	var order_id = obj.data("order_id");
	var barcode_number = obj.data("barcode_number");
	
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/bining-advice/download-barcode-modal",
        type:'post',
        dataType:'json',
        data: {order_id:order_id, barcode_number:barcode_number},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // var urlCreator = window.URL || window.webkitURL;
            // var imageUrl = urlCreator.createObjectURL(res);
            // var tag = document.createElement('a');
            // tag.href = imageUrl;
            // tag.download = fileName;
            // document.body.appendChild(tag);
            // tag.click();
            // document.body.removeChild(tag);
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Download Barcode");
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
        complete:function(res){
            hideLoader();
        }
    });
    
});

$('body').on('click', '#download_barcode', function() {
    var download_no = $('#download_no').val();
    var barcode_number = $('#barcode_number').val();
    //window.location.href=base_url+'/bining-advice/download-barcode?download_no='+download_no+'&barcode_number='+barcode_number;
    window.open(base_url+"/bining-advice/download-barcode?download_no="+download_no+"&barcode_number="+barcode_number, '_blank');
})

