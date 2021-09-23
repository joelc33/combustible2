<?php

namespace App\Models\Combustible;

use Illuminate\Database\Eloquent\Model;

class tab_despacho_vehiculo extends Model
{
      //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'combustible.tab_despacho_vehiculo';
}
