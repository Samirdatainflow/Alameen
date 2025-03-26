<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class TransferDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'transfer_detail';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'transfer_detail_id', 'transfer_id', 'bureau_id', 'product_id', 'qty', 'volume','poids'
    ];
}
