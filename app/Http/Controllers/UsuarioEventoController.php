<?php

namespace App\Http\Controllers;
use App\Models\UsuarioEvento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsuarioEventoController extends Controller
{
    public function __construct() { }

    public function listarPorEvento($evento)
    {   
        $result = UsuarioEvento::with('usuario')->with('usuario.tipo_usuario')->with('evento')->where('evento', $evento)->get();
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

    public function validarUsuario(Request $request)
    {
	$usuario = $request['usuario'];
	$evento = $request['evento'];
        try{
            $result = UsuarioEvento::where('usuario', $usuario)->where('evento', $evento)->first();
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
 public function insertar(Request $request)
    {
        $data = array(
            'usuario' => $request['usuario'],
            'evento'=> $request['evento'],
            'eliminado' => 0
        );
        try {
            $areas = UsuarioEvento::insert($data);
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
            'id' => $request['id'],
            'usuario' => $request['usuario'],
            'evento' => $request['evento']
        );
        try {
            UsuarioEvento::findOrFail($id) -> update($data);
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
            UsuarioEvento::findOrFail($id) -> update($data);
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

    public function listar()
    {   
        $result = UsuarioEvento::with('usuario')->with('evento')->get();
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

    public function consultar($id)
    {
        try{
            $result = UsuarioEvento::with('usuario')->with('evento')->find($id);
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

    public function cargaMasiva(Request $request)
    {
        $cedulas = array();
        $filevar = "archivo";
        $noExistente = array();
        $CedulasRepetidas = array();
        $evento = $request['evento'];
        if ($request->file($filevar)->isValid()) 
        {
            $file = $request -> file($filevar);
            $extension = $file -> getClientOriginalExtension();
            if( $extension == "csv"){
                $handle = fopen($file, "r");
                $csvLine = fgetcsv($handle, 1000, ";");
                $cedulas = $csvLine;
            }else{
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.10.message')),
                    config('constants.messages.10.code')
                ); 
            }
        }

        foreach($cedulas as $cedula)
        {
            $usuario = Usuario::where('documento', $cedula)->first();
            $idUsuario= $usuario['id'];
                $data = array(
                    'usuario' => $usuario['id'],
                    'evento' => $request['evento'],
                    'eliminado' => 0
                );
                if(!empty($usuario)){

                    $validacion = UsuarioEvento::where('usuario', $idUsuario)->where('evento', $evento)->first();
                    if(empty($validacion)){
                    $usuariosEvento = UsuarioEvento::insert($data);
                    }else{
                        array_push($CedulasRepetidas, $validacion);
                        print_r($CedulasRepetidas);
                    }
                }else{
                     array_push($noExistente, $cedula);
                     print_r($noExistente);
                }
            }
            return response()-> json(
                array('data' => $noExistente));
            }            
    }
