<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class SalesReceipt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sales_receipt';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'client_id','invoice_date','invoice_number','invoice_amount','due_amount','pay_amount','pay_mode','reference_number','payment_date','remarks','created_at','updated_at'
    ];
}