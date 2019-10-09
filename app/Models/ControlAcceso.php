<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlAcceso extends Model
{
    //tabla selecionada
    protected $table='controles_accesos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'ws',
        'ip',
        'puerto',
        'sitio',
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

    //Llaves foraneas
    public function sitio()
    {
        return $this -> belongsTo(Sitio::class, 'sitio');
    }
}