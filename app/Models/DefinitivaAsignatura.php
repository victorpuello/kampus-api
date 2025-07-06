<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefinitivaAsignatura extends Model
{
    use HasFactory;

    protected $table = 'definitivas_asignatura';

    protected $fillable = [
        'calificacion',
        'estudiante_id',
        'asignatura_id',
        'periodo_id',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}
