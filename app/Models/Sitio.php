<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sitio extends Model 
{
	//Tabla seleccionada
	protected $table = 'sitios';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'nombre',
        'ubicacion',
        'logo',
        'neoface',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];

    // LLaves foraneas
    public function controles_acceso()
    {
        return $this->hasMany(ControlAcceso::class, 'sitio', 'id');
    }

    // LLaves foraneas subsitio
    public function subsitios()
    {
        return $this->hasMany(Subsitio::class, 'sitio', 'id')
        ->where('eliminado', '=', '0');
	
    }

    public function neoface()
    {
        return $this->belongsTo(Neoface::class, 'neoface', 'id');
    }


}