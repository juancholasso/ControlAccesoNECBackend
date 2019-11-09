<?php

namespace App\Http\Controllers;
use App\Models\ControlAcceso;
use App\Models\Neoface;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ControlAccesoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los controles de acceso
     */
    public function listar()
    {   
        $result = Neoface::where('eliminado', 0)->get();
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
     *  Consultar un control de accesp
     */
    public function consultar($id)
    {
        try{
            $result = Neoface::where('eliminado', 0)->find($id);
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
     *  Agregar una nuevo control de acceso
     */
    public function insertar(Request $request)
    {
        $data = array(
            'guid' => $request['guid'],
            'descripcion' => $request['descripcion'],
            'ip' => $request['ip'],
            'puerto' => $request['puerto'],
            'usuario' => $request['usuario'],
            'clave' => $request['clave'],
            'eliminado' => 0,
        );
        try {
            $neoface = Neoface::insert($data);
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
     *  Actualizar o renovar un control de acceso
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'guid' => $request['guid'],
            'descripcion' => $request['descripcion'],
            'ip' => $request['ip'],
            'puerto' => $request['puerto'],
            'usuario' => $request['usuario'],
            'clave' => $request['clave'],
        );
        try {
            Neoface::findOrFail($id) -> update($data);
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
     *  Remover un control de acceso
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Neoface::findOrFail($id) -> update($data);
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
