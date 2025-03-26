<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class FunctionalArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'functional_area';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'functional_area_id', 'function_area_name', 'warehouseid', 'created_at', 'updated_at'
    ];
}