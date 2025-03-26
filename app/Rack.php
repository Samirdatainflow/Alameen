<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Rack extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'rack';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'rack_id','location_id','zone_id','row_id','rack_name','status','created_at','updated_at'
    ];
}