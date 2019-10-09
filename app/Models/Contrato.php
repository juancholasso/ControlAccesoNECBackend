<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    //tabla selecionada
    protected $table='contratos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'contrato',
        'supervisor',
        'numero',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'empresa',
        'contratista',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

      public function empresa()
      {
        return $this->belongsTo(Empresa::class, 'empresa');
      }

      public function contratista()
      {
        return $this->belongsTo(Contratista::class, 'contratista');
      }



}