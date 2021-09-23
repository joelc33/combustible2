<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_ramo extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_ramo';

    public static $validarCrear = array(
                "descripcion" => "required|min:1|max:600",
	);

	public static $validarEditar = array(
                "descripcion" => "required|min:1|max:600",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_ramo', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
