<?php

namespace App\Models\Proceso;

use Illuminate\Database\Eloquent\Model;

class tab_referir extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'proceso.tab_referir';

    public static $validarEditar = array(
        "especialidad"      => "required|numeric",
        "instituto"         => "required|numeric",
        "id_tipo_solicitud" => "required|numeric",
        "de_observacion"    => "required|min:1|max:1200"
    );
}
