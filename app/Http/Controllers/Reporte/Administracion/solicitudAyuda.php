<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Nomina\tab_nomina;
use App\Models\Proceso\tab_ruta;
use DB;
use Session;
use Storage;
use TCPDF;
use File;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\Vertical;

class solicitudAyuda extends Controller
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
    public function documento( $ruta)
    {
        /*$tab_ruta = tab_ruta::select( 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'id_tab_proceso', 'in_actual', 'de_nomina')
        ->join('nomina.tab_nomina as t01','t01.id_tab_solicitud','=','proceso.tab_ruta.id_tab_solicitud')
        ->with(['usuario:id,nb_usuario'])
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();*/

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
        $pdf->SetFont('','B',9);
        $pdf->MultiCell(70, 5, 'PLANILLA DE DEPOSITO BANCARIO', 0, 'L', 0, 0, '', '', true);
	      $pdf->SetFont('','B',9);
        $pdf->MultiCell(95, 5, $ruta, 0, 'L', 0, 0, '', '', true);

        //******FIN CONTENIDO*******//
        $pdf->lastPage();

        //*******CLASE PARA ANEXAR DOCUMENTOS AL REPORTE********//
        HelperReporte::importarAnexo($pdf, $ruta);

        $directorio = '/App/reporte';
        $disk = Storage::disk('ftp');
        $disk->makeDirectory($directorio);
        $disk->put($directorio.'/'.$ruta.'.pdf', $pdf->output( $ruta.'.pdf', 'S'));

    }
}
