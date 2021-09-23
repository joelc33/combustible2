<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_pago_nomina extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_pago_nomina';
    
    public static $validarCrear = array(
                "solicitud" => "required|numeric",
                "tx_concepto" => "required|min:1|max:1200",
                "archivo" => "required",      
                "fecha_pago" => "required",
	);

	public static $validarEditar = array(
                "solicitud" => "required|numeric",
                "tx_concepto" => "required|min:1|max:1200",     
                "fecha_pago" => "required",
    );    

}
