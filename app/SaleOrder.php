<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class SaleOrder extends Model 
{
    use Notifiable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'sale_order';
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
        'sale_order_id', 'client_id', 'sub_total', 'gst', 'grand_total', 'discount', 'review', 'delete_status', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    
}
