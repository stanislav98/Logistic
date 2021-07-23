<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TovariTransport
 * 
 * @property int $id
 * @property string|null $description
 * @property float $price
 * @property Carbon $from_date
 * @property Carbon $to_date
 * @property bool $fast_payment
 * @property bool $adr
 * @property bool $type
 * @property Carbon $created_at
 * @property int $tov_trans_types
 * @property int $user_id
 * @property int $firm_id
 * @property int $vehicle_type
 * @property float $from_lat
 * @property float $from_lng
 * @property string $from_city
 * @property float $to_lat
 * @property float $to_lng
 * @property string $to_city
 * 
 * @property Firm $firm
 * @property TovTransType $tov_trans_type
 * @property Vehicle $vehicle
 *
 * @package App\Models
 */
class TovariTransport extends Model
{
	protected $table = 'tovari_transport';
	public $timestamps = false;

	protected $casts = [
		'price' => 'float',
		'fast_payment' => 'bool',
		'adr' => 'bool',
		'type' => 'bool',
		'both_directions' => 'bool',
		'tov_trans_types' => 'int',
		'user_id' => 'int',
		'firm_id' => 'int',
		'vehicle_type' => 'int',
		'from_lat' => 'float',
		'from_lng' => 'float',
		'to_lat' => 'float',
		'to_lng' => 'float',
		'description' => 'string',
		'number_of_vehicles' => 'int'
	];

	protected $dates = [
		'from_date',
		'to_date'
	];

	protected $fillable = [
		'description',
		'price',
		'from_date',
		'to_date',
		'fast_payment',
		'adr',
		'type',
		'tov_trans_types',
		'user_id',
		'firm_id',
		'vehicle_type',
		'from_lat',
		'from_lng',
		'from_city',
		'to_lat',
		'to_lng',
		'to_city',
		'both_directions',
		'number_of_vehicles'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function firm()
	{
		return $this->belongsTo(Firm::class);
	}

	// public function tov_trans_type()
	// {
	// 	return $this->belongsTo(TovTransType::class, 'tov_trans_types');
	// }

	public function vehicle()
	{
		return $this->belongsTo(Vehicle::class, 'vehicle_type');
	}
}
