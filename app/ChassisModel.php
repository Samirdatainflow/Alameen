<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ChassisModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'chassis_model';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'chassis_model_id', 'chassis_model', 'product_id','created_at','updated_at','status'
    ];
}