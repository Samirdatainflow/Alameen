<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsUnitLoads extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_unit_loads';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
       'unit_load_type', 'location', 'stock_unit', 'status'
    ];
}