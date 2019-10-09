<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoContratista extends Model
{
    //tabla selecionada
    protected $table='documentos_contratistas';

    //campos de la tabla
    protected $fillable = [
        'id',
        'tipo',
        'documento',
        'contratista',
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
      	return $this -> belongsTo(TipoDocumentoContratista::class, 'tipo');
      }
}