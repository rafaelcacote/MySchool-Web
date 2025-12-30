<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, HasUuids, Notifiable, SoftDeletes;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shared.usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password_hash',
        'nome_completo',
        'cpf',
        'telefone',
        'avatar_url',
        'ativo',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the full_name attribute (maps to nome_completo).
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->attributes['nome_completo'] ?? '';
    }

    /**
     * Set the full_name attribute (maps to nome_completo).
     *
     * @param  string  $value
     * @return void
     */
    public function setFullNameAttribute(string $value): void
    {
        $this->attributes['nome_completo'] = $value;
    }

    /**
     * Get the name attribute (maps to nome_completo).
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['nome_completo'] ?? '';
    }

    /**
     * Set the name attribute (maps to nome_completo).
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['nome_completo'] = $value;
    }

    /**
     * Get the password attribute (maps to password_hash).
     *
     * @return string
     */
    public function getPasswordAttribute(): string
    {
        return $this->attributes['password_hash'] ?? '';
    }

    /**
     * Set the password attribute (maps to password_hash and hashes it).
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute(string $value): void
    {
        // Only hash if the value is not already hashed (doesn't start with $2y$)
        if (! str_starts_with($value, '$2y$') && ! str_starts_with($value, '$2a$') && ! str_starts_with($value, '$2x$')) {
            $this->attributes['password_hash'] = Hash::make($value);
        } else {
            $this->attributes['password_hash'] = $value;
        }
    }

    /**
     * Get the password for authentication.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->attributes['password_hash'] ?? '';
    }

    /**
     * Get the phone attribute (maps to telefone).
     *
     * @return string|null
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['telefone'] ?? null;
    }

    /**
     * Set the phone attribute (maps to telefone).
     *
     * @param  string|null  $value
     * @return void
     */
    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['telefone'] = $value;
    }

    /**
     * Get the is_active attribute (maps to ativo).
     *
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->attributes['ativo'] ?? false;
    }

    /**
     * Set the is_active attribute (maps to ativo).
     *
     * @param  bool  $value
     * @return void
     */
    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['ativo'] = $value;
    }

    /**
     * Get the tenants that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tenants()
    {
        return $this->belongsToMany(
            Tenant::class,
            'shared.usuario_tenants',
            'usuario_id',
            'tenant_id'
        )->withPivot('created_at');
    }

    /**
     * Get the first tenant (for backward compatibility).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tenant()
    {
        return $this->tenants();
    }
}
