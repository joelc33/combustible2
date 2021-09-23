<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_solicitud extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_solicitud';

    public static $validarCrear = array(
        "proceso" => "required|numeric",
        "descripcion" => "required|min:1|max:600",
        "identificador" => "required|min:1|max:6"
	);

	public static $validarEditar = array(
        "proceso" => "required|numeric",
		"descripcion" => "required|min:1|max:600",
        "identificador" => "required|min:1|max:6"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_proceso', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }

    protected function getProceso($codigo){

        $tab_tipo_solicitud = tab_solicitud::select( 'id', 'id_tab_proceso', 'de_solicitud', 'in_ver', 'in_activo', 'created_at', 
        'updated_at', 'nu_identificador')
        ->where('id', '=', $codigo)
        ->first();

        return $tab_tipo_solicitud->id_tab_proceso;
        
    }
}
