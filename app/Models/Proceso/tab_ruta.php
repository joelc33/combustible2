<?php

namespace App\Models\Proceso;

use Illuminate\Database\Eloquent\Model;

class tab_ruta extends Model
{
	//Nombre de la conexion que utitlizara este modelo
	protected $connection= 'principal';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 'proceso.tab_ruta';

  	/**
	 * @return object
	 */
	public function estatus(){
		return $this->belongsTo('App\Models\Configuracion\tab_estatus','id_tab_estatus');
  	}
  
  	/**
	 * @return object
	 */
	public function proceso(){
		return $this->belongsTo('App\Models\Configuracion\tab_proceso','id_tab_proceso');
	}

	/**
	 * @return object
	 */
	public function usuario(){
		return $this->belongsTo('App\Models\Autenticar\tab_usuario','id_tab_usuario');
	}
	
	/**
	 * @return object
	 */
	public function solicitud(){
		return $this->belongsTo('App\Models\Configuracion\tab_solicitud','id_tab_tipo_solicitud');
	}
	
	protected function getValidarCargarDatos( $solicitud){

        $data = tab_ruta::select( 'in_datos')
        ->where('id_tab_solicitud', '=', $solicitud)
        ->where('in_actual', '=', true)
        ->first();
        
        return $data->in_datos;

	}
	
	protected function getRuta( $solicitud){

        $data = tab_ruta::select( 'id')
        ->where('id_tab_solicitud', '=', $solicitud)
        ->where('in_activo', '=', true)
        ->where('in_actual', '=', true)
        ->first();
        
        return $data->id;

    }
}
