<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_archivo_pago_nomina extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_archivo_pago_nomina';
    
    public static $validarCrear = array(
        "nu_cedula" => "required|numeric",
        "tx_cedula" => "required|max:9",
        "nu_cuenta_bancaria" => "required|min:10"
    );

}
