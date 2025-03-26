<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsUnit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_units';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        'unit_name', 'unit_type', 'base_factor', 'base_measurement_unit'
    ];
}
