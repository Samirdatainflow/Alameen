<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class StorageLocationType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'storage_location_type';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'storage_location_id', 'storage_location_name', 'height','width','depth','created_date','modified_date','warehouseid'
    ];
}