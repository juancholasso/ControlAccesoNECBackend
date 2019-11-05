<?php

namespace App\Http\Controllers;
use App\Models\GrupoUsuario;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\HttpFoundation\File\MimeType;
use Illuminate\Http\Request;

class GrupoController extends Controller
{

    public function __construct() { }

    public function listar()
    {   
        $result = GrupoUsuario::all();
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

    public function consultar($id)
    {   
        $result = GrupoUsuario::where('id', $id)->first();
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

    public function insertar(Request $request)
    {
        $filevar = "foto";
         if($request->hasFile($filevar))
        {
            //Si el archivo se ha cargado
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
                    $destinationPath = "./uploads/profile/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar a la base de datos
                    $data = array(
                        'guid' => GUID(),
                        'nombre' => $request['nombre'],
                        'foto' => $fileName,
                        'guidfoto' => GUID(),
                        'eliminado' => 0,
                    );
                    try {
                        $permiso = GrupoUsuario::insert($data);
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
        }else{

             $data = array(
            'guid' => GUID(),
            'nombre' => $request['nombre'],
            'foto' => '',
            'guidfoto' => '',
            'eliminado'=> 0
        );
            try {

                $permiso = GrupoUsuario::insert($data);
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

    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'guid' => GUID(),
            'nombre' => $request['nombre'],
            'guidfoto' => GUID()
        );

        $filevar = "foto";
        //Si el archivo se subio
        if ($request->hasFile($filevar)) 
        {
            //Si el archivo se ha cargado
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
                    $destinationPath = "./uploads/profile/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Actualizar nombre en la base de datos
                    $data['foto'] = $fileName;
                    
                }else{
                    return response() -> json(
                        array('data' => $e, 'message' => config('constants.messages.10.message')),
                        config('constants.messages.10.code')
                    );
                }
            }
        }
        try {
            GrupoUsuario::findOrFail($id) -> update($data);
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

    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            GrupoUsuario::findOrFail($id) -> update($data);
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
