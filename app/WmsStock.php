<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsStock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_stock';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
       'stock_units', 'part_no','part_name','lot_name','unit_load','loaction','qty','recived_qty', 'date'
    ];
}