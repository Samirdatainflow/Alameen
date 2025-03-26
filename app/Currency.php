<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Currency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currency';
    protected $dateFormat = 'Y-m-d h:i:s';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'currency_code','currency_description','status'
    ];
}
