<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';
    protected $fillable = [
        'repuesto_id', 'nombre', 'ruta'
    ];

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
}
