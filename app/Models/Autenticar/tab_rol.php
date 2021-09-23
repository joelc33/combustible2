<?php

namespace App\Models\Autenticar;

use Illuminate\Database\Eloquent\Model;

class tab_rol extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_rol';

    public static $validarCrear = array(
        "descripcion" => "required|min:2|max:50|unique:principal.autenticacion.tab_rol,de_rol",
    );

    public static $validarEditar = array(
        "descripcion" => "required|min:2|max:50",
    );
}

