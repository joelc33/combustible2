<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_estacion_servicio extends Model
{
    //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'principal';

	//Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_estacion_servicio';

}
