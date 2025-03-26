var item = $('#item').DataTable({
  filter: false,
  "scrollX": true,
  "processing": true,
  "serverSide": true,
  "ordering":true,
  "order": [0, 'desc'],
  "ajax": {
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      "url": base_url+"/get-item-list",
      "type": "POST",
      'data': function(data){
        data.filter_car_manufacture=$("#filter_car_manufacture").val();
        data.filter_car_model=$("#filter_car_model").val();
        data.filter_from_year=$("#filter_from_year").val();
        data.filter_from_month=$("#filter_from_month").val();
        data.filter_to_year=$("#filter_to_year").val();
        data.filter_to_month=$("#filter_to_month").val();
        data.category_id=$("#category_id").val();
        data.sub_category_id=$("#sub_category_id").val();
        data.product_name=$("#product_name").val();
        data.part_no=$("#part_no").val();
        data.filter_part_brand=$("#filter_part_brand").val();
      },
  },
  'columns': [
    {data: 'product_id', name: 'product_id', orderable: true, searchable: false},
    {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
    {data: 'name', name: 'name', orderable: false, searchable: false},
    {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
    {data: 'unit', name: 'unit', orderable: false, searchable: false},
    {data: 'category', name: 'category', orderable: false, searchable: false},
    // {data: 'warehouse', name: 'warehouse', orderable: false, searchable: false},
    // {data: 'inventory', name: 'inventory', orderable: false, searchable: false},
    {data: 'add_to_cart', name: 'add_to_cart', orderable: false, searchable: false},
  ],  
}).on('xhr.dt', function(e, settings, json, xhr) {});
$("#filter_car_manufacture").on('change',function(){
  item.draw();
});
$("#filter_car_model").on('change',function(){
  item.draw();
});
$("#filter_from_year").on('change',function(){
  item.draw();
});
$("#filter_from_month").on('change',function(){
  item.draw();
});
$("#filter_to_year").on('change',function(){
  item.draw();
});
$("#filter_to_month").on('change',function(){
  item.draw();
});
$("#category_id").on('change',function(){
  item.draw();
});
$("#sub_category_id").on('change',function(){
  item.draw();
});
$("#product_name").on('change',function(){
  item.draw();
});
$("#part_no").on('keyup input',function(){
  item.draw();
});
$("#filter_part_brand").on('change',function(){
  item.draw();
});
$('#reset').on('click',function(){
  $("#filter_car_manufacture").val('');
  $("#filter_car_model").val('');
  $("#filter_from_year").val('');
  $("#filter_from_month").val('');
  $("#filter_to_year").val('');
  $("#filter_to_month").val('');
  $('#category_id').val('');
  $('#sub_category_id').find('option:not(:first)').remove();
  $('#product_name').val('');
  $('#part_no').val('');
  $("#filter_part_brand").val('');
  item.draw();
})
// Model Filter //
// $("#model_id").on('change', function(){
//     $("#category_id").html("<option value=''>Select Category </option>");
//     var model_id = $(this).val();
//     $.ajax({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url:base_url+"/get-category",
//         data: {model_id:model_id},
//         type: "POST",
//         dataType: "json",
//         beforeSend:function(){  
//             // console.log("Before");
//         },  
//         success:function(res){

//             $(res).each(function(i){
//             $("#category_id").append("<option value='"+res[i]['category_id']+"'>"+res[i]['category_name']+"</option>");
//         })
//         hideLoader();
//         },
//     });
// });
// Category Filter //
$("#category_id").on('change', function(){
    $("#sub_category_id").html("<option value=''>Select Sub Category </option>");
    var category_id = $(this).val();
    // console.log(category_id);
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-sub-category",
        data: {category_id:category_id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
          // console.log(res);
            $(res).each(function(i){
            $("#sub_category_id").append("<option value='"+res[i]['sub_category_id']+"'>"+res[i]['sub_category_name']+"</option>");
        })
        hideLoader();
        },
    });
});
// Sub Category Filter //
$("#sub_category_id").on('change', function(){
    $("#oem_id").html("<option value=''>Select Oem No </option>");
    var sub_category_id = $(this).val();
    // console.log(sub_category_id);
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-oem-no-list",
        data: {sub_category_id:sub_category_id},
        type: "POST",
        dataType: "json",
        beforeSend:function(){  
            // console.log("Before");
        },  
        success:function(res){
          
            $(res).each(function(i){
            $("#oem_id").append("<option value='"+res[i]['oem_id']+"'>"+res[i]['oem_no']+"</option>");
        })
        hideLoader();
        },
    });
});
$('#AddNewProduct').on('click',function(){
  // console.log('hi');
    $.ajax({
        // url:base_url+"product-list",
        url: base_url+"/add-product-form",
        dataType: 'html',
        type: "get",
        beforeSend:function(){  
            //$('#pageOverlay').css('display', 'block');
        },  
        success:function(res){
          $('.modal-title').text('').text("Add Product");
          $('#productAddModal').modal('show');
          $('#productAddModal').find('.modal-body').html(res);
          // issue_cylinder_form();
           
        },
        error: function(e) {
            //$('#pageOverlay').css('display', 'none');
            swal("Opps!", "There is an error", "error");
        },
        complete: function(c) {
            //$('#pageOverlay').css('display', 'none');
        }
    });
});
$('#product_list_download').on('click',function(){
  var product_name=$("#product_name").val();
  var category=$("#category").val();
  $.ajax({
    url : base_url+"/export-item-csv", 
    type: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { category:category,product_name:product_name },
    beforeSend:function(){  
      $("#loader").css("display","block");
    },
    success: function(data){

      var downloadLink = document.createElement("a");
      var fileData = ['\ufeff'+data];

      var blobObject = new Blob(fileData,{
         type: "text/csv;charset=utf-8;"
       });

      var url = URL.createObjectURL(blobObject);
      downloadLink.href = url;
      downloadLink.download = "items.csv";

      /*
       * Actually download CSV
       */
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);

    },
    complete:function(){
      $("#loader").css("display","none");
    }
});
});
$("#item").on("click",".add_to_cart",function(){
  var product_id=$(this).data('product-id');
  var qty = $(this).parents('tr').find('.qty').val();
  var ava_qty = $(this).data('qty');
  if(qty > 0)
  {
    //if(ava_qty >= qty) {
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
            if(data['status'])
            {
              swal("Success", "Product is successfully added in your cart", "success");
            }
            if(!data['status'])
            {
              swal("Opps!", data['msg'], "error");
            }
          },
          complete:function(){
            $("#loader").css("display","none");
          }
      });
    //}
    // else
    // {
    //   swal("Opps!", "This quantity is not available", "error");
    // }
  }
  
  
})