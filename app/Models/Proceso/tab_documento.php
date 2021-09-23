<?php

namespace App\Models\Proceso;

use Illuminate\Database\Eloquent\Model;

class tab_documento extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'proceso.tab_documento';

    public static $validarCrear = array(
      "solicitud" => "required|numeric",
      "ruta" => "required|numeric",
      "descripcion" => "required|min:1|max:1200",
      "archivo" => "required|max:26144|mimes:jpg,gif,csv,png,zip,rar,txt,xls,doc"
    );

    public static $validarEditar = array(
      "solicitud" => "required|numeric",
      "ruta" => "required|numeric",
      "descripcion" => "required|min:1|max:1200",
      "archivo" => "required|max:6144|mimes:jpg,gif,csv,png,zip,rar,txt,xls,doc"
    );
}
