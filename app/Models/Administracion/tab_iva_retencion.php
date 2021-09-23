<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_iva_retencion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_iva_retencion';

    public static $validarCrear = array(
                "descripcion" => "required|numeric",
	);

	public static $validarEditar = array(
                "descripcion" => "required|numeric",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
		if(trim($q)!=""){
		return $query->where('nu_iva_retencion', '=', $q);
		}              
              
          break;
            default:
              return $query;
          break;
      }
    }
}
