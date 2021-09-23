<?php

namespace App\Models\Autenticar;

use Illuminate\Database\Eloquent\Model;

class tab_privilegio_menu extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_privilegio_menu';
}
