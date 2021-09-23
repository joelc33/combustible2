<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Administracion\tab_compra;
use App\Models\Administracion\tab_compra_detalle;
use App\Models\Administracion\tab_requisicion;
use DB;
use Session;
use Storage;
use TCPDF;
use File;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\Vertical;

class compraContrato extends Controller
{
    //
    public function __construct()
    {
      $this->middleware('auth');
    }

        /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function presupuestoBase( $ruta)
    {
        $tab_ruta = tab_ruta::select( 'id_tab_solicitud')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();

        $pdf = new Vertical("P", PDF_UNIT, 'Letter', true, 'UTF-8', false);   
        $pdf->SetMargins(10,10,10);
        $pdf->SetTopMargin(30);
        $pdf->SetPrintHeader(true);
        $pdf->SetPrintFooter(true);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetTitle('REPORTE');
        $pdf->SetSubject('App');
        $pdf->SetKeywords('App, PDF, REPORTE');
        $pdf->AddPage();

        //******CONTENIDO*******//
        $pdf->SetY(25);
        $pdf->SetFont('','B',11);
        $pdf->MultiCell(190, 5, 'PRESUPUESTO BASE', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'administracion.tab_compra.de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion',
        'nu_solicitud', 'mo_iva_compra', 'mo_total_compra')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->join('proceso.tab_solicitud as t02', 'administracion.tab_compra.id_tab_solicitud', '=', 't02.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total', 'in_excento_iva')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $tab_ruta->id_tab_solicitud)
        ->get();

        $tab_requisicion = tab_requisicion::where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();

        $cantidad_detalle = $tab_compra_detalle->count();

        $pdf->SetFillColor(209, 209, 209);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->ln(15);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'DATOS DEL PRESUPUESTO BASE', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(95, 6, 'SOLICITUD N°: '.$tab_compra->nu_solicitud, 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(95, 6, 'FECHA: '.$tab_compra->fe_ini , 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(190, 6, 'CONCEPTO: '.$tab_requisicion->de_concepto, 1, 'L', 0, 0, '', '', true);
        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'ESPECIFICACIONES TÉCNICAS', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(15, 6, 'ITEM', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(65, 6, 'DESCRIPCIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 6, 'CANT.', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(35, 6, 'UNIDAD DE MEDIDA', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 6, 'PRECIO UNITARIO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 6, 'IMPORTE TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);

        $sum_excento_iva = 0;
        $sum_precio_total = 0;

        foreach($tab_compra_detalle as $key => $campo){

            $pdf->MultiCell(15, 6, $campo->nu_producto, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(65, 6, $campo->de_producto, 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(15, 6, $campo->nu_cantidad, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            //$pdf->MultiCell(35, 6, number_format( $campo->nu_monto, 2, ',','.'), 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(35, 6, $campo->de_unidad_medida, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(30, 6, number_format( $campo->mo_precio_unitario, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(30, 6, number_format( $campo->mo_precio_total, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->ln(6);

            $sum_precio_total = $sum_precio_total + $campo->mo_precio_total;

            if($campo->in_excento_iva == true){
                $sum_excento_iva = $sum_excento_iva + $campo->mo_precio_total;
            }

        }

        for ( $i = $cantidad_detalle; $i < 10; $i++){

            $pdf->MultiCell(15, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(65, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(15, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(35, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(30, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(30, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->ln(6);

        }

        $pdf->SetFont('','B',10);
        $pdf->MultiCell(160, 6, 'Sub-Total:', 1, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(30, 6, number_format( $sum_precio_total, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(160, 6, 'Total I.V.A:', 1, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(30, 6, number_format( $tab_compra->mo_iva_compra, 2, ',','.'), 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(160, 6, 'Total Excento:', 1, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(30, 6, number_format( $sum_excento_iva, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(160, 6, 'Total Generado:', 1, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(30, 6, number_format( $tab_compra->mo_total_compra - $tab_compra->mo_iva_compra, 2, ',','.'), 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);

        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 8, 'DIRECCIÓN DE COMPRAS Y SUMINISTRO', 1, 'C', 1, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(8);
        $pdf->MultiCell(60, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(60, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(70, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(18);
        $pdf->SetFont('','B',6);
        $pdf->MultiCell(60, 5, 'Elaborado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(60, 5, 'Conformado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(70, 5, 'Aprobado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);

        $pdf->AddPage();

        $pdf->SetY(25);
        $pdf->SetFont('','B',11);
        $pdf->MultiCell(190, 5, 'PRESUPUESTO BASE', 0, 'C', 0, 0, '', '', true);

        $pdf->ln(15);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 6, 'Ciudadano(a):', 0, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(190, 6, 'SUBSECRETARIA DE PRESUPUESTO', 0, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(190, 6, 'Su despacho.-', 0, 'L', 0, 0, '', '', true);

        $pdf->ln(12);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'Por medio del presente me dirijo a usted, en la oportunidad de solicitar la disponibilidad presupuestaria y certificación para la ejecución de una orden por el concepto de:', 0, 'L', 0, 0, '', '', true);

        $pdf->ln(15);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 6, $tab_requisicion->de_concepto, 0, 'L', 0, 0, '', '', true);

        $pdf->ln(12);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'Discriminado por partidas presupuestarias de la siguiente manera:', 0, 'L', 0, 0, '', '', true);

        $tab_compra_detalle_partida = tab_compra_detalle::select( 'co_partida', 'de_partida', DB::raw('sum(mo_precio_total) as mo_precio_total'))
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_catalogo_partida as t02', 'administracion.tab_compra_detalle.id_tab_catalogo_partida', '=', 't02.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $tab_ruta->id_tab_solicitud)
        ->groupBy(DB::raw('1, 2'))
        ->orderby('co_partida','ASC')
        ->get();

        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'PARTIDAS ASOCIADAS', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(45, 6, 'PARTIDA', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(100, 6, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 6, 'MONTO', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);

        $sum_precio_total = 0;

        foreach($tab_compra_detalle_partida as $key => $campo){

            $pdf->MultiCell(45, 6, $campo->co_partida, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(100, 6, $campo->de_partida, 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(45, 6, number_format( $campo->mo_precio_total, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->ln(6);

            $sum_precio_total = $sum_precio_total + $campo->mo_precio_total;

        }

        $pdf->SetFont('','B',10);
        $pdf->MultiCell(145, 6, 'Total:', 1, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(45, 6, number_format( $sum_precio_total, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);

        $pdf->ln(12);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'Sin otro particular al cual hacer mención, me despido de usted.', 0, 'L', 0, 0, '', '', true);

        $pdf->ln(25);
        $pdf->MultiCell(190, 6, 'Atentamente', 0, 'C', 0, 0, '', '', true);

        $pdf->ln(25);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 6, 'DIRECTOR(A) DE COMPRAS Y SUMINISTRO', 0, 'C', 0, 0, '', '', true);

        //******FIN CONTENIDO*******//
        $pdf->lastPage();

        //*******CLASE PARA ANEXAR DOCUMENTOS AL REPORTE********//
        //HelperReporte::importarAnexo($pdf, $ruta);

        $directorio = '/App/reporte';
        $disk = Storage::disk('ftp');
        $disk->makeDirectory($directorio);
        $disk->put($directorio.'/'.$ruta.'.pdf', $pdf->output( $ruta.'.pdf', 'S'));

    }
}
