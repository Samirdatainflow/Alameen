<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class Clients extends Model 
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
    protected $fillable = [
        'client_id', 'customer_id', 'customer_name', 'reg_no', 'sponsor_name', 'sponsor_category', 'customer_address', 'delete_status', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    
}
