<?php

namespace App\Models\Configuracion;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_proceso;
use App\Models\Configuracion\tab_solicitud;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_solicitud_usuario extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_solicitud_usuario';

    public static $validarCrear = array(
            "usuario" => "required|integer"
        );

    protected function getListaTramiteAsignado( $usuario){

        $data = tab_solicitud_usuario::select( 'id_tab_solicitud')
        ->where('id_tab_usuario', '=', $usuario)
        ->where('in_activo', '=', true)
        ->get();

        $registro=array();
        $i=0;
        foreach($data as $proceso){
            $registro[$i] = $proceso->id_tab_solicitud;
            $i++;
        }
        
        return $registro;

    }

    protected function ArmaTramitePrivilegio( $usuario){

        $data = tab_proceso::select( 'id', 'de_proceso')
        ->where('in_activo', '=', true)
        ->orderby('de_proceso','ASC')
        ->get();

        $menu = '';
        foreach($data as $resul){
            $cantidad = self::cantidad_hijosPrivilegio($resul->id);
            if($cantidad > 0)
            {
                $menu.= '<div class="block block-rounded mb-1">
                    <div class="block-header block-header-default" role="tab" id="accordion_h'.$resul->id.'">
                        <a class="font-w600 collapsed" data-toggle="collapse" data-parent="#accordion_q'.$resul->id.'" href="#accordion_q'.$resul->id.'" aria-expanded="false" aria-controls="accordion_q'.$resul->id.'">'.$resul->de_proceso.'</a>
                    </div>
                    '.self::ArmaSubTramitePrivilegio( $resul->id, $usuario).'
                </div>';
                
            }
        }

        return $menu;

    }

    protected function cantidad_hijosPrivilegio($codigo){

        $data = tab_solicitud::where('id_tab_proceso', '=', $codigo)
        ->where('in_activo', '=', true)
        ->count();

        return $data;

    }

    static public function ArmaSubTramitePrivilegio( $codigo, $usuario){

        $data = tab_solicitud::select( 'id', 'de_solicitud', 'nu_identificador')
        ->where('id_tab_proceso', '=', $codigo)
        ->where('in_activo', '=', true)
        ->orderby('nu_identificador','ASC')
        ->get();

        $submenu = '<div id="accordion_q'.$codigo.'" class="collapse show" role="tabpanel" aria-labelledby="accordion_h'.$codigo.'" data-parent="#accordion" style="">
                        <div class="block-content">
                            <div class="form-group">';

        $registro = self::getStatus( $usuario);
        
        foreach($data as $result){

            if(array_key_exists($result->id, $registro)){
                $checked = 'checked';
            }else{
                $checked = '';
            }

            $submenu.='<div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="seleccion['.$result->id.']" name="seleccion['.$result->id.']" '.$checked.'>
                            <label class="custom-control-label" for="seleccion['.$result->id.']">'.$result->nu_identificador.' '.$result->de_solicitud.'</label>
                        </div>';
        }

        $submenu.='</div>
            </div>
        </div>';

        return  $submenu;

    }

    static public function getStatus( $usuario){

        $data = tab_solicitud_usuario::select( 'id_tab_solicitud')
        ->where('id_tab_usuario', '=', $usuario)
        ->where('in_activo', '=', true)
        ->get();

        $registro=array();
        $i=0;
        foreach($data as $proceso){
            $registro[$proceso->id_tab_solicitud]= 'true';
            $i++;
        }
        
        return $registro;

    }
}
