<?php

namespace App\Http\Controllers;
use App\Models\ExcepcionPermiso;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExcepcionPermisoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = ExcepcionPermiso::with('permiso')->where('eliminado', 0)->get();
        if (count($result) > 0) {
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
     *  Listar Excepciones de permisos por usuarios
     */
    public function listarPorUsuario($id)
    {
        $result = ExcepcionPermiso::
                      with('permiso')
                    ->join('permisos', 'permisos.id', '=', 'excepciones_permisos.permiso')
                    ->join('usuarios', 'usuarios.id', '=', 'permisos.usuario')
                    ->where('usuarios.id', $id)
                    ->where('excepciones_permisos.eliminado', 0)
                    ->get();
                    
        if ( !empty($result) ) {
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
    *   Listar Excepciones por usuario
    */
    public function listarPorPermiso($id)
    {   
        $result = ExcepcionPermiso::
                  with('permiso')
                ->where('permiso', $id)
                ->where('eliminado', 0)
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
     *  Consultar las areas
     */
    public function consultar($id)
    {
        try{
            $result = ExcepcionPermiso::with('permiso')->where('eliminado', 0)->find($id);
            return response()-> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code'));
        }catch (Exception $ex){
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    /**
     *  Agregar una nueva areas
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion' => $request['descripcion'],
            'fecha_inicial' => $request['fecha_inicial'],
            'fecha_final' => $request['fecha_final'],
            'permiso' => $request['permiso'],
	        'eliminado'=> 0
        );
        try {
            $areas = ExcepcionPermiso::insert($data);
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
     *  Actualizar o renovar una area
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'descripcion' => $request['descripcion'],
            'fecha_inicial' => $request['fecha_inicial'],
            'fecha_final' => $request['fecha_final'],
            'permiso' => $request['permiso'],
        );
        try {
            ExcepcionPermiso::findOrFail($id) -> update($data);
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
     *  Remover las areas
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            ExcepcionPermiso::findOrFail($id) -> update($data);
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
