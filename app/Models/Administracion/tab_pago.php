<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_pago extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_pago';

    public static $validarCrear = array(
                "fe_pago" => "required",
                "forma_pago" => "required|numeric|min:1",
                "banco" => "required|numeric|min:1",
                "cuenta_bancaria" => "required|numeric|min:1",
                "monto" => "required|numeric|min:1",
                "numero_transaccion" => "required|numeric",
//                "monto_pendiente" => "required|numeric|min:1",
//                "monto_pagado" => "required|numeric",
	);


}
