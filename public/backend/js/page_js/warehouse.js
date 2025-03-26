
// Table
var warehousTable = $('#warehouseTable').DataTable({
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
        "url": base_url+"/list-warehouse",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'name', name: 'name', orderable: true, searchable: false},
        {data: 'address', name: 'address', orderable: false, searchable: false},
        {data: 'city_id', name: 'city_id', orderable: false, searchable: false},
        {data: 'state_id', name: 'state_id', orderable: false, searchable: false},
        {data: 'country_id', name: 'country_id', orderable: false, searchable: false},
        {data: 'manager', name: 'manager', orderable: false, searchable: false},
        {data: 'contact', name: 'contact', orderable: false, searchable: false},
        {data: 'surface', name: 'surface', orderable: false, searchable: false},
        {data: 'volume', name: 'volume', orderable: false, searchable: false},
        {data: 'freezone', name: 'freezone', orderable: false, searchable: false},
        {data: 'select_warehouse', name: 'select_warehouse', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_warehouse" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Warehouse</button> <button type="button" aria-haspopup="true" id="add_warehouse" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/warehouse-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/warehouse-form",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Warehouse");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            save_warehouse_form();
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
// Warehouse save
function save_warehouse_form() {
    
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    
    $("#CommonModal").find("#saveWarehouseForm").validate({
        rules: {
            name: "required",
            //address: "required",
            city: "required",
            state: "required",
            country: "required",
            //manager_name: "required",
            //manager_c_number: "required",
            //warehouse_area: "required",
            //warehouse_volume: "required",
            //free_zone_volume: "required",
            //total_area_of_warehouse: "required",
            //ground_floor: "required",
           //mezzanine_floor: "required",
            //first_floor: "required",
            //racks_and_bins: "required",
            //pallets: "required",
            //inbound_check_area: "required",
            //outbound_check_area: "required",
            //work_area: "required",
            //area_of_office: "required",
            //accommodation: "required",
            //security_office: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#saveWarehouseForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-warehouse",  
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
                        $('#saveWarehouseForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            warehousTable.draw();
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
// Edit Warehouse-->
$('body').on('click',"a.edit-warehouse",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-warehouse",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Warehouse");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                save_warehouse_form();
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
$("body").on("click", "a.delete-warehouse", function(e) {                   
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
                url:base_url+"/delete-warehouse",  
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
                            warehousTable.draw();
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
// Select Warehouse
$('body').on('click',"a.select-warehouse",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/select-warehouse",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                window.location.reload();
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
$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/warehouse.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-warehouse', function(){
    var file_data = $('#warehouse_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/warehouse/warehouse-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Warehouse Preview");
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
$('body').on('click', '.save-warehouse-bulk-csv', function(){
    var file_data = $('#warehouse_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/warehouse/save-warehouse-bulk-csv", 
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