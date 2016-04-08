<?php
namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    protected $fillable = array('subject','description','status','importance');
    static $allStatus=array(
        'new','under-review', 'assigned', 'question', 'answer', 'resolved', 'cancelled','closed'
    );
    static $allImportances=array(
        'low', 'normal', 'high', 'urgent',
    );
    function user(){
        $this->belongsTo(User::class);
    }
}