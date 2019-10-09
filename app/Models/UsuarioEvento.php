<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioEvento extends Model
{
    //tabla selecionada
    protected $table='usuarios_eventos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'usuario',
        'evento',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;
  
    //Elementos ocultos
    protected $hidden = [
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento');
    }
}