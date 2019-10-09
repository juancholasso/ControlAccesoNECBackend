<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcepcionPermiso extends Model
{
    //tabla selecionada
    protected $table='excepciones_permisos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'fecha_inicial',
        'fecha_final',
        'permiso',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

    // Llaves foraneas
    public function permiso()
    {
        return $this -> belongsTo(Permiso::class, 'permiso');
    }
}