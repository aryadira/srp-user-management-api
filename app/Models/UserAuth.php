<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserAuth extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_auth';

    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'last_login_at',
        'last_login_ip',
        'is_verified',
        'user_verified_at',
        // ga kepake kalo pake package OTP sadiqsalau
        // 'otp',
        // 'otp_expires_at',
    ];

    public function generateOTP(): string
    {
        $otp = rand(100000, 999999);
        $this->otp = (string) $otp;
        $this->otp_expires_at = now()->addMinutes(5);
        $this->save();

        return (string) $otp;
    }

    // public function verifyOTP($inputOTP): bool
    // {
    //     if ($this->otp === $inputOTP && now()->lt($this->otp_expires_at)) {
    //         $this->otp = null;
    //         $this->otp_expires_at = null;
    //         $this->save();
    //         return true;
    //     }

    //     return false;
    // }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
