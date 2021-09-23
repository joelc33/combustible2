<?php

namespace gobela\Models\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_cuenta_contable_documento extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_cuenta_contable_documento';

    public static $validarCrear = array(
        "descripcion" => "required|min:1|max:600",
        "cuenta_gasto" => "required",
        "cuenta_orden_pago" => "required",
        "ruta" => "required",
        "solicitud" => "required",
        "siglas" => "required"
    );

    public static $validarEditar = array(
        "descripcion" => "required|min:1|max:600",
        "cuenta_gasto" => "required",
        "cuenta_orden_pago" => "required",
        "ruta" => "required",
        "solicitud" => "required",
        "siglas" => "required"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'id':
                return $query->where('administracion.tab_cuenta_contable_documento.de_cc_documento', 'ILIKE', "%{$q}%");
            break;
            default:
                return $query;
            break;
        }
    }

    protected function getCuentaDocumento( $ruta){

        $ubicacion = tab_ruta::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'id_tab_proceso', 'in_actual', 
        'in_activo', 'created_at', 'updated_at', 'in_reporte', 'in_datos', 'in_definitivo')
        ->where('id', '=', $ruta)
        ->first();

        $data = tab_cuenta_contable_documento::select( 'id', 'de_cc_documento', 'id_cc_gasto_pago', 'id_cc_odp', 'id_tab_proceso', 
        'id_tab_solicitud', 'de_sigla', 'nu_cuenta_gasto', 'nu_cuenta_odp', 'in_activo', 
        'created_at', 'updated_at')
        ->where('id_tab_solicitud', '=', $ubicacion->id_tab_tipo_solicitud)
        ->where('id_tab_proceso', '=', $ubicacion->id_tab_proceso)
        ->first();
        
        return $data;

	}
}
