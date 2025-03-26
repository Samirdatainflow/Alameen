<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Row extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'row';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'row_id','zone_id','location_id','row_name','status','created_at','updated_at'
    ];
}