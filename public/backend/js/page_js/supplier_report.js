// dataTable
var SupplierReportList = $('#SupplierReportList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6 text-right'B>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [
            {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o fa-w-20"></i> Export PDF',
                className: 'btn-shadow btn btn-datatable'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-excel-o fa-w-20"></i> Export CSV',
                className: 'btn-shadow btn btn-datatable'
            },
            {
                extend:    'print',
                text:      '<i class="fa fa-print fa-w-20"></i> Export Print',
                className: 'btn-shadow btn btn-datatable'
            },
        ],
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "pageLength" : 10,
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/supplier-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_supplier_code=$("#filter_supplier_code").val();
            data.filter_country=$("#filter_country").val();
            data.filter_state=$("#filter_state").val();
            data.filter_status=$("#filter_status").val();
        },
        
    },
    'columns': [
        {data: 'supplier_id', name: 'supplier_code', orderable: true, searchable: false},
        {data: 'supplier_code', name: 'supplier_code', orderable: false, searchable: false},
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'city_id', name: 'city_id', orderable: false, searchable: false},
        {data: 'state_id', name: 'state_id', orderable: false, searchable: false},
        {data: 'zipcode', name: 'zipcode', orderable: false, searchable: false},
        {data: 'country_id', name: 'country_id', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_supplier_code").on('keyup input',function(){
    SupplierReportList.draw();
});
$('#filter_country').selectpicker().change(function(){
    SupplierReportList.draw();
});
$('#filter_state').selectpicker().change(function(){
    SupplierReportList.draw();
});
$("#filter_status").on('change',function(){
    SupplierReportList.draw();
});
$('body').on('click', '.reset-filter', function() {
    $('#filter_supplier_code').val('');
    $("#filter_country").val('').selectpicker("refresh");
    $("#filter_state").val('').selectpicker("refresh");
    $("#filter_city").val('').selectpicker("refresh");
    $('#filter_status').val('');
    SupplierReportList.draw();
});
function chnageCountry(val) {
    $.ajax({
        url:base_url+"/get-state",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{country_id:val},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            var data = "";
            for(i=0; i<res.length; i++){
                data += '<option value="'+res[i]['state_id']+'">'+res[i]['state_name']+'</option>';
            }
            $("#filter_state").html(data);
            $("#filter_state").selectpicker('refresh');
            hideLoader();
            
        },
        error:function(){
            swal({title: "Sorry!",text: "There is an error",type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    })
}
function chnageState(val) {
    $.ajax({
        url:base_url+"/get-city",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{state_id:val},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            var data = "";
            for(i=0; i<res.length; i++){
                data += '<option value="'+res[i]['city_id']+'">'+res[i]['city_name']+'</option>';
            }
            $("#filter_city").html(data);
            $("#filter_city").selectpicker('refresh');
            hideLoader();
            
        },
        error:function(){
            swal({title: "Sorry!",text: "There is an error",type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    })
}