<?php

namespace App\Http\Controllers;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContratoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = Contrato::with('empresa')->with('contratista')->where('eliminado', 0)->get();
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

    public function listarPorContratista($id)
    {   
        $result = Contrato::with('empresa')->with('contratista')->where('contratista', $id)->where('eliminado', 0)->get();
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
            $result = Contrato::with('empresa')
            ->where('eliminado', 0)
            ->where('id', $id)
            ->with('contratista')
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
     *  Agregar una nueva areas
     */
    public function insertar(Request $request)
    {
        $data = array(
            'contrato' => $request['contrato'],
            'supervisor' => $request['supervisor'],
            'numero' => $request['numero'],
            'fecha_inicio' => $request['fecha_inicio'],
            'fecha_fin' => $request['fecha_fin'],
            'observaciones' => $request['observaciones'],
            'empresa' => $request['empresa'],
            'contratista' => $request['contratista'],
            'eliminado' => 0
        );
        try {
            $areas = Contrato::insert($data);
            return response() -> json(
                array('data' => $areas, 'message' => config('constants.messages.5.message')),
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
            'contrato' => $request['contrato'],
            'supervisor' => $request['supervisor'],
            'numero' => $request['numero'],
            'fecha_inicio' => $request['fecha_inicio'],
            'fecha_fin' => $request['fecha_fin'],
            'observaciones' => $request['observaciones'],
            'empresa' => $request['empresa'],
            'contratista' => $request['contratista']
        );
        try {
            Contrato::findOrFail($id) -> update($data);
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
            Contrato::findOrFail($id) -> update($data);
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
