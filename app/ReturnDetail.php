<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ReturnDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'return_detail';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'return_detail_id', 'return_id', 'product_id', 'qty_ret', 'ret_reason', 'qty_dmg'
    ];
}
