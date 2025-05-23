<?php

namespace Devdojo\Auth\Tests\Models;

use Devdojo\Auth\Models\User as AuthUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $loginBy
 * @property string $type
 * @property string $address
 * @property string $password
 * @property string $otp_code
 * @property string $otp_activated_at
 * @property string $last_login
 * @property string $agent
 * @property string $host
 * @property int $attempts
 * @property bool $login
 * @property bool $activated
 * @property bool $blocked
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Account extends AuthUser implements HasAvatar
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'phone',
        'parent_id',
        'type',
        'name',
        'username',
        'loginBy',
        'address',
        'password',
        'otp_code',
        'otp_activated_at',
        'last_login',
        'agent',
        'host',
        'is_login',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_login' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'otp_activated_at',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'otp_activated_at',
        'host',
        'agent',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        $email = $this->email;
        $default = 'mp';
        $size = 40;
        $grav_url = 'https://www.gravatar.com/avatar/' . hash('sha256', strtolower(trim($email))) . '?d=' . urlencode($default) . '&s=' . $size;

        return $this->getFirstMediaUrl('avatar') ?: $grav_url;
    }

    public function avatar(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
