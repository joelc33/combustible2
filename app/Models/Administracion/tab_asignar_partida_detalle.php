<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_asignar_partida_detalle extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_asignar_partida_detalle';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "ejecutor" => "required",
        "proyecto_ac" => "required",
        "accion_especifica" => "required",
        "partida" => "required|numeric|min:1",
        "monto_disponible" => "required|numeric",
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "ejecutor" => "required",
        "proyecto_ac" => "required",
        "accion_especifica" => "required",
        "partida" => "required|numeric|min:1",
        "monto_disponible" => "required|numeric",
    );
}
