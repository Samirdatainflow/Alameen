<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrdersExpenses extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'orders_expenses';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'expense_description', 'expense_value', 'expense_vat'
    ];
}
