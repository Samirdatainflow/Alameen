// dataTable
var productRateTable = $('#productRateTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-product-rate",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'default_rate', name: 'default_rate', orderable: false, searchable: false},
        {data: 'level_1', name: 'level_1', orderable: false, searchable: false},
        {data: 'level_2', name: 'level_2', orderable: false, searchable: false},
        {data: 'level_3', name: 'level_3', orderable: false, searchable: false},
        {data: 'level_4', name: 'level_4', orderable: false, searchable: false},
        {data: 'level_5', name: 'level_5', orderable: false, searchable: false},
        {data: 'warehouse_id', name: 'warehouse_id', orderable: false, searchable: false},
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_prduct_rate" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Product Rate</button> <button type="button" aria-haspopup="true" id="add_prduct_rate" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/product-rate-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/add-product-rate",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Product Rate");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            product_rate_form();
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
// $("body").on('change', '#product_id', function(){
//     alert();
// });
$("body").on('keyup input', '.bs-searchbox input', function(e) {
    // var make_id = $(this).find("#product_id").val();
    var search_key = $(this).val();
    // console.log(product_id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-product-name",
            data: {search_key:search_key},
            type: "POST",
            dataType: "json",
            beforeSend:function(){  
                // console.log("Before");
            },  
            success:function(res){
                console.log(res);
                var html='';
                if(res["status"]) {
                    
                    $("#product_id").html(res.data);
                    $("#product_id").selectpicker('refresh');
                    //$("#product_name").html(""+res.data[0]['product_name']);
                }
                else
                {
                    console.log("no data");
                    $("#product_id").html(html);
                    $("#product_id").selectpicker('refresh');
                }
            },
            error:function(){
            }
        });
});
// Insert Js
function product_rate_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#productRateForm").validate({
        rules: {
            default_rate: "required",
            level_1: "required",
            level_2: "required",
            level_3: "required",
            level_4: "required",
            level_5: "required",
            warehouse_id: "required",
            product_id: "required"
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "product_id") {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var formData = new FormData($('#productRateForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-product-rate",  
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
                        $('#productRateForm')[0].reset();
                        swal({title: "success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            productRateTable.draw();
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
// Delete Js
$("body").on("click", "a.delete-product-rate", function(e) {                   
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
                url:base_url+"/delete-product-rate",  
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
                            productRateTable.draw();
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
$('body').on('click',"a.edit-product-rate",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-product-rate",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Product Rate");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                $('.selectpicker').selectpicker().change(function(){
                    $(this).valid()
                });
                product_rate_form();
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
// Bulk Upload
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/product_rate.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-product-rate', function(){
    var file_data = $('#product_rate_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/product-rate/product-rate-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Product Rate Preview");
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
$('body').on('click', '.save-product-rate-bulk-csv', function(){
    var file_data = $('#product_rate_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/product-rate/save-product-rate-bulk", 
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