<?php

namespace App\Models\Teleconsulta;

use Illuminate\Database\Eloquent\Model;

class tab_informe extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'telemedicina.tab_informe';

    //id_persona, medico, id_tipo_informe, de_informe, de_ruta_imagen
    public static $validarCrear = array(
        "id_persona" 	          => "required|numeric",
        "de_informe"              => "required|min:1|max:1200",
        "de_protocolo_tecnico"    => "required|min:1|max:1200",
        "de_conclusion"           => "required|min:1|max:1200"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'id':
                return $query->where('id', '=', "{$q}");
            break;
                default:
                return $query;
            break;
        }
    }
}
