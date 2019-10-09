<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    //tabla selecionada
    protected $table='empleados';

    //campos de la tabla
    protected $fillable = [
        'id',
        'contrato',
        'usuario',
        'tipo',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

      public function usuario()
      {
        return $this->belongsTo(Usuario::class, 'usuario');
      }
}