<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefinitivaPeriodo extends Model
{
    use HasFactory;

    protected $table = 'definitivas_periodo';

    protected $fillable = [
        'calificacion',
        'estudiante_id',
        'periodo_id',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
} 