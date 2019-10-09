<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    //tabla selecionada
    protected $table='turnos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'hora_inicio',
        'hora_fin',
        'dia',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

}