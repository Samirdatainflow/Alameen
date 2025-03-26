// Table
var Packing = $('#PackingList').DataTable({
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
        "url": base_url+"/list-packing",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'packing_id', name: 'packing_id', orderable: true, searchable: false},
        {data: 'sale_order_id', name: 'sale_order_id', orderable: false, searchable: false},
        {data: 'items', name: 'items', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});

function ExportTable() {
    window.location.href = base_url+"/category-export";
}
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Packing</button> <button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportTable() {
    window.location.href = base_url+"/packing-export";
}

// $('body').on('click', '.export-excel', function() {
    
//     console.log(sales_order_id); 
// });

function ExportDetailsTable() {
    var sales_order_id = $('#sales_order_id').val();
    window.location.href = base_url+"/packing-export-details?id="+sales_order_id;
    
}

function show_form(){
    $.ajax({
        url:base_url+"/add-packing",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Packing");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            packing_form();
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
function packing_form() {
    $("#CommonModal").find("#PackingForm").validate({
        rules: {
            order_id: "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('input');
            if(last_tr.length > 0) {
                var formData = new FormData($('#PackingForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/save-packing",  
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
                            $('#PackingForm')[0].reset();
                            swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                                $('#CommonModal').modal('hide');
                                Packing.draw();
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
            }else {
                swal("Warning!", "Load data first", "warning");
            }
        }
    });
}
//

// export excel


// export excel 
$('body').on('click', '#get_order_details', function() {
    var order_id = $('#order_id').val();
    if(order_id == "") {
        swal("Sorry!", "Please Enter A Order ID", "warning");
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/packing/get-order-details",  
            type: "POST",
            data:  {order_id:order_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    hideLoader();
                    $('#OrderDetails').after().html(res.message);
                    // var listData = '';
                    // for(i=0; i< res.data.length; i++) {
                    //     listData += '<tr><td>'+(i+1)+'</td><td><input type="hidden" name="product_id[]" value="'+res.data[i].product_id+'"><input type="text" class="form-control" value="'+res.data[i].part_name+'" readonly></td><td><input type="text" class="form-control" value="'+res.data[i].pmpno+'" readonly></td><td><input type="text" class="form-control" name="price[]" value="'+res.data[i].price+'" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" value="'+res.data[i].quantity+'" readonly></td></tr>';
                    // }
                    // $('#entryProductTbody').append(listData);
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
$('body').on('click',"a.view-packing",function(){
    // console.log('hi');
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-packing",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Packing Details");
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
$('body').on('click', '.print-packing-slip', function() {
    var sales_order_id = $('#sales_order_id').val();
    window.open(base_url+"/print-packing?id="+sales_order_id, '_blank');
});

