<?php

namespace App\Http\Controllers;
use App\Models\Subsitio;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CamaraController extends Controller
{
    public function consultar($id){

        $result = Subsitio::select('subsitios.nombre as subsitioNombre','neofaces.descripcion as descripcion','neofaces.ip as ip','neofaces.puerto as puerto',
                                    'neofaces.usuario as usuario','neofaces.clave as clave')
         ->from('subsitios')
         ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
         ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
         ->Where('neofaces.eliminado', '=', '0')
         ->Where('subsitios.id', '=', $id)
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
}
