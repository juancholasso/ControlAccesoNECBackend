<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\NeoFaceController;


class UsuarioController extends Controller
{

    public function __construct() { }

    /**
    * M�todo para listar los usuario
    **/
    public function listar()
    {   
        $result = Usuario::
                  with('tipo_usuario')
                ->with('tipo_documento')
                ->with('grupo')
                ->with('area')
                ->where('eliminado', 0)
                ->orderBy('id','desc')
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

    public function listarTipoUsuario($id)
    {   
        $result = Usuario::with('tipo_usuario')
	    ->with('grupo')
            ->where('tipo_usuario', $id)
            ->get();

        if (count((array)($result)) > 0) {
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
    * M�todo para Consultar usuarios por id
    **/
    public function consultar($id)
    {   
        $result = Usuario::
                  with('tipo_usuario')
                ->with('tipo_documento')
                ->with('grupo')
                ->with('area')
                ->where('id', $id)
                ->where('eliminado', 0)
                ->first();
        if (count((array)($result)) > 0) {
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
    * M�todo para Insertar usuarios
    **/
    public function insertar(Request $request)
    {
        $data = array(
            'guid'=> GUID(),
            'documento'=> $request['documento'],
            'tipo_documento'=> $request['tipo_documento'],
            'nombre'=> $request['nombre'],
            'apellido'=> $request['apellido'],
            'telefono'=> $request['telefono'],
            'tipo_usuario'=> $request['tipo_usuario'],
            'grupo' => $request['grupo'],
            'area' => $request['area'],
            'fecha_enrolamiento' => date("Y-m-d\Th:i:s"),
            'observaciones'=> $request['observaciones'],
            'eliminado'=> 0 
        );
        try {
            $validacion = Usuario::where('documento','=',$data['documento'])->get();
            if(count($validacion) > 0 ){
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.2.message')),
                    config('constants.messages.15.code')
                );
            }
            $response = Usuario::insertGetId($data);
            return response() -> json(
                array('data' => $response, 'message' => config('constants.messages.5.message')),
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
     * M�todo para Actualizar usuarios
    **/
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'documento'=> $request['documento'],
            'tipo_documento'=> $request['tipo_documento'],
            'nombre'=> $request['nombre'],
            'apellido'=> $request['apellido'],
            'telefono'=> $request['telefono'],
            'tipo_usuario'=> $request['tipo_usuario'],
            'grupo' => $request['grupo'],
            'area' => $request['area'],
            'observaciones'=> $request['observaciones'],
            'eliminado'=> 0 
        );
        try {
            $usuario = Usuario::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => $usuario, 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
        } catch (QueryException $e) {
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    public function foto(Request $request)
    {
        $id = $request['id'];
        $filevar = "foto";
        //Si el archivo se ha cargado
        if ($request->file($filevar)->isValid()) 
        {
            $file = $request -> file($filevar);
            //Si el archivo tiene una extension permitida
            $extension = $file -> getClientOriginalExtension();
            if( $extension == "jpeg" || $extension == "jpg"  || $extension == "png" ){

                //Componer archivo
                $destinationPath = "./uploads/profile/";
                $fileName = rand() . date('YmHis') . "." .$extension;
                $request->file($filevar)->move($destinationPath, $fileName);
                
                //Agregar a la base de datos
                $data = array(
                    'foto' => $fileName,
                    'guidfoto' => GUID()
                );
                try {

                    // Obtener foto anterior de usuario
                    
                    $foto = Usuario::select('foto')->where('id', $id)->first();
                    $foto = $foto['foto'];
                    $usuario = Usuario::findOrFail($id) -> update($data);
                    //Eliminar foto anterior
                    if(!empty($usuario))  
                    
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
            }else{
                return response() -> json(
                    array('data' => $e, 'message' => config('constants.messages.10.message')),
                    config('constants.messages.10.code')
                );
            }
        }else{
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.9.message')),
                config('constants.messages.9.code')
            );
        }
    }


    /**
    *  M�todo para Desactivar usuarios
    **/
    public function eliminar($id)
    {    
	$data = array(
            'eliminado' => 1,
        );
	//$neoface = new NeoFaceController;
        //$statusResultado = $neoface->ELIMINAR_USUARIO($usuario ->guid);
        $usuario = Usuario::find($id);
        
        $result = Usuario::findOrFail($id) -> update($data);        
        
        if (count((array)($result)) > 0) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.7.message')), config('constants.messages.7.code')
            );
        }
        else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    public function eliminarNeoface($id)
    {    
	$data = array(
            'neoface' => 0
        );
	//$neoface = new NeoFaceController;
        //$statusResultado = $neoface->ELIMINAR_USUARIO($usuario ->guid);
        $usuario = Usuario::find($id);
        
        $result = Usuario::findOrFail($id) -> update($data);        
        $neoface = new NeoFaceController;
        $desenrolNeoface = $neoface->ELIMINAR_USUARIO($usuario->guid);
        
        if (count((array)($result)) > 0) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.7.message')), config('constants.messages.7.code')
            );
        }
        else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    public function consultarPorDocumento(request $request)
    {   
        //$request = $request -> json() -> all();

         $documento = $request['documento'];
        
         $int= (int)$documento;
        
        $result = Usuario::where('documento', $int)->first();

        
        //var_dump($result['nombre']);
        if ($result != null ) {
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

    public function eliminacionMasiva(Request $request)
    {
        $cedulas = array();
        $filevar = "archivo";
        $noExistente = array();
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
            
            $result = Usuario::where('documento', $cedula)->first();
            $id = $result['id'];
            $data = array( 'eliminado' => 1  );
            
            if(!empty($result)){
                $usuario = Usuario::findOrFail($id) -> update($data);
            }else{
                array_push($noExistente, $cedula);
            }
            
        }
        return response()-> json(array('data' => $noExistente)); 
    }
    
}
