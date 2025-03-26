<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Returns extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'returns';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'return_id', 'delivery_id', 'transfer_id', 'return_date', 'warehouse_id', 'user_id'
    ];
}
