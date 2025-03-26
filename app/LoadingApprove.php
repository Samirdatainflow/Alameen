<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class LoadingApprove extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loading_approve';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'loading_approve_id', 'delivery_id', 'date_approve', 'warehouse_id', 'product_id', 'qty','qry_appr','agent_id'
    ];
}
