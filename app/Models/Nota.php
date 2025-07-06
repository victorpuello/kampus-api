<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nota extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notas';

    protected $fillable = [
        'estudiante_id',
        'asignacion_id',
        'competencia_id',
        'periodo_id',
        'valor',
        'observacion',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function competencia()
    {
        return $this->belongsTo(Competencia::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}
