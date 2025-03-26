// dataTable
var userRollTable = $('#userRollList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-user-role",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'name', name: 'name', orderable: true, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_user_role" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add User Role</button> <button type="button" aria-haspopup="true" id="add_user_role" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/user-role-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/add-user-role",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add User Role");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#formContent").html(res);
            user_roll_form();
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
// User Roll save
function user_roll_form() {
    $("#CommonModal").find("#userRollForm").validate({
        rules: {
            name: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#userRollForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-user-role",  
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
                        $('#userRollForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            userRollTable.draw();
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
// Change status Js
$('body').on('click',"a.role-change-status",function(){
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
        url:base_url+"/change-user-role-status",  
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
                    title: "Success",
                    text: res["msg"],
                    type: "success"
                }).then(function() {
                    if(status == "Active") {
                        inactiveuserTable.draw();
                    }else {
                        userRollTable.draw();
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
$("body").on("click", "a.role-change-status", function(e) {                   
    var id = $(this).data('id');
    var status = $(this).data('status');
    swal({
        title: "Are you sure?",
        text: "You want to change this status.",
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
                url:base_url+"/change-user-role-status",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    if(res["status"]) {
		                hideLoader();
		                swal({
		                    title: "Success",
		                    text: res["msg"],
		                    type: "success"
		                }).then(function() {
		                    if(status == "Active") {
		                        inactiveuserTable.draw();
		                    }else {
		                        userRollTable.draw();
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});
// Edit Roll
$('body').on('click',"a.edit-role",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-user-role",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Role");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                user_roll_form();
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
// Delete Roll
$("body").on("click", "a.delete-role", function(e) {                   
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
                url:base_url+"/delete-user-role",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                	hideLoader();
                    if(res["status"]) {
                        swal({
                            title: "Success",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            userRollTable.draw();
                        });
                    }else {
                        //$('#pageOverlay').css('display', 'none');
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
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});
