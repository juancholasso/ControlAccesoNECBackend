<?php

namespace App\Http\Controllers;
use App\Models\Evento;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = Log::with('actividad')
            ->with('componente')
            ->with('usuario')
            ->with('rol')
            ->orderBy('id','desc')
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

    public function not()
    {   
        $result = Log::with('actividad')
            ->with('componente')
            ->with('usuario')
            ->with('rol')
            ->orderBy('id','desc')
            ->skip(0)
            ->take(15)
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
     *  Agregar una nueva areas
     */
    public function insertar(Request $request)
    {
        $data = array(
            'actividad' => $request['actividad'],
            'componente'=> $request['componente'],
            'usuario'=> $request['usuario'],
            'rol'=> $request['rol'],
        );
        try {
            $areas = Log::insert($data);
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

}
