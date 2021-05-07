<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Tymon \ JWTAuth \ Contracts \ JWTSubject; 


/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $owner_id
 * @property string $phone_number
 * @property int $company_id
 * @property bool $has_payed
 * 
 * @property Firm $firm
 *
 * @package App\Models
 */
class User extends Model implements
             Authenticatable,CanResetPasswordContract,JWTSubject  
{

     use AuthenticableTrait,CanResetPassword,Notifiable;

	protected $table = 'users';

	protected $casts = [
		'owner_id' => 'int',
		'company_id' => 'int',
		'has_payed' => 'bool'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'password',
		'remember_token',
		'owner_id',
		'phone_number',
		'company_id',
		'has_payed'
	];

	public function firm()
	{
		return $this->belongsTo(Firm::class, 'company_id');
	}
	/**
	 * Override the mail body for reset password notification mail.
	*/
	public function sendPasswordResetNotification($token) {
		
    	$this->notify(new \App\Notifications\MailResetPasswordNotification($token));
	}

	public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function getJWTIdentifier() 
    { 
        return $this->getKey(); 
    } 
    
    public function getJWTCustomClaims() 
    { 
        return []; 
    } 
}
