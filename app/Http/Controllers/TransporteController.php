<?php

namespace App\Http\Controllers;
use App\Models\Transporte;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransporteController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las Transporte
     */
    public function listar()
    {   
        $result = Transporte::
                  with('tipo')
                ->with('autoriza')
                ->with('empresa')
                ->with('tipo_documento')
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
     *  Consultar las Transporte
     */
    public function consultar($id)
    {
        try{
            $result = Transporte::
              with('tipo')
            ->with('autoriza')
            ->with('empresa')
            ->with('tipo_documento')
            ->where('id', $id)
            ->where('eliminado', 0)
            ->first();
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
     *  Agregar una nueva transportaodra
     */
    public function insertar(Request $request)
    {
        $data = array(
            'tipo_transporte' => $request['tipo_transporte'],
            'placa' => $request['placa'],
            'empresa' => $request['empresa'],
            'tipo_documento' => $request['tipo_documento'],
            'documento' => $request['documento'],
            'conductor' => $request['conductor'],
            'autoriza' => $request['autoriza'],
            'centro_costos' => $request['centro_costos'],
            'fecha_ingreso' => $request['fecha_ingreso'],
            'fecha_salida' => $request['fecha_salida'],
            'destino' => $request['destino'],
            'observaciones' => $request['observaciones'],
            'eliminado'=> 0
        );
        try {
            $Transporte = Transporte::insert($data);
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
     *  Actualizar o renovar una Transporte
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'tipo_transporte' => $request['tipo_transporte'],
            'placa' => $request['placa'],
            'empresa' => $request['empresa'],
            'tipo_documento' => $request['tipo_documento'],
            'documento' => $request['documento'],
	        'conductor' => $request['conductor'],
            'autoriza' => $request['autoriza'],
            'centro_costos' => $request['centro_costos'],
            'fecha_ingreso' => $request['fecha_ingreso'],
            'fecha_salida' => $request['fecha_salida'],
            'destino' => $request['destino'],
            'observaciones' => $request['observaciones'],
            'eliminado'=> 0
        );
        try {
            Transporte::findOrFail($id) -> update($data);
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
     *  Remover una Transporte de la base de datos
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Transporte::findOrFail($id) -> update($data);
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