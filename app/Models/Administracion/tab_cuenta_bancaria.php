<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_cuenta_bancaria extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_cuenta_bancaria';

    public static $validarCrear = array(
                "banco" => "required|numeric|min:1",
                "tipo_cuenta" => "required|numeric|min:1",
                "descripcion" => "required|min:1|max:600",
                "numero_cuenta_bancaria" => "required|numeric",
                "numero_contrato" => "numeric"
	);

	public static $validarEditar = array(
                "banco" => "required|numeric|min:1",
                "tipo_cuenta" => "required|numeric|min:1",
                "descripcion" => "required|min:1|max:600",
                "numero_cuenta_bancaria" => "required|numeric"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_cuenta_bancaria', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
