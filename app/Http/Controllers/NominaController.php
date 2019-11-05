<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers;
use App\Models\Nomina;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\HttpFoundation\File\MimeType;
class NominaController extends BaseController
{
    public function listar()
    {   
        $result = Nomina::all();
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
    
    public function consultar($fecha_inicial, $fecha_final)
    {
        $fecha_inicial = $fecha_inicial . " 00:00:00";
        $fecha_final = $fecha_final . " 23:59:59";
        $result = Nomina::
                  where('ns3horai', '>=', $fecha_inicial)
                ->where('ns3horai', '<=', $fecha_final)
                ->get();
        return $result;
    }
}