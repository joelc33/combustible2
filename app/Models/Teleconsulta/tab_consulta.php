<?php

namespace App\Models\Teleconsulta;

use Illuminate\Database\Eloquent\Model;

class tab_consulta extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'telemedicina.tab_consulta';

    //id_persona, medico, id_tipo_informe, de_informe, de_ruta_imagen
    public static $validarCrear = array(
        //"fecha" => "required|date_format:d-m-Y",
        "fecha" => "required|date_format:Y-m-d H:i",
        "informe" => "required",
    );

}
