<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Puerta;
use App\Models\Subsitio;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function integracionKactus(){
        $ingresos = Ingreso::all();
        $ingresosSeparadosEntradaSalida = array();
        foreach($ingresos as $ingreso){
            $puertaEntrada = Puerta::find($ingreso->puerta);
            $puertaSalida = Puerta::find($ingreso->puerta_salida);

            $entrada = [
                'pSmCodEmpr' => '689',
                'pFlCodEmpl' => $ingreso->usuario,
                'pStNroCont' => $ingreso->id,
                'pStFecRelo' => date('d/m/Y',strtotime($ingreso->ingreso)),
                'pStHorRelo' => date('H:i',strtotime($ingreso->ingreso)),
                'pStCodRelo' => '1',
                'pStCodCrel' => null,
                'pStTipMovi' => 'E',
                'pSmCodCcos' => '0',
                'pStCodCetr' => null,
            ];
            $salida = [
                'pSmCodEmpr' => '689',
                'pFlCodEmpl' => $ingreso->usuario,
                'pStNroCont' => $ingreso->id,
                'pStFecRelo' => date('d/m/Y',strtotime($ingreso->salida)),
                'pStHorRelo' => date('H:i',strtotime($ingreso->salida)),
                'pStCodRelo' => '1',
                'pStCodCrel' => null,
                'pStTipMovi' => 'S',
                'pSmCodCcos' => '0',
                'pStCodCetr' => null,
            ];
            array_push($ingresosSeparadosEntradaSalida, $entrada);
            array_push($ingresosSeparadosEntradaSalida, $salida);
        }
        $arraySerializado = serialize($ingresosSeparadosEntradaSalida);
        $hash = md5($arraySerializado);

        return response() -> json(
            array('data' => $ingresosSeparadosEntradaSalida, 'hash' => $hash,'message' => config('constants.messages.3.message')),
            config('constants.messages.3.code')
        );
    }
}
