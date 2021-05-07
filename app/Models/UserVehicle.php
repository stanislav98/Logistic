<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserVehicle
 * 
 * @property int $id
 * @property int $company_id
 * @property int $vehicles_id
 * 
 * @property Firm $firm
 * @property Vehicle $vehicle
 *
 * @package App\Models
 */
class UserVehicle extends Model
{
	protected $table = 'company_vehicles';
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'vehicles_id' => 'int',
		'count' => 'int'
	];

	protected $fillable = [
		'company_id',
		'vehicles_id',
		'count'
	];

	public function firm()
	{
		return $this->belongsTo(Firm::class, 'company_id');
	}

	public function vehicle()
	{
		return $this->belongsTo(Vehicle::class, 'vehicles_id');
	}
}
