<?php

namespace App;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user';
    protected $fillable = [
        'first_name','last_name','email', 'password','phone','country','date_of_birth','city','state',
    ];
    protected $primaryKey = 'id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
     /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function setGoogle2faSecretAttribute($value)
    {
         $this->attributes['google2fa_secret'] = encrypt($value);
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {
        return decrypt($value);
    }
    /**
     * User tokens relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Return the country code and phone number concatenated
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->country_code.$this->phone;
    }
    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction', 'user_id', 'id');
    }
    public function balance()
    {
        return $this->hasMany('App\Balance', 'user_id', 'id');
    }
    public function withdraw()
    {
        return $this->hasMany('App\WithdrawRequest', 'user_id', 'id');
    }
    public function kycDocument()
    {
        return $this->hasMany('App\kycDocument', 'user_id', 'id');
    }
    public static function select_list()
    {
        // get
        $keyed = user::get()->mapWithKeys(function($item) {
            return [$item['id'] => $item['first_name'].' '.$item['last_name']];
        });

        return $keyed;
    }
    /**
     *  Send e-mail verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }
    /**
     *  Send password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
