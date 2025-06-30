<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Clase User
 *
 * Representa un usuario en el sistema. Puede ser un administrador, docente, estudiante o acudiente.
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property int $institucion_id
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read \App\Models\Docente|null $docente
 * @property-read \App\Models\Estudiante|null $estudiante
 * @property-read \App\Models\Acudiente|null $acudiente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'username',
        'email',
        'password_hash',
        'institucion_id',
        'estado',
    ];

    /**
     * Los atributos que deberían ser ocultados para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Obtiene los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Establece el hash de la contraseña.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordHashAttribute($value)
    {
        if ($value) {
            $this->attributes['password_hash'] = Hash::make($value);
        }
    }

    /**
     * Obtiene la institución a la que pertenece el usuario.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los roles a los que pertenece el usuario.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    /**
     * Obtiene el registro de docente asociado con el usuario.
     */
    public function docente()
    {
        return $this->hasOne(Docente::class);
    }

    /**
     * Obtiene el registro de estudiante asociado con el usuario.
     */
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class);
    }

    /**
     * Obtiene el registro de acudiente asociado con el usuario.
     */
    public function acudiente()
    {
        return $this->hasOne(Acudiente::class);
    }

    /**
     * Verifica si el usuario tiene un permiso específico a través de sus roles.
     *
     * @param string $permission El nombre del permiso a verificar.
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('nombre', $permission);
        })->exists();
    }
}