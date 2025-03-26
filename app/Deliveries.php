<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Deliveries extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'deliveries';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'delivery_id', 'datetime', 'dateissued', 'client_id', 'client_order_ref', 'user_id','warehouse_id','invoiced','delivered'
    ];
}
