<?php

namespace App\Http\Controllers;
use App\Models\Cuenta;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpAes\Aes;

class CuentaController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los Cuentas del sistema
     */
    public function listar()
    {   
        $result = Cuenta::where('eliminado', 0)->with('usuario')->with('rol')->get();
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
     * Consultar la información de un Cuenta por ID
     */
    public function consultar($id)
    {   
        $result = Cuenta::where('eliminado', 0)->with('usuario')->with('rol')->where('id', $id)->first();
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

    public function insertar(request $request)
    {
       //$request = $request -> json() -> all();
       $email=$request['email']; 
       $clave = $request['clave'];
        
        $claveEncriptada = ENCRIPTAR($clave);
        
        //Generar Token
        $token = token();
        //Guardar datos en array
        $data = array(
            'email' => $request['email'],
            'clave' => $claveEncriptada,
            'usuario' => $request['usuario'],
            'rol' => $request['rol'],
            'token' => $token,
            'eliminado' => 0
        );
        
        //Agregar Cuenta
        Cuenta::insert($data);
        return response() -> json(
            array('data' => [], 'message' => config('constants.messages.5.message')),
            config('constants.messages.5.code')
        );
     }

    
    public function actualizar(Request $request)
    {
      
        $id = $request['id'];
        $data = array(
            'email' => $request['email'],
            'usuario' =>$request['usuario'],
            'rol' => $request['rol'],
        );
        try {

            //Actualizar Cuenta
            Cuenta::findOrFail($id) -> update($data);
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

    public function actualizarClave(Request $request)
    {
        $id = $request['id'];
        $clave = $request['clave'];
        $claveRepetida = $request['clave_repetida'];
        
        
        if ( $clave == $claveRepetida)
        {
            $claveEncriptada = ENCRIPTAR($clave);
            
            $data = array(
                'clave' => $claveEncriptada
            );
            try {

                //Actualizar Cuenta
                Cuenta::findOrFail($id) -> update($data);
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
        }else{
            return "Las claves no coinciden por favor verifique";
        }
    }

    /**
     *   En caso que el registro no se desee almacenar más, podrá ser eliminado por medio de este método
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Cuenta::findOrFail($id) -> update($data);
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
