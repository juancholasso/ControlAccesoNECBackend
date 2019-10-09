<?php

namespace App\Http\Controllers;
use App\Models\Controladora;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ControladoraController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los controles de acceso
     */
    public function listar()
    {   
        $result = Controladora::with('puerta')->where('eliminado', 0)->get();
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
     *  Consultar un control de accesp
     */
    public function consultar($id)
    {
        try{
            $result = Controladora::where('eliminado', 0)->find($id);
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
     *  Consultar un control de acceso con puerta
     */
    public function consultarPuerta($id)
    {
        try{
            $result = Controladora::where('eliminado', 0)
            ->with('puerta')
            ->where('puerta', $id)
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
     *  Agregar una nuevo control de acceso
     */
    public function insertar(Request $request)
    {
        $data = array(
            'ip' => $request['ip'],
            'mac' => $request['mac'],
            'command_code' => $request['command_code'],
            'parameters' => $request['parameters'],
            'puerta' => $request['puerta'],
            'fecha' => date("Y-m-d\Th:i:s"),
            'eliminado' => 0,
        );
        try {
            $areas = Controladora::insert($data);
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
            'ip' => $request['ip'],
            'mac' => $request['mac'],
            'command_code' => $request['command_code'],
            'parameters' => $request['parameters'],
            'puerta' => $request['puerta'],
        );
        try {
            Controladora::findOrFail($id) -> update($data);
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
            Controladora::findOrFail($id) -> update($data);
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
