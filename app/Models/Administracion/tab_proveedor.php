<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_proveedor extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_proveedor';

    public static $validarCrear = array(
                "tipo_documento" => "required|numeric",
                "codigo" => "required|numeric",
                "descripcion" => "required|min:1|max:600",
                "direccion" => "required|min:1|max:600",
                "email" => "required|email|min:1|max:600",
                "nombre_representante" => "required|min:1|max:600",
                "telefono_representante" => "required|numeric",
                "cedula_representante" => "required|numeric",
                "cuenta_bancaria" => "required|numeric",
	);

	public static $validarEditar = array(
                "tipo_documento" => "required|numeric",
                "codigo" => "required|numeric",
                "descripcion" => "required|min:1|max:600",
                "direccion" => "required|min:1|max:600",
                "email" => "required|email|min:1|max:600",
                "nombre_representante" => "required|min:1|max:600",
                "telefono_representante" => "required|numeric",
                "cedula_representante" => "required|numeric",
                "cuenta_bancaria" => "required|numeric",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_proveedor', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
