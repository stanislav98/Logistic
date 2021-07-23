<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersForAuthorize
 * 
 * @property int $id
 * @property string $name
 * @property string $path
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UsersForAuthorize extends Model
{
	protected $table = 'users_for_authorize';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'path',
		'user_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
