<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_requisicion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_requisicion';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "concepto" => "required|min:1|max:600",
        "observacion" => "required|min:1|max:1200"
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "concepto" => "required|min:1|max:600",
        "observacion" => "required|min:1|max:1200"
    );
}
