<?php 

namespace App\Helpers;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Configuracion\tab_ruta as tab_configuracion_ruta;
use App\Models\Proceso\tab_documento;
use DB;
use Session;
use Storage;
use setasign\Fpdi\PdfParser\StreamReader;
//*******************************//
use Illuminate\Container\Container;

class Reporte
{

    public static function getAppNamespace()
    {
        return Container::getInstance()->getNamespace()."Http\Controllers\Reporte";
    }

    public static function generarReporte($solicitud){

        $data = tab_ruta::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'id_tab_proceso', 'in_actual', 
        'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_solicitud', '=', $solicitud)
        ->where('in_actual', '=', true)
        ->first();

        $tab_ruta_dato = tab_ruta::find( $data->id);
        $tab_ruta_dato->in_datos = true;
        $tab_ruta_dato->save();



        if (tab_configuracion_ruta::where('id_tab_solicitud', '=', $data->id_tab_tipo_solicitud)
                                    ->where('id_tab_proceso', '=', $data->id_tab_proceso)
                                    ->whereNotNull( 'nb_reporte')->exists()) {

            $tab_configuracion_ruta = tab_configuracion_ruta::select( 'id_tab_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
            'nb_accion', 'nb_reporte', 'de_variable', DB::raw('de_variable||nb_controlador as de_controlador'))
            ->join('configuracion.tab_entorno as t01', 'configuracion.tab_ruta.id_tab_entorno', '=', 't01.id')
            ->where('id_tab_solicitud', '=', $data->id_tab_tipo_solicitud)
            ->where('id_tab_proceso', '=', $data->id_tab_proceso)
            ->whereNotNull( 'nb_reporte')
            ->first();

            if (tab_ruta::where('id', '=', $data->id)->where('in_definitivo', '=', false)->exists()) {

                $tab_ruta = tab_ruta::find( $data->id);
                $tab_ruta->in_reporte = true;
                $tab_ruta->save();

                $namespace = self::getAppNamespace();

                $entorno = $namespace.$tab_configuracion_ruta->de_controlador;
        
                return (new  $entorno)->{$tab_configuracion_ruta->nb_reporte}( $data->id);

            }
        }else{

            $tab_ruta = tab_ruta::find( $data->id);
            $tab_ruta->in_reporte = false;
            $tab_ruta->save();

        }

    }

    public static function importarAnexo($pdf, $ruta)
    {

        $tab_documento = tab_documento::select( 'id', 'id_tab_ruta', 'de_documento', 'nb_archivo', 'de_extension')
        ->where('id_tab_ruta', '=', $ruta)
        //->where('de_extension', '=', 'pdf')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();

        foreach($tab_documento as $anexo){

            $directorio = '/gobela/documento/'.$anexo->id.'.'.$anexo->de_extension;
            $archivo = Storage::disk('local')->get($directorio);

            if($anexo->de_extension=='pdf'){

                //$pageCount = $pdf->setSourceFile(public_path().'/images'.'/'.$id.'.pdf');
                $pageCount = $pdf->setSourceFile(StreamReader::createByString($archivo));
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $pageId = $pdf->ImportPage($pageNo);
                    //$s = $pdf->getTemplatesize($pageId);
                    //$pdf->AddPage($s['orientation'], $s);
                    $pdf->SetPrintHeader(false);
                    $pdf->AddPage();
                    $pdf->SetPrintFooter(false);
                    $pdf->useImportedPage($pageId, 0, 0, 210);
                    //$pdf->useImportedPage($pageId, 10, 10, 90);
                }

            }elseif($anexo->de_extension=='png'){

                $pdf->SetPrintHeader(false);
                $pdf->AddPage();
                $pdf->SetPrintFooter(false);
                $pdf->setImageScale(2.1);
                $pdf->Image('@'.$archivo);

            }elseif($anexo->de_extension=='jpg'){

                $pdf->SetPrintHeader(false);
                $pdf->AddPage();
                $pdf->SetPrintFooter(false);
                $pdf->setImageScale(2.1);
                $pdf->Image('@'.$archivo);

            }

        }

    }

}