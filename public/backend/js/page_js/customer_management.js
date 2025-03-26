// dataTable
var customerList = $('#CustomerList').DataTable({
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
        "url": base_url+"/list-customer-management",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'customer_id', name: 'customer_id', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'customer_off_msg_no', name: 'customer_off_msg_no', orderable: false, searchable: false},
        {data: 'customer_email_id', name: 'customer_email_id', orderable: false, searchable: false},
        {data: 'create_order', name: 'create_order', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_customer_management" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Customer</button> <button type="button" aria-haspopup="true" id="add_customer_management" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/customer-management-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/add-customer-management",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Customer");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            customer_management_form();
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
// Item Management save
function customer_management_form() {
    $("#CommonModal").find("#CustomerManagementForm").validate({
        rules: {
            customer_id: "required",
            customer_name: "required",
            reg_no: "required",
            //sponsor_name: "required",
            //sponsor_id: "required",
            // password: "required",
            // customer_email_id: {
            //     required: true,
            //     email: true
            // },
            // customer_wa_no: {
            //     required: true,
            //     minlength: 10,
            //     maxlength: 10,
            // },
            customer_off_msg_no: {
                required: true,
                minlength: 10,
                maxlength: 10,
            },
            // customer_wa_no: {
            //     required: true,
            //     minlength:8,
            //     maxlength: 10
            // },
            // customer_off_msg_no: {
            //     required: true,
            //     minlength:8,
            //     maxlength: 10
            // },
            //customer_address: "required",
            //store_address: "required",
            //ho_address: "required",
            //cr_address: "required"
        },
        messages: {
            // customer_wa_no: {
            //     minlength: "Please enter a valid mobile no.",
            //     maxlength: "Please enter a valid mobile no."
            // },
            // customer_off_msg_no: {
            //     minlength: "Please enter a valid mobile no.",
            //     maxlength: "Please enter a valid mobile no."
            // },
        },
        submitHandler: function() {
            var formData = new FormData($('#CustomerManagementForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-customer-management",  
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
                    console.log(res);
                    if(res["status"]) {
                        hideLoader();
                        $('#CustomerManagementForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            customerList.draw();
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
// Edit Category-->
$('body').on('click',"a.edit-customer",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-customer-management",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Customer");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                $('#password').prop('disabled', true);
                customer_management_form();
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
// Delete Category 
$("body").on("click", "a.delete-customer", function(e) {                   
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
                url:base_url+"/delete-customer-management",  
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
                            customerList.draw();
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
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/customer.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-customer', function(){
    var file_data = $('#customer_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/customer/customer-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Customer Preview");
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".order_details").html(res);
                $("#OrderPreviewModal .modal-dialog").addClass('modal-xl');
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
$('body').on('click', '.save-customer-bulk-csv', function(){
    var file_data = $('#customer_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/customer/save-customer-bulk-csv", 
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
function ViewCustomerDocument(extention, file) {
    var html = "";
    if(extention == "pdf") {
        html = '<div class="row"><iframe src="'+file+'" style="width:100%; height:500px;" frameborder="0"></iframe></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>';
        $('.location-modal-title').text('').text("Customer Document");
        $("#LocationModal").modal({
            backdrop: 'static',
            keyboard: false
        });
        $("#LocationformContent").html(html);
        $(".location-dailog").addClass('modal-lg');
    }else if(extention == "png" || extention == "jpg" || extention == "jpeg"){
        html = '<div class="row"><div class="col-md-12 text-center"><img src="'+file+'" style="width:100%"></div></div><div class="row"><p>&nbsp;<?p><div class="col-md-12"><p class="text-right"><button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p></div></div>';
        $('.location-modal-title').text('').text("Customer Document");
        $("#LocationModal").modal({
            backdrop: 'static',
            keyboard: false
        });
        $("#LocationformContent").html(html);
        $(".location-dailog").addClass('modal-lg');
    }else {
        window.open(file);
    }
}
$('body').on('change', '#customer_region', function() {
    $('#customer_teritory option').each(function() {
        $(this).css('display', 'block');
        // $(this).attr('selected', false);
        // $(this).attr('disabled', false);
    });
    var teritory = $(this).find(':selected').data('teritory');
    $('#customer_teritory option[value='+teritory+']').attr('selected','selected');
    $('#customer_teritory option').each(function() {
        if(!this.selected) {
            //$(this).attr('disabled', true);
            $(this).css('display', 'none');
        }
    });
});

// View Customer Document
$('body').on('click',"a.view-customer-documents",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-customer-documents",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Customer Documents");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
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
