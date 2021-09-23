<?php

namespace App\Models\Proceso;

use Illuminate\Database\Eloquent\Model;

class tab_persona extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'telemedicina.tab_persona';

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'cedula':
                return $query->where('cedula', 'like', "%{$q}%");
            break;
                default:
                return $query;
            break;
        }
    }


}
