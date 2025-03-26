// Get Model By Car Manufacture
$("body").on("click", ".car-manufacture", function(e) {  
    _this = $(this);
    
    var obj = $(this);
    var id = obj.data("car_manufacture_id");
    $("#hidden_car_manufacture").val(id);
    obj.parent('ul').find('.list-group-item').removeClass("active");
    obj.addClass("active");
    
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-car-model-by-car-manufacture",
        data: {id:id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },
        success:function(res){
            hideLoader();

            $("#car_model_id").html(""); 
            if(res['status']) {
                $(res.data).each(function(i){
                    $("#car_model_id").append("<li data-brand_id='"+res.data[i]['brand_id']+"' data-brand_name='"+res.data[i]['brand_name']+"' style='cursor: pointer' class='brand list-group-item'>"+res.data[i]['brand_name']+"</li>");
                })
                
            }
            choose_items();
        },
        error: function(e) {
            hideLoader();
        },
        complete: function(c){
            hideLoader();
        }
    });
});
// Get Model By Model Name
// $('body').on('keyup input', '#search_model', function() {
//     var search_key = $(this).val();
//     $.ajax({
//         headers: {
//           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url:base_url+"/get-model-by-model-name",
//         data: {search_key:search_key},
//         type: "POST",
//         dataType: "json",
//         beforeSend:function(){  
//             //showLoader();
//         },
//         success:function(res){
//             $("#brand_id").html('');
//             if(res['status']) {
//                 $(res.data).each(function(i){
//                     $("#brand_id").append("<li data-brand_id='"+res.data[i]['brand_id']+"' data-brand_name='"+res.data[i]['brand_name']+"' style='cursor: pointer' class='brand list-group-item'>"+res.data[i]['brand_name']+"</li>");
//                 })
//             }
//         },
//         error: function(e) {
//             //hideLoader();
//         },
//         complete: function(c){
//             //hideLoader();
//         }
//     });
// })
// Get Part No
$('body').on('keyup input', '#search_part_no', function() {
    var search_key = $(this).val();
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-part-no-by-search",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            //showLoader();
        },
        success:function(res){
            $("#part_no_id").html('');
            if(res['status']) {
                $(res.data).each(function(i){
                    $("#part_no_id").append("<li data-pmpno='"+res.data[i]['pmpno']+"' style='cursor: pointer' class='part-no list-group-item'>"+res.data[i]['pmpno']+"</li>");
                })
            }
        },
        error: function(e) {
            //hideLoader();
        },
        complete: function(c){
            //hideLoader();
        }
    });
})
// Get Category By Model
$("body").on("click", ".brand", function(e) {  
    _this = $(this);
    $('#hidden_model_name').val(_this.data('brand_name'));
    //$("#category_id").html(""); 
    //$('.subcategory-card').css('display','none');
    //$('.oem-card').css('display','none');
    var obj = $(this);
    var id = obj.data("brand_id");
    _this.parent('ul').find('.list-group-item').removeClass("active");
    _this.addClass("active");
    $('#hidden_model').val(id);
    choose_items();
});
// fromyear
$("body").on("click", ".fromyear", function(e) {
    var obj = $(this);
    var fromyear = obj.data("fromyear");
    obj.parent('ul').find('.list-group-item').removeClass("active");
    obj.addClass("active");
    $('#hidden_from_year').val(fromyear);
    choose_items();
});
// frommonth
$("body").on("click", ".frommonth", function(e) {
    var obj = $(this);
    var frommonth = obj.data("frommonth");
    obj.parent('ul').find('.list-group-item').removeClass("active");
    obj.addClass("active");
    $('#hidden_from_month').val(frommonth);
    choose_items();
});
// toyear
$("body").on("click", ".toyear", function(e) {
    var obj = $(this);
    var toyear = obj.data("toyear");
    obj.parent('ul').find('.list-group-item').removeClass("active");
    obj.addClass("active");
    $('#hidden_to_year').val(toyear);
    choose_items();
});
// tomonth
$("body").on("click", ".tomonth", function(e) {
    var obj = $(this);
    var tomonth = obj.data("tomonth");
    obj.parent('ul').find('.list-group-item').removeClass("active");
    obj.addClass("active");
    $('#hidden_to_month').val(tomonth);
    choose_items();
});
// Get Sub Category By Category
$("body").on("click", ".category", function(e) {  
    _this = $(this);
    $("#subcategory_id").html(""); 
    $('.oem-card').css('display','none');
    $('#hidden_subcategory_name').val('');
    $('#hidden_category_name').val(_this.data('category_name'));
    _this.parent('ul').find('.list-group-item').removeClass("active");
    _this.addClass("active");
    
    var obj = $(this);
    var id = obj.data("category_id");
    $('#hidden_ct').val(id);
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/item-search/get-subcategory-by-category",
        data: {id:id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },
        success:function(res){
            hideLoader();
            
            if(res['status']) {
                $('.subcategory-card').css('display','block');
                $(res.data).each(function(i){
                    $("#subcategory_id").append("<li data-sub_category_id='"+res.data[i]['sub_category_id']+"' data-sub_category_name='"+res.data[i]['sub_category_name']+"' style='cursor: pointer' class='subcategory list-group-item'>"+res.data[i]['sub_category_name']+"</li>");

                })
            }
            choose_items();
        },
        error: function(e) {
            hideLoader();
        },
        complete: function(c){
            hideLoader();
        }
    });
});
// Get OEM No by Sub Category
$("body").on("click", ".subcategory", function(e) {
    _this = $(this);
    var obj = $(this);
    var sub_category_id = obj.data("sub_category_id");
    var hidden_car_manufacture = $('#hidden_car_manufacture').val();
    var hidden_model = $('#hidden_model').val();
    var hidden_from_year = $('#hidden_from_year').val();
    var hidden_from_month = $('#hidden_from_month').val();
    var hidden_to_year = $('#hidden_to_year').val();
    var hidden_to_month = $('#hidden_to_month').val();
    var hidden_ct = $('#hidden_ct').val();
    if(hidden_model == "") {
        swal("Warning!", "Please select a Model", "warning");
    }else {
        $(".item-search").css('display','none');
        $('.all-item').css('display','block');
        var page = $('#page_count').val();
        $('#hidden_sct').val(sub_category_id);
        _this.parent('ul').find('.list-group-item').removeClass("active");
        _this.addClass("active");
        get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, sub_category_id, page);
        choose_items();
    }
    // $.ajax({
    //     headers: {
    //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     url:base_url+"/item-search/get-oem-no-by-sub-category",
    //     data: {id:id},
    //     type: "POST",
    //     dataType: "json",
    //     beforeSend:function(){  
    //         showLoader();
    //     },  
    //     success:function(res){
    //         if(res['status']) {
    //             hideLoader();
    //             _this.parent('ul').find('.list-group-item').removeClass("active");
    //             _this.addClass("active");
    //             $('.oem-card').css('display','block');
    //             $(res.data).each(function(i){
    //                 $("#oem_id").append("<li data-oem_no_id='"+res.data[i]['oem_id']+"' style='cursor: pointer' class='oem list-group-item'>"+res.data[i]['oem_no']+"</li>");
    //             })
    //         }else {
    //             hideLoader();
    //             swal("Sorry!", res['msg'], "warning");
    //         }
    //     },
    //     error: function(e) {
    //         hideLoader();
    //     },
    //     complete: function(){
    //         hideLoader();
    //     }
    // });
});
// Get Item Details by Oem
// $("body").on("click", ".oem", function(e) {
//     $(".item-search").css('display','none');
//     $('.all-item').css('display','block');
//     var page = $('#page_count').val();
//     var obj = $(this);
//     var id = "";
//     var hidden_part_no = $('#hidden_part_no').val();
//     if(hidden_part_no != "") {
//         var id = hidden_part_no;
//     }else {
//         id = obj.data("oem_no_id");
//     }
//     get_seach_item(id, page);
// });
function get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, sub_category_id, page) {
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/item-search/get-item-details",
        data: {hidden_car_manufacture:hidden_car_manufacture, hidden_model:hidden_model, hidden_from_year:hidden_from_year, hidden_from_month:hidden_from_month, hidden_to_year:hidden_to_year, hidden_to_month:hidden_to_month, hidden_ct:hidden_ct, sub_category_id:sub_category_id, page:page},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('#page_count').val(parseInt(page)+1);
                //$('#hidden_sct').val(id);
                $('.all-item').css('display','block');
                if(page == "1") {
                    $('#listSearchItems').html('');
                }
                $('#listSearchItems').append(res["message"]);
                $("#loader").css("display","none");
                if(res["total_row"] > 50 && res["select_row"] > 0) {
                    $('.load-more').css('display','block');
                }else {
                    $('.load-more').css('display','none');
                }
            }else {
                hideLoader();
                $('#listSearchItems').html('');
                $("#loader").css("display","none");
            }
        },
        error: function(e) {
            hideLoader();
        },
        complete: function(){
            hideLoader();
        }
    });
}
$(".back").on('click',function(){
    $('#page_count').val(parseInt(1));
    $('#hidden_sct').val('');
    $(".item-search").css('display','flex'); 
    $('.all-item').css('display','none');
});
$('body').on('keyup input', '.filter-item-search', function() {
    var filter_val = $(this).val();
    var hidden_car_manufacture = $('#hidden_car_manufacture').val();
    var hidden_model = $('#hidden_model').val();
    var hidden_from_year = $('#hidden_from_year').val();
    console.log("hidden_from_year "+hidden_from_year);
    var hidden_from_month = $('#hidden_from_month').val();
    console.log("hidden_from_month "+hidden_from_month);
    var hidden_to_year = $('#hidden_to_year').val();
    console.log("hidden_to_year "+hidden_to_year);
    var hidden_to_month = $('#hidden_to_month').val();
    console.log("hidden_to_month "+hidden_to_month);
    var hidden_ct = $('#hidden_ct').val();
    var hidden_sct = $('#hidden_sct').val();
    var page = $('#page_count').val();
    if(filter_val != "") {
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/item-search/filter-item-search",
            data: {hidden_car_manufacture:hidden_car_manufacture, hidden_model:hidden_model, hidden_from_year:hidden_from_year, hidden_from_month:hidden_from_month, hidden_to_year:hidden_to_year, hidden_to_month:hidden_to_month, hidden_ct:hidden_ct, hidden_sct:hidden_sct, filter_val:filter_val},
            type: "POST",
            dataType: "json",
            beforeSend:function(){
            },  
            success:function(res){
                if(res["status"]) {
                    //hideLoader();
                    $('#listSearchItems').html('');
                    $('#listSearchItems').append(res["message"]);
                    $("#loader").css("display","none");
                    $('.load-more').css('display','none');
                }else {
                    //hideLoader();
                }
            },
            error: function(e) {
                //hideLoader();
            },
            complete: function(c){
                //hideLoader();
            }
        });
    }else {
        $('#page_count').val(parseInt(1));
        get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, hidden_sct, '1');
    }
});
function choose_items() {
    var html = ""
    $('#choose_items').html("");
    $(".item-search").find('li.active').each(function(i){
        html +='<span class="btn-shadow btn btn-info">'+$(this).text().trim()+'</span> / ';
    });
    $('#choose_items').html(html.slice(0,-2));
}
$("body").on("click","a.add-to-cart",function(){
	var hidden_cart_count = $('#hidden_cart_count').val();
    var product_id = $(this).data('product-id');
    var qty = $(this).parents('.cart-details').find('.cart-qty').val();
    if(qty > 0) {
		$.ajax({
			url : base_url+"/item-search/add-to-cart", 
			type: 'POST',
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {product_id:product_id,qty:qty},
			dataType:'json',
			beforeSend:function(){  
				showLoader();
			},
			success: function(data){
				hideLoader();
				if(data['status']) {
					$('#cart_count').html(parseInt(hidden_cart_count) + 1);
					$('#hidden_cart_count').val(parseInt(hidden_cart_count) + 1);
					swal("Success", "Product is successfully added in your cart", "success");
				}else {
					swal("Opps!", data['msg'], "error");
				}
			},
			error: function(e) {
				hideLoader();
			},
			complete:function(){
				hideLoader();
			}
		});
    }else {
        swal("Sorry!", "Please enter quantity.", "warning");
    }
});