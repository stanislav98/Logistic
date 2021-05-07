<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;
/**
 * Class Firm
 * 
 * @property int $id
 * @property string $name
 * @property int $bulstat
 * @property string $manager
 * @property string $address
 * @property Carbon|null $created_at
 * 
 * @property Collection|CompanyVehicle[] $company_vehicles
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Firm extends Model
{
	Use Billable;
	protected $table = 'firms';
	public $timestamps = false;

	protected $casts = [
		'bulstat' => 'int'
	];

	protected $fillable = [
		'name',
		'bulstat',
		'manager',
		'address'
	];

	public function company_vehicles()
	{
		return $this->hasMany(CompanyVehicle::class, 'company_id');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'company_id');
	}
}
