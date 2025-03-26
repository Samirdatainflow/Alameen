<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class StroageCapacity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'storage_capacity';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'storage_capacity_id', 'storage_location_type', 'unit_load_type','order_index','created_date','modified_date','warehouseid','modified_date','warehouseid'
    ];
}