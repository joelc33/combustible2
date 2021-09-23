<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_fondo_tercero extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_fondo_tercero';
    
    public static $validarCrear = array(
                "solicitud" => "required|numeric",
                "tipo_documento" => "required|numeric",
                "proveedor" => "required",
                "documento" => "required",
                "razon_social" => "required|min:1|max:1200",
                "direccion" => "required",        
                "fecha_pago" => "required",
	);

	public static $validarEditar = array(
                "solicitud" => "required|numeric",
                "tipo_documento" => "required|numeric",
                "proveedor" => "required",
                "documento" => "required",
                "razon_social" => "required|min:1|max:1200",
                "direccion" => "required",        
                "fecha_pago" => "required",
    );    

}
