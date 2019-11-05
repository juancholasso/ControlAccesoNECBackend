<?php

namespace App\Http\Controllers;
use App\Models\DocumentoContratista;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DocumentoContratistaController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los documentos del contratista
     */
    public function listar()
    {   
        $result = DocumentoContratista::where('eliminado', 0)->get();
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
     *  Listar por contratista
     */

    public function listarPorContratista($id)
    {   
        $result = DocumentoContratista::with('tipo')->where('eliminado', 0)->where('contratista', $id)->get();
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
     *  Consultar documento de un contratista
     */
    public function consultar($id)
    {
        try{
            $result = DocumentoContratista::where('eliminado', 0)->where('id', $id)->first();
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
     *  Agregar una nuevo documento
     */
    public function insertar(Request $request)
    {
        $filevar = "archivo";
        if($request->hasFile($filevar))
        {
        //Si el archivo se ha cargado
            if ($request->file($filevar)->isValid()) 
            {
                $file = $request -> file($filevar);
                //Si el archivo tiene una extension permitida
                $extension = $file -> getClientOriginalExtension();
                if( $extension == "pdf" || $extension == "doc"  || $extension == "docx" ){

                    //Componer archivo
                    $destinationPath = "./uploads/documents/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar a la base de datos
                    $data = array(
                        'tipo' => $request['tipo'],
                        'contratista' => $request['contratista'],
                        'documento' => $fileName,
                        'eliminado' => 0
                    );
                    try {
                        $doc = DocumentoContratista::insert($data);
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
                'tipo' => $request['tipo'],
                'contratista' => $request['contratista'],
                'documento' => '',
                'eliminado' => 0
            );

            try {
                $doc = DocumentoContratista::insert($data);
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
        $id= $request['id'];

        $filevar = "archivo";
        if($request->hasFile($filevar))
        {
        //Si el archivo se ha cargado
            if ($request->file($filevar)->isValid()) 
            {
                $file = $request -> file($filevar);
                //Si el archivo tiene una extension permitida
                $extension = $file -> getClientOriginalExtension();
                if( $extension == "pdf" || $extension == "doc"  || $extension == "docx" ){

                    //Componer archivo
                    $destinationPath = "./uploads/documents/";
                    $fileName = rand() . date('YmHis') . "." .$extension;
                    $request->file($filevar)->move($destinationPath, $fileName);
                    
                    //Agregar a la base de datos
                    $data = array(
                        'tipo' => $request['tipo'],
                        'contratista' => $request['contratista'],
                        'documento' => $fileName,
                        'eliminado' => 0
                    );
                    $result = DocumentoContratista::findOrFail($id) -> update($data);
                        if ($result != null)
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
                'tipo' => $request['tipo'],
                'contratista' => $request['contratista'],
                'documento'=> '',
                'eliminado' => 0
            );

            try {
                $doc = DocumentoContratista::findOrFail($id) -> update($data);
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
     *  Remover las areas
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            DocumentoContratista::findOrFail($id) -> update($data);
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
