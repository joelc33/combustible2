<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_formular_presupuesto extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_formular_presupuesto';
    
    protected $primaryKey = 'id';
    public $timestamps = false;    

    public static $validarCrear = array(
                "tipo_presupuesto" => "required|numeric",
                "ejecutor" => "required|numeric",
                "sector_presupuesto" => "required|numeric",
                "codigo" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600",
                "monto" => "required|numeric",
	);

	public static $validarEditar = array(
                "tipo_presupuesto" => "required|numeric",
                "ejecutor" => "required|numeric",
                "sector_presupuesto" => "required|numeric",
		"codigo" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600",
                "monto" => "required|numeric",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_presupuesto', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
