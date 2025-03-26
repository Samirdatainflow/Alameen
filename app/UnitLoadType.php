<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class UnitLoadType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'unit_load_type';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'unit_load_id', 'unit_load_type', 'height','width','depth','weight','created_date','modified_date','warehouseid'
    ];
}