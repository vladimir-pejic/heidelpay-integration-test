<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'subscription_id', 'subscription_date', 'domain', 'subdomain'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function subscription() {
        return $this->belongsTo('App\Subscription', 'subscription_id');
    }

    public function invoices() {
        return $this->hasMany('App\Invoice', 'user_id')->orderBy('created_at', 'asc');
    }

    public function route($name, $parameters = []) {
        $host = $this->domain ?? $this->subdomain;
        return 'https://' . $host . app('url')->route($name, $parameters, false);
    }


}
