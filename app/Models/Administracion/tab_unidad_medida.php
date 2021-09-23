<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_unidad_medida extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_unidad_medida';

    public static $validarCrear = array(
        "descripcion" => "required|min:1|max:600|unique:principal.administracion.tab_unidad_medida,de_unidad_medida"
    );

    public static $validarEditar = array(
        "descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'id':
                return $query->where('de_unidad_medida', 'ILIKE', "%{$q}%");
            break;
              default:
                return $query;
            break;
        }
    }
}
