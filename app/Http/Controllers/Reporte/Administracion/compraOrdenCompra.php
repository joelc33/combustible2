<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Administracion\tab_compra;
use App\Models\Administracion\tab_compra_detalle;
use DB;
use Session;
use Storage;
use TCPDF;
use File;
use HelperReporte;
use HelperUtiles;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\Vertical;

class compraOrdenCompra extends Controller
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
    public function OCS_SoporteAntiguo( $ruta)
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
        $pdf->MultiCell(190, 5, 'PUNTO DE CUENTA', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'administracion.tab_compra.de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion',
        'nu_solicitud', 'mo_iva_compra', 'mo_total_compra', 'de_inicial', 'nu_iva_factura', 'nu_ejecutor', 'de_ejecutor', 'de_tipo_contrato', 'nu_codigo',
        'de_sigla')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_iva_factura as t03', 'administracion.tab_compra.id_tab_iva_factura', '=', 't03.id')
        ->join('administracion.tab_ejecutor as t04', 'administracion.tab_compra.id_tab_ejecutor_entrega', '=', 't04.id')
        ->join('administracion.tab_tipo_contrato as t05', 'administracion.tab_compra.id_tab_tipo_contrato', '=', 't05.id')
        ->join('proceso.tab_solicitud as t06', 'administracion.tab_compra.id_tab_solicitud', '=', 't06.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();

        $pdf->SetFillColor(209, 209, 209);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->ln(15);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'PUNTO DE CUENTA', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(30, 6, 'ASUNTO', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(100, 6, 'RESULTADO FINAL DE '.$tab_compra->de_tipo_contrato, 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 6, 'Pto. Cuenta' , 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 6, '' , 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(30, 6, 'RECOMENDACIÓN', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(100, 6, 'OTORGAR LA ADJUDICACIÓN A LA PARTICIPANTE '.$tab_compra->de_proveedor, 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(30, 6, 'FECHA' , 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 6, $tab_compra->fe_compra , 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',10);
        
        $montoletra = HelperUtiles::numletras( $tab_compra->mo_total_compra, 1 );
        $montonum = number_format($tab_compra->mo_total_compra, 2, ',','.');

        $txt = "Cumplidas las formalidades de Ley para el desarrollo del procedimiento administrativo de selección"
        ." de contratista, signado con las siglas N° ".$tab_compra->nu_expediente.", "
        ." y visto el resultado de la evaluación efectuada por la Comisión Interna de Contrataciones Públicas, de conformidad con "
        ." la cual resultó valida la oferta de la empresa ".$tab_compra->de_proveedor
        ." alcanzando un puntaje final de Cien (100) puntos, según se evidenció del análisis legal, técnico, económico y financiero contenido en el informe "
        ." de Recomendación, asignado con el Nro. ".$tab_compra->nu_expediente.' de fecha '.$tab_compra->fe_compra.", conjuntamente con el "
        ." Presupuesto Base emanado del contrantante por la cantidad de ".$montoletra." (".$montonum   
        .", el cual incluye el IVA del ".$tab_compra->nu_iva_factura."%) y fecha estimada de entrega ".$tab_compra->fe_entrega
        .", por haber cumplido con todos los requerimientos exigidos en el pliego de condiciones para la contratación.";

        // print a blox of text using multicell()
        $pdf->MultiCell(190, 6, $txt."\n", 1, 'J', 0, 1, '' ,'', true);
        
        //$pdf->ln(6);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'Por la Comisión Interna de Contratación', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',7);
        $pdf->MultiCell(50, 6, 'Area Júridica', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(50, 6, 'Area Financiera', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(50, 6, 'Area Técnica', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(40, 6, 'Secretaria', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(150, 6, 'DESICIÓN', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(40, 6, 'FECHA / FIRMA', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',7);
        $pdf->MultiCell(25, 6, 'Aprobado:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 6, 'Informado:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 6, 'Negado:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 6, 'Diferido:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 6, 'Visto:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 6, 'Otro:____', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(20, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(20, 6, '', 1, 'L', 0, 0, '', '', true);

        $pdf->AddPage();

        $pdf->SetFont( '', '', 8);
        $pdf->MultiCell(170, 6, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(20, 6, $tab_compra->fe_compra, 0, 'R', 0, 0, '', '', true);
        $pdf->ln(12);
        $pdf->MultiCell(100, 6, $tab_compra->de_proveedor, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 6, $tab_compra->de_sigla, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(12);
        $pdf->MultiCell(190, 6, $tab_compra->nu_codigo, 0, 'C', 0, 0, '', '', true);
        $pdf->ln(12);
        $pdf->SetFont( '', 'B', 8);
        $pdf->MultiCell(190, 6, $tab_compra->de_ejecutor, 0, 'C', 0, 0, '', '', true);
        $pdf->SetFont( '', '', 8);
        $pdf->ln(12);

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total', 'administracion.tab_compra_detalle.id_tab_catalogo_partida', 'co_partida', 'de_partida',
        'id_tab_asignar_partida_detalle')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->leftJoin('administracion.tab_asignar_partida_detalle as t04', 'administracion.tab_compra_detalle.id_tab_asignar_partida_detalle', '=', 't04.id')
        ->leftJoin('administracion.tab_partida_egreso as t05', 't04.id_tab_partida_egreso', '=', 't05.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $tab_ruta->id_tab_solicitud)
        ->get();

        foreach($tab_compra_detalle as $key => $campo){

            $pdf->MultiCell(20, 6, $campo->nu_cantidad, 0, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(110, 6, $campo->de_producto, 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(30, 6, number_format( $campo->mo_precio_unitario, 2, ',','.') , 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(30, 6, number_format( $campo->mo_precio_total, 2, ',','.') , 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->ln(6);

        }

        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(20, 6, '', 0, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(190, 6, 'CATEGORIAS PRESUPUESTARIAS', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('','',8);
        $pdf->ln(6);

        $tab_compra_detalle_partida = tab_compra_detalle::select( 'co_partida', 'de_partida', DB::raw('sum(mo_precio_total) as mo_precio_total'))
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_catalogo_partida as t02', 'administracion.tab_compra_detalle.id_tab_catalogo_partida', '=', 't02.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $tab_ruta->id_tab_solicitud)
        ->groupBy(DB::raw('1, 2'))
        ->orderby('co_partida','ASC')
        ->get();

        $sum_precio_total = 0;

        foreach($tab_compra_detalle_partida as $key => $campo){

            $pdf->MultiCell(20, 6, '', 0, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(100, 6, $campo->co_partida, 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(45, 6, number_format( $campo->mo_precio_total, 2, ',','.') , 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->ln(6);

            $sum_precio_total = $sum_precio_total + $campo->mo_precio_total;

        }
        $pdf->ln(12);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(20, 6, '', 0, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(100, 6, 'TOTAL', 0, 'R', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(45, 6, number_format( $campo->mo_precio_total, 2, ',','.') , 0, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(6);

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
