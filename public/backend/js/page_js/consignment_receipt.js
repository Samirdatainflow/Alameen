// Table
var ConsignmentReceipt = $('#ConsignmentReceiptList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-consignment-receipt",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'consignment_receipt_id', name: 'consignment_receipt_id', orderable: false, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'items', name: 'items', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Consignment Receipt</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="TableExport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function TableExport() {
    window.location.href = base_url+"/consignment-receipt-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-consignment-receipt",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Consignment Receipt");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            consignment_receipt_form();
        },
        error:function(){
            swal({
              title: "Sorry!",
              text: "There is an error",
              type: "error" // type can be error/warning/success
            });
        },
        complete:function(){
            hideLoader();
        }
    })
}
// Countries save
function consignment_receipt_form() {
    $("#CommonModal").find("#ConsignmentReceiptForm").validate({
        rules: {
            inbound_order_no: "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('input');
            if(last_tr.length > 0) {
                var x=0;
                $(last_tr).each(function(){
                    if($(this).val()=="" && !$(this).hasClass('entry-manufacture-no')) {
                        x=1;
                    }
                })
                if(x==1) {
                    swal("Warning!", "Enter data first", "warning");
                }else {
                    var formData = new FormData($('#ConsignmentReceiptForm')[0]);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url:base_url+"/save-consignment-receipt",  
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
                                $('#ConsignmentReceiptForm')[0].reset();
                                swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                                    $('#CommonModal').modal('hide');
                                    ConsignmentReceipt.draw();
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
            }else {
                swal("Warning!", "Load data first", "warning");
            }
        }
    });
}
// 
$('body').on('click', '#get_order_details', function() {
    var inbound_order_no = $('#inbound_order_no').val();
    if(inbound_order_no == "") {
        swal("Sorry!", "Please Select Inbound Order No", "warning");
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/consignment-receipt/get-order-details",  
            type: "POST",
            data:  {inbound_order_no:inbound_order_no},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    hideLoader();
                    $('#entryProductTbody').html('');
                    var listData = '';
                    for(i=0; i< res.data.length; i++) {
                        listData += '<tr><td>'+(i+1)+'</td><td><input type="hidden" name="product_id[]" value="'+res.data[i].product_id+'"><input type="text" class="form-control" value="'+res.data[i].part_name+'" readonly></td><td><input type="text" class="form-control" value="'+res.data[i].pmpno+'" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]"></td></tr>';
                    }
                    $('#entryProductTbody').append(listData);
                }else {
                    hideLoader();
                    swal("Sorry!", res.msg, "warning");
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
// Edit Country-->
$('body').on('click',"a.view-consignment-receipt",function(){
    // console.log('hi');
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-consignment-receipt",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Consignment Receipt Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-lg');
                consignment_receipt_form();
            }else {
                hideLoader();
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
});
// Delete Countries 
$("body").on("click", "a.delete-consignment-receipt", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
    swal({
        title: "Are you sure?",
        text: "You want to delete it.",
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
                url:base_url+"/delete-consignment-receipt",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    // console.log(res);
                    if(res["status"]) {
                        //DataTable4CylinderTypeTable.draw();
                        //$('#pageOverlay').css('display', 'none');
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            ConsignmentReceipt.draw();
                        });
                    }else {
                        //$('#pageOverlay').css('display', 'none');
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    //$('#pageOverlay').css('display', 'none');
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    //$('#pageOverlay').css('display', 'none');
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});