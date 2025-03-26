<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class PurchaseReceipt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'purchase_receipt';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'supplier_id','invoice_date','invoice_number','invoice_amount','due_amount','pay_amount','pay_mode','reference_number','payment_date','remarks','created_at','updated_at'
    ];
}