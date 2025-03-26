// dataTable
var itemTable = $('#ItemList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    'orderable': true,
    "responsive": true,
    "pageLength" : 50,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-item-management",
        "type": "POST",
        'data': function(data){
            data.filter_car_manufacture=$("#filter_car_manufacture").val();
            data.filter_car_model=$("#filter_car_model").val();
            data.filter_from_year=$("#filter_from_year").val();
            data.filter_from_month=$("#filter_from_month").val();
            data.filter_to_year=$("#filter_to_year").val();
            data.filter_to_month=$("#filter_to_month").val();
            data.filter_category=$("#filter_category").val();
            data.filter_sub_category=$("#filter_sub_category").val();
            data.filter_part_name=$("#filter_part_name").val();
            data.filter_part_no=$("#filter_part_no").val();
            data.filter_part_brand=$("#filter_part_brand").val();
            data.engine_no=$("#engine_no").val();
            data.chassis=$("#chassis").val();
            data.manufacturer_no=$("#manufacturer_no").val();
            data.alternate_part_no=$("#alternate_part_no").val();
            data.filter_units=$("#filter_units").val();
        },
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: true, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'alternate_part_no', name: 'alternate_part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'unit', name: 'unit', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'stock_alert', name: 'stock_alert', orderable: false, searchable: false},
        {data: 'current_stock', name: 'current_stock', orderable: false, searchable: false},
        {data: 'lc_price', name: 'lc_price', orderable: false, searchable: false},
        {data: 'selling_price', name: 'selling_price', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {});
$("#filter_car_manufacture").on('change',function(){
    itemTable.draw();
});
$("#filter_car_model").on('change',function(){
    itemTable.draw();
});
$("#filter_from_year").on('change',function(){
    itemTable.draw();
});
$("#filter_from_month").on('change',function(){
    itemTable.draw();
});
$("#filter_to_year").on('change',function(){
    itemTable.draw();
});
$("#filter_to_month").on('change',function(){
    itemTable.draw();
});
$("#filter_category").on('change',function(){
    itemTable.draw();
});
$("#filter_sub_category").on('change',function(){
    itemTable.draw();
});
$("#filter_part_name").on('change',function(){
    itemTable.draw();
});
$("#filter_part_no").on('keyup input',function(){
    itemTable.draw();
});
$("#filter_part_brand").on('change',function(){
    itemTable.draw();
});
$("#engine_no").on('keyup input',function(){
    itemTable.draw();
});
$("#manufacturer_no").on('keyup input',function(){
    itemTable.draw();
});
$("#chassis").on('keyup input',function(){
    itemTable.draw();
});
$("#alternate_part_no").on('keyup input',function(){
    itemTable.draw();
});
$("#filter_units").on('change',function(){
    itemTable.draw();
});
$('#ResetFilter').on('click', function(){
    $("#filter_car_manufacture").val('');
    $("#filter_car_model").val('');
    $("#filter_from_year").val('');
    $("#filter_from_month").val('');
    $("#filter_to_year").val('');
    $("#filter_to_month").val('');
    $('#filter_category').val('');
    $('#filter_sub_category').find('option:not(:first)').remove();
    $('#filter_part_name').val('');
    $('#filter_part_no').val('');
    $("#filter_part_brand").val('');
    $("#engine_no").val('');
    $("#chassis").val('');
    $('#filter_units').val('');
    $('#manufacturer_no').val('');
    itemTable.draw();
})//
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_item_management" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Item</button> <button type="button" aria-haspopup="true" id="add_item_management" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_bulk_upload_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Bulk Upload</button> <button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportItemManagementTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportItemManagementTable() {
    var info = itemTable.page.info();
    window.location.href = base_url+"/item-management-export?start="+info.start+"&end="+info.end;
}

