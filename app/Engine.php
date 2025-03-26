<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Engine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'engine';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'engine_name', 'status', 'created_at','updated_at'
    ];
}