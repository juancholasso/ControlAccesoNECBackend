<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionTurno extends Model
{
    //tabla selecionada
    protected $table='asignaciones_turnos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'turno',
        'permiso',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;
  
    //Elementos ocultos
    protected $hidden = [
    ];

    //Llaves foraneas
    public function turno()
    {
        return $this -> belongsTo(Turno::class, 'turno');
    }

    public function permiso()
    {
        return $this -> belongsTo(Permiso::class, 'permiso');
    }
    
}