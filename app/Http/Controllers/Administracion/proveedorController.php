<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_proveedor;
use gobela\Models\Configuracion\tab_documento;
use gobela\Models\Administracion\tab_tipo_proveedor;
use gobela\Models\Administracion\tab_tipo_residencia_proveedor;
use gobela\Models\Administracion\tab_clasificacion_proveedor;
use gobela\Models\Administracion\tab_iva_retencion;
use gobela\Models\Administracion\tab_ramo;
use gobela\Models\Administracion\tab_ramo_proveedor;
use gobela\Models\Administracion\tab_estado;
use gobela\Models\Administracion\tab_municipio;
use gobela\Models\Administracion\tab_retencion;
use gobela\Models\Administracion\tab_tipo_retencion;
use gobela\Models\Administracion\tab_retencion_proveedor;
use View;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class proveedorController extends Controller
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
    public function lista( Request $request)
    {
        $sortBy = 'administracion.tab_proveedor.id';
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

        $tab_proveedor = tab_proveedor::select( 'administracion.tab_proveedor.id','administracion.tab_proveedor.nu_codigo','t01.de_inicial', 'administracion.tab_proveedor.nu_documento', 'administracion.tab_proveedor.de_proveedor', 'administracion.tab_proveedor.in_activo', 'administracion.tab_proveedor.created_at', 'administracion.tab_proveedor.updated_at')
        ->join('configuracion.tab_documento as t01','t01.id','=','administracion.tab_proveedor.id_tab_documento')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.proveedor.lista')->with([
          'tab_proveedor' => $tab_proveedor,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function nuevo()
    {
        $data = array( "id" => null);
        
        $tab_documento = tab_documento::select( 'id','de_inicial', 'tx_documento', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();       
        
        $tab_tipo_proveedor = tab_tipo_proveedor::select( 'id','de_tipo_proveedor', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_tipo_residencia_proveedor = tab_tipo_residencia_proveedor::select( 'id','de_tipo_residencia_proveedor', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        
        
        $tab_clasificacion_proveedor = tab_clasificacion_proveedor::select( 'id','de_clasificacion_proveedor', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_iva_retencion = tab_iva_retencion::select( 'id','nu_iva_retencion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();       
        
        $tab_estado = tab_estado::select( 'id','de_estado', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        

        return View::make('administracion.proveedor.nuevo')->with([
            'data'  => $data,
            'tab_documento'  => $tab_documento,
            'tab_tipo_proveedor'  => $tab_tipo_proveedor,
            'tab_tipo_residencia_proveedor'  => $tab_tipo_residencia_proveedor,
            'tab_clasificacion_proveedor'  => $tab_clasificacion_proveedor,
            'tab_iva_retencion'  => $tab_iva_retencion,
            'tab_estado'  => $tab_estado
        ]);
    }
    
  public function municipio( Request $request)
  {

        $id_tab_estado        = $request->estado;

        $tab_municipio = tab_municipio::select( 'id','de_municipio', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tab_estado', '=', $id_tab_estado)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_municipio
		)); 

  }    

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {

        $tab_documento = tab_documento::select( 'id','de_inicial', 'tx_documento', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();       
        
        $tab_tipo_proveedor = tab_tipo_proveedor::select( 'id','de_tipo_proveedor', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_tipo_residencia_proveedor = tab_tipo_residencia_proveedor::select( 'id','de_tipo_residencia_proveedor', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        
        
        $tab_clasificacion_proveedor = tab_clasificacion_proveedor::select( 'id','de_clasificacion_proveedor', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_iva_retencion = tab_iva_retencion::select( 'id','nu_iva_retencion', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
        
        $tab_estado = tab_estado::select( 'id','de_estado', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();     
                  
        
        $filtro_ramo = tab_ramo_proveedor::select('id_tab_ramo')
        ->where('id_tab_proveedor', '=', $id)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_ramo = tab_ramo::select( 'id','de_ramo', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->whereNotIn('id',$filtro_ramo)                
        ->orderby('id','ASC')
        ->get();          
        
        $tab_ramo_proveedor = tab_ramo_proveedor::select( 'administracion.tab_ramo_proveedor.id','t01.de_ramo')
        ->join('administracion.tab_ramo as t01','t01.id','=','administracion.tab_ramo_proveedor.id_tab_ramo')
        ->where('id_tab_proveedor', '=', $id)
        ->orderby('id','ASC')
        ->get();          
        
        $data = tab_proveedor::select( 'id','id_tab_documento', 'nu_documento', 'de_proveedor', 'de_siglas','de_email','de_sitio_web','tx_direccion','nb_representante_legal','nu_cedula_representante_legal','nu_telefono_representante_legal','id_tab_tipo_proveedor','id_tab_tipo_residencia_proveedor','id_tab_clasificacion_proveedor','id_tab_iva_retencion',DB::raw("to_char(fe_registro, 'dd/mm/YYYY') as fe_registro"),DB::raw("to_char(fe_vencimiento, 'dd/mm/YYYY') as fe_vencimiento"),'nu_cuenta_bancaria','tx_observacion','id_tab_estado','id_tab_municipio')             
        ->where('id', '=', $id)
        ->first();        

        $tab_municipio = tab_municipio::select( 'id','de_municipio', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_estado', '=', $data->id_tab_estado)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_tipo_retencion = tab_tipo_retencion::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();      
        
        $tab_retencion_proveedor = tab_retencion_proveedor::select( 'administracion.tab_retencion_proveedor.id','t01.de_retencion')
        ->join('administracion.tab_retencion as t01','t01.id','=','administracion.tab_retencion_proveedor.id_tab_retencion')
        ->where('id_tab_proveedor', '=', $id)
        ->orderby('id','ASC')
        ->get();          
        
        return View::make('administracion.proveedor.editar')->with([
            'data'  => $data,
            'tab_documento'  => $tab_documento,
            'tab_tipo_proveedor'  => $tab_tipo_proveedor,
            'tab_tipo_residencia_proveedor'  => $tab_tipo_residencia_proveedor,
            'tab_clasificacion_proveedor'  => $tab_clasificacion_proveedor,
            'tab_iva_retencion'  => $tab_iva_retencion,
            'tab_estado'  => $tab_estado,
            'tab_municipio'  => $tab_municipio,
            'tab_ramo'  => $tab_ramo,
            'tab_ramo_proveedor'  => $tab_ramo_proveedor,     
            'tab_tipo_retencion'  => $tab_tipo_retencion,
            'tab_retencion_proveedor'  => $tab_retencion_proveedor
        ]);
    }
    
      public function retencion( Request $request)
  {

        $id_tab_tipo_retencion        = $request->tipo_retencion;
        
        $filtro_retencion = tab_retencion_proveedor::select('id_tab_retencion')
        ->where('id_tab_proveedor', '=', $request->proveedor) 
        ->orderby('id','ASC')
        ->get();        

        $tab_retencion = tab_retencion::select( 'id','de_retencion')
        ->where('in_activo', '=', true)
        ->where('id_tab_tipo_retencion', '=', $id_tab_tipo_retencion)
        ->whereNotIn('id',$filtro_retencion)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_retencion
		)); 

  }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_proveedor::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $fe_registro = Carbon::parse($request->fe_registro)->format('Y-m-d');
            $fe_vencimiento = Carbon::parse($request->fe_vencimiento)->format('Y-m-d');           
              
            $tabla = tab_proveedor::find($id);
            $tabla->id_tab_documento = $request->tipo_documento;
            $tabla->nu_documento = $request->codigo;
            $tabla->nu_nit = $request->nu_nit;
            $tabla->de_proveedor = $request->descripcion;
            $tabla->de_siglas = $request->siglas;
            $tabla->de_email = $request->email;
            $tabla->de_sitio_web = $request->web;
            $tabla->tx_direccion = $request->direccion;
            $tabla->nb_representante_legal = $request->nombre_representante;
            $tabla->nu_cedula_representante_legal = $request->cedula_representante;
            $tabla->nu_telefono_representante_legal = $request->telefono_representante;
            $tabla->id_tab_tipo_proveedor = $request->tipo_proveedor;
            $tabla->id_tab_tipo_residencia_proveedor = $request->tipo_residencia_proveedor;
            $tabla->id_tab_clasificacion_proveedor = $request->clasificacion_proveedor;
            $tabla->id_tab_iva_retencion = $request->iva_retencion;            
            $tabla->fe_registro = $fe_registro;
            $tabla->fe_vencimiento = $fe_vencimiento;
            $tabla->nu_cuenta_bancaria = $request->cuenta_bancaria;
            $tabla->tx_observacion = $request->tx_observacion;
            $tabla->id_tab_estado = $request->tab_estado;
            $tabla->id_tab_municipio = $request->tab_municipio;            
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/proveedor/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_proveedor::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
            
              
            $fe_registro = Carbon::parse($request->fe_registro)->format('Y-m-d');
            $fe_vencimiento = Carbon::parse($request->fe_vencimiento)->format('Y-m-d');

            $tabla = new tab_proveedor;
            $tabla->id_tab_documento = $request->tipo_documento;
            $tabla->nu_documento = $request->codigo;
            $tabla->nu_nit = $request->nu_nit;
            $tabla->de_proveedor = $request->descripcion;
            $tabla->de_siglas = $request->siglas;
            $tabla->de_email = $request->email;
            $tabla->de_sitio_web = $request->web;
            $tabla->tx_direccion = $request->direccion;
            $tabla->nb_representante_legal = $request->nombre_representante;
            $tabla->nu_cedula_representante_legal = $request->cedula_representante;
            $tabla->nu_telefono_representante_legal = $request->telefono_representante;
            $tabla->id_tab_tipo_proveedor = $request->tipo_proveedor;
            $tabla->id_tab_tipo_residencia_proveedor = $request->tipo_residencia_proveedor;
            $tabla->id_tab_clasificacion_proveedor = $request->clasificacion_proveedor;
            $tabla->id_tab_iva_retencion = $request->iva_retencion;            
            $tabla->fe_registro = $fe_registro;
            $tabla->fe_vencimiento = $fe_vencimiento;
            $tabla->nu_cuenta_bancaria = $request->cuenta_bancaria;
            $tabla->tx_observacion = $request->tx_observacion;
            $tabla->id_tab_estado = $request->tab_estado;
            $tabla->id_tab_municipio = $request->tab_municipio;            
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/proveedor/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }
    
    public function guardarRetencion(Request $request)
    {
        DB::beginTransaction();

          $validador = Validator::make( $request->all(), tab_retencion_proveedor::$validarCrear);
          if ($validador->fails()) {
              Session::flash('msg_alerta_retencion', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_retencion_proveedor;
            $tabla->id_tab_retencion = $request->retencion;
            $tabla->id_tab_proveedor = $request->proveedor;           
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Retencion Asociada con Exito!');
            return Redirect::to('/administracion/proveedor/editar/'.$request->proveedor);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }  
    
    public function guardarRamo(Request $request)
    {
        DB::beginTransaction();

          $validador = Validator::make( $request->all(), tab_ramo_proveedor::$validarCrear);
          if ($validador->fails()) {
              Session::flash('msg_alerta', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_ramo_proveedor;
            $tabla->id_tab_ramo = $request->ramos;
            $tabla->id_tab_proveedor = $request->proveedor;           
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Ramo Asociado con Exito!');
            return Redirect::to('/administracion/proveedor/editar/'.$request->proveedor);

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

        $tabla = tab_ambito::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/proveedor/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function eliminarRamo( Request $request)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_ramo_proveedor::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Ramo borrado con Exito!');
        return Redirect::to('/administracion/proveedor/editar/'.$tabla->id_tab_proveedor);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function eliminarRetencion( Request $request)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_retencion_proveedor::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Retencion borrada con Exito!');
        return Redirect::to('/administracion/proveedor/editar/'.$tabla->id_tab_proveedor);

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
    public function deshabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_proveedor::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/proveedor/lista');

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
    public function habilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_proveedor::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/proveedor/lista');

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
    public function buscar(Request $request)
    {

      if (tab_proveedor::where('id_tab_documento', '=', $request->tipo_documento)
      ->where('nu_documento', '=', $request->documento)
      ->exists()) {

        $tab_proveedor = tab_proveedor::where('id_tab_documento', '=', $request->tipo_documento)
        ->where('nu_documento', '=', $request->documento)
        ->first()->toArray();

        $response['success']  = 'true';
        $response['data']  = $tab_proveedor;

        return Response::json($response, 200);

      }else{

        $response['success']  = 'false';
        $response['data']  = '';
        $response['msg']  = 'Proveedor no Encontrado!';

        return Response::json($response, 200);

      }

    }
}
