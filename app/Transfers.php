<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Transfers extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'transfers';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'transfer_id', 'datetime', 'warehouse_id', 'destination_id', 'agent_id', 'approved','received'
    ];
}
