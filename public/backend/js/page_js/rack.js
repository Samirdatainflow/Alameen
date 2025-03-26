// Table
var RackTable = $('#RackList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-12'<'toolbar'>><'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
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
        "url": base_url+"/config/list-rack",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'rack_name', name: 'rack_name', orderable: true, searchable: false},
        {data: 'location', name: 'location', orderable: false, searchable: false},
        {data: 'zone_name', name: 'zone_name', orderable: false, searchable: false},
        {data: 'row_name', name: 'row_name', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('#RackList_wrapper div.toolbar').html('<button type="button" aria-haspopup="true" id="add_country" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_rack_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Rack</button> <button type="button"  aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportRackTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportRackTable(){
    window.location.href = base_url+"/config/rack-export-table";
}

function show_rack_form(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/add-rack",
        type:'post',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Rack");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#formContent").html(res);
            rack_form();
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
// Save
function rack_form() {
    $("#CommonModal").find("#RackForm").validate({
        rules: {
            location_id: "required",
            zone_id: "required",
            row_id: "required",
            rack_name: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#RackForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/config/save-rack",  
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
                        $('#RackForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            RackTable.draw();
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
// Edit-->
$('body').on('click',"a.edit-rack",function(){
    // console.log('hi');
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/edit-rack",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Rack");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                rack_form();
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
// Delete 
$("body").on("click", "a.delete-rack", function(e) {                   
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
                url:base_url+"/config/delete-rack",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    // console.log(res);
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            RackTable.draw();
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
// Get Zone By Location
function changeLocation(id) {
    if(id != "") {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-zone-by-location",  
            type: "POST",
            data:  {id:id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    hideLoader();
                    $('#zone_id').children('option:not(:first)').remove();
                    $('#row_id').children('option:not(:first)').remove();
                    $(res.data).each(function(i){
                        $("#zone_id").append("<option value='"+res.data[i]['zone_id']+"'>"+res.data[i]['zone_name']+"</option>");
                    });
                }else {
                    hideLoader();
                    swal("Sorry!", res["msg"], "error");
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
        $('#zone_id').children('option:not(:first)').remove();
        $('#row_id').children('option:not(:first)').remove();
    }
}
// Get Wor By Zone
function changeZone(id) {
    if(id != "") {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-row-by-zone",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('#row_id').children('option:not(:first)').remove();
                $(res.data).each(function(i){
                    $("#row_id").append("<option value='"+res.data[i]['row_id']+"'>"+res.data[i]['row_name']+"</option>");
                });
            }else {
                hideLoader();
                swal("Sorry!", res["msg"], "error");
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
        $('#row_id').children('option:not(:first)').remove();
    }
}
// Bulk Upload
$("body").on('click', '#rack_download_template',function(){
  window.open(base_url+"/public/backend/file/rack.csv");
});
$('body').on('click', '.rack-file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-rack', function(){
    var file_data = $('#rack_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/rack/rack-bulk-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("Rack Preview");
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
$('body').on('click', '.save-rack-bulk-csv', function(){
    var file_data = $('#rack_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/rack/save-rack-bulk", 
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