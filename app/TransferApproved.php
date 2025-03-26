<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class TransferApproved extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'transfer_approved';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'transfer_approved_id', 'transfer_id', 'date_approve', 'bureau_id', 'product_id', 'qty','qty_appr','agent_id'
    ];
}
