<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_orden_pago extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_orden_pago';
    
    public static $validarCrear = array(
                "tx_concepto" => "required|min:1|max:1200",    
                "fecha_pago" => "required",
                "tipo_orden_pago" => "required|numeric",
	);
   

}
