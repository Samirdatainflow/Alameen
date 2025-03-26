// dataTable
var DeliveryManagemenTable = $('#DeliveryManagementList').DataTable({
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
        "url": base_url+"/list-delivery-management",
        "type": "POST",
        'data': function(data){
            data.filter_model=$("#filter_model").val();
            data.filter_category=$("#filter_category").val();
            data.filter_subcategory=$("#filter_subcategory").val();
        },
        
    },
    'columns': [
        {data: 'delivery_management_id', name: 'delivery_management_id', orderable: true, searchable: true},
        {data: 'shipping_id', name: 'shipping_id', orderable: false, searchable: true},
        {data: 'sale_order_id', name: 'sale_order_id', orderable: false, searchable: false},
        {data: 'sale_order_date', name: 'sale_order_date', orderable: false, searchable: false},
        {data: 'vehicle_no', name: 'vehicle_no', orderable: false, searchable: false},
        {data: 'driver_name', name: 'driver_name', orderable: false, searchable: false},
        {data: 'contact_no', name: 'contact_no', orderable: false, searchable: false},
        {data: 'vehicle_in_out_date_show', name: 'vehicle_in_out_date_show', orderable: false, searchable: false},
        {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
        {data: 'courier_date_show', name: 'courier_date_show', orderable: false, searchable: false},
        {data: 'courier_number', name: 'courier_number', orderable: false, searchable: false},
        {data: 'no_of_box', name: 'no_of_box', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_model").on('change',function(){
    oemList.draw();
});
$("#filter_category").on('change',function(){
    oemList.draw();
});
$("#filter_subcategory").on('change',function(){
    oemList.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_model').val('');
    $('#filter_category').val('');
    $('#filter_subcategory').val('');
    oemList.draw();
})
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Delivery</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="TableExport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function TableExport() {
    window.location.href = base_url+"/delivery-management-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-delivery-management",
        type:'get',
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Delivery Management");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res.message);
            delivery_management_form();
            // }
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
$("#CommonModal").on('change','#shipping_id', function(){
    var shipping_id = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/delivery-management/get-shipping-details",
        data: {shipping_id:shipping_id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            console.log(res);
            if(res['status']) {
                hideLoader();
                $('#shipping_id').css('border-color', 'green');
                $('#sale_order_id').val(res.sale_order_id);
                $('#order_date').val(res.order_date);
            }else {
                hideLoader();
                $('#shipping_id').val('');
                $('#shipping_id').css('border-color', 'red');
                swal("Sorry!", res["msg"], "warning");
            }
        },
    });
});
// Item Oem save
function delivery_management_form() {
    $('.datetimepicker').datepicker({
        format : 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
    });
    $("#CommonModal").find("#DeliveryManagementForm").validate({
        rules: {
            shipping_id: "required",
            order_date: "required",
            // vehicle_no: "required",
            // driver_name: "required",
            // contact_no: {
            //     "required": true,
            //     "minlength": 8
            // },
            // vehicle_in_out_date: "required",
            // courier_company_id: "required",
            // courier_date: "required",
            // courier_number: "required",
            // no_of_box: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#DeliveryManagementForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-delivery-management",  
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
                    // console.log(res);
                    if(res["status"]) {
                        hideLoader();
                        $('#DeliveryManagementForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            DeliveryManagemenTable.draw();
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
// Delete 
$("body").on("click", "a.delete-delivery-management", function(e) {                   
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
                url:base_url+"/delete-delivery-management",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    if(res["status"]) {
                        hideLoader();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            DeliveryManagemenTable.draw();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});
// Edit -->
$('body').on('click',"a.edit-delivery-management",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-delivery-management",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            // console.log(res);
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Delivery Management");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-lg');
                $('#order_number').prop('disabled', true);
                delivery_management_form();
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
// Print
$('body').on('click', '.print-delivery-management', function() {
    var obj = $(this);
    var shipping_id = obj.data("shipping_id");
    window.open(base_url+"/print-delivery-management?id="+shipping_id, '_blank');
});