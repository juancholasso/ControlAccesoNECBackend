<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contratista extends Model
{
    //tabla selecionada
    protected $table='contratistas';

    //campos de la tabla
    protected $fillable = [
        'id',
        'cargo',
        'elementos',
        'induccion',
        'seguridad_ocupacional',
        'salud_ocupacional' ,
        'usuario',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

      //Llaves foraneas
    
    public function usuario()
    {
        return $this -> belongsTo(Usuario::class, 'usuario');
    }
}