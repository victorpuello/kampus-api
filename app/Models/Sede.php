<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Sede
 *
 * Representa una sede de una institución educativa.
 *
 * @property int $id
 * @property int $institucion_id
 * @property string $nombre
 * @property string $direccion
 * @property string|null $telefono
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 *
 * @method static \Database\Factories\SedeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Sede newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sede newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sede onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sede query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sede withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sede withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Sede extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sedes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'institucion_id',
        'nombre',
        'direccion',
        'telefono',
    ];

    /**
     * Obtiene la institución a la que pertenece esta sede.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los grupos asociados a esta sede.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Obtiene los grupos de esta sede para un año académico específico.
     *
     * @param  int  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gruposPorAnio($anioId)
    {
        return $this->grupos()->where('anio_id', $anioId);
    }

    /**
     * Obtiene los grupos de esta sede para un grado específico.
     *
     * @param  int  $gradoId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gruposPorGrado($gradoId)
    {
        return $this->grupos()->where('grado_id', $gradoId);
    }

    /**
     * Obtiene estadísticas de grupos por nivel educativo para un año específico.
     *
     * @param  int  $anioId
     * @return array
     */
    public function estadisticasGruposPorNivel($anioId)
    {
        $estadisticas = [];
        $niveles = Grado::getNivelesDisponibles();

        foreach ($niveles as $nivel) {
            $count = $this->grupos()
                ->whereHas('grado', function ($q) use ($nivel) {
                    $q->where('nivel', $nivel);
                })
                ->where('anio_id', $anioId)
                ->count();

            $estadisticas[$nivel] = $count;
        }

        return $estadisticas;
    }

    /**
     * Obtiene el total de estudiantes en esta sede para un año específico.
     *
     * @param  int  $anioId
     * @return int
     */
    public function totalEstudiantesPorAnio($anioId)
    {
        return $this->grupos()
            ->where('anio_id', $anioId)
            ->withCount('estudiantes')
            ->get()
            ->sum('estudiantes_count');
    }
}
