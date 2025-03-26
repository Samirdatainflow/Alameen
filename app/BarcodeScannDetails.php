<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class BarcodeScannDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'barcode_scann_details';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_id', 'part_no', 'part_name', 'supplier_name', 'barcode_number', 'invoice_no','customer','date_of_invoice'
    ];
}