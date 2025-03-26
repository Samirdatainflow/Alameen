<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class PerformaInvoice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'performa_invoice';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'performa_invoice_id', 'order_request_id', 'supplier_id','invoice','status','created_at','updated_at'
    ];
}