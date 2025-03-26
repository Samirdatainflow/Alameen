$("#ProfileForm").validate({
    rules: {
        email: {
            required: true,
            email: true
        },
        password: "required",
        conpassword: {
            required: true,
            equalTo : "#password"
        }
    },
    submitHandler: function() {
        var formData = new FormData($('#ProfileForm')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/profile-save",  
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
                    $('#ProfileForm')[0].reset();
                    swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                        window.location.reload();
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