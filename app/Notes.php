 <?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Notes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'notes';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'note_id', 'note_date', 'note_title', 'note_detail', 'user_id', 'warehouse_id','readstatus'
    ];
}
