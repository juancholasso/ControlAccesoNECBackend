<?php

namespace App\Http\Controllers;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TurnoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = Turno::where('eliminado', 0)

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

    public function listarporGrupo()
    {   
        $result = Turno::where('eliminado', 0)
        ->groupBy('descripcion')
        ->get();
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
     *  Consultar las areas
     */
    public function consultar($id)
    {
        try{
            $result = Turno::where('eliminado', 0)->where('id', $id)->first();
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
            'hora_inicio' => $request['hora_inicio'],
            'hora_fin' => $request['hora_fin'],
            'dia' => $request['dia'],
        );
        try {
            $areas = Turno::insert($data);
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
            'hora_inicio' => $request['hora_inicio'],
            'hora_fin' => $request['hora_fin'],
            'dia' => $request['dia'],
        );
        try {
            Turno::findOrFail($id) -> update($data);
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
            Turno::findOrFail($id) -> update($data);
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
