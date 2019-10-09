<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model 
{
	//Tabla seleccionada
	protected $table = 'usuarios';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'guid',
        'documento',
        'tipo_documento',
        'nombre',
        'apellido',
        'telefono',
        'foto',
        'guidfoto',
        'tipo_usuario',
        'grupo',
        'area',
        'neoface',
        'fecha_enrolamiento',
        'observaciones',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];


    //Llaves foraneas
    
    public function tipo_documento()
    {
        return $this -> belongsTo(TipoDocumento::class, 'tipo_documento');
    }

    public function tipo_usuario()
    {
        return $this -> belongsTo(TipoUsuario::class, 'tipo_usuario');
    }

    public function grupo()
    {
        return $this -> belongsTo(GrupoUsuario::class, 'grupo');
    }
    
    public function area()
    {
        return $this -> belongsTo(AreaUsuario::class, 'area');
    }

}
