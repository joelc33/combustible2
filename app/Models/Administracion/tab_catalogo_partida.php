<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_catalogo_partida extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_catalogo_partida';

    public static $validarCrear = array(
                "descripcion" => "required|min:1|max:600",
                "tipo_partida" => "required|numeric",
                "nu_nivel" => "required|numeric",      
                "nu_pa" => "required|min:3|max:3",
                "nu_ge" => "required|min:2|max:2",
                "nu_es" => "required|min:2|max:2",
                "nu_se" => "required|min:2|max:2",
                "nu_sse" => "required|min:3|max:3",
	);

	public static $validarEditar = array(
                "descripcion" => "required|min:1|max:600",
                "tipo_partida" => "required|numeric",
                "nu_nivel" => "required|numeric",            
                "nu_pa" => "required|min:3|max:3",
                "nu_ge" => "required|min:2|max:2",
                "nu_es" => "required|min:2|max:2",
                "nu_se" => "required|min:2|max:2",
                "nu_sse" => "required|min:3|max:3",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('co_partida', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
