<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class User_Role extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'user_role';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'user_role_id','name','status','created_at','updated_at'
    ];
    
}
