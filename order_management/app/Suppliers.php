<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class Suppliers extends Model 
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
        'supplier_id', 'supplier_code', 'full_name', 'business_title', 'mobile', 'phone', 'address', 'city', 'state', 'zipcode', 'country', 'email', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    
}
