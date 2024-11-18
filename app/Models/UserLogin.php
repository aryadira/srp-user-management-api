<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserLogin extends Model
{
    use HasUlids;

    protected $table = 'user_logins';

    protected $fillable = [
        'username',
        'email',
        'password',
        'confirm_password'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user_login_id', 'id');
    }
}
