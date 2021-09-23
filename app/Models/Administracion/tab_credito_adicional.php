<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_credito_adicional extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_credito_adicional';

    public static $validarCrear = array(
                "fecha_credito" => "required",
                "tx_descripcion" => "required|min:1|max:600", 
                "tx_justificacion" => "required|min:1|max:600",
                "fuente_financiamiento" => "required|numeric|min:1",        
                "nu_financiamiento" => "required|numeric|min:1", 
                "fecha_oficio" => "required",
                "articulo_ley" => "required|min:1|max:600",
                "tipo_credito" => "required|numeric|min:1"
	);

	public static $validarEditar = array(         
                "fecha_credito" => "required",
                "tx_descripcion" => "required|min:1|max:600", 
                "tx_justificacion" => "required|min:1|max:600",
                "fuente_financiamiento" => "required|numeric|min:1",        
                "nu_financiamiento" => "required|numeric|min:1", 
                "fecha_oficio" => "required",
                "articulo_ley" => "required|min:1|max:600",
                "tipo_credito" => "required|numeric|min:1"
    );


}
