<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    //tabla selecionada
    protected $table='transportes';

    //campos de la tabla
    protected $fillable = [
        'id',
        'tipo_transporte',
        'placa',
        'empresa',
        'tipo_documento',
        'documento',
        'conductor',
        'autoriza',
        'centro_costos',
        'fecha_ingreso',
        'fecha_salida',
        'destino',
        'observaciones',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

      public function tipo()
      {
        return $this->belongsTo(TipoTransporte::class, 'tipo_transporte');
      }

      public function autoriza()
      {
        return $this->belongsTo(Usuario::class, 'autoriza');
      }

      public function empresa()
      {
        return $this->belongsTo(Empresa::class, 'empresa');
      }

      public function tipo_documento()
      {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento');
      }
}