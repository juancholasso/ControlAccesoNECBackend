<?php

namespace App\Http\Controllers;
use App\Models\Sitio;
use App\Models\Subsitio;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SitioController extends Controller
{

    public function __construct() { }

    public function listar()
    {   
        $result = Sitio::where('eliminado', 0)->with('controles_acceso')
        ->with('subsitios')
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

    public function consultar($id)
    {   
        $result = Sitio::where('eliminado', 0)->with('controles_acceso')->with('subsitios')->with('neoface')->where('id', $id)->first();
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
                    $destinationPath = "./media/images/logos/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar a la base de datos
                    $data = array(
                        'nombre' => $request['nombre'],
                        'ubicacion' => $request['ubicacion'],
                        'neoface' => $request['neoface'],
                        'logo' => $fileName,
                        'eliminado' => 0
                    );
                    try {
                        $sitio = Sitio::insert($data);
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
                'nombre' => $request['nombre'],
                'ubicacion' => $request['ubicacion'],
                'neoface' => $request['neoface'],
                'eliminado' => 0
            );
            try {
                $permiso = Sitio::insert($data);
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
        $id= $request['id'];

            $filevar = "logo";
            if ($request->hasFile($filevar)) 
            {
                if($request->file($filevar)->isValid())
                {
            //foreach($filevar as $file){
                    $file= $request ->file($filevar);

                    $extension = $file -> getClientOriginalExtension();

                    if(

                        $extension == "jpeg" || 
                        $extension == "jpg"  || 
                        $extension == "png" 
                    )
                    {

                        $filename = rand() . date('YmHis') . "." .$extension;

                        $data = array(

                            'nombre'=> $request['nombre'],
                            'ubicacion'=> $request['ubicacion'],
                            'neoface' => $request['neoface'],
                            'logo'=> $filename,
                            'eliminado'=> 0 
                        );

                        $destinationPath = "./media/images/logos/";

                        $request->file($filevar)->move($destinationPath, $filename);


                        $result = Sitio::findOrFail($id) -> update($data);
                        if (count((array)($result)) > 0)
                        {
                            return response() -> json(
                                array('data' => $result, 'message' => config('constants.messages.6.message')),config('constants.messages.6.code')
                            );
                        }
                        else
                        {
                            unlink($destinationPath . $filename) or die("el archivo se elimina porque no se cargo");
                            return response() -> json(
                               array('data' => $result, 'message' => config('constants.messages.2.message')),
                               config('constants.messages.2.code'));
                        }
                    }else{
                        return response() -> json(
                        array('data' => $result, 'message' => config('constants.messages.10.message')),
                        config('constants.messages.10.code')
                    );
                    }  
                }else{
                     return response() -> json(
                    array('data' => $result, 'message' => config('constants.messages.9.message')),
                    config('constants.messages.9.code'));
                }
            }else{

                $data = array(

                    'nombre'=> $request['nombre'],
                    'ubicacion'=> $request['ubicacion'],
                    'eliminado'=> 0 
                );
                $result = Sitio::findOrFail($id) -> update($data);
                if (count((array)($result)) > 0)
                {
                    return response() -> json(
                        array('data' => $result, 'message' => config('constants.messages.6.message')),config('constants.messages.6.code')
                    );
                }
                else
                {
                    unlink($destinationPath . $filename) or die("el archivo se elimina porque no se cargo");
                    return response() -> json(
                       array('data' => $result, 'message' => config('constants.messages.4.message')),
                       config('constants.messages.4.code'));
                }
            }
    }

    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1
        );

        $result = Sitio::findOrFail($id) -> update($data);
        if (count((array)($result)) > 0) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.7.message')),
                config('constants.messages.7.code')
            );
        }
        else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

}
