<?php

namespace App\Http\Controllers\Proceso;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Configuracion\tab_ruta as tab_configuracion_ruta;
use App\Models\Configuracion\tab_estatus;
use App\Models\Proceso\tab_solicitud;
use App\Models\Proceso\tab_referir;
use App\Models\Telemedicina\tab_persona;
use App\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use App\Models\Configuracion\tab_instituto;
use App\Models\Configuracion\tab_especialidad;
use App\Models\Configuracion\tab_proceso_usuario;
use App\Models\Configuracion\tab_solicitud_usuario;
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
use Illuminate\Container\Container;

class rutaController extends Controller
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
    public function lista( Request $request, $id)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 10;
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

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id', 'nu_solicitud','proceso.tab_solicitud.id_tab_tipo_solicitud','proceso.tab_solicitud.id_persona')
        ->join("proceso.tab_ruta as t01","t01.id_tab_solicitud","=","proceso.tab_solicitud.id")
        ->where('t01.id', '=', $id)
        ->first();               
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();        
        
        $tab_ruta = tab_ruta::select( 'proceso.tab_ruta.id', 'proceso.tab_ruta.id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'proceso.tab_ruta.id_tab_proceso', 'in_actual', 
        'proceso.tab_ruta.in_activo', 'proceso.tab_ruta.created_at', 'proceso.tab_ruta.updated_at', 
        DB::raw("to_char(proceso.tab_ruta.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"),
        DB::raw("proceso.sp_verificar_anexo(proceso.tab_ruta.id) as in_anexo"), 'proceso.tab_ruta.in_reporte')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->with(['estatus', 'proceso', 'usuario'])
        ->where('proceso.tab_ruta.id', '=', $id)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);


        $ubicacion = tab_ruta::select( 'nu_orden')
        ->where('id', '=', $id)
        ->where('in_activo', '=', true)
        ->where('in_actual', '=', true)
        ->first();

        if( $ubicacion->nu_orden > 1){

            return View::make('proceso.ruta.pendiente')->with([
                'tab_ruta' => $tab_ruta,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud,                
                'orderBy' => $orderBy,
                'sortBy' => $sortBy,
                'perPage' => $perPage,
                'columnas' => $columnas,
                'q' => $q,
                'id' => $id
            ]);

        }else{

            return View::make('proceso.ruta.lista')->with([
                'tab_ruta' => $tab_ruta,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud,                  
                'orderBy' => $orderBy,
                'sortBy' => $sortBy,
                'perPage' => $perPage,
                'columnas' => $columnas,
                'q' => $q,
                'id' => $id
            ]);

        }

    }


    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function referir( Request $request, $id)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 10;
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

        $tab_especialidad      = tab_especialidad::get();
        $tab_instituto         = tab_instituto::get();
        $tab_tipo_solicitud    = tab_tipo_solicitud::get();


        $tab_persona = tab_persona::select('telemedicina.tab_persona.id', 'nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"),'de_solicitud')
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->leftjoin('proceso.tab_ruta as t02', 'telemedicina.tab_persona.id', '=', 't02.id_persona')
        ->leftjoin('configuracion.tab_solicitud as t03', 't03.id', '=', 't02.id_tab_tipo_solicitud')
        ->where('t02.id', '=', $id)
        ->first();

        return View::make('proceso.ruta.referir')->with([
                'tab_persona'        => $tab_persona,           
                'orderBy'            => $orderBy,
                'sortBy'             => $sortBy,
                'perPage'            => $perPage,
                'columnas'           => $columnas,
                'tab_especialidad'   => $tab_especialidad,
                'tab_instituto'      => $tab_instituto,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'q'                  => $q,
                'id'                 => $id
        ]);

    }


            /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function expediente( Request $request, $id)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 10;
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


        $tab_persona = tab_persona::select(DB::raw("nombres||' '||apellidos as nombre"))
        ->where('id', '=', $id)->first();
        
        
        $tab_ruta = tab_ruta::select( 'proceso.tab_ruta.id', 'de_solicitud','de_instituto',
        DB::raw("to_char(proceso.tab_ruta.created_at, 'dd/mm/YYYY') as fe_creado"),
        DB::raw("proceso.sp_verificar_anexo(proceso.tab_ruta.id) as in_anexo"), 'proceso.tab_ruta.in_reporte')
        ->join('configuracion.tab_solicitud as t01', 'proceso.tab_ruta.id_tab_tipo_solicitud', '=', 't01.id')
        ->join('configuracion.tab_instituto as t02', 'proceso.tab_ruta.id_instituto', '=', 't02.id')
        ->where('proceso.tab_ruta.id_persona', '=', $id)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        $ubicacion = tab_ruta::select( 'nu_orden')
        ->where('id_tab_solicitud', '=', $id)
        ->where('in_activo', '=', true)
        ->where('in_actual', '=', true)
        ->first();



        return View::make('proceso.ruta.expediente')->with([
                'tab_ruta' => $tab_ruta, 
                'tab_persona'=>$tab_persona,                   
                'orderBy' => $orderBy,
                'sortBy' => $sortBy,
                'perPage' => $perPage,
                'columnas' => $columnas,
                'q' => $q,
                'id' => $id
        ]);

       

    }

            /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function listaVer( Request $request, $id)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 10;
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

        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();               
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();        
        
        $tab_ruta = tab_ruta::select( 'proceso.tab_ruta.id', 'proceso.tab_ruta.id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'proceso.tab_ruta.id_tab_proceso', 'in_actual', 
        'proceso.tab_ruta.in_activo', 'proceso.tab_ruta.created_at', 'proceso.tab_ruta.updated_at', 
        DB::raw("to_char(proceso.tab_ruta.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"),
        DB::raw("proceso.sp_verificar_anexo(proceso.tab_ruta.id) as in_anexo"), 'proceso.tab_ruta.in_reporte')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->with(['estatus', 'proceso', 'usuario'])
        ->where('proceso.tab_ruta.id_tab_solicitud', '=', $id)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('proceso.ruta.listaVer')->with([
            'tab_ruta' => $tab_ruta,
            'tab_tipo_solicitud' => $tab_tipo_solicitud,
            'tab_solicitud' => $tab_solicitud,              
            'orderBy' => $orderBy,
            'sortBy' => $sortBy,
            'perPage' => $perPage,
            'columnas' => $columnas,
            'q' => $q,
            'id' => $id
        ]);

    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function enviar( $id)
    {
 
        $data = tab_ruta::select( 'proceso.tab_ruta.id', 'id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'proceso.tab_ruta.id_tab_proceso', 'in_actual', 'de_proceso', 'de_solicitud',
        'proceso.tab_ruta.in_activo', 'proceso.tab_ruta.created_at', 'proceso.tab_ruta.updated_at', DB::raw("to_char(proceso.tab_ruta.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"))
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->join('configuracion.tab_solicitud as t02', 'proceso.tab_ruta.id_tab_tipo_solicitud', '=', 't02.id')
        ->where('id_tab_solicitud', '=', $id)
        ->where('proceso.tab_ruta.in_activo', '=', true)
        ->where('in_actual', '=', true)
        ->first();

        $tab_estatus = tab_estatus::orderBy('id','asc')
        ->whereIn('id',  [ 2, 3])
        ->get();

        return View::make('proceso.ruta.enviar')->with([
            'data' => $data,
            'tab_estatus' => $tab_estatus
        ]);
    }

        /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function procesar( Request $request)
    {
        DB::beginTransaction();
        try {

            $in_datos_config = tab_configuracion_ruta::getInCargarDatos( $request->solicitud);
            $in_datos_ruta = tab_ruta::getValidarCargarDatos( $request->solicitud);

            if($in_datos_config==true){
                if($in_datos_ruta==true){

                    $tab_ruta = tab_ruta::find( tab_ruta::getRuta($request->solicitud));
                    $tab_ruta->id_tab_estatus = $request->estatus;
                    $tab_ruta->de_observacion = $request->observacion;
                    $tab_ruta->in_definitivo = true;
                    $tab_ruta->id_tab_usuario = Auth::user()->id;
                    $tab_ruta->save();

                    DB::commit();

                    Session::flash('msg_side_overlay', 'La solicitud se proceso exitosamente!');
                    return Redirect::to('/proceso/solicitud/pendiente');

                }else{

                    return Redirect::back()->withErrors([
                        'da_alert_form' => 'No es posible procesar la solicitud, ya que se debe cargar los datos!'
                    ])->withInput( $request->all());

                }
            }else{

                $tab_ruta = tab_ruta::find( tab_ruta::getRuta($request->solicitud));
                $tab_ruta->id_tab_estatus = $request->estatus;
                $tab_ruta->de_observacion = $request->observacion;
                $tab_ruta->in_definitivo = true;
                $tab_ruta->id_tab_usuario = Auth::user()->id;
                $tab_ruta->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'La solicitud se proceso exitosamente!');
                return Redirect::to('/proceso/solicitud/pendiente');

            }

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function datos( Request $request, $id)
    {
        $tab_ruta = tab_ruta::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'id_tab_proceso', 'in_actual', 
        'in_activo', 'created_at', 'updated_at', DB::raw("to_char(created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"))
        ->where('id', '=', $id)
        ->where('in_activo', '=', true)
        ->where('in_actual', '=', true)
        ->first();


        $data = tab_ruta::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_solicitud', 'id_tab_usuario', 
        'de_observacion', 'id_tab_estatus', 'nu_orden', 'id_tab_proceso', 'in_actual', 
        'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        $tab_configuracion_ruta = tab_configuracion_ruta::select( 'id_tab_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
        'nb_accion', 'nb_reporte', 'de_variable', DB::raw('de_variable||nb_controlador as de_controlador'))
        ->join('configuracion.tab_entorno as t01', 'configuracion.tab_ruta.id_tab_entorno', '=', 't01.id')
        ->where('id_tab_solicitud', '=', $data->id_tab_tipo_solicitud)
        ->where('id_tab_proceso', '=', $data->id_tab_proceso)
        ->first();

        $namespace = self::getAppNamespace();

        $entorno = $namespace.$tab_configuracion_ruta->de_controlador;

        return (new  $entorno)->{$tab_configuracion_ruta->nb_accion}( $request, $id, $tab_ruta->id);

    }

    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace()."Http\Controllers";
    }


          /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function guardarReferido(Request $request)
    {
        DB::beginTransaction();
        try {

           
            try {

                    $validator= Validator::make($request->all(), tab_referir::$validarEditar);

                    if ($validator->fails()){
                        return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                    }

                    if(!empty($request->id))
                        $tab_referir = tab_referir::find($request->id);
                    else
                        $tab_referir = new tab_referir;

                    $tab_referir->id_tab_ruta           = $request->id_ruta;
                    $tab_referir->id_tab_especialidad   = $request->especialidad;
                    $tab_referir->id_tab_instituto      = $request->instituto;
                    $tab_referir->id_tab_usuario        = Auth::user()->id;
                    $tab_referir->de_observacion        = $request->de_observacion;
                    $tab_referir->id_tab_tipo_solicitud = $request->id_tipo_solicitud;
                    $tab_referir->save();

                    DB::commit();

                    Session::flash('msg_side_overlay', 'La solicitud se proceso exitosamente!');
                    return Redirect::to('/proceso/ruta/lista/'.$request->id_ruta);

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }

               

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
    public function listaReferido( Request $request)
    {
        $sortBy = 'cedula';
        $orderBy = 'asc';
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

        $proceso = tab_proceso_usuario::getListaProcesoAsignado(Auth::user()->id);
        $tramite = tab_solicitud_usuario::getListaTramiteAsignado(Auth::user()->id);
       
        $tab_solicitud = tab_persona::select( 't02.id', 
        'nb_usuario','t01.id_persona',
         DB::raw("to_char(t02.created_at, 'dd/mm/YYYY') as fe_creado"),'nombres','apellidos','cedula',"t01.id as id_ruta","de_especialidad","de_instituto","de_municipio","t02.de_observacion","de_solicitud")        
        ->join('proceso.tab_ruta as t01', 't01.id_persona', '=', 'telemedicina.tab_persona.id')
        ->join('proceso.tab_referir as t02', 't02.id_tab_ruta', '=', 't01.id')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't02.id_tab_usuario')
        ->leftjoin('configuracion.tab_instituto as t04', 't04.id', '=', 't02.id_tab_instituto')
        ->leftjoin('configuracion.tab_especialidad as t05', 't05.id', '=', 't02.id_tab_especialidad')
        ->leftjoin('configuracion.tab_municipio as t06', 't06.id', '=', 'telemedicina.tab_persona.id_municipio')
         ->leftjoin('configuracion.tab_solicitud as t07', 't07.id', '=', 't02.id_tab_tipo_solicitud')
        ->whereNull('id_solicitud_asignada')
        ->search($q, $sortBy)
        ->orderBy('id', $orderBy)
        ->paginate($perPage);

        return View::make('proceso.ruta.listaReferido')->with([
          'tab_solicitud' => $tab_solicitud,
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
    public function procesarReferido( Request $request,$id)
    {
        $sortBy = 'cedula';
        $orderBy = 'asc';
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

        $tab_tipo_solicitud    = tab_tipo_solicitud::get();

        $tab_persona = tab_persona::select('t03.id as id_referir','telemedicina.tab_persona.id as id_persona', 'nombres', 'apellidos', 'de_sexo', 'telefono', 'direccion', DB::raw("SUBSTRING(cast(age(now(),fe_nacimiento) as varchar),0,3) as edad"),'t03.de_observacion','de_especialidad','de_instituto','t03.id_tab_tipo_solicitud','cedula')
        ->join('configuracion.tab_sexo as t01', 'telemedicina.tab_persona.id_sexo', '=', 't01.id')
        ->leftjoin('proceso.tab_ruta as t02', 'telemedicina.tab_persona.id', '=', 't02.id_persona')
        ->leftjoin('proceso.tab_referir as t03', 't02.id', '=', 't03.id_tab_ruta')
        ->leftjoin('configuracion.tab_solicitud as t04', 't03.id', '=', 't02.id_tab_tipo_solicitud')
        ->leftjoin('configuracion.tab_especialidad as t05', 't05.id', '=', 't03.id_tab_especialidad')
        ->leftjoin('configuracion.tab_instituto as t06', 't06.id', '=', 't03.id_tab_instituto')
        ->where('t03.id', '=', $id)
        ->first();

        return View::make('proceso.ruta.procesarReferido')->with([
          'tab_persona'         => $tab_persona,
          'orderBy'             => $orderBy,
          'sortBy'              => $sortBy,
          'perPage'             => $perPage,
          'columnas'            => $columnas,
          'tab_tipo_solicitud'  => $tab_tipo_solicitud,
          'q'                   => $q,
          'id'                  => $id
        ]);
    }

}
