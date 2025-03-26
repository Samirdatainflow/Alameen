<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WarehouseAccess extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'warehouse_access';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'access_id', 'user_id', 'warehouse_id', 'clients', 'products', 'reports', 'transfers', 'orders', 'deliveries', 'suppliers', 'stock', 'receptions','returns'
    ];
}
