<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Proceso\tab_persona;
use App\Models\Teleconsulta\tab_informe;
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

class resonanciaMagneticaCraneo extends Controller
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
    public function reporte($ruta)
    {
        $tab_ruta = tab_ruta::where('id','=',$ruta)->first();

        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'cedula','nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->where('telemedicina.tab_persona.id', '=', $tab_ruta->id_persona)
        ->first();

        $tab_informe = tab_informe::where('id_ruta', '=',$ruta)->first();   
        $tab_ruta = tab_ruta::select('nb_usuario','de_especialidad','de_instituto')
                    ->join('autenticacion.tab_usuario t01','t01.id','=','proceso.tab_ruta.id_tab_usuario')
                    ->join('configuracion.tab_especialidad t02','t02.id','=','proceso.tab_ruta.id_especialidad')
                    ->join('configuracion.tab_instituto t03','t03.id','=','proceso.tab_ruta.id_instituto')
                    ->where('id', '=',$ruta)->first();   

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
        $pdf->MultiCell(190, 5, $tab_ruta->de_instituto, 0, 'C', 0, 0, '', '', true);
        $pdf->ln();        
        $pdf->MultiCell(190, 5, 'RESONANCIA MAGNETICA DE CRANEO', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        $pdf->ln(15);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(190, 6, 'Nombre del Paciente: '.$tab_persona->nombres.' '.$tab_persona->apellidos, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(190, 6, 'Edad: '.$tab_persona->edad, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(190, 6, 'Fecha:'.$tab_informe->created_at, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->SetFont('','',10);

        $pdf->ln(10);
        $pdf->MultiCell(190, 5, 'Protocolo Técnico', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(190, 6, $tab_informe->de_protocolo_tecnico."\n\n", 0, 'J', 0, 1, '' ,'', true);

        $pdf->ln(10);
        $pdf->MultiCell(190, 5, 'Informe', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(190, 6, $tab_informe->de_protocolo_tecnico."\n\n", 0, 'J', 0, 1, '' ,'', true);

        $pdf->ln(10);
        $pdf->MultiCell(190, 5, 'Conclusiones', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(190, 6, $tab_informe->de_conlusion."\n\n", 0, 'J', 0, 1, '' ,'', true);


        $pdf->ln(16);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(190, 6, $tab_ruta->nb_usuario, 0, 'R', 0, 0, '', '', true);
        $pdf->ln();
        $pdf->MultiCell(190, 6, $tab_ruta->de_especialidad, 0, 'R', 0, 0, '', '', true);

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
