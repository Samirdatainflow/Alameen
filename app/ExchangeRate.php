<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ExchangeRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'exchange_rate';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'trading_date', 'source_currency','target_currency','closing_rate','average_rate','status'
    ];
}