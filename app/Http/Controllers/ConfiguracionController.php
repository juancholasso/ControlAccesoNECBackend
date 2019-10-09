<?php

namespace App\Http\Controllers;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConfiguracionController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = Configuracion::all();
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
            $result = Configuracion::find($id);
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
        $filevar = "logo";
        //Si el archivo se ha cargado
        if($request->hasFile($filevar))
        {
            if ($request->file($filevar)->isValid()) 
            {
                $file = $request -> file($filevar);
                //Si el archivo tiene una extension permitida
                $extension = $file -> getClientOriginalExtension();
                if(
                    $extension == "jpeg" || 
                    $extension == "jpg"  || 
                    $extension == "png"  
                ){

                    //Componer archivo
                    $destinationPath = "./uploads/configuration/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar a la base de datos
                    $data = array(
                        'logo' => $fileName,
                        'ipws' => $request['ipws'],
                        'identificacion' => $request['identificacion'],
                        'nombre' => $request['nombre'],
                        'telefono' => $request['telefono'],
                        'correo' => $request['correo'],
                        'eliminado' => 0
                    );
                    try {
                        $permiso = Configuracion::insert($data);
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
                        array('data' => [], 'message' => config('constants.messages.10.message')),
                        config('constants.messages.10.code')
                    );
                }
            }else{
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.9.message')),
                    config('constants.messages.9.code')
                );
            }
        }else{
            $data = array(
                'ipws' => $request['ipws'],
                'identificacion' => $request['identificacion'],
                'nombre' => $request['nombre'],
                'telefono' => $request['telefono'],
                'correo' => $request['correo'],
            );
            try {
                $permiso = Configuracion::insert($data);
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

    /**
     *  Actualizar o renovar una area
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        // Cargar información
        $data = array(
            'ipws' => $request['ipws'],
            'identificacion' => $request['identificacion'],
            'nombre' => $request['nombre'],
            'telefono' => $request['telefono'],
            'correo' => $request['correo'],
        );

        // variable del archivo
        $filevar = "logo";
        
        // bandera archivo cargado
        $uploadImg = false;

        // Si existe un archivo subido
        if ($request->hasFile($filevar)) 
        {
            // Informacion de archivo
            $file = $request -> file($filevar);
            $extension = $file -> getClientOriginalExtension();
            $destinationPath = "./uploads/configuration/";
            $fileName = rand() . date('YmHis') . "." .$extension;

            //Si el archivo se ha cargado
            if ($request->file($filevar)->isValid()) 
            {
                // Si el archivo tiene una extension permitida
                if( $extension == "jpeg" ||   $extension == "jpg"  ||  $extension == "png" ){
                    
                    // Subir archivo al servidor
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar nombre del archivo al arreglo
                    $data['logo'] = $fileName;
                    $uploadImg = true;
                }else{
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.10.message')),
                        config('constants.messages.10.code')
                    );
                }
            }else{
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.9.message')),
                    config('constants.messages.9.code')
                );
            }
        }

        try {
            $result = Configuracion::where('id', $id)->update($data);
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.6.message')),config('constants.messages.6.code')
            );
        } catch (QueryException $e) {
            if($uploadImg == true)
                unlink($destinationPath . $filename) or die("el archivo se elimina porque no se cargo");
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.2.message')),
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
            Configuracion::findOrFail($id) -> update($data);
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
