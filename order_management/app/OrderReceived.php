<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderReceived extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_received';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_received_id', 'order_id', 'date_reception', 'warehouse_id', 'product_id', 'qty','qry_appr','agent_id'
    ];
}
