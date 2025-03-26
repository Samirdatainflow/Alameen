$(".update").on('click',function(){
	var qty = $(this).parents('tr').find('.qty').val();
	var product_id = $(this).data('product-id');
	$.ajax({
		headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
		url:base_url+"/update-cart",
		data:{product_id:product_id,qty:qty},
		dataType:'json',
		type:'post',
		beforeSend:function(){
            $("#loader").css("display","block");
        },
		success:function(res){
			if(res['status'])
			{

				swal({
                      title: 'Success',
                      text: "Cart is updated successfully",
                      icon: 'success',
                      type:'success',
                    }).then(function() {
                      window.location.reload();
                    });
			}
			else
			{
				swal({
                      title: 'Opps!',
                      text: "Sorry! There quantity is not available",
                      icon: 'error',
                      type:'error',
                    }).then(function() {
                      window.location.reload();
                    });
			}
		},
		error:function(){
			swal("Opps!", "Sorry! There is an error", "error");
			
		},
		complete:function(){
			$("#loader").css("display","none");
		}
	})
})
$(".trash").on('click',function(){
	var product_id = $(this).data('product-id');
	$.ajax({
		headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
		url:base_url+"/delete-cart-item",
		data:{product_id:product_id},
		dataType:'json',
		beforeSend:function(){
            $("#loader").css("display","block");
        },
		success:function(res){
			if(res['status'])
			{
				swal({
                      title: 'Success',
                      text: "Item is deleted successfully",
                      icon: 'success',
                      type:'success',
                    }).then(function() {
                      window.location.reload();
                    });
			}
			
		},
		error:function(){
			swal("Opps!", "Sorry! There is an error", "error");
		},
		complete:function(){
			$("#loader").css("display","none");
		}
	})
})