var item = $('#item').DataTable({
        filter: false,
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "order": [0, 'desc'],
        "ajax": {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            "url": base_url+"/get-item",
            "type": "POST",
            'data': function(data){
                var product_name = $('#product_name').val();
                var category_id = $('#category').val();
                data.product_name = product_name;
                data.category_id = category_id;
            },
            
        },
        'columns': [
            {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
            {data: 'name', name: 'name', orderable: false, searchable: false},
            {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
            {data: 'unit', name: 'unit', orderable: false, searchable: false},
            {data: 'category', name: 'category', orderable: false, searchable: false},
            {data: 'warehouse', name: 'warehouse', orderable: false, searchable: false}
        ],
       //  'columnDefs': [
       //      {
       //          "targets": [ 0, 1, 2],
       //          "className": "text-left",
       //          "width": "4%"
       //     },
       //     {
       //      "targets": [ 3 ],
       //      "className": "text-right",
       //      "width": "4%"
       // }],
    }).on('xhr.dt', function(e, settings, json, xhr) {
       
  
    });
    $('#product_name').on('keyup input',function(){
        item.draw();
    });
    $('#category').on('change',function(){
        item.draw();
    });
    $('#reset').on('click',function(){
        $('#product_name').val("");
        $('#category').val("");
        item.draw();
    })