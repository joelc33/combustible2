<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Proceso\tab_solicitud;
use App\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use App\Models\Administracion\tab_asignar_partida;
use App\Models\Administracion\tab_tipo_orden_pago;
use App\Models\Administracion\tab_orden_pago;
use App\Models\Administracion\tab_liquidacion;
use App\Models\Administracion\tab_proveedor;
use App\Models\Administracion\tab_proceso_retencion;
use App\Models\Administracion\tab_proceso_retencion_factura;
use App\Models\Administracion\tab_proceso_retencion_detalle;
use App\Models\Administracion\tab_asiento_contable;
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

class ordenPago extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

        /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function ODP_SoporteAntiguo( $ruta)
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

        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $tab_ruta->id_tab_solicitud)
        ->first();       
        
        $tab_proceso = tab_ruta::select( 't01.de_proceso')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();        
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();         
        
        $tab_tipo_orden_pago = tab_tipo_orden_pago::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();
        
        $tab_orden_pago = tab_orden_pago::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_orden_pago', 'nu_orden_pago', 'de_concepto_pago', 'mo_pago','mo_retencion','tx_documento',
        DB::raw(" to_char( fe_pago, 'dd-mm-YYYY') as fe_pago"))  
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();        
        
        $tab_asignar_partida = tab_asignar_partida::select(DB::raw("coalesce(sum(mo_presupuesto),0.00) as mo_pago"), 'id_tab_ejecutor')             
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->where('in_activo', '=', true)
        ->groupBy('id_tab_solicitud', 'id_tab_ejecutor')
        ->first();        
        
        $tab_asignar_partida_detalle = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id',  'administracion.tab_asignar_partida.mo_presupuesto', 
        'administracion.tab_asignar_partida.id_tab_ejecutor', 'co_partida', 'de_partida', 't01.id as id_tab_asignar_partida_detalle', 
        'administracion.tab_asignar_partida.id_tab_fuente_financiamiento','de_concepto', 'nu_pa', 'nu_ge', 'nu_es', 'nu_se', 'nu_sse', 
        'id_tab_ejercicio_fiscal', 'nu_ejecutor', 'nu_financiamiento')
        ->leftJoin('administracion.tab_asignar_partida_detalle as t01','t01.id_tab_asignar_partida', '=', 'administracion.tab_asignar_partida.id') 
        ->leftJoin('administracion.tab_partida_egreso as t02', 't02.id', '=', 't01.id_tab_partida_egreso')
        ->leftJoin('administracion.tab_ejecutor as t03', 't03.id', '=', 't01.id_tab_ejecutor')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->get();

        $tab_proveedor = tab_proveedor::select( 'administracion.tab_proveedor.id', 'nu_codigo', 'id_tab_documento', 'nu_documento', 'nu_nit', 'de_proveedor', 
        'de_siglas', 'de_email', 'de_sitio_web', 'tx_direccion', 'nb_representante_legal', 
        'nu_cedula_representante_legal', 'nu_telefono_representante_legal', 
        'id_tab_tipo_proveedor', 'id_tab_tipo_residencia_proveedor', 'id_tab_clasificacion_proveedor', 
        'id_tab_iva_retencion', 'fe_registro', 'fe_vencimiento', 'nu_cuenta_bancaria', 
        'tx_observacion', 'id_tab_estado', 'id_tab_municipio', 't01.de_inicial')
        ->join('configuracion.tab_documento as t01','t01.id','=','administracion.tab_proveedor.id_tab_documento')         
        ->where('administracion.tab_proveedor.id', $tab_asignar_partida->id_tab_ejecutor)
        ->first(); 

        $tab_proceso_retencion_detalle = tab_proceso_retencion_detalle::select( 'administracion.tab_proceso_retencion_detalle.id', 'id_tab_proceso_retencion_factura', 
        'id_tab_retencion_proveedor', 'nu_porcentaje_retencion', 'administracion.tab_proceso_retencion_detalle.mo_retencion', 'de_tipo_retencion', 't02.de_retencion', 'de_inicial', 
        'nu_documento', 'de_proveedor', 'tx_direccion', 'nu_factura', DB::raw(" to_char( fe_emision, 'dd-mm-YYYY') as fe_emision"), 'de_concepto', 
        'mo_base_imponible', 'mo_total')
        ->join('administracion.tab_retencion_proveedor as t01', 'administracion.tab_proceso_retencion_detalle.id_tab_retencion_proveedor', '=', 't01.id')
        ->join('administracion.tab_retencion as t02', 't01.id_tab_retencion', '=', 't02.id')
        ->join('administracion.tab_proveedor as t03', 't01.id_tab_proveedor', '=', 't03.id')
        ->join('configuracion.tab_documento as t04', 't03.id_tab_documento', '=', 't04.id')
        ->join('administracion.tab_proceso_retencion_factura as t05', 'administracion.tab_proceso_retencion_detalle.id_tab_proceso_retencion_factura', '=', 't05.id')
        ->join('administracion.tab_proceso_retencion as t06', 't05.id_tab_proceso_retencion', '=', 't06.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->get();

        //******CONTENIDO*******//
        $pdf->SetY(10);
        $pdf->SetFont('','B',12);
        $pdf->MultiCell(140, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(50, 5, 'ORDEN DE PAGO', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(140, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(50, 5, $tab_orden_pago->nu_orden_pago, 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $avance = '';
        $permanente = '';
        if($tab_orden_pago->id_tab_tipo_orden_pago == 1 ){
            $avance = 'X';
        }else{
            $permanente = 'X';
        }

        //Rounded rectangle
        $pdf->Line(10, 30, 70, 30, null);
        $pdf->Line(30, 35, 30, 30, null);
        $pdf->Line(35, 35, 35, 30, null);
        $pdf->Line(64, 35, 64, 30, null);
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 25, 60, 10, 2.50, '1111', null);
        $pdf->SetFont('','B',10);
        $pdf->Text(20, 25, 'TIPO ORDEN DE PAGO');
        $pdf->SetFont('','',9);
        $pdf->Text(10, 30, 'AVANCE:');
        $pdf->Text(30, 30, $avance);
        $pdf->Text(36, 30, 'PERMANENTE:');
        $pdf->Text(65, 30, $permanente);

        //Rounded rectangle
        $pdf->Line(135, 30, 200, 30, null);
        $pdf->Line(154, 35, 154, 30, null);
        $pdf->Line(159, 35, 159, 30, null);
        $pdf->Line(194, 35, 194, 30, null);
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(135, 25, 65, 10, 2.50, '1111', null);
        $pdf->SetFont('','B',10);
        $pdf->Text(150, 25, 'FORMA DE PAGO');
        $pdf->SetFont('','',9);
        $pdf->Text(135, 30, 'CHEQUE:');
        $pdf->Text(161, 30, 'TRANSFERENCIA:');

        $montoletra = HelperUtiles::numletras( $tab_orden_pago->mo_pago, 1 );
        $montonum = number_format( $tab_orden_pago->mo_pago, 2, ',','.');
        $texto_pago = 'HEMOS RECIBIDO DE '.'xxxxxxxxxxxxxxxxxxxxxx'.' LA CANTIDAD DE: '.$montoletra.'**********'.$montonum;

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 35, 190, 20, 2.50, '1111', null);

        $pdf->SetY(36);
        $pdf->MultiCell(190, 5, $texto_pago."\n", 0, 'J', 0, 0, '', '', true);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 55, 190, 15, 2.50, '1111', null);

        $pdf->SetY(56);
        $pdf->MultiCell(190, 5, 'A FAVOR DE: '.$tab_proveedor->de_inicial.'-'.$tab_proveedor->nu_documento.' - '.$tab_proveedor->de_proveedor."\n", 0, 'J', 0, 0, '', '', true);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 70, 190, 20, 2.50, '1111', null);

        $pdf->SetY(71);
        $pdf->MultiCell(190, 5, 'POR CONCEPTO DE: '.$tab_orden_pago->de_concepto_pago."\n", 0, 'J', 0, 0, '', '', true);

        //Rounded rectangle
        $pdf->Line(10, 95, 200, 95, null);
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 90, 190, 50, 2.50, '1111', null);
        $pdf->SetFont('','B', 9);
        $pdf->Text(80, 90, 'DOCUMENTOS Y RETENCIONES');

        $pdf->SetFont('','', 7);
        $pdf->SetY(96);
        $pdf->MultiCell(20, 5, 'DOCUM.', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(20, 5, 'FECHA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, 'MONTO BASE', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(10, 5, '', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, 'RETENCIONES', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, 'MONTO', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, 'CANCELADO', 0, 'R', 0, 0, '', '', true);

        $y_retencion = 101;
        $mo_base_imponible = 0;
        $mo_retencion = 0;
        $mo_total = 0;

        foreach($tab_proceso_retencion_detalle as $key => $campo){

            $pdf->SetFont('','', 7);
            $pdf->SetY($y_retencion);
            $pdf->MultiCell(20, 5, $campo->nu_factura, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, $campo->fe_emision, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(30, 5, number_format( $campo->mo_base_imponible, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(30, 5, $campo->de_retencion, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(40, 5, number_format($campo->mo_retencion, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(40, 5, number_format($campo->mo_total, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(3);

            $mo_base_imponible = $mo_base_imponible + $campo->mo_base_imponible;
            $mo_retencion = $mo_retencion + $campo->mo_retencion;
            $mo_total = $mo_total + $campo->mo_total;

        }

        $pdf->MultiCell(40, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, '------------------------', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, '------------------------', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, '------------------------', 0, 'R', 0, 0, '', '', true);
        $pdf->ln(3);
        $pdf->MultiCell(40, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, number_format( $mo_base_imponible, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, number_format( $mo_retencion, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, number_format( $mo_total, 2, '.', ','), 0, 'R', 0, 0, '', '', true);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 140, 190, 5, 2.50, '1111', null);
        $pdf->SetFont('','B', 9);
        $pdf->Text(30, 140, 'CODIGOS CONTABLES');
        $pdf->Text(120, 140, 'CATEGORIAS PRESUPUESTARIAS');

        $pdf->SetFont('','B', 7);
        $pdf->SetY(146);
        $pdf->Text(10, 146, 'CUENTA');
        $pdf->Text(60, 146, 'DEBITOS');
        $pdf->Text(80, 146, 'CREDITOS');
        $pdf->Text(100, 146, 'AÑO');
        $pdf->Text(109, 146, 'UE');
        $pdf->Text(117, 146, 'PAC');
        $pdf->Text(126, 146, 'AE');
        $pdf->Text(134, 146, 'P');
        $pdf->Text(142, 146, 'G');
        $pdf->Text(150, 146, 'E');
        $pdf->Text(158, 146, 'SE');
        $pdf->Text(165, 146, 'SSE');
        $pdf->Text(174, 146, 'F');
        $pdf->Text(187, 146, 'MONTO');

        $pdf->SetFont('','', 7);
        $pdf->SetY(151);

        $tab_asiento_contable = tab_asiento_contable::select( 'nu_cuenta_contable', 'id_tab_solicitud', 'id_tab_cuenta_contable', 'id_tab_tipo_asiento', 
        'mo_debe', 'mo_haber', 'id_tab_usuario', 'de_cuenta_contable', 'co_cuenta_contable')
        ->join('administracion.tab_cuenta_contable as t01', 'administracion.tab_asiento_contable.id_tab_cuenta_contable', '=', 't01.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->where('id_tab_tipo_asiento', 2)
        ->get();

        foreach($tab_asiento_contable as $key => $campo4){

            $pdf->MultiCell(50, 5, $campo4->co_cuenta_contable, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, number_format( $campo4->mo_debe, 2, '.', ','), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, number_format( $campo4->mo_haber, 2, '.', ','), 0, 'L', 0, 0, '', '', true);
            $pdf->ln(3);

        }

        $pdf->SetY(151);
        $pdf->SetX(10);

        foreach($tab_asignar_partida_detalle as $key => $campo3){

            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->id_tab_ejercicio_fiscal, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_ejecutor, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_pa, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_ge, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_es, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_se, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, $campo3->nu_sse, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, $campo3->nu_financiamiento, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(23, 5, number_format( $campo3->mo_presupuesto, 2, '.', ','), 0, 'C', 0, 0, '', '', true);
            $pdf->ln(3);

        }

        $y_categoria = 150;

        /*foreach($tab_proceso_retencion_factura as $key => $campo){

            $pdf->SetFont('','', 7);
            $pdf->SetY($y_categoria);
            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'AÑO', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'UE', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'PAC', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'AE', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'P', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'G', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'E', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'SE', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'SSE', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(8, 5, 'F', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, 'MONTO', 0, 'C', 0, 0, '', '', true);

        }*/

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 145, 190, 50, 2.50, '1111', null);
        $pdf->Line(100, 140, 100, 195, null);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 195, 190, 5, 2.50, '1111', null);
        $pdf->Line(60, 195, 60, 245, null);
        $pdf->Line(140, 195, 140, 245, null);
        $pdf->SetFont('','B', 9);
        $pdf->Text(25, 195, 'BANCO');
        $pdf->Text(80, 195, 'NUMERO DE CUENTA');
        $pdf->Text(145, 195, 'MONTO EN Bs.S QUE CANCELA');

        $pdf->SetFont('','B', 13);
        $pdf->SetY(205);
        $pdf->SetX(108);
        $pdf->MultiCell(90, 5, number_format( $mo_total, 2, '.', ','), 0, 'R', 0, 0, '', '', true);
        $pdf->SetFont('','B', 9);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 200, 190, 20, 2.50, '1111', null);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 220, 190, 5, 2.50, '1111', null);
        $pdf->Text(25, 220, 'REVISADO POR:');
        $pdf->Text(80, 220, 'ORDENADO POR:');
        $pdf->Text(145, 220, 'APROBADO POR:');

        $pdf->SetFont('','', 8);
        $pdf->SetY(241);
        $pdf->MultiCell(50, 5, 'Presupuesto', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(80, 5, 'Administracion y Finanzas', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(60, 5, 'Gobernador del Zulia', 0, 'C', 0, 0, '', '', true);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 225, 190, 20, 2.50, '1111', null);

        //Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 245, 190, 5, 2.50, '1111', null);

        //Rounded rectangle
        $pdf->Line(10, 255, 200, 255, null);
        $pdf->Line(100, 250, 100, 285, null);
        $pdf->Line(60, 250, 60, 285, null);
        $pdf->Line(140, 250, 140, 285, null);
        $pdf->Line(170, 250, 170, 285, null);
        $pdf->SetLineStyle(array('width' => 0.20, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(10, 250, 190, 35, 2.50, '1111', null);
        $pdf->SetFont('','B', 9);
        $pdf->Text(80, 245, 'RECIBE CONFORME BENEFICIARIO');
        $pdf->Text(20, 250, 'NOMBRE Y APELLIDO');
        $pdf->Text(75, 250, 'C.I.N°');
        $pdf->Text(112, 250, 'FIRMA');
        $pdf->Text(147, 250, 'FECHA');
        $pdf->Text(178, 250, 'SELLO');

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
