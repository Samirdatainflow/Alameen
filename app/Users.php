<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class Users extends Model 
{
    use Notifiable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = 'tbl_user_master';
    // protected $primaryKey = 'id';
	// protected $dateFormat = 'Y-m-d H:i:s';
	// const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
    //use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'user_id','first_name', 'last_name','gender','date_of_birth','address','mobile','phone','username', 'email', 'password', 'status','activation_key','date_register','user_type','fk_user_role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'remember_token',
    ];

}
