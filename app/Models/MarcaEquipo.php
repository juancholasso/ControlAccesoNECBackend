<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcaEquipo extends Model
{
    //tabla selecionada
    protected $table='marcas_equipos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
  
}