<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inasistencia extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inasistencias';

    protected $fillable = [
        'estudiante_id',
        'asignacion_id',
        'horario_id',
        'fecha',
        'justificada',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'justificada' => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}
