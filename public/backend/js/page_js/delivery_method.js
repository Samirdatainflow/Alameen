// List Delivery Method
var DeliveryMethod = $('#DeliveryMethodList').DataTable({
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
        "url": base_url+"/config/list-delivery-method",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'delivery_method_id', name: 'delivery_method_id', orderable: true, searchable: false},
        {data: 'delivery_method', name: 'delivery_method', orderable: false, searchable: false},
        {data: 'delivery_description', name: 'delivery_description', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addDeliveryMethod()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Delivery Method</button> <button type="button" aria-haspopup="true" onclick="ExportTable()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/delivery-method-export";
}
// Add Delivery Method
function addDeliveryMethod() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/add-delivery-method",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Delivery Method");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                // $('.datetimepicker').datepicker({
                //     format : 'dd/mm/yyyy',
                //     todayHighlight: true,
                //     autoclose: true,
                // });
                //$(".modal-dialog").addClass('modal-lg');
                //$("#order_date").attr('readonly', true);
                delivery_method_form();
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
// Save Delivery Method
function delivery_method_form() {
    $("#CommonModal").find("#DeliveryMethodForm").validate({
        rules: {
            delivery_method: "required",
            delivery_description: "required",
            warehouseid: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#DeliveryMethodForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/config/save-delivery-method",  
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
                        $('#DeliveryMethodForm')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            DeliveryMethod.draw();
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
// Edit Delivery Method
$('body').on('click', 'a.edit-delivery-method', function() {
    var obj = $(this);
    var id = obj.data("id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/edit-delivery-method",
        type:'post',
        data: {id:id},
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Update Delivery Method");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                delivery_method_form();
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
// Delete Delivery Method
$("body").on("click", "a.delete-delivery-method", function(e) {                   
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
                url:base_url+"/config/delete-delivery-method",  
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
                            DeliveryMethod.draw();
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
  window.open(base_url+"/public/backend/file/delivery_method.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-delivery-method', function(){
    var file_data = $('#delivery_method_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/delivery-method/delivery-method-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Delivery Method Preview");
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
});
$('body').on('click', '.save-delivery-method-bulk-csv', function(){
    var file_data = $('#delivery_method_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/delivery-method/save-delivery-method-bulk-csv", 
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