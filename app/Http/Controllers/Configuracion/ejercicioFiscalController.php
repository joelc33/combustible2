<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_ejercicio_fiscal;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ejercicioFiscalController extends Controller
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
    public function lista( Request $request)
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

        $tab_ejercicio_fiscal = tab_ejercicio_fiscal::select( 'id', DB::raw("to_char(fe_inicio, 'dd-mm-YYYY') as fe_inicio"), DB::raw("to_char(fe_fin, 'dd-mm-YYYY') as fe_fin"), 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('configuracion.ejercicioFiscal.lista')->with([
          'tab_ejercicio_fiscal' => $tab_ejercicio_fiscal,
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

        return View::make('configuracion.ejercicioFiscal.nuevo')->with([
            'data'  => $data
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_ejercicio_fiscal::select( 'id', DB::raw("to_char(fe_inicio, 'dd-mm-YYYY') as fe_inicio"), DB::raw("to_char(fe_fin, 'dd-mm-YYYY') as fe_fin"), 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('configuracion.ejercicioFiscal.editar')->with([
            'data'  => $data
        ]);
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

            $validador = Validator::make( $request->all(), tab_ejercicio_fiscal::$validarEditar);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = tab_ejercicio_fiscal::find($id);
                $tabla->id = $request->ejercicio;
                $tabla->fe_inicio = $request->fecha_desde;
                $tabla->fe_fin = $request->fecha_hasta;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/configuracion/ejercicio/lista');

            }catch (\Illuminate\Database\QueryException $e)
            {
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }

        }else{

            $validador = Validator::make( $request->all(), tab_ejercicio_fiscal::$validarCrear);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = new tab_ejercicio_fiscal;
                $tabla->id = $request->ejercicio;
                $tabla->fe_inicio = $request->fecha_desde;
                $tabla->fe_fin = $request->fecha_hasta;
                $tabla->in_activo = true;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro creado con Exito!');
                return Redirect::to('/configuracion/ejercicio/lista');

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
    public function deshabilitar( $id)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_ejercicio_fiscal::find( $id);
            $tabla->in_activo = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
            return Redirect::to('/configuracion/ejercicio/lista');

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

            $tabla = tab_ejercicio_fiscal::find( $id);
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
            return Redirect::to('/configuracion/ejercicio/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
