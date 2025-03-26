
// Table
var mailApiKeyTable = $('#mailApiKeyTable').DataTable({
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
        "url": base_url+"/config/list-mail-api-key",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'smtp_host', name: 'smtp_host', orderable: false, searchable: false},
        {data: 'smtp_user', name: 'smtp_user', orderable: false, searchable: false},
        {data: 'smtp_port', name: 'smtp_port', orderable: false, searchable: false},
        {data: 'from_mail', name: 'from_mail', orderable: false, searchable: false},
        {data: 'from_name', name: 'from_name', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_mail_api_key" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Smtp Details</button> <button type="button" aria-haspopup="true" id="add_mail_api_key" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/config/mail-config-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/config/mail-api-key-form",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add SMTP Details");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#formContent").html(res);
            mail_api_key_form();
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
// Mail Api Key save
function mail_api_key_form() {
    $("#CommonModal").find("#configMailApiKeyForm").validate({
        rules: {
            smtp_user: "required",
            smtp_pass: "required",
            smtp_host: "required",
            smtp_port: "required",
            from_mail: "required",
            from_name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#configMailApiKeyForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/config/save-mail-api-key",  
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
                        $('#configMailApiKeyForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            mailApiKeyTable.draw();
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
// Edit Mail Api key-->
$('body').on('click',"a.edit-mail-api-key",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/edit-mail-api-key",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update SMTP Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                mail_api_key_form();
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
// Delete Mail Api key 
$("body").on("click", "a.delete-mail-api-key", function(e) {                   
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
                url:base_url+"/config/delete-mail-api-key",  
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
                            mailApiKeyTable.draw();
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
// Change status Js
$('body').on('click',"a.mail-api-key-change-status",function(){
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
   function (res) { 
    console.log(res);
    if(res.value)
    {
        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/change-mail-api-status",  
        type: "POST",
        data:  {id:id, status:status},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            console.log(res);
            if(res["status"]) {
                // hideLoader();
                swal({
                    title: "Success!",
                    text: res["msg"],
                    type: "success"
                }).then(function() {
                    mailApiKeyTable.draw();
                });
            }else {
                // hideLoader();
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
    
   },
   function () { 
       return false; 
   });
});