$(document).ready(function() {
    $("#loginForm").validate({
        rules: {
            email: {
                required: true
            },
            password: {
                required: true
            }
        },
        submitHandler: function() {
            $('.btn-primary').prop('disabled', true);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/login-match",
                method:"POST",  
                data:$('#loginForm').serialize(),  
                beforeSend:function(){  
                    //
                },  
                success:function(res){
                    if(res["status"]) {
                        $('.btn-primary').prop('disabled', false);
                        window.location.href= base_url+"/dashboard";
                    }else {
                        swal("Opps!", res["msg"], "error");
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function(e) {
                    $('.btn-primary').prop('disabled', false);
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    $('.btn-primary').prop('disabled', false);
                }
            });  
        }
    });
});