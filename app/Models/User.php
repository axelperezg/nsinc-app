<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'institution_id',
        'role_id',
        'sector_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtiene la institución a la que pertenece este usuario
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Obtiene el sector al que pertenece este usuario
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Obtiene el rol del usuario
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Verifica si el usuario es super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name === 'super_admin';
    }

    /**
     * Verifica si el usuario es coordinador de sector
     */
    public function isSectorCoordinator(): bool
    {
        return $this->role && $this->role->name === 'sector_coordinator';
    }

    /**
     * Verifica si el usuario es usuario DGNC
     */
    public function isDgncUser(): bool
    {
        return $this->role && $this->role->name === 'dgnc_user';
    }

    /**
     * Verifica si el usuario puede ver todas las instituciones
     */
    public function canViewAllInstitutions(): bool
    {
        return $this->isSuperAdmin() || $this->isDgncUser();
    }

    /**
     * Obtiene el ID de la institución del usuario (null si es super admin o dgnc)
     */
    public function getInstitutionId(): ?int
    {
        return $this->canViewAllInstitutions() ? null : $this->institution_id;
    }

    /**
     * Obtiene el ID del sector del usuario
     */
    public function getSectorId(): ?int
    {
        return $this->sector_id;
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->role && $this->role->hasPermission($permissionName);
    }
}
