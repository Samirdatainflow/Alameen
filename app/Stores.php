<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Stores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'stores';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'store_id', 'store_manual_id', 'store_name', 'business_type', 'address1', 'address2','city','state','country','zip_code','phone','email','currency','store_logo','description','user_id'
    ];
}
