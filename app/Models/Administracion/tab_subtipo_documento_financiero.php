<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_subtipo_documento_financiero extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_subtipo_documento_financiero';

    public static $validarCrear = array(
                "tipo_documento" => "required|numeric|min:1",
                "codigo" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600"
	);

	public static $validarEditar = array(  
                "tipo_documento" => "required|numeric|min:1",
                "codigo" => "required|min:1|max:600",
		"descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_subtipo_documento_financiero', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
