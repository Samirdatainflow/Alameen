// dataTable
var subcategoryList = $('#subcategoryList').DataTable({
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
        "url": base_url+"/list-sub-category",
        "type": "POST",
        'data': function(data){
            data.filter_model=$("#filter_model").val();
            data.filter_category=$("#filter_category").val();
        },
        
    },
    'columns': [
        {data: 'sub_category_name', name: 'sub_category_name', orderable: true, searchable: false},
        {data: 'category_name', name: 'category_name', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_model").on('change',function(){
    subcategoryList.draw();
});
$("#filter_category").on('change',function(){
    subcategoryList.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_model').val('');
    $('#filter_category').val('');
    subcategoryList.draw();
})
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_sub_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Sub Category</button> <button type="button" aria-haspopup="true" id="add_sub_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/sub-category-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-sub-category",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Sub Category");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            // $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            item_sub_category_form();
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
$("#CommonModal").on('keyup input', '.model-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-model-name",
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
                    $("#brand_id").html(res.data);
                    $("#brand_id").selectpicker('refresh');
                }else {
                    console.log("no data");
                    $("#brand_id").html(html);
                    $("#brand_id").selectpicker('refresh');
                }
            },
            error:function(){
            }
        });
});
// $("#CommonModal").on('keyup input', '.category-search .bs-searchbox input', function(e) {
//     var search_key = $(this).val();
//         $.ajax({
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             url:base_url+"/get-category-data",
//             data: {search_key:search_key},
//             type: "POST",
//             dataType: "json",
//             beforeSend:function(){  
//                 // console.log("Before");
//             },  
//             success:function(res){
//                 console.log(res);
//                 var html='';
//                 if(res["status"]) {
//                     $("#category_id").html(res.data);
//                     $("#category_id").selectpicker('refresh');
//                 }else {
//                     //$("#category_id").html(html);
//                     $("#category_id").selectpicker('refresh');
//                 }
//             },
//             error:function(){
//             }
//         });
// });
// $("#CommonModal").on('change','#brand_id', function(){
//     $("#category_id").html("<option value=''>Select Category </option>");
//     var brand_id = $(this).val();
//     $.ajax({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url:base_url+"/get-category",
//         data: {brand_id:brand_id},
//         type: "POST",
//         dataType: "json",
//         beforeSend:function(){  
//             showLoader();
//         },  
//         success:function(res){
//             $("#category_id").html(res.data);
//             $("#category_id").selectpicker('refresh');
//             hideLoader();
//         },
//     });
// });
// Item Sub Category save
function item_sub_category_form() {
    $('#brand_id').selectpicker();
    $('#category_id').selectpicker();
    $("#CommonModal").find("#itemSubCategoryForm").validate({
        rules: {
            brand_id: "required",
            category_id: "required",
            sub_category_name: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#itemSubCategoryForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-item-sub-category",  
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
                        $('#itemSubCategoryForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            subcategoryList.draw();
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
// Delete Sub Category 
$("body").on("click", "a.delete-sub-Category", function(e) {                   
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
                url:base_url+"/delete-item-sub-category",  
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
                            subcategoryList.draw();
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
// Edit Sub Category-->
$('body').on('click',"a.edit-sub-category",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-item-sub-category",  
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
                $('.modal-title').text('').text("Update Sub Category");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                item_sub_category_form();
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
// Get Category By Model
$('#filter_model').on('change', function(){
    var id = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/item-management/get-category-by-model",
        type:'post',
        data: {id: id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#filter_category').find('option:not(:first)').remove();
                for(i=0; i<res.data.length; i++){
                    $("#filter_category").append('<option value="'+res.data[i]['category_id']+'">'+res.data[i]['category_name']+'</option>');
                }
            }else {
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
});
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/product_sub_category.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-sub-category', function(){
    var file_data = $('#product_sub_category_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/sub-category/sub-category-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Sub Category Preview");
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
$('body').on('click', '.save-sub-category-bulk-csv', function(){
    var file_data = $('#product_sub_category_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/sub-category/save-sub-category-bulk-csv", 
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