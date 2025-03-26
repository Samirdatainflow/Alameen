var ReturnsManagementReportList = $('#ReturnsManagementReportList').DataTable({
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
        "url": base_url+"/report/returns-management-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_order_id=$("#filter_order_id").val();
            data.filter_customer=$("#filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'sale_order_id', name: 'sale_order_id', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'return_type', name: 'return_type', orderable: false, searchable: false},
        {data: 'return_date', name: 'return_date', orderable: true, searchable: false},
        {data: 'return_date', name: 'return_date', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});

$("#filter_customer").on('change',function(){
    ReturnsManagementReportList.draw();
});

$("#filter_order_id").on('keyup input',function(){
    ReturnsManagementReportList.draw();
});

$('body').on('click', 'a.reset-filter', function() {
    $("#filter_order_id").val('');
    $("#filter_customer").val('');
    //$("#filter_customer").selectpicker('refresh');
    ReturnsManagementReportList.draw();
})
