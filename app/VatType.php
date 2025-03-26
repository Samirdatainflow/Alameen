<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class VatType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'vat_type';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'description', 'percentage'
    ];
}