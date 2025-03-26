<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Oem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'oem';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'oem_id', 'oem_no', 'oem_details','sub_category_id', 'created_at','updated_at','status'
    ];
}