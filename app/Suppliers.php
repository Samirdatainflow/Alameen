<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Suppliers extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'suppliers';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'supplier_id', 'supplier_code', 'full_name', 'business_title', 'mobile', 'phone','address','city','state','zipcode','country','email','status'
    ];
}
