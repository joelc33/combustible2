<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Administracion\tab_requisicion;
use App\Models\Administracion\tab_requisicion_detalle;
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

class compraRequisicion extends Controller
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
    public function ordenRequisicion( $ruta)
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
		$pdf->MultiCell(190, 5, 'REQUISICIÓN', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $tab_requisicion = tab_requisicion::select( 'administracion.tab_requisicion.id', 'de_concepto', DB::raw("to_char(administracion.tab_requisicion.created_at, 'dd-mm-YYYY') as fe_requisicion"),
        'nu_ejecutor', 'de_ejecutor')
        ->join('administracion.tab_ejecutor as t01', 'administracion.tab_requisicion.id_tab_ejecutor', '=', 't01.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud )
        ->first();

        $tab_requisicion_detalle = tab_requisicion_detalle::select( 'administracion.tab_requisicion_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida')
        ->join('administracion.tab_requisicion as t01', 'administracion.tab_requisicion_detalle.id_tab_requisicion', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_requisicion_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_requisicion_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_requisicion_detalle.in_activo', '=', true)
        ->where('id_tab_requisicion', '=', $tab_requisicion->id)
        ->get();

        $cantidad_detalle = $tab_requisicion_detalle->count();

        $pdf->SetFillColor(209, 209, 209);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->ln(15);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'DATOS DE LA REQUISICIÓN', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(95, 6, 'REQUISICIÓN N°: ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(95, 6, 'FECHA: '.$tab_requisicion->fe_requisicion , 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(190, 6, 'UNIDAD SOLICITANTE: '.$tab_requisicion->nu_ejecutor.' - '.$tab_requisicion->de_ejecutor, 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);
        $pdf->MultiCell(190, 6, 'CONCEPTO: '.$tab_requisicion->de_concepto , 1, 'L', 0, 0, '', '', true);
        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(190, 6, 'DETALLES DE MATERIALES', 1, 'C', 1, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(35, 6, 'CÓDIGO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(65, 6, 'DESCRIPCIÓN DEL ITEM', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 6, 'CANTIDAD', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 6, 'UNIDAD', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('','',8);

        foreach($tab_requisicion_detalle as $key => $campo){

            $pdf->MultiCell(35, 6, $campo->nu_producto, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(65, 6, $campo->de_producto, 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(45, 6, $campo->nu_cantidad, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            //$pdf->MultiCell(45, 6, number_format( $campo->nu_monto, 2, ',','.'), 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(45, 6, $campo->de_unidad_medida, 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->ln(6);

        }

        for ( $i = $cantidad_detalle; $i < 10; $i++){

            $pdf->MultiCell(35, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(65, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(45, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->MultiCell(45, 6, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'T', true);
            $pdf->ln(6);

        }

        foreach($tab_requisicion_detalle as $key => $campo){

            $pdf->MultiCell(190, 6, 'ESPECIFICACIONES TÉCNICAS / CÓDIGO: '.$campo->nu_producto.' - '.$campo->de_especificacion , 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
            $pdf->ln(6);

        }

        $pdf->ln(12);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(60, 8, 'UNIDAD SOLICITANTE', 1, 'C', 1, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(60, 8, 'DIRECCIÓN DE COMPRAS Y SUMINISTRO', 1, 'C', 1, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(70, 8, 'UNIDAD EJECUTORA', 1, 'C', 1, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(8);
        $pdf->MultiCell(60, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(60, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(70, 18, '', 1, 'C', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->ln(18);
        $pdf->SetFont('','B',6);
        $pdf->MultiCell(60, 5, 'Solicitado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(60, 5, 'Registrado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);
        $pdf->MultiCell(70, 5, 'Aprobado por:', 1, 'L', 0, 0, '', '', true, 0, false, false, 0, 'M', true);

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
