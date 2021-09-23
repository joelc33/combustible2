<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_empresa extends Model
{
	//Nombre de la conexion que utitlizara este modelo
	protected $connection= 'principal';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 'configuracion.tab_empresa';

	/**
	 * @return object
	 */
	public function documento(){
		return $this->belongsTo('bpm\Models\Configuracion\tab_documento','id_tab_documento');
	}

	//override the toArray function (called by toJson)
	public function toArray(){
		$data = parent::toArray();
		if($this->documento){
			$data['de_inicial'] = $this->documento->de_inicial;
		}else{
			$data['de_inicial'] = null;
		}
		return $data;
	}

	public static $validarCrear = array(
		"tipo" => "required|numeric",
		"documento" => "required",
		"nombre" => "required",
		"siglas" => "required",
		/*"direccion"    => "required",
		"telefono"    => "required",
		"correo"    => "email",
		"url"    => "url"*/
		"superior_izquierda" => "image|max:1024|mimes:jpeg,png",
		"superior_derecha"       => "image|max:1024|mimes:jpeg,png",
		"superior_centro"       => "image|max:1024|mimes:jpeg,png",
		"inferior_izquierda"       => "image|max:1024|mimes:jpeg,png",
		"inferior_derecha"       => "image|max:1024|mimes:jpeg,png",
		"inferior_centro"       => "image|max:1024|mimes:jpeg,png"
	);

	public static $validarEditar = array(
		"tipo" => "required|numeric",
		"documento" => "required",
		"nombre" => "required",
		"siglas" => "required",
		/*"direccion"    => "required",
		"telefono"    => "required",
		"correo"    => "email",
		"url"    => "url"*/
		"superior_izquierda" => "image|max:1024|mimes:jpeg,png",
		"superior_derecha"       => "image|max:1024|mimes:jpeg,png",
		"superior_centro"       => "image|max:1024|mimes:jpeg,png",
		"inferior_izquierda"       => "image|max:1024|mimes:jpeg,png",
		"inferior_derecha"       => "image|max:1024|mimes:jpeg,png",
		"inferior_centro"       => "image|max:1024|mimes:jpeg,png"
	);
}