function show_form(){
    $.ajax({
        url:base_url+"/add-item-management",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Item");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $('.input-tags').tagsInput();
            $("#engine_tag").attr("placeholder", "Engine No");
            $("#engine_tag").css("width", "100px");
            $("#chassis_model_tag").attr("placeholder", "Chassis / Model");
            $("#chassis_model_tag").css("width", "100px");
            $("#manfg_no_tag").attr("placeholder", "Manufacturer No");
            $("#manfg_no_tag").css("width", "110px");
            $("#altn_part_tag").attr("placeholder", "Alternate Part No");
            $("#altn_part_tag").css("width", "110px");
            item_management_form();
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
$('body').on('keyup, input',"#engine_tag, #chassis_model_tag, #manfg_no_tag, #altn_part_tag",function(){
    if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
    }
    var _msgLenght = $(this).val().length;
    if (_msgLenght > 30) {
        $(this).val($(this).val().substring(0, 30));
    }
});
function show_bulk_upload_form(){
    $.ajax({
        url:base_url+"/add-item-management-bulk-upload",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            $('.modal-title').text('').text("Add Item");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").removeClass('modal-xl');
            $("#formContent").html(res);
            //$('.input-tags').tagsInput();
            //$("#engine_tag").attr("placeholder", "Engine No*");
            //$("#engine_tag").css("width", "100px");
            //$("#chassis_model_tag").attr("placeholder", "Chassis / Model");
            //$("#chassis_model_tag").css("width", "100px");
            //$("#manfg_no_tag").attr("placeholder", "Manufacturer No*");
            //$("#manfg_no_tag").css("width", "110px");
            //$("#altn_part_tag").attr("placeholder", "Alternate Part No*");
            //$("#altn_part_tag").css("width", "110px");
            //item_management_bulk_upload_form();
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
function item_management_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
    }).change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#ItemManagementForm").validate({
        rules: {
            part_brand_id: "required",
            pmpno: "required",
            part_name_id: "required",
            car_manufacture_id: "required",
            car_name_id: "required",
            //from_year: "required",
            //from_month: "required",
            //to_year: "required",
            //to_month: "required",
            engine_id: "required",
            'car_model_id[]': "required",
            ct: "required",
            sct: "required",
            oem_no: "required",
            pno_created_date: "required",
            gr: "required",
            application: "required",
            manfg_no: "required",
            altn_part: "required",
            unit: "required",
            //pmrprc: "required",
            //mark_up: "required",
            //lc_price: "required",
            //lc_date: "required",
            //prvious_lc_price: "required",
            //prvious_lc_date: "required",
            moq: "required",
            //country_id: "required",
            //supplier_id: "required",
            //supplier_currency: "required",
            //re_order_level: "required",
            //no_re_order: "required",
            //stop_sale: "required",
            //'warehouse_id[]': "required",
            //reserved_qty: "required",
            //allocation_qty: "required",
            //last_month_stock: "required",
            current_stock: "required",
            //qty_in_transit: "required",
            //qty_on_order: "required",
            //stock_alert: "required",
            alert: "required",
            current_stock: "required",
        },
        messages: {
            'warehouse_id[]': "This field is required",
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "part_brand_id") {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'part_name_id') {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'car_manufacture_id') {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'car_name_id') {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'ct') {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'car_model_id[]') {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var formData = new FormData($('#ItemManagementForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-item-management",  
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
                        $('#ItemManagementForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            itemTable.draw();
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
// View Item Management-->
$('body').on('click',"a.view-item",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/item-management/view-item",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Item Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                //$(".modal-dialog").removeClass('modal-xl');
                $(".modal-dialog").addClass('modal-xl');
                $("#formContent").html(res["message"]);
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
$("body").on("click", "a.delete-item", function(e) {                   
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
                url:base_url+"/delete-item-management",  
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
                            itemTable.draw();
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
// Edit Item Management-->
$('body').on('click',"a.edit-item",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-item-management",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Item");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").removeClass('modal-lg');
                $(".modal-dialog").addClass('modal-xl');
                $("#formContent").html(res["message"]);
                $('.input-tags').tagsInput();
                $("#engine_tag").attr("placeholder", "Engine");
                $("#engine_tag").css("width", "100px");
                $("#chassis_model_tag").attr("placeholder", "Chassis / Model");
                $("#chassis_model_tag").css("width", "100px");
                $("#manfg_no_tag").attr("placeholder", "Manufacturer No");
                $("#manfg_no_tag").css("width", "110px");
                $("#altn_part_tag").attr("placeholder", "Alternate Part");
                $("#altn_part_tag").css("width", "110px");
                item_management_form();
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
// Part Brand Search
$("#CommonModal").on('keyup input', '.part-brand-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-part-brand-for-search-box",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
            console.log(res);
            if(res["status"]) {
                $("#part_brand_id").html(res.data);
                $("#part_brand_id").selectpicker('refresh');
            }else {
                $("#part_brand_id").html('');
                $("#part_brand_id").selectpicker('refresh');
            }
        },
        error:function(){
        }
    });
});
// Part Name Search
$("#CommonModal").on('keyup input', '.part-name-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-part-name-for-search-box",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
            console.log(res);
            if(res["status"]) {
                $("#part_name_id").html(res.data);
                $("#part_name_id").selectpicker('refresh');
            }else {
                $("#part_name_id").html('');
                $("#part_name_id").selectpicker('refresh');
            }
        },
        error:function(){
        }
    });
});
// Car Manufacture Search
$("#CommonModal").on('keyup input', '.car-manufacture-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-car-manufacture-for-search-box",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
            console.log(res);
            if(res["status"]) {
                $("#car_manufacture_id").html(res.data);
                $("#car_manufacture_id").selectpicker('refresh');
            }else {
                $("#car_manufacture_id").html('');
                $("#car_manufacture_id").selectpicker('refresh');
            }
        },
        error:function(){
        }
    });
});
// Get Car Model By Car Manufacture
function changeCarManufacture(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-car-model-by-car-manufacture",
        type:'post',
        data: {id: id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                //$('.list-car-model').find('option:not(:first)').remove();
                var data = "";
                for(i=0; i<res.data.length; i++){
                    data += '<option value="'+res.data[i]['brand_id']+'">'+res.data[i]['brand_name']+'</option>';
                    //$(".list-car-model").append('<option value="'+res.data[i]['brand_id']+'">'+res.data[i]['brand_name']+'</option>');
                }
                $("#car_model_id").html(data);
                //$("#car_manufacture_id").selectpicker('refresh');
                $("#car_model_id").selectpicker('refresh');
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
}
// Car Name Search
$("#CommonModal").on('keyup input', '.car-name-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-car-name-for-search-box",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
            console.log(res);
            if(res["status"]) {
                $("#car_name_id").html(res.data);
                $("#car_name_id").selectpicker('refresh');
            }else {
                $("#car_name_id").html('');
                $("#car_name_id").selectpicker('refresh');
            }
        },
        error:function(){
        }
    });
});
// Get Model By Model Name
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
                    $("#brn").html(res.data);
                    $("#brn").selectpicker('refresh');
                }else {
                    $("#brn").html(html);
                    $("#brn").selectpicker('refresh');
                }
            },
            error:function(){
            }
        });
});
$("#CommonModal").on('keyup input', '.category-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-category-data",
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
                    $("#ct").html(res.data);
                    $("#ct").selectpicker('refresh');
                }else {
                    $("#ct").html(html);
                    $("#ct").selectpicker('refresh');
                }
            },
            error:function(){
            }
        });
});
// Get Sub Category By Category
function changeCategory(id) {
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
                $('.list-sub-category').find('option:not(:first)').remove();
                for(i=0; i<res.data.length; i++){
                    $(".list-sub-category").append('<option value="'+res.data[i]['sub_category_id']+'">'+res.data[i]['sub_category_name']+'</option>');
                }
            }else {
                $('.list-sub-category').find('option:not(:first)').remove();
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
// Get OEM No By Sub Category
function changeSubCategory(id) {
    // $.ajax({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     url:base_url+"/item-management/get-oem-no-by-sub-category",
    //     type:'post',
    //     data: {id: id},
    //     dataType:'json',
    //     beforeSend:function(){
    //         showLoader();
    //     },
    //     success:function(res){
    //         if(res['status']) {
    //             hideLoader();
    //             $('#oem_no').find('option:not(:first)').remove();
    //             for(i=0; i<res.data.length; i++){
    //                 $("#oem_no").append('<option value="'+res.data[i]['oem_id']+'">'+res.data[i]['oem_no']+'</option>');
    //             }
    //         }else {
    //             hideLoader();
    //         }
    //     },
    //     error:function(){
    //         hideLoader();
    //         swal("Opps!", "There is an error", "error");
    //     },
    //     complete:function(){
    //         hideLoader();
    //     }
    // })
}
// Remove Engine
$('body').on('click', 'a.remove-engine', function() {
    _this = $(this);
    var id = $(this).data('id');
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
                url:base_url+"/item-management/remove-engine",  
                type: "POST",
                data:  {id:id},
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    hideLoader();
                    if(res["status"]) {
                        _this.remove();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    });
});
// Remove Chassis Model
$('body').on('click', 'a.remove-chassis-model', function() {
    _this = $(this);
    var id = $(this).data('id');
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
                url:base_url+"/item-management/remove-chassis-model",  
                type: "POST",
                data:  {id:id},
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    hideLoader();
                    if(res["status"]) {
                        _this.remove();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    });
});
// Remove Manufacturing No
$('body').on('click', 'a.remove-manufacturing-no', function() {
    _this = $(this);
    var id = $(this).data('id');
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
                url:base_url+"/item-management/remove-manufacturing-no",  
                type: "POST",
                data:  {id:id},
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    hideLoader();
                    if(res["status"]) {
                        _this.remove();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    });
});
// Remove Alternate No
$('body').on('click', 'a.remove-alternate-no', function() {
    _this = $(this);
    var id = $(this).data('id');
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
                url:base_url+"/item-management/remove-alternate-no",  
                type: "POST",
                data:  {id:id},
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    hideLoader();
                    if(res["status"]) {
                        _this.remove();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    });
});
// Add Part Brand
$('body').on('click', 'a.new-part-brand', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-part-brand",
        type: 'post',
        dataType: 'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Part Brand");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            part_brand_form();
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
});
function part_brand_form() {
    $('#brand_id').selectpicker();
    $("#OrderPreviewModal").find("#PartBrandForm").validate({
        rules: {
            part_brand_name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#PartBrandForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-part-brand",  
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
                        $('#PartBrandForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            $("#part_brand_id").selectpicker('refresh');
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
// Add Part Name
$('body').on('click', 'a.new-part-name', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-part-name",
        type: 'post',
        dataType: 'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Part Name");
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            part_name_form();
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
});
function part_name_form() {
    $("#OrderPreviewModal").find("#PartNameForm").validate({
        rules: {
            part_name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#PartNameForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-part-name",  
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
                        $('#PartNameForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            $("#part_name_id").selectpicker('refresh');
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
// Add Car Manufacture
$('body').on('click', 'a.new-car-manufacture', function() {
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-car-manufacture",
        type: 'post',
        dataType: 'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Car Manufacture");
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            car_manufacture_form();
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
});
function car_manufacture_form() {
    $('#brand_id').selectpicker();
    $("#OrderPreviewModal").find("#CarManufactureForm").validate({
        rules: {
            car_manufacture: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#CarManufactureForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-car-manufacture",  
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
                        $('#CarManufactureForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            $("#car_manufacture_id").selectpicker('refresh');
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
// Add Car Model
$('body').on('click', 'a.new-car-model', function() {
    var car_manufacture_id = $('#car_manufacture_id').val();
    if(car_manufacture_id == "") {
        swal({ title: "Sorry!", text: "Please Select A Car Manufacture", type: "warning" });
    }else {
    	$.ajax({
            url:base_url+"/add-brand",
            type:'get',
            dataType:'html',
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                hideLoader();
                $('#OrderPreviewModal .modal-title').text('').text("Add Model");
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
                $("#OrderPreviewModal #formContent").html(res);
                $('.csv-upload').css('display', 'none');
                $('#hidden_id').val(car_manufacture_id);
                item_brand_form();
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
});
function item_brand_form() {
    $("#OrderPreviewModal").find("#userBrandForm").validate({
        rules: {
            Brand_name: "required",
            car_manufacture_id: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#userBrandForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-item-brand",  
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
                        $('#userBrandForm')[0].reset();
                        swal({title: "success!", text: res["msg"], type: "success"}).then(function() {
                            $('body #CommonModal').find('#car_manufacture_id').selectpicker('refresh');
                            $('body #CommonModal').find('#car_model_id').selectpicker('refresh');
                            $('#OrderPreviewModal').modal('hide');
                            $("#car_model_id").append('<option value="'+res.data[0]['brand_id']+'">'+res.data[0]['brand_name']+'</option>');
                            $("#car_model_id").selectpicker('refresh');
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
// Add Category 
$('body').on('click', 'a.new-category', function() {
	$.ajax({
        url:base_url+"/add-category",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Catagory");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            item_category_form();
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
});
function item_category_form() {
    $("#OrderPreviewModal").find("#itemCategoryForm").validate({
        rules: {
            category_name: "required",
            category_description: "required",
            brand_id: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#itemCategoryForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-item-category",  
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
                        $('#itemCategoryForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            $('#ct').selectpicker('refresh');
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
// Add Sub Category 
$('body').on('click', 'a.new-sub-category', function() {
	var ct = $('#ct').val();
    if(ct == "") {
        swal({ title: "Sorry!", text: "Please Select A Category", type: "warning" });
    }else {
	    $.ajax({
	        url:base_url+"/add-sub-category",
	        type:'get',
	        dataType:'html',
	        beforeSend:function(){
	            showLoader();
	        },
	        success:function(res){
	            hideLoader();
	            $('#OrderPreviewModal .modal-title').text('').text("Add Sub Catagory");
	            $("#OrderPreviewModal").modal({
	                backdrop: 'static',
	                keyboard: false
	            });
	            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
	            $("#OrderPreviewModal #formContent").html(res);
	            $('.csv-upload').css('display', 'none');
	            $('#hidden_id').val(ct);
	            item_sub_category_form();
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
});
$("#OrderPreviewModal").on('keyup input', '.category-search .bs-searchbox input', function(e) {
    var search_key = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-category-data",
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
                $("#category_id").html(res.data);
                $("#category_id").selectpicker('refresh');
            }else {
                $("#category_id").html(html);
                $("#category_id").selectpicker('refresh');
            }
        },
        error:function(){
        }
    });
});
function item_sub_category_form() {
    $('#category_id').selectpicker();
    $("#OrderPreviewModal").find("#itemSubCategoryForm").validate({
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
                url:base_url+"/common-url/save-item-sub-category",  
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
                        $('#itemSubCategoryForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            $('#ct').selectpicker('refresh');
                            $("#sct").append('<option value="'+res.data[0]['sub_category_id']+'">'+res.data[0]['sub_category_name']+'</option>');
                            //$('.list-sub-category').find('option:not(:first)').remove();
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
// Add Group
$('body').on('click', 'a.new-group', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-group",
        type:'get',
        dataType:'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Group");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            //$('.csv-upload').css('display', 'none');
            item_group_form();
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
});
function item_group_form() {
    //$('#brand_id').selectpicker();
    $("#OrderPreviewModal").find("#itemGroupForm").validate({
        rules: {
            group_name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#itemGroupForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-item-group",  
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
                        $('#gr').find('option:not(:first)').remove();
                        for(i=0; i<res.data.length; i++){
                            $("#gr").append('<option value="'+res.data[i]['group_id']+'">'+res.data[i]['group_name']+'</option>');
                        }
                        hideLoader();
                        $('#itemGroupForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            //$("#part_brand_id").selectpicker('refresh');
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
// Add Unit 
$('body').on('click', 'a.new-unit', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-units",
        type:'get',
        dataType:'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Unit");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            //$('.csv-upload').css('display', 'none');
            config_units_form();
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
});
function config_units_form() {
    $("#OrderPreviewModal").find("#configUnitsForm").validate({
        rules: {
            unit_name: "required",
            unit_type: "required",
            base_factor: "required",
            base_measurement_unit: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#configUnitsForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-config-unit",  
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
                        $('#unit').find('option:not(:first)').remove();
                        for(i=0; i<res.data.length; i++){
                            $("#unit").append('<option value="'+res.data[i]['unit_id']+'">'+res.data[i]['unit_name']+'</option>');
                        }
                        hideLoader();
                        $('#configUnitsForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
                            //$("#part_brand_id").selectpicker('refresh');
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
// Add Country 
$('body').on('click', 'a.new-country', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/countries-form",
        type:'get',
        dataType:'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Country");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            save_countries_form();
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
});
function save_countries_form() {
    $("#OrderPreviewModal").find("#configContriesForm").validate({
        rules: {
            country_code: "required",
            country_name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#configContriesForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-config-countries",  
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
                        $('#country_id').find('option:not(:first)').remove();
                        for(i=0; i<res.data.length; i++){
                            $("#country_id").append('<option value="'+res.data[i]['country_id']+'">'+res.data[i]['country_name']+'</option>');
                        }
                        hideLoader();
                        $('#configContriesForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
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
// Add Country 
$('body').on('click', 'a.new-supplier-currency', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-currency",
        type:'get',
        dataType:'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('#OrderPreviewModal .modal-title').text('').text("Add Currency");
            $("#OrderPreviewModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#OrderPreviewModal .modal-dialog').removeClass('modal-xl');
            $("#OrderPreviewModal #formContent").html(res);
            $('.csv-upload').css('display', 'none');
            config_currency_form();
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
});
function config_currency_form() {
    $("#OrderPreviewModal").find("#configCurrencyForm").validate({
        rules: {
            currency_code: "required",
            currency_description: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#configCurrencyForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/common-url/save-config-currency",  
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
                        $('#supplier_currency').find('option:not(:first)').remove();
                        for(i=0; i<res.data.length; i++){
                            $("#supplier_currency").append('<option value="'+res.data[i]['currency_id']+'">'+res.data[i]['currency_code']+'</option>');
                        }
                        hideLoader();
                        $('#configCurrencyForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#OrderPreviewModal').modal('hide');
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
$('.modal').on("hidden.bs.modal", function (e) { 
    if ($('.modal:visible').length) { 
        $('body').addClass('modal-open');
    }
});
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/item_management.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-item-management', function(){
    var file_data = $('#item_management_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/item-management/item-management-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Item Preview");
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
$('body').on('click', '.save-item-bulk-csv', function(){
    var file_data = $('#item_management_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/item-management/save-item-management-bulk", 
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