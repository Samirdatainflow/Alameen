<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class GateEntry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'gate_entry';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'transaction_type', 'order_number', 'order_date', 'vehicle_no', 'driver_name', 'contact_no', 'vehicle_in_out_date', 'courier_date', 'courier_number', 'no_of_box', 'status', 'created_at', 'updated_at'
    ];
}