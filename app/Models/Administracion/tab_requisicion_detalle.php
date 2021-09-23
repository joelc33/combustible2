<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_requisicion_detalle extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_requisicion_detalle';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "producto" => "required|numeric",
        "cantidad" => "required|numeric",
        "unidad" => "required|numeric",
        "especificacion" => "required|min:1|max:1200"
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "producto" => "required|numeric",
        "cantidad" => "required|numeric",
        "unidad" => "required|numeric",
        "especificacion" => "required|min:1|max:1200"
    );
}
