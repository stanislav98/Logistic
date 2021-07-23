<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * 
 * @property int $id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $sender_id
 * @property int $reciever_id
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Message extends Model
{
	protected $table = 'messages';

	protected $casts = [
		'sender_id' => 'int',
		'reciever_id' => 'int',
		// 'created_at' => 'date:Y-m-d',
	];

	protected $fillable = [
		'message',
		'sender_id',
		'reciever_id',
		'status'
	];
	
	// protected $dateFormat = 'Y-m-d';


	public function user()
	{
		return $this->belongsTo(User::class, 'sender_id');
	}
}
