<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_cuenta_contable extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_cuenta_contable';

    public static $validarCrear = array(
                "nu_nivel" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600",
                "cuenta" => "required|min:1|max:600"
	);

	public static $validarEditar = array(
		"nu_nivel" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600",
                "cuenta" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('administracion.tab_cuenta_contable.nu_cuenta_contable', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
