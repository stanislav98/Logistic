<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TovTransType
 * 
 * @property int $id
 * @property string $name
 *
 * @package App\Models
 */
class TovTransType extends Model
{
	protected $table = 'tov_trans_types';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];
}
