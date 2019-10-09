<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neoface extends Model
{
    //tabla selecionada
    protected $table = 'neofaces';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'ip',
        'puerto',
        'usuario',
        'clave',
        'eliminado'

    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
}