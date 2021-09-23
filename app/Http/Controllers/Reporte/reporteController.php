<?php

namespace App\Http\Controllers\Reporte;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Proceso\tab_persona;
use App\Models\Teleconsulta\tab_informe;
use App\Models\Teleconsulta\tab_consulta;
use App\Models\Configuracion\tab_especialidad;
use App\Models\Configuracion\tab_instituto;
use App\Models\Proceso\tab_solicitud;
use DB;
use Session;
use Storage;
use TCPDF;
use File;
use HelperReporte;
use HelperUtiles;
use View;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\Vertical;

class reporteController extends Controller
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
    public function morbilidad(Request $request)
    {

        $tab_especialidad = tab_especialidad::getEspecialidad(Auth::user()->id);
        $tab_instituto    = tab_instituto::getInstituto(Auth::user()->id);
  
        return View::make('reporte.morbilidad')->with([
            'tab_especialidad'  => $tab_especialidad,
            'tab_instituto' => $tab_instituto
        ]);

        
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imprimirmorbilidad(Request $request)
    {

        /*$validador = Validator::make( $request->all(), tab_comentario::$validarFiltro);
        if ($validador->fails()) {
            return Redirect::back()->withErrors( $validador)->withInput( $request->all());
        }*/

        /***distribucion***/
        $htmlReporte = '
        <!-- Tabla 1 -->
        <img src="/imagenes/telemedicina.jpg" alt="test alt attribute" width="250" height="125" border="0" /> 
        <br>
        <table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
        <thead>
        <tr align="left" bgcolor="#E6E6E6">
        <th colspan="5" style="width: 100%;"><b>LISTA DE MORBILIDAD LAS FECHAS: '.$request->get('fecha_desde').' - '.$request->get('fecha_hasta').'</b></th>
        </tr>

        
        <tr style="font-size:9px">
        <th align="center" bgcolor="#E6E6E6" style="width: 6%;"><b>N°</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 12%;"><b>Nombre completo</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 6%;"><b>Edad</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 6%;"><b>Genero</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 6%;"><b>P</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 6%;"><b>S</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 22%;"><b>Solicitud</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 22%;"><b>Instituto</b></th>
        <th align="center" bgcolor="#E6E6E6" style="width: 14%;"><b>Médico Tratamiento</b></th>
        </tr>
        </thead>
        ';

        $htmlReporte.='
        <tbody>
        ';

        /*list($dia_ini, $mes_ini, $anio_ini) = explode('-', $request->get('fecha_inicio'));
        $fecha_ini = $anio_ini."-".$mes_ini."-".$dia_ini;*/

        $fecha_ini = $request->get('fecha_desde');

        /*list($dia_fin, $mes_fin, $anio_fin) = explode('-', $request->get('fecha_corte'));
        $fecha_fin = $anio_fin."-".$mes_fin."-".$dia_fin;*/

        $fecha_fin = $request->get('fecha_hasta');

        $especialidad = $request->get('especialidad');
        $instituto    = $request->get('instituto');


                
        $tab_consulta = tab_ruta::select('nombres','apellidos',DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"),'de_sexo','t02.de_solicitud','de_instituto','nb_usuario')
        ->join('telemedicina.tab_persona as t01','t01.id','=','proceso.tab_ruta.id_persona')
        ->join('configuracion.tab_solicitud as t02','t02.id','=','proceso.tab_ruta.id_tab_tipo_solicitud')
        ->join('configuracion.tab_instituto as t03','t03.id','=','proceso.tab_ruta.id_instituto')
        ->join('autenticacion.tab_usuario as t04','t04.id','=','proceso.tab_ruta.id_tab_usuario')
        ->join('configuracion.tab_sexo as t06','t06.id','=','t01.id_sexo')
        ->whereBetween(DB::raw("cast(proceso.tab_ruta.created_at as date)"), [ $fecha_ini, $fecha_fin ]);
        
        if(!empty($instituto ))
            $tab_consulta = $tab_consulta->where('proceso.tab_ruta.id_instituto','=',$instituto);
        if(!empty($especialidad  ))
            $tab_consulta = $tab_consulta->where('proceso.tab_ruta.id_especialidad', '=',$especialidad);
        $tab_consulta = $tab_consulta->orderBy('apellidos','ASC')
        ->get();

        $i = 0;

        foreach ($tab_consulta as $key => $value) {
        // Set cell An to the "name" column from the database (assuming you have a column called name)
            $i++;

            $count = tab_solicitud::join('telemedicina.tab_persona as t01','t01.id','=','proceso.tab_solicitud.id_persona')->
                     where('cedula','=',$value->cedula)->count();

            $p ='';
            $s = '';
            if($count == 1){
                $p = 'X';
            }else{
                $s = 'X';
            }


            $htmlReporte.='
            <tr style="font-size:9px" nobr="true">
                <td style="width: 6%;" align="center"><b>'.$i.'</b></td>
                <td style="width: 12%;">'.$value->nombres.''.$value->apellidos.'</td>
                <td style="width: 6%;">'.$value->edad.'</td>
                <td style="width: 6%;">'.$value->de_sexo.'</td>
                <td align="center" style="width: 6%;">'.$p.'</td>
                <td align="center" style="width: 6%;">'.$s.'</td>
                <td style="width: 22%;">'.$value->de_solicitud.'</td>
                <td style="width: 22%;">'.$value->de_instituto.'</td>
                <td style="width: 14%;" align="left">'.$value->nb_usuario.'</td>
                </tr>';
        }

        $htmlReporte.='
        </tbody>
        </table>';

        $pdf = new TCPDF("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
        $pdf->SetTitle('Reporte');
        $pdf->SetSubject('Reporte');
        $pdf->SetKeywords('Planilla');
        $pdf->SetMargins(10,10,10);
        $pdf->SetTopMargin(10);
        $pdf->SetPrintHeader(true);
        $pdf->SetPrintFooter(true);
        
        
        //$pdf->Image(public_path().'/imagenes/telemedicina.jpg', 180, 60, 15, 15, 'JPG');
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();
        //Cuerpo de la planilla
        $pdf->writeHTML($htmlReporte, true, false, false, false, '');
        $pdf->lastPage();
        $pdf->output('LISTA_'.date("H:i:s").'.pdf', 'D');

    }
    
}

