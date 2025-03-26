$("#PasswordChangeForm").on("click",function(){
    $("#passwordChangeModal").modal('show');
});
$("#current_password").on("change",function(){
    var current_password = $(this).val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/check-current-password",
        type:'post',
        dataType:'JSON',
        data:{current_password,current_password},
        beforeSend:function(){
            $("#loader").css("display","block");
        },
        success:function(res){
            if(!res['status']) {
              $("#current_password").val("");  
              $("#current_pass_wrong").css("display",'block');
            }
            else
            {
                $("#current_pass_wrong").css("display",'none');
            }
        },
        error:function(){
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            $("#loader").css("display","none");
        }
    })
})
// $("#PasswordChange").on("click",function(){
//     $.ajax({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url:base_url+"/change-pass",
//         type:'post',
//         dataType:'JSON',
//         beforeSend:function(){
//             showLoader();
//         },
//         success:function(res){
//             hideLoader();
//             if(res['status']) {
//                 $('.modal-title').text('').text("Change Password");
//                 $("#CommonModal").modal('show');
//                 //$(".modal-dialog").addClass('modal-lg');
//                 $("#formContent").html(res['message']);
//                 password_change_form();
//             }
//         },
//         error:function(){
//             hideLoader();
//             swal({title: "Sorry!", text: "There is an error", type: "error"});
//         },
//         complete:function(){
//             hideLoader();
//         }
//     })
// });

$("#Update_profile").validate({
        rules: {
            customer_name: {
                required: true
            },
            sponsor_name: {
                required: true
            }
        },
        submitHandler: function() {
            var customer_name=$("#customer_name").val();
            var sponsor_name=$("#sponsor_name").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/update-profile",
                data:{customer_name:customer_name,sponsor_name:sponsor_name},
                type:'post',
                dataType:'JSON',
                beforeSend:function(){
                    $("#loader").css("display","block");
                },
                success:function(res){
                    if(res['status'])
                    {
                        swal({title: "Success", text: res['msg'], type: "success"});
                    }
                },
                error:function(){
                    swal({title: "Sorry!", text: "There is an error", type: "error"});
                },
                complete:function(){
                    $("#loader").css("display","none");
                }
            })
        }
    });

$("#update_password").validate({
        rules: {
            current_password: {
                required: true
            },
            new_password: {
                required: true
            },
            confirm_password:{
                required: true,
                equalTo : "#new_password"
            }
        },
        submitHandler: function() {
            var new_password = $("#new_password").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/update-password",
                data:{new_password:new_password},
                type:'post',
                dataType:'JSON',
                beforeSend:function(){
                    $("#loader").css("display","block");
                },
                success:function(res){
                    if(res['status'])
                    {
                        swal({title: "Success", text: res['msg'], type: "success"}).then(function() {
                         window.location.reload();
                    });;
                    }

                },
                error:function(){
                    swal({title: "Sorry!", text: "There is an error", type: "error"});
                },
                complete:function(){
                    $("#loader").css("display","none");
                }
            })
        }
    });