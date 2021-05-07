<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vehicle
 * 
 * @property int $id
 * @property string $name
 * 
 * @property Collection|CompanyVehicle[] $company_vehicles
 *
 * @package App\Models
 */
class Vehicle extends Model
{
	protected $table = 'vehicles';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function company_vehicles()
	{
		return $this->hasMany(CompanyVehicle::class, 'vehicles_id');
	}
}
