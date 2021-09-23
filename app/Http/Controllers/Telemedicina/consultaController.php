<?php

namespace App\Http\Controllers\Telemedicina;
//*******agregar esta linea******//
use App\Models\tab_comentario;
use App\Models\tab_clasificacion;
use App\Models\tab_institucion;
use App\Models\Proceso\tab_solicitud;
use App\Models\Proceso\tab_ruta;
use App\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use App\Models\Configuracion\tab_tipo_informe;
use App\Models\Proceso\tab_persona;
use App\Models\Teleconsulta\tab_informe;
use App\Models\Teleconsulta\tab_consulta;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use Redirect;
use Auth;
use Mail;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class consultaController extends Controller
{
    /**
     * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('optimizar');
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lista(Request $request)
    {
        
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 10;
        $cedula = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('cedula')){
            $cedula = $request->query('cedula');
        }        

       $tab_consulta = tab_consulta::select( 'id',DB::raw(" to_char( fe_consulta, 'dd-mm-YYYY') as fe_consulta"), 'cedula', 'persona', 'edad', 'municipio', 'parroquia', 'telefono', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->search($cedula, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);


         $tab_menu = DB::table('tab_menu')
            ->select('co_menu', 'tx_menu', 'tx_href','co_padre','in_padre','icono','in_padre')
            ->orderBy('co_menu', 'asc')
            ->get();

        return View::make('consulta.lista')->with([
          'tab_consulta' => $tab_consulta,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'cedula'  => $cedula,
          'tab_menu' =>  $tab_menu
        ]);

    }
    
    public function registrar(Request $request,$id, $ruta)
    {

        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();       
        
        $tab_proceso = tab_ruta::select( 't01.de_proceso','tab_ruta.id_persona')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();        
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();
        
        $tab_consulta = tab_consulta::where('id_ruta', '=', $ruta)->first(); 
        
        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'cedula','nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->where('telemedicina.tab_persona.id', '=', $tab_proceso->id_persona)
        ->first();        
        
        if(!$tab_consulta){    
            
        return View::make('consulta.registrar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_solicitud' => $tab_solicitud,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_persona' => $tab_persona
        ]);            
            
        }else{
        
        return View::make('consulta.editar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_consulta' => $tab_consulta,
          'tab_proceso' => $tab_proceso,
          'tab_solicitud' => $tab_solicitud,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_persona' => $tab_persona
        ]);            
            
        }    
        
        

    }    
    
    public function editar(Request $request,$id)
    {

        $tab_institucion = tab_institucion::orderBy('id','asc')
        ->get();
        
        $tab_consulta = tab_consulta::where('id', '=', $id)->first();
             
        
         $tab_menu = DB::table('tab_menu')
            ->select('co_menu', 'tx_menu', 'tx_href','co_padre','in_padre','icono','in_padre')
            ->orderBy('co_menu', 'asc')
            ->get();        
        

        return View::make('consulta.editar')->with([
            'tab_consulta'  => $tab_consulta,
            'tab_institucion'  => $tab_institucion,
            'tab_menu' =>  $tab_menu            
        ]);

    }    

  public function clasificacion( Request $request)
  {

        $id_tab_red_social        = $request->red_social;

        $tab_clasificacion = tab_clasificacion::select( 'id','de_clasificacion','de_responsable', 'in_activo', 'de_email')
        ->where('in_activo', '=', true)
        ->where('id_tab_red_social', '=', $id_tab_red_social)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_clasificacion
		)); 

  }     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request, $id = NULL)
    {

        DB::beginTransaction();
        
        if($id!=''||$id!=null){

        $validador = Validator::make( $request->all(), tab_consulta::$validarCrear);
        if ($validador->fails()) {
            return Redirect::back()->withErrors( $validador)->withInput( $request->all());
        }

        try {

            $fecha = $request->get('fecha');
            $fecha_covid = $request->get('fecha_covid');
            $fecha_vacuna = $request->get('fecha_vacuna');            
            $tabla = tab_consulta::find($id);
            //$tabla->fe_consulta = $fecha;
            $tabla->fe_covid = $fecha_covid;
            $tabla->fe_vacuna = $fecha_vacuna;
            $tabla->id_persona = $request->id_persona;            
            
            if (array_key_exists('diabetes', $request->all())) {
                $tabla->diabetes = true;
            }else{
                $tabla->diabetes = false;
            } 
            if (array_key_exists('fumador', $request->all())) {
                $tabla->fumador = true;
            }else{
                $tabla->fumador = false;
            }
            if (array_key_exists('obesidad', $request->all())) {
                $tabla->obesidad = true;
            }else{
                $tabla->obesidad = false;
            }
            if (array_key_exists('cancer', $request->all())) {
                $tabla->cancer = true;
            }else{
                $tabla->cancer = false;
            }
            if (array_key_exists('hipertencion', $request->all())) {
                $tabla->hipertension = true;
            }else{
                $tabla->hipertension = false;
            }
            if (array_key_exists('hepatitis', $request->all())) {
                $tabla->hepatitis = true;
            }else{
                $tabla->hepatitis = false;
            }          
            if (array_key_exists('asmatico', $request->all())) {
                $tabla->asmatico = true;
            }else{
                $tabla->asmatico = false;
            } 
            if (array_key_exists('tiroide', $request->all())) {
                $tabla->tiroide = true;
            }else{
                $tabla->tiroide = false;
            } 
            if (array_key_exists('cardiopata', $request->all())) {
                $tabla->cardiopata = true;
            }else{
                $tabla->cardiopata = false;
            }       
            if (array_key_exists('diabetesf', $request->all())) {
                $tabla->diabetesf = true;
            }else{
                $tabla->diabetesf = false;
            } 
            if (array_key_exists('fumadorf', $request->all())) {
                $tabla->fumadorf = true;
            }else{
                $tabla->fumadorf = false;
            }
            if (array_key_exists('obesidadf', $request->all())) {
                $tabla->obesidadf = true;
            }else{
                $tabla->obesidadf = false;
            }
            if (array_key_exists('cancerf', $request->all())) {
                $tabla->cancerf = true;
            }else{
                $tabla->cancerf = false;
            }
            if (array_key_exists('hipertencionf', $request->all())) {
                $tabla->hipertensionf = true;
            }else{
                $tabla->hipertensionf = false;
            }
            if (array_key_exists('hepatitisf', $request->all())) {
                $tabla->hepatitisf = true;
            }else{
                $tabla->hepatitisf = false;
            }          
            if (array_key_exists('asmaticof', $request->all())) {
                $tabla->asmaticof = true;
            }else{
                $tabla->asmaticof = false;
            } 
            if (array_key_exists('tiroidef', $request->all())) {
                $tabla->tiroidef = true;
            }else{
                $tabla->tiroidef = false;
            } 
            if (array_key_exists('cardiopataf', $request->all())) {
                $tabla->cardiopataf = true;
            }else{
                $tabla->cardiopataf = false;
            } 
            if (array_key_exists('covidf', $request->all())) {
                $tabla->covidf = true;
            }else{
                $tabla->covidf = false;
            } 
            if (array_key_exists('tabaco', $request->all())) {
                $tabla->tabaco = true;
            }else{
                $tabla->tabaco = false;
            }
            if (array_key_exists('alcohol', $request->all())) {
                $tabla->alcohol = true;
            }else{
                $tabla->alcohol = false;
            }
            if (array_key_exists('droga', $request->all())) {
                $tabla->droga = true;
            }else{
                $tabla->droga = false;
            }            
            
            if ($request->in_alergico==1) {
                $tabla->in_alergico = true;
            }else{
                $tabla->in_alergico = false;
            }
            if ($request->covid==1) {
                $tabla->covid = true;
            }else{
                $tabla->covid = false;
            } 
            if ($request->vacuna==1) {
                $tabla->vacuna = true;
            }else{
                $tabla->vacuna = false;
            }            
            $tabla->alergico = $request->alergico;
            $tabla->otros = $request->otros;
            $tabla->otrosf = $request->otrosf;
            $tabla->otrosh = $request->otrosh;
            $tabla->medico = $request->medico;
            $tabla->especialidad = $request->especialidad;
            $tabla->de_consulta = $request->informe;
            $tabla->de_diagnostico = $request->diagnostico;
            $tabla->de_tratamiento =  $request->tratamiento;
            $tabla->de_posologia =  $request->posologia;
            
            $tabla->save();
            
            $tab_ruta = tab_ruta::find($request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();             

            DB::commit();
            HelperReporte::generarReporte($tab_ruta->id_tab_solicitud);
            Session::flash('msg_side_overlay', 'Registro editado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->ruta);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
        
        }else{
            
        $validador = Validator::make( $request->all(), tab_consulta::$validarCrear);
        if ($validador->fails()) {
            return Redirect::back()->withErrors( $validador)->withInput( $request->all());
        }

        try {
            
            $fecha = $request->get('fecha');
            $fecha_covid = $request->get('fecha_covid');
            $fecha_vacuna = $request->get('fecha_vacuna');
            $tabla = new tab_consulta;
            $tabla->fe_consulta = $fecha;
            $tabla->fe_covid = $fecha_covid;
            $tabla->fe_vacuna = $fecha_vacuna;
            $tabla->id_persona = $request->id_persona;
            
            if (array_key_exists('diabetes', $request->all())) {
                $tabla->diabetes = true;
            }else{
                $tabla->diabetes = false;
            } 
            if (array_key_exists('fumador', $request->all())) {
                $tabla->fumador = true;
            }else{
                $tabla->fumador = false;
            }
            if (array_key_exists('obesidad', $request->all())) {
                $tabla->obesidad = true;
            }else{
                $tabla->obesidad = false;
            }
            if (array_key_exists('cancer', $request->all())) {
                $tabla->cancer = true;
            }else{
                $tabla->cancer = false;
            }
            if (array_key_exists('hipertencion', $request->all())) {
                $tabla->hipertension = true;
            }else{
                $tabla->hipertension = false;
            }
            if (array_key_exists('hepatitis', $request->all())) {
                $tabla->hepatitis = true;
            }else{
                $tabla->hepatitis = false;
            }          
            if (array_key_exists('asmatico', $request->all())) {
                $tabla->asmatico = true;
            }else{
                $tabla->asmatico = false;
            } 
            if (array_key_exists('tiroide', $request->all())) {
                $tabla->tiroide = true;
            }else{
                $tabla->tiroide = false;
            } 
            if (array_key_exists('cardiopata', $request->all())) {
                $tabla->cardiopata = true;
            }else{
                $tabla->cardiopata = false;
            }       
            if (array_key_exists('diabetesf', $request->all())) {
                $tabla->diabetesf = true;
            }else{
                $tabla->diabetesf = false;
            } 
            if (array_key_exists('fumadorf', $request->all())) {
                $tabla->fumadorf = true;
            }else{
                $tabla->fumadorf = false;
            }
            if (array_key_exists('obesidadf', $request->all())) {
                $tabla->obesidadf = true;
            }else{
                $tabla->obesidadf = false;
            }
            if (array_key_exists('cancerf', $request->all())) {
                $tabla->cancerf = true;
            }else{
                $tabla->cancerf = false;
            }
            if (array_key_exists('hipertencionf', $request->all())) {
                $tabla->hipertensionf = true;
            }else{
                $tabla->hipertensionf = false;
            }
            if (array_key_exists('hepatitisf', $request->all())) {
                $tabla->hepatitisf = true;
            }else{
                $tabla->hepatitisf = false;
            }          
            if (array_key_exists('asmaticof', $request->all())) {
                $tabla->asmaticof = true;
            }else{
                $tabla->asmaticof = false;
            } 
            if (array_key_exists('tiroidef', $request->all())) {
                $tabla->tiroidef = true;
            }else{
                $tabla->tiroidef = false;
            } 
            if (array_key_exists('cardiopataf', $request->all())) {
                $tabla->cardiopataf = true;
            }else{
                $tabla->cardiopataf = false;
            } 
            if (array_key_exists('covidf', $request->all())) {
                $tabla->covidf = true;
            }else{
                $tabla->covidf = false;
            } 
            if (array_key_exists('tabaco', $request->all())) {
                $tabla->tabaco = true;
            }else{
                $tabla->tabaco = false;
            }
            if (array_key_exists('alcohol', $request->all())) {
                $tabla->alcohol = true;
            }else{
                $tabla->alcohol = false;
            }
            if (array_key_exists('droga', $request->all())) {
                $tabla->droga = true;
            }else{
                $tabla->droga = false;
            }            
            
            if ($request->in_alergico==1) {
                $tabla->in_alergico = true;
            }else{
                $tabla->in_alergico = false;
            }
            if ($request->covid==1) {
                $tabla->covid = true;
            }else{
                $tabla->covid = false;
            }
            if ($request->vacuna==1) {
                $tabla->vacuna = true;
            }else{
                $tabla->vacuna = false;
            }            
            $tabla->alergico = $request->alergico;
            $tabla->otros = $request->otros;
            $tabla->otrosf = $request->otrosf;
            $tabla->otrosh = $request->otrosh;            
            $tabla->medico = $request->medico;
            $tabla->especialidad = $request->especialidad;            
            $tabla->de_consulta = $request->informe;
            $tabla->de_diagnostico = $request->diagnostico;
            $tabla->de_tratamiento =  $request->tratamiento;
            $tabla->de_posologia =  $request->posologia;
            $tabla->id_ruta =  $request->ruta;
            
            $tabla->save();

            $tab_ruta = tab_ruta::find($request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();            
            
            DB::commit();
            HelperReporte::generarReporte($tab_ruta->id_tab_solicitud);
            
            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->ruta);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }            
            
        }
        

    
}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function atender( Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_comentario::find( $request->id);
            $tabla->in_atendido = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro atendido con Exito!');
            return Redirect::to('/inicio');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function desatender( Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_comentario::find( $request->id);
            $tabla->in_atendido = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro marcado como desatendido!');
            return Redirect::to('/inicio');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar( Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_consulta::find( $request->id);
            $tabla->in_activo = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/consulta');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }


    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function listapaciente( Request $request)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }

       // $proceso = tab_proceso_usuario::getListaProcesoAsignado(Auth::user()->id);
       // $tramite = tab_solicitud_usuario::getListaTramiteAsignado(Auth::user()->id);age(timestamp ‘2015-02-14’,timestamp ‘1980-08-24’)

        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->where('telemedicina.tab_persona.cedula', '=', $q)->get();

        return View::make('consulta.listapaciente')->with([
          'tab_persona' => $tab_persona,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

       /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function informe( Request $request, $id = NULL)
    {

        $tab_ruta = tab_ruta::where('id','=',$id)->first();

        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'cedula','nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->where('telemedicina.tab_persona.id', '=', $tab_ruta->id_persona)
        ->first();

        $tab_informe = tab_informe::where('id_ruta', '=', $id)->first();

        return View::make('consulta.informe')->with([
          'tab_persona' => $tab_persona,
          'id_ruta'     => $id,
          'tab_informe' => $tab_informe
        ]);
    }

          /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardarInforme( Request $request, $id = NULL )
    {
        DB::beginTransaction();

        $id_informe = $request->id_informe; 

        if($id_informe !=''||$id_informe !=null){              
  
                $validator = Validator::make($request->all(), tab_informe::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors($validator)->withInput($request->all());
                }

                $tab_informe = tab_informe::find($id_informe);
                $tab_informe->id_persona            = $request->id_persona;
                $tab_informe->medico                = $request->medico; 
                $tab_informe->de_informe            = $request->de_informe;
                $tab_informe->de_protocolo_tecnico  = $request->de_protocolo_tecnico;
                $tab_informe->de_conclusion         = $request->de_conclusion;
                $tab_informe->id_ruta               = $request->id_ruta;
                $tab_informe->save();  

                DB::commit();

                $tab_ruta = tab_ruta::find($request->id_ruta);
                $tab_ruta->in_datos = true;
                $tab_ruta->id_instituto    = Session::get('id_instituto');
                $tab_ruta->id_especialidad = Session::get('id_especialidad');
                $tab_ruta->save();

                HelperReporte::generarReporte($tab_ruta->id_tab_solicitud);

                Session::flash('msg_side_overlay', 'Informe resgistrado exitosamente!');
                return Redirect::to('/proceso/ruta/lista/'.$request->id_ruta);

        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_informe::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput($request->all());
                }

                if (isset($_FILES['de_ruta_imagen']) && $_FILES['de_ruta_imagen']['error'] === UPLOAD_ERR_OK) {

                        $fileTmpPath   = $_FILES['de_ruta_imagen']['tmp_name'];
                        $fileName      = $_FILES['de_ruta_imagen']['name'];
                        $fileSize      = $_FILES['de_ruta_imagen']['size'];
                        $fileType      = $_FILES['de_ruta_imagen']['type'];
                        $fileNameCmps  = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));

                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                        $allowedfileExtensions = array('jpg', 'gif','csv','png', 'zip', 'rar','txt', 'xls', 'doc');
                        if (in_array($fileExtension, $allowedfileExtensions)) {
                            $uploadFileDir = base_path().'/public/documentos/imagenes/';
                            $dest_path = $uploadFileDir . $newFileName;
                             
                            if(move_uploaded_file($fileTmpPath, $dest_path))
                            {
                              $message ='File is successfully uploaded.';
                            }
                            else
                            {
                                return Redirect::back()->withErrors([
                                    'da_alert_form' => "El archivo cargado no es compatible"
                                ])->withInput( $request->all());
                            }
                        }
                }

                $tab_informe = new tab_informe;
                $tab_informe->id_persona            = $request->id_persona;
                $tab_informe->medico                = $request->medico; 
                $tab_informe->de_informe            = $request->de_informe;
                $tab_informe->de_protocolo_tecnico  = $request->de_protocolo_tecnico;
                $tab_informe->de_conclusion         = $request->de_conclusion;
                $tab_informe->id_ruta               = $request->id_ruta;
                $tab_informe->save();


                $tab_ruta = tab_ruta::find( $request->id_ruta);
                $tab_ruta->in_datos = true;
                $tab_ruta->id_instituto    = Session::get('id_instituto');
                $tab_ruta->id_especialidad = Session::get('id_especialidad');
                $tab_ruta->save();

                

                HelperReporte::generarReporte($tab_ruta->id_tab_solicitud);

                DB::commit();

                Session::flash('msg_side_overlay', 'Informe resgistrado exitosamente!');
                return Redirect::to('/proceso/ruta/lista/'.$request->id_ruta);

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
        }
    }

   

}
