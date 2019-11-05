<?php

namespace App\Http\Controllers;
use App\Models\Subsitio;
use Illuminate\Http\Request;

class SubsitioController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los permisos de los usuarios
     */
    public function listar()
    {   
        $result = Subsitio::with('sitio')
    ->where('eliminado', 0)->get();
    
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
     *  Consultar la información de un permiso en la base de datos
     */
    public function consultar($id)
    {   
        
        $result = Subsitio::with('sitio')->where('eliminado', 0)->where('id', $id)->with('puertas')->first();
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

    public function listarPorSitio($sitio)
    {   
        $result = Subsitio::where('sitio', $sitio)->where('eliminado', 0)->get();
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
     *  Agregar un nuevo permiso para un usuario
     */
    public function insertar(Request $request)
    {
        $data = array(
            'nombre' => $request['nombre'],
            'sitio' => $request['sitio'],
            'eliminado' => 0
        );
        try {
            $Subsitio = Subsitio::insert($data);
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
     *  Actualizar o renovar permiso de un usuario
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'id' =>$request['id'],
            'nombre' => $request['nombre']
        );
        try {
            Subsitio::findOrFail($id) -> update($data);
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
     *  Remover permiso de un usuario
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Subsitio::findOrFail($id) -> update($data);
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