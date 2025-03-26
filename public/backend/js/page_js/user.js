    var userTable = $('#user').DataTable({
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
            "url": base_url+"/get-user",
            "type": "POST",
            'data': function(data){
              
            },
            
        },
        'columns': [
            {data: 'name', name: 'name', orderable: true, searchable: false},
            {data: 'gender', name: 'gender', orderable: false, searchable: false},
            {data: 'date_of_birth', name: 'date_of_birth', orderable: false, searchable: false},
            {data: 'address', name: 'address', orderable: false, searchable: false},
            {data: 'mobile', name: 'mobile', orderable: false, searchable: false},
            {data: 'email', name: 'email', orderable: false, searchable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'user_type', name: 'user_type', orderable: false, searchable: false},
            {data: 'user_role', name: 'user_role', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    }).on('xhr.dt', function(e, settings, json, xhr) {
       
    });

    $('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_user" aria-expanded="false" class="btn-shadow btn btn-info" onclick="add_user_form()"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add User</button> <button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportUserTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

    function ExportUserTable() {
    window.location.href = base_url+"/users/user-export";
    }

    function add_user_form(){
        $.ajax({
            url:base_url+"/user-form",
            type:'get',
            dataType:'html',
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                $('.modal-title').text('').text("Add User");
                $("#CommonModal").modal({
                	backdrop: 'static',
    				keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res);
                $('.datetimepicker').datepicker({
                    format : 'dd/mm/yyyy',
                    todayHighlight: true,
          			autoclose: true,
                });
                user_form();
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
    }
    //User Form save
    function user_form() {
        $("#CommonModal").find("#saveUserForm").validate({
            rules: {
                first_name: "required",
                last_name: "required",
                gender: "required",
                //date_of_birth: "required",
                address: "required",
                // mobile: {
                //     required: true,
                //     minlength:8,
                //     maxlength: 12
                // },
                //driving_licence: "required",
                //date_of_joining: "required",
                phone: {
                    minlength:8,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                username: "required",
                password: "required",
                confirm_password: {
                    required: true,
                    equalTo: "#password",
                },
                status: "required",
                user_type: "required",
                fk_user_role: "required",
            },
            messages: {
                mobile: {
                    minlength: "Please enter a valid mobile no.",
                    maxlength: "Please enter a valid mobile no."
                },
                confirm_password: {
                    equalTo: "Please re-enter the password",
                },
            },
            submitHandler: function() {
                var formData = new FormData($('#saveUserForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/save-user",  
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
                            $('#saveUserForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#CommonModal').modal('hide');
                                userTable.draw();
                                //DataTable4ProductBrands.draw();
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
    //Change status
    $("body").on("click", "a.change-status", function(e) {                   
	    var obj = $(this);
	    var id = obj.data("id");
	    var status = obj.data("status");
	    swal({
	        title: "Are you sure?",
	        text: "You want to change this status!",
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
	                url:base_url+"/change-user-status",  
	                type: "POST",
	                data:  {id: id, status:status},
	                beforeSend:function(){  
	                    //$('#pageOverlay').css('display', 'block');
	                },  
	                success:function(res){
	                    if(res["status"]) {
	                        swal({
	                            title: "Success!",
	                            text: res["msg"],
	                            type: "success"
	                        }).then(function() {
	                            if(res['user_status'] == "Active") {
	                            inactiveuserTable.draw();
	                        }else {
	                            userTable.draw();
	                        }
	                        });
	                    }else {
	                        swal("Opps!", res["msg"], "error");
	                    }
	                },
	                error: function(e) {
	                    swal("Opps!", "There is an error", "error");
	                },
	                complete: function(c) {
	                	//
	                }
	            });
	        } else if (
	            result.dismiss === Swal.DismissReason.cancel
	        ) {
	            swal("Cancelled", "Data is safe :)", "error")
	        }
	    })
	});
    // Edit User
    $('body').on('click',"a.edit-user",function(){
        var id = $(this).data('id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/edit-user",  
            type: "POST",
            data:  {id:id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    $('.modal-title').text('').text("Update User");
                    $("#CommonModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $(".modal-dialog").addClass('modal-lg');
                    $("#formContent").html(res["message"]);
                    $('.datetimepicker').datepicker({
                        format : 'dd/mm/yyyy',
                        todayHighlight: true,
                          autoclose: true,
                    });
                    $('#email').attr('readonly',true);
                    $('#username').attr('readonly',true);
                    user_form();
                    hideLoader();
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
    })
    //Inactive user
    var inactiveuserTable = $('#inactiveUser').DataTable({
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
            "url": base_url+"/get-inactive-user",
            "type": "POST",
            'data': function(data){
               
            },
            
        },
        'columns': [
            {data: 'name', name: 'name', orderable: true, searchable: false},
            {data: 'gender', name: 'gender', orderable: false, searchable: false},
            {data: 'date_of_birth', name: 'date_of_birth', orderable: false, searchable: false},
            {data: 'address', name: 'address', orderable: false, searchable: false},
            {data: 'mobile', name: 'mobile', orderable: false, searchable: false},
            {data: 'email', name: 'email', orderable: false, searchable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'user_type', name: 'user_type', orderable: false, searchable: false},
            {data: 'user_role', name: 'user_role', orderable: false, searchable: false},
        ],
       
    }).on('xhr.dt', function(e, settings, json, xhr) {
       
    });

    $('#inactiveUser_wrapper div.toolbar').html('<button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportIactiveUserTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

    function ExportIactiveUserTable() {
    window.location.href = base_url+"/all-inactive-user-export";
    }

$("body").on('click', '#download_template',function(){
  window.open(base_url+"/public/backend/file/users.csv");
});
$('body').on('click', '.file-upload-browse', function() {
    var file = $(this).parent().parent().parent().find('.file-upload-default');
    file.trigger('click');
});
$('body').on('change', '.file-upload-default', function() {
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});
$('body').on('click', '.preview-multiple-users', function(){
    var file_data = $('#users_csv').prop('files')[0];
    if(file_data) {  
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: base_url+"/users/users-preview", 
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
                $('#OrderPreviewModal .modal-title').text('').text("User Preview");
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
$('body').on('click', '.save-users-bulk-csv', function(){
    var file_data = $('#users_csv').prop('files')[0];   
    if(file_data) {
        var form_data = new FormData();                  
        form_data.append('file', file_data);                      
        $.ajax({
            url: base_url+"/users/save-users-bulk-csv", 
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