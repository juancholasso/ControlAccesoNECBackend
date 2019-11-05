<?php

namespace App\Http\Controllers;
use App\Models\TipoPermiso;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TipoPermisoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los tipos de permisos de los usuarios
     */


   public function listar()
    {   
        $result = TipoPermiso::where('eliminado',0)
	->get();
                
    if (!empty($result)) {
        return response() -> json(
            array('data' => $result, 'message' => config('constants.messages.3.message')),
            config('constants.messages.3.code')
        );
    }else{
        return response() -> json(
            array('data' => $result, 'message' => config('constants.messages.4.message')),
            config('constants.messages.4.code')
        );
    }
    }

    /**
     *  Consultar la información de un tipo de permiso en la base de datos
     */

    public function consultar($id)
    {   
        $result = TipoPermiso::where('id', $id)->where('eliminado', 0)->first();
        if ($result != null) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
        }else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    /**
     *  Agregar un nuevo tipo de permiso para un usuario
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion' => $request['descripcion'],
            'eliminado' => 0,
        );
        try {
            $permiso = TipoPermiso::insert($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
        } catch (QueryException $e) {
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    /**
     *  Actualizar o renovar tipo de permiso de un usuario
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'descripcion' => $request['descripcion'],
            'eliminado' => 0,
        );
        try {
            TipoPermiso::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.6.message')),
                config('constants.messages.6.code')
            );
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    /**
     *  Remover tipo de permiso de un usuario
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            TipoPermiso::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.7.message')),
                config('constants.messages.7.code')
            );
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

}
