// dataTable
var supplierTable = $('#supplierList').DataTable({
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
        "url": base_url+"/list-supplier",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'supplier_code', name: 'supplier_code', orderable: false, searchable: false},
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'business_title', name: 'business_title', orderable: false, searchable: false},
        {data: 'mobile', name: 'mobile', orderable: false, searchable: false},
        {data: 'phone', name: 'phone', orderable: false, searchable: false},
        {data: 'address', name: 'address', orderable: false, searchable: false},
        {data: 'city_id', name: 'city_id', orderable: false, searchable: false},
        {data: 'state_id', name: 'state_id', orderable: false, searchable: false},
        {data: 'zipcode', name: 'zipcode', orderable: false, searchable: false},
        {data: 'country_id', name: 'country_id', orderable: false, searchable: false},
        {data: 'email', name: 'email', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_supplier" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Supplier</button> <button type="button" aria-haspopup="true" id="add_supplier" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/supplier-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/add-supplier",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Supplier");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            supplier_form();
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
// Supplier save
function supplier_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#supplierForm").validate({
        rules: {
            supplier_code: "required",
            full_name: "required",
            //business_title: "required",
            //address: "required",
            //mobile: "required",
            //phone: "required",
            // mobile: {
            //     required: true,
            //         minlength:10,
            //         maxlength: 10
            //     },
            // phone: {
            //     required: true,
            //         minlength:10,
            //         maxlength:10
            //     },
            //city: "required",
            state: "required",
            country: "required",
            // email: {
            //     required: true,
            //     email: true
            // },
            "group_ids[]": "required"
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == 'group_ids[]') {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var formData = new FormData($('#supplierForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-supplier",  
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
                        $('#supplierForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            supplierTable.draw();
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
// Delete Suppiler 
$("body").on("click", "a.delete-supplier", function(e) {                   
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
                url:base_url+"/delete-supplier",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    if(res["status"]) {
                        //DataTable4CylinderTypeTable.draw();
                        //$('#pageOverlay').css('display', 'none');
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            supplierTable.draw();
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
// Edit Js
$('body').on('click',"a.edit-supplier",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-supplier",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Supplier");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                supplier_form();
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
// Change status Js
$('body').on('click',"a.supplier-change-status",function(){
    var id = $(this).data('id');
    var status = $(this).data('status');
    swal({
        title: "Are you sure?",
        text: "You want to change this status.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, I am sure!',
        cancelButtonText: "No, cancel it!"
     }).then(
   function () { 
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/change-supplier-status",  
        type: "POST",
        data:  {id:id, status:status},
        dataType:"json", 
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
                    if(status == "Active") {
                        inactiveuserTable.draw();
                    }else {
                        supplierTable.draw();
                    }
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
   },
   function () { 
       return false; 
   });
});
$("#CommonModal").on("change",'#country',function(){
    
    $("#state").html('');
    $("#city").html('');
    $("#city").selectpicker('refresh');
    var country_id = $(this).find(':selected').data('country_code');
    
    $('#country_code').val(country_id);
    
    $.ajax({
        url:base_url+"/get-state",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{country_id:country_id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            
            $(res).each(function(i){
                $("#state").append("<option value='"+res[i]['name']+"' data-state_code='"+res[i]['iso2']+"'>"+res[i]['name']+"</option>");
            });
            $("#state").selectpicker('refresh');
            hideLoader();
            
        },
        error:function(){
           
        },
        complete:function(){
            hideLoader();
        }
    })
})
$("#CommonModal").on("change",'#state',function(){
    
    $("#city").html('');
    var country_id = $('#country').find(':selected').data('country_code');
    var state_id = $(this).find(':selected').data('state_code');
    $('#state_code').val(state_id);
    
    $.ajax({
        url:base_url+"/get-city",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{state_id:state_id, country_id:country_id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            
            $(res).each(function(i){
                $("#city").append("<option value='"+res[i]['name']+"'>"+res[i]['name']+"</option>");
            });
            $("#city").selectpicker('refresh');
            hideLoader();
            
        },
        error:function(){
            
        },
        complete:function(){
            hideLoader();
        }
    })
});
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/supplier.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-supplier', function(){
    var file_data = $('#supplier_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/supplier/supplier-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Supplier Preview");
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
$('body').on('click', '.save-supplier-bulk-csv', function(){
    var file_data = $('#supplier_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/supplier/save-supplier-bulk-csv", 
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