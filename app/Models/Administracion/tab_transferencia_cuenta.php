<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_transferencia_cuenta extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_transferencia_cuenta';

    public static $validarCrear = array(
                "banco_debito" => "required|numeric|min:1",
                "cuenta_bancaria_debito" => "required|numeric|min:1", 
                "banco_credito" => "required|numeric|min:1",
                "cuenta_bancaria_credito" => "required|numeric|min:1",       
                "monto_transferencia" => "required|numeric|min:1",
                "fecha_transferencia" => "required",
                "tx_observacion" => "required|min:1|max:600",        
	);

	public static $validarEditar = array(         
                "banco_debito" => "required|numeric|min:1",
                "cuenta_bancaria_debito" => "required|numeric|min:1", 
                "banco_credito" => "required|numeric|min:1",
                "cuenta_bancaria_credito" => "required|numeric|min:1",         
                "monto_transferencia" => "required|numeric|min:1",
                "fecha_transferencia" => "required",
                "tx_observacion" => "required|min:1|max:600",
    );

}
