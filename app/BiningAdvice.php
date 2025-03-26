<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class BiningAdvice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'bining_advice';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'order_approved_id', 'order_id', 'date_approve', 'warehouse_id', 'product_id', 'qty','qty_appr','agent_id'
    ];
}
