<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsProductRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_product_rate';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
       'default_rate', 'level_1','level_2','level_3','level_4','level_5','warehouse_id','product_id'
    ];
}