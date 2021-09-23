<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_producto extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_producto';

    public static $validarCrear = array(
        "codigo" => "required|min:1|max:10",
        "descripcion" => "required|min:1|max:600|unique:principal.administracion.tab_producto,de_producto"
    );

    public static $validarEditar = array(
        "codigo" => "required|min:1|max:10",
        "descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'id':
                return $query->where('de_producto', 'ILIKE', "%{$q}%");
            break;
              default:
                return $query;
            break;
        }
    }
}
