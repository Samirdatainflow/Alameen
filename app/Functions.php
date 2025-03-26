<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Functions extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'functions';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'function_id', 'function_code', 'function_name'
    ];
}
