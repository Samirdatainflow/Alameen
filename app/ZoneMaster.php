<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ZoneMaster extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'zone_master';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
       'zone_master_id', 'zone_name', 'created_date','modified_date','warehouse_id'
    ];
}