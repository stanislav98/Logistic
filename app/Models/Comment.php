<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 * 
 * @property int $id
 * @property int $user_id
 * @property int $firm_id
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Firm $firm
 * @property User $user
 *
 * @package App\Models
 */
class Comment extends Model
{
	protected $table = 'comments';

	protected $casts = [
		'user_id' => 'int',
		'firm_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'firm_id',
		'body'
	];

	public function firm()
	{
		return $this->belongsTo(Firm::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
