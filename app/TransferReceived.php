<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class TransferReceived extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'transfer_received';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'transfer_received_id', 'transfer_id', 'date_reception', 'bureau_id', 'product_id', 'qty','qty_appr','agent_id'
    ];
}
