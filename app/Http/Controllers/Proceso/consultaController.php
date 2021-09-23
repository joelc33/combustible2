<?php

namespace App\Http\Controllers\Proceso;
//*******agregar esta linea******//
use App\Models\Proceso\tab_persona;
use App\Models\Configuracion\tab_tipo_informe;
use App\Models\Teleconsulta\tab_informe;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class consultaController extends Controller
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
    public function informe( Request $request, $id = NULL)
    {

        //$tab_tipo_informe =  tab_tipo_informe::orderBy('de_tipo_informe','asc')->get();

        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->leftjoin('tab_informe as t02', 'telemedicina.tab_persona.id', '=', 't02.id_persona')
        ->where('telemedicina.tab_persona.id', '=', $id)
        ->where('t02.id_ruta', '=', $id)
        ->first();

        return View::make('consulta.informe')->with([
          'tab_persona' => $tab_persona,
          'tab_tipo_informe'=> $tab_tipo_informe
        ]);


    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function historicoInforme( Request $request, $id = NULL )
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

        $tab_persona = tab_informe::select('telemedicina.tab_persona.id', 'nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"))
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->where('telemedicina.tab_persona.id', '=', $q)->get();

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
        ->where('telemedicina.tab_persona.id', '=', $q)->get();

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
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardarInforme( Request $request, $id = NULL )
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){
  
             
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_informe::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors($validator)->withInput( $request->all());
                }

              /*  if (isset($_FILES['de_ruta_imagen']) && $_FILES['de_ruta_imagen']['error'] === UPLOAD_ERR_OK) {

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
                }*/

                $tab_informe = new tab_informe;
                $tab_informe->id_persona            = $request->id_persona;
                $tab_informe->medico                = $request->medico; 
                $tab_informe->id_tipo_informe       = $request->id_tipo_informe; 
                $tab_informe->de_informe            = $request->de_informe;
                $tab_informe->de_protocolo_tecnico  = $request->de_protocolo_tecnico;
                $tab_informe->de_conclusion         = $request->de_conclusion;
                $tab_informe->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Informe resgistrado exitosamente!');
                return Redirect::to('/proceso/solicitud/lista');

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
        }
    }




  
}
