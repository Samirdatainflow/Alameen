<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsLots extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_lots';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
       'lot_name', 'product_id', 'qty', 'status'
    ];
}