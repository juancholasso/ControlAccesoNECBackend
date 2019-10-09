<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    //tabla selecionada
    protected $table='empresas';

    //campos de la tabla
    protected $fillable = [
        'id',
        'NIT',
        'nombre',
        'eliminado',
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
}