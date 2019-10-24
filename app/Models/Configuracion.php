<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    //tabla selecionada
    protected $table = 'configuraciones';

    //campos de la tabla
    protected $fillable = [
        'id',
        'logo',
        'ipws',
        'eliminado',
        'identificacion',
        'nombre',
        'telefono',
        'correo' 
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
}