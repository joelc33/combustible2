<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_asignar_partida extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_asignar_partida';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "proveedor" => "required",
        "documento" => "required",
        "razon_social" => "required|min:1|max:1200",
        "direccion" => "required",
        "monto" => "required|numeric",
        "ejecutor" => "required|numeric",
        "fuente_financiamiento" => "required|numeric",
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "proveedor" => "required",
        "documento" => "required",
        "razon_social" => "required|min:1|max:1200",
        "direccion" => "required",
        "monto" => "required|numeric",
        "ejecutor" => "required|numeric",
        "fuente_financiamiento" => "required|numeric",
    );

}
