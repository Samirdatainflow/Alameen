// Table
var ShippingTable = $('#ShippingList').DataTable({
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
        "url": base_url+"/list-shipping",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'shipping_id', name: 'shipping_id', orderable: true, searchable: false},
        // {data: 'sale_order_id', name: 'sale_order_id', orderable: false, searchable: false},
        {data: 'items', name: 'items', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Shipping</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/shipping-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-shipping",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Shipping");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            $('.selectpicker').selectpicker().change(function(){
                $(this).valid()
            });
            shipping_form();
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
function shipping_form() {
    $("#CommonModal").find("#ShippingForm").validate({
        rules: {
            order_id: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#ShippingForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-shipping",  
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
                        $('#ShippingForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            ShippingTable.draw();
                        });
                    }else {
                        hideLoader();
                        swal("Sorry!", res["msg"], "error");
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
// 
$('body').on('click', '#get_order_details', function() {
    
    var client_id = $('#client_id').val();
    var order_id = $('#order_id').val();
    
    if(client_id == "" || client_id == null) {
        
        swal("Sorry!", "Please Select A Customer", "warning");
        
    }else if(order_id.length < 1) {
        
        swal("Sorry!", "Please Enter A Order ID", "warning");
        
    } else {
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/shipping/get-order-details",  
            type: "POST",
            data:  {order_id:order_id,client_id:client_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    hideLoader();
                    $('#OrderDetails').after().html(res.message);
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
$('body').on('click',"a.view-shipping",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-shipping",  
        type: "POST",
        data: {id:id},
        dataType:"json",
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Shipping Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-lg');
                $('#sales_order_id').val(id);
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
$("body").on("click", "a.delete-packing", function(e) {                   
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
                url:base_url+"/delete-packing",  
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
                            Packing.draw();
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
// Packing slip download
$('body').on('click', '.add-shipping-address', function() {
    var shipping_address_count = $('#shipping_address_count').val();
    //console.log(shipping_address_count);
    $('#ShippingAddressSection').append('<div class="row" style="margin-bottom:10px" id="shippingAddrInput'+shipping_address_count+'"><div class="col-md-1"><input type="radio" id="html" name="shipping_address_active" value="1" title="Set as Primary Address" style="position: absolute;top: 25px;width: 100%;height: 20px;" class="shipping-address-active"><input type="hidden" class="address-status" name="address_status[]" value=""></div><div class="col-md-9"><input type="hidden" name="shipping_address_id[]" value=""><textarea class="form-control" name="shipping_address[]"></textarea></div><div class="col-md-2"><button type="button" class="btn-shadow btn btn-info" onclick="removeShippingAddress('+shipping_address_count+')" style="position: absolute;top: 12px;"> <i class="fa fa-trash"></i> </button></div></div>');
    $('#shipping_address_count').val(parseInt(shipping_address_count) + 1);
});
function removeShippingAddress(id) {
    console.log(id);
    $('#shippingAddrInput'+id).remove();
}
// $('#CommonModal').find("input:radio[name='shipping_address_active']").chnage(function() {
//   if($(this).is(':checked')) {
//     console.log("You have a checked radio button here");
//   } 
//   else {
//     console.log("You have a unchecked");
//   }
// });
$("body").on("change", ".shipping-address-active", function(e) {
    $('.address-status').val('');
    $('#CommonModal').find('input[type="radio"]').each(function(){
        if ($(this).is(':checked')) {
            var obj = $(this);
            obj.parent().find('.address-status').val('10');
        }
    });
});

function changeCustomer(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-packing-ids-by-customer",
        type:'post',
        data: {id: id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            
            if(res['status']) {
                hideLoader();
                var data = "";
                for(i=0; i<res.data.length; i++){
                    data += '<option value="'+res.data[i]['sale_order_id']+'">'+res.data[i]['sale_order_id']+'</option>';
                }
                $("#order_id").html(data);
                $("#order_id").selectpicker('refresh');
            }else {
                var data = "";
                $("#order_id").html(data);
                $("#order_id").selectpicker('refresh');
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal("Opps!", "There is an error", "error");
        },
        complete:function(){
            hideLoader();
        }
    })
}
