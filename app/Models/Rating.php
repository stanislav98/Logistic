<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rating
 * 
 * @property int $id
 * @property int $user_id
 * @property int $firm_id
 * @property int $rate
 * 
 * @property Firm $firm
 * @property User $user
 *
 * @package App\Models
 */
class Rating extends Model
{
	protected $table = 'rating';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'firm_id' => 'int',
		'rate' => 'int'
	];

	protected $fillable = [
		'user_id',
		'firm_id',
		'rate'
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
