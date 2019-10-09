<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoEquipo extends Model
{
    //tabla selecionada
    protected $table='ingresos_equipos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'color',
        'cantidad',
        'estado',
        'marca',
        'tipo',
        'permiso',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

    public function estado()
    {
        return $this -> belongsTo(EstadoEquipo::class, 'estado');
    }

    public function marca()
    {
        return $this -> belongsTo(MarcaEquipo::class, 'marca');
    }

    public function tipo()
    {
        return $this -> belongsTo(TipoEquipo::class, 'tipo');
    }


}