<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

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
 * @property string $password
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
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;

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
        'password',
        'institucion_id',
        'estado',
    ];

    /**
     * Los atributos que deberían ser ocultados para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
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
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
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
     * @param  string  $permission  El nombre del permiso a verificar.
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('nombre', $permission);
        })->exists();
    }

    /**
     * Verifica si el usuario tiene un rol específico (por nombre o id).
     *
     * @param  string|int  $role  Nombre o id del rol
     */
    public function hasRole($role): bool
    {
        if (is_numeric($role)) {
            return $this->roles()->where('id', $role)->exists();
        }

        return $this->roles()->where('nombre', $role)->exists();
    }

    /**
     * Permite verificar permisos usando el método can() de Laravel.
     *
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        // Mapeo de alias de permisos
        $map = [
            'users.create' => 'crear_usuarios',
            'users.view.any' => 'ver_usuarios',
            'users.update.any' => 'editar_usuarios',
            'users.delete.any' => 'eliminar_usuarios',
            // Agrega otros alias si es necesario
        ];
        $permiso = $map[$ability] ?? $ability;

        return $this->hasPermissionTo($permiso);
    }

    /**
     * Devuelve una colección de todos los permisos únicos del usuario a través de sus roles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        return $this->roles
            ->load('permissions')
            ->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->values();
    }

    /**
     * Alias para hasPermissionTo (compatibilidad)
     */
    public function hasPermission($permission)
    {
        return $this->hasPermissionTo($permission);
    }
}
