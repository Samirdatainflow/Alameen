var PurchaseReportTable = $('#PurchaseReportTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-purchase-order-purchase-report",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'outstanding_amount', name: 'outstanding_amount', orderable: false, searchable: false},
        {data: 'partial_amount', name: 'partial_amount', orderable: false, searchable: false},
        {data: 'receipt_amount', name: 'receipt_amount', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$("#filter_supplier").on('change',function(){
    PurchaseReportTable.draw();
});

$('body').on('click', 'a.rest-filter', function() {
    $('#filter_supplier').val('');
    PurchaseReportTable.draw();
});