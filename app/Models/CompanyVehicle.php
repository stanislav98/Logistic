<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CompanyVehicle
 * 
 * @property int $id
 * @property int $company_id
 * @property int $vehicles_id
 * @property int $count
 * 
 * @property Firm $firm
 * @property Vehicle $vehicle
 *
 * @package App\Models
 */
class CompanyVehicle extends Model
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
