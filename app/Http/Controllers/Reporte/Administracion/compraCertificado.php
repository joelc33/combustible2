<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Administracion\tab_asignar_partida;
use App\Models\Administracion\tab_asignar_partida_detalle;
use App\Models\Administracion\tab_requisicion;
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

class compraCertificado extends Controller
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
    public function presupuestoCertificado( $ruta)
    {
        $tab_ruta = tab_ruta::select( 'id_tab_solicitud')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();

        $tab_asignar_partida = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id', 'id_tab_solicitud', 'id_tab_proveedor', 'mo_presupuesto', 'id_tab_ejecutor', 'id_tab_fuente_financiamiento',
        'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial', 'nu_ejecutor', 'de_ejecutor')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_asignar_partida.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_ejecutor as t03', 'administracion.tab_asignar_partida.id_tab_ejecutor', '=', 't03.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();

        $tab_asignar_partida_detalle = tab_asignar_partida_detalle::select( 'administracion.tab_asignar_partida_detalle.id', 'nu_producto',
        'de_producto', 'co_partida', 'de_partida', 'mo_gasto')
        ->join('administracion.tab_producto as t02', 'administracion.tab_asignar_partida_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_partida_egreso as t05', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', '=', 't05.id')
        ->where('administracion.tab_asignar_partida_detalle.in_activo', '=', true)
        ->where('id_tab_asignar_partida', '=', $tab_asignar_partida->id)
        ->get();

        $tab_requisicion = tab_requisicion::select( 'administracion.tab_requisicion.id', 'de_concepto', DB::raw("to_char(administracion.tab_requisicion.created_at, 'dd-mm-YYYY') as fe_requisicion"))
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud )
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
        $pdf->MultiCell(190, 5, 'CERTIFICACIÓN DE DISPONIBILIDAD PRESUPUESTARIA', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $pdf->ln(15);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 6, 'Ciudadano(a):', 0, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(190, 6, 'Su despacho.-', 0, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->SetFont('','',10);

        $montoletra = HelperUtiles::numletras( $tab_asignar_partida->mo_presupuesto, 1 );
        $montonum = number_format( $tab_asignar_partida->mo_presupuesto, 2, ',','.');

        $txt = "Por medio de la presente se CERTIFICA LA DISPONIBILIDAD PRESUPUESTARIA contemplada en el presente ejercicio fiscal, la cantidad de ".$montoletra
        ." (".$montonum.") correspondiente a recursos de , para:";

        // print a blox of text using multicell()
        $pdf->MultiCell(190, 6, $txt."\n\n", 0, 'J', 0, 1, '' ,'', true);

        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 12, 'Descripción: '.$tab_requisicion->de_concepto, 0, 'L', 0, 0, '', '', true);

        $pdf->ln(10);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'A continuación se describen las partidas presupuestarias:', 0, 'L', 0, 0, '', '', true);

        $pdf->ln(10);
        $pdf->SetFillColor(209, 209, 209);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'PARTIDAS ASOCIADAS', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(40, 6, 'PARTIDA', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(100, 6, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(50, 6, 'MONTO', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);

        $sum_precio_total = 0;

        foreach($tab_asignar_partida_detalle as $key => $campo){

            $pdf->MultiCell(40, 6, $campo->co_partida, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(100, 6, $campo->de_partida, 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->MultiCell(50, 6, number_format( $campo->mo_gasto, 2, ',','.') , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->ln(6);

            $sum_precio_total = $sum_precio_total + $campo->mo_gasto;

        }

        $pdf->ln(10);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'Sin más a que hacer referencia, me despido de usted.', 0, 'L', 0, 0, '', '', true);

        $pdf->ln(16);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, 'Atentamente', 0, 'C', 0, 0, '', '', true);

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
