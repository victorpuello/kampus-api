<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Acudiente
 *
 * Representa un acudiente (padre/madre/tutor) en el sistema.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $nombre
 * @property string|null $telefono
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Estudiante> $estudiantes
 *
 * @method static \Database\Factories\AcudienteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente query()
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Acudiente withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Acudiente extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'acudientes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nombre',
        'telefono',
        'email',
    ];

    /**
     * Obtiene el usuario asociado al acudiente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los estudiantes asociados a este acudiente.
     */
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_acudiente')
            ->withPivot('parentesco');
    }
}
