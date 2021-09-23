<?php

namespace App\Models\Combustible;

use Illuminate\Database\Eloquent\Model;

class tab_vehiculo extends Model
{
     //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'combustible.tab_vehiculo';

    public static $validar = array(
                "placa"       => "required",
                "marca"      => "required|min:1|max:100",
                "modelo"     => "required|min:1|max:100",
                "color" => "required|min:1"        
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'de_placa':
              return $query->where('de_placa', '=', "{$q}");
          break;
            default:
              return $query;
          break;
      }
    }
}
