<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * 
 * @property int $id
 * @property string $reason
 * @property string $body
 * @property bool $active
 * @property string|null $name
 * @property string|null $path
 * @property int $user_id
 * @property int $firm_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Firm $firm
 * @property User $user
 *
 * @package App\Models
 */
class Report extends Model
{
	protected $table = 'reports';

	protected $casts = [
		'active' => 'bool',
		'user_id' => 'int',
		'firm_id' => 'int'
	];

	protected $fillable = [
		'reason',
		'body',
		'active',
		'name',
		'path',
		'user_id',
		'firm_id'
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
