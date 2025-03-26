<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Orders extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'orders';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'order_id', 'order_request_id', 'datetime', 'deliverydate', 'supplier_id', 'warehouse_id', 'agent_id','approved','received'
    ];
}
