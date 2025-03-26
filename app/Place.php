<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Place extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'place';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'place_id', 'place', 'location_id','zone_id','row_id','rack_id','plate_id','status','created_at','updated_at'
    ];
}