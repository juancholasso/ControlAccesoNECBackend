<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puerta extends Model 
{
	//Tabla seleccionada
	protected $table = 'puertas';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'guid',
        'descripcion',
        'urlCamara',
        'subsitio',
        'tipo_puerta',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];

    //Llaves foraneas
    public function tipo_puerta()
    {
        return $this -> belongsTo(TipoPuerta::class, 'tipo_puerta');
    }

    public function subsitio()
    {
        return $this -> belongsTo(Subsitio::class, 'subsitio');
    }


    public function controladoras()
    {
        return $this -> hasMany(Controladora::class, 'puerta', 'id');
    }


}