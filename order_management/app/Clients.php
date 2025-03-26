<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
// use Tymon\JWTAuth\Contracts\JWTSubject;

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
        'client_id', 'customer_id', 'customer_name', 'reg_no', 'sponsor_name', 'sponsor_category', 'sponsor_place_of_work', 'sponsor_id', 'credit_appl_ind', 'other_customer_code', 'authorizer_name', 'customer_email_id', 'customer_wa_no', 'customer_off_msg_no', 'security_cheques_submitted', 'application_required_ind', 'customer_credit_limit', 'customer_credit_period', 'customer_discount_percent', 'customer_payment_method', 'customer_region', 'customer_teritory', 'customer_area', 'customer_stopsale', 'delete_status', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }
    // public function getJWTCustomClaims()
    // {
    //     return [];
    // }
    
}
