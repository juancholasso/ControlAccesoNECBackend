<?php

namespace App\Http\Controllers;
use App\Models\IngresoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IngresoVehiculoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = IngresoVehiculo::with('tipo')->where('eliminado', 0)->get();
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

    public function listarPorPermiso($id)
    {   
        $result = IngresoVehiculo::with('tipo')->where('permiso', $id)->where('eliminado', 0)->get();
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
            $result = IngresoVehiculo::with('tipo')->where('id', $id)->where('eliminado', 0)->first();
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
            'placa' => $request['placa'],
            'marca' => $request['marca'],
            'tipo' => $request['tipo'],
            'permiso' => $request['permiso'],
            'eliminado'=> 0
        );
        try {
            $areas = IngresoVehiculo::insert($data);
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
            'placa' => $request['placa'],
            'marca' => $request['marca'],
            'tipo' => $request['tipo'],
            'permiso' => $request['permiso'],
            'eliminado'=> 0
        );
        try {
            IngresoVehiculo::findOrFail($id) -> update($data);
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
            IngresoVehiculo::findOrFail($id) -> update($data);
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
