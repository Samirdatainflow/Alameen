@php
$i=1;
@endphp
@if(sizeof($order_data) > 0)
	@foreach($order_data as $data)
	<tr>
        <td>{{$data['part_brand_name']}}</td>
        <td>{{$data['pmpno']}}</td>
        <td>{{$data['part_name']}}</td>
        <td>{{$data['unit_name']}}</td>
        <td>
            @php
            if(!empty($data['manufacturing_no'])) {
                foreach($data['manufacturing_no'] as $man) {
                @endphp
                <a href="javascript::void(0)" class="badge badge-success remove-manufacturing-no" data-id="15">{{$man['manufacturing_no']}}</a>
                @php
                }
            }
            @endphp
        </td>
		<td>{{$data['qty']}}</td>
	</tr>
    @php
    $i++;
    @endphp
	@endforeach
@endif