// dataTable
var oemList = $('#oemList').DataTable({
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
        "url": base_url+"/list-oem",
        "type": "POST",
        'data': function(data){
            data.filter_model=$("#filter_model").val();
            data.filter_category=$("#filter_category").val();
            data.filter_subcategory=$("#filter_subcategory").val();
        },
        
    },
    'columns': [
        {data: 'oem_no', name: 'oem_no', orderable: true, searchable: false},
        {data: 'oem_details', name: 'oem_details', orderable: false, searchable: false},
        {data: 'brand_name', name: 'brand_name', orderable: false, searchable: false},
        {data: 'category_name', name: 'category_name', orderable: false, searchable: false},
        {data: 'sub_category_name', name: 'sub_category_name', orderable: false, searchable: false},
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
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_oem" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Oem No</button>');
function show_form(){
     $.ajax({
        url:base_url+"/add-oem",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Oem No");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            // $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            item_oem_form();
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
    // var search_key = $(this).find(".model-search").val();
    var search_key = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-model-name-by-oem",
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
                    //$("#product_name").html(""+res.data[0]['product_name']);
                }
                else
                {
                    console.log("no data");
                    $("#brand_id").html(html);
                    $("#brand_id").selectpicker('refresh');
                }
            },
            error:function(){
            }
        });
});
$("#CommonModal").on('change','#brand_id', function(){
    var brand_id = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-category-oem",
        data: {brand_id:brand_id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#category_id').find('option:not(:first)').remove();
                $("#category_id").append(res.data);
            }else {
                hideLoader();
                $('#category_id').find('option:not(:first)').remove();
            }
        },
    });
});
$("#CommonModal").on('change','#category_id', function(){
    var category_id = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-sub-category-oem",
        data: {category_id:category_id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#sub_category_id').find('option:not(:first)').remove();
                $("#sub_category_id").append(res.data);
            }else {
                hideLoader();
                $('#sub_category_id').find('option:not(:first)').remove();
            }
        },
    });
});
// Item Oem save
function item_oem_form() {
    $('#brand_id').selectpicker();
    $("#CommonModal").find("#itemOemForm").validate({
        rules: {
            brand_id: "required",
            category_id: "required",
            sub_category_id: "required",
            oem_no: "required",
            oem_details: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#itemOemForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-item-oem",  
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
                        $('#itemOemForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            oemList.draw();
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
$("body").on("click", "a.delete-oem", function(e) {                   
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
                url:base_url+"/delete-item-oem",  
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
                            oemList.draw();
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
// Edit -->
$('body').on('click',"a.edit-oem",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-item-oem",  
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
                $('.modal-title').text('').text("Update Oem");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                item_oem_form();
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
// Get Sub Category By Model
$('#filter_category').on('change', function(){
    var id = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/item-management/get-subcategory-by-category",
        type:'post',
        data: {id: id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('#filter_subcategory').find('option:not(:first)').remove();
                for(i=0; i<res.data.length; i++){
                    $("#filter_subcategory").append('<option value="'+res.data[i]['sub_category_id']+'">'+res.data[i]['sub_category_name']+'</option>');
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