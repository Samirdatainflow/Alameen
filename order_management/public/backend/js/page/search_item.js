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
            $("#loader").css("display","block");
        },
        success:function(res){
            $("#loader").css("display","none");
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
$('body').on('keyup input', '#search_model', function() {
    var search_key = $(this).val();
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-model-by-model-name",
        data: {search_key:search_key},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            //showLoader();
        },
        success:function(res){
            $("#brand_id").html('');
            if(res['status']) {
                $(res.data).each(function(i){
                    $("#brand_id").append("<li data-brand_id='"+res.data[i]['brand_id']+"' data-brand_name='"+res.data[i]['brand_name']+"' style='cursor: pointer' class='brand list-group-item'>"+res.data[i]['brand_name']+"</li>");
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
// Sub Category Filter //
$("body").on("click", ".brand", function(e) {
    _this = $(this);
    _this = $(this);
    $('#hidden_model_name').val(_this.data('brand_name'));
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
$("body").on("click", ".category", function(e) {
	_this = $(this);
    $("#subcategory_id").html(""); 
    //$('.oem-card').css('display','none');
    _this.parent('ul').find('.list-group-item').removeClass("active");
    _this.addClass("active");
    var obj = $(this);
    var id = obj.data("category-id");
    $('#hidden_subcategory_name').val('');
    $('#hidden_category_name').val(_this.data('category_name'));
    $('#hidden_ct').val(id);
    choose_items();
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-subcategory-id",
        data: {id:id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            $("#loader").css("display","block");
        },  
        success:function(res){
            $('.subcategory-card').css('display','block');
            $(res).each(function(i){
                $("#subcategory_id").append("<li data-sub_category_id='"+res[i]['sub_category_id']+"' data-sub_category_name='"+res[i]['sub_category_name']+"' class='subcategory list-group-item'>"+res[i]['sub_category_name']+"</li>");
            })
         
        },
        complete:function(){
            $("#loader").css("display","none");
        }
    });
});

$("body").on("click", ".subcategory", function(e) {  
    // _this = $(this);
    // var hidden_model = $('#hidden_model').val();
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
    //     url:base_url+"/get-oem-no",
    //     data: {id:id},
    //     type: "POST",
    //     dataType: "json",
    //     beforeSend:function(){  
    //         $("#loader").css("display","block");
    //     },  
    //     success:function(res){
    //         $('.oem-card').css('display','block');
    //         $(res).each(function(i){
    //             $("#oem_id").append("<li data-oem-no-id='"+res[i]['oem_id']+"' class='oem list-group-item'>"+res[i]['oem_no']+"</li>");
    //         })
         
    //     },
    //     complete:function(){
    //         $("#loader").css("display","none");
    //     }
    // });
});
$("body").on("click", ".oem", function(e) {  
    $(".item-search").css('display','none'); 
    $('.all-item').css('display','block');
    var page = $('#page_count').val();
    var obj = $(this);
    var id = "";
    var hidden_part_no = $('#hidden_part_no').val();
    if(hidden_part_no != "") {
        var id = hidden_part_no;
    }else {
        id = obj.data("oem-no-id");
    }
    get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, sub_category_id, page);
    // $.ajax({
    //     headers: {
    //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     url:base_url+"/get-items",
    //     data: {id:id, page:page},
    //     type: "POST",
    //     dataType: "json",
    //     beforeSend:function(){  
    //         $("#loader").css("display","block");
    //     },  
    //     success:function(res){
    //         if(res["status"]) {
    //             $('#page_count').val(parseInt(page)+1);
    //             $('#hidden_part_no').val(id);
    //             $('.all-item').css('display','block');
    //             if(page == "1") {
    //                 $('#listSearchItems').html('');
    //             }
    //             $('#listSearchItems').append(res["message"]);
    //             $("#loader").css("display","none");
    //             if(res["total_row"] > 50 && res["select_row"] > 0) {
    //                 $('.load-more').css('display','block');
    //             }else {
    //                 $('.load-more').css('display','none');
    //             }
    //         }else {
    //             $('#listSearchItems').html('');
    //             $("#loader").css("display","none");
    //         }
    //         //$('.all-item').css('display','block');
    //         // $(res).each(function(i){
    //         //     $("#oem_id").append("<li data-oem-no-id='"+res[i]['oem_id']+"' class='oem list-group-item'>"+res[i]['oem_no']+"</li>");
    //         // })
         
    //     },
    //     complete:function(){
    //         $("#loader").css("display","none");
    //     }
    // });
});
$(".back").on('click',function(){
    $('#page_count').val(parseInt(1));
    $('#hidden_sct').val('');
    $(".item-search").css('display','flex'); 
    $('.all-item').css('display','none');
});
$('body').on('keyup, input', '.filter-item-search', function() {
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
                $("#loader").css("display","block");
            },  
            success:function(res){
                if(res["status"]) {
                    $('#listSearchItems').html('');
                    $('#listSearchItems').append(res["message"]);
                    $("#loader").css("display","none");
                    $('.load-more').css('display','none');
                }
            },
            complete:function(){
                $("#loader").css("display","none");
            }
        });
    }else {
        $('#page_count').val(parseInt(1));
        get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, hidden_sct, '1');
    }
});
function get_seach_item(hidden_car_manufacture, hidden_model, hidden_from_year, hidden_from_month, hidden_to_year, hidden_to_month, hidden_ct, sub_category_id, page) {
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-items",
        data: {hidden_car_manufacture:hidden_car_manufacture, hidden_model:hidden_model, hidden_from_year:hidden_from_year, hidden_from_month:hidden_from_month, hidden_to_year:hidden_to_year, hidden_to_month:hidden_to_month, hidden_ct:hidden_ct, sub_category_id:sub_category_id, page:page},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            $("#loader").css("display","block");
        },  
        success:function(res){
            if(res["status"]) {
                console.log(page);
                console.log(res["message"]);
                $("#loader").css("display","none");
                $('#page_count').val(parseInt(page)+1);
                //$('#hidden_sct').val(id);
                $('.all-item').css('display','block');
                if(page == "1") {
                    $('#listSearchItems').html('');
                }
                $('#listSearchItems').append(res["message"]);
                if(res["total_row"] > 50 && res["select_row"] > 0) {
                    $('.load-more').css('display','block');
                }else {
                    $('.load-more').css('display','none');
                }
            }else {
                $('#listSearchItems').html('');
                $("#loader").css("display","none");
            }
        },
        complete:function(){
            $("#loader").css("display","none");
        }
    });
}
$("body").on("click","a.add_to_cart",function(){
    var product_id=$(this).data('product-id');
    var qty = $(this).parents('.col-md-12').find('.qty').val();
    var ava_qty = $(this).data('qty');
    if(qty > 0) {
        if(ava_qty >= qty) {
            $.ajax({
                url : base_url+"/add-to-cart", 
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {product_id:product_id,qty:qty},
                dataType:'json',
                beforeSend:function(){  
                    $("#loader").css("display","block");
                },
                success: function(data){
                    if(data['status']) {
                        swal("Success", "Product is successfully added in your cart", "success");
                    }
                    if(!data['status']) {
                        swal("Opps!", data['msg'], "error");
                    }
                },
                complete:function(){
                    $("#loader").css("display","none");
                }
            });
        }else {
            swal("Opps!", "This quantity is not available", "error");
        }
    }else {
        swal("Opps!", "This quantity is not available", "error");
    }
});
function choose_items() {
    var html = ""
    $('#choose_items').html("");
    $(".item-search").find('li.active').each(function(i){
        html +='<span class="btn-shadow btn btn-info">'+$(this).text().trim()+'</span> / ';
    });
    $('#choose_items').html(html.slice(0,-2));
    // var hidden_model_name = $('#hidden_model_name').val();
    // if(hidden_model_name != '') {
    //     $('#choose_items').html('<span class="btn-shadow btn btn-info">'+hidden_model_name+'</span>');
    // }
    // var hidden_category_name = $('#hidden_category_name').val();
    // if(hidden_category_name != '' ) {
    //     $('#choose_items').html('<span class="btn-shadow btn btn-info">'+hidden_category_name+'</span>');
    // }
    // if(hidden_model_name != '' && hidden_category_name != '') {
    //     $('#choose_items').html('<span class="btn-shadow btn btn-info">'+hidden_model_name+'</span> / <span class="btn-shadow btn btn-info">'+hidden_category_name+'</span>');
    // }
    // var hidden_subcategory_name = $('#hidden_subcategory_name').val();
    // if(hidden_subcategory_name != '') {
    //     $('#choose_items').html('<span class="btn-shadow btn btn-info">'+hidden_model_name+'</span> / <span class="btn-shadow btn btn-info">'+hidden_category_name+'</span> / <span class="btn-shadow btn btn-info">'+hidden_subcategory_name+'</span>');
    // }
    // else
    // {
    	
    // }
}