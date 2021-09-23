<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_usuario;
use App\Models\Autenticar\tab_notificacion;
use View;
use Redirect;
use Validator;
use Response;
use Crypt;
use Session;
use DB;
use Mail;
use Auth;
use Config;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class passwordController extends Controller
{
    /**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	*/
	public function __construct()
	{
		$this->middleware('optimizar');
		$this->middleware('guest');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recuperar()
    {
		// Verificamos si hay sesión activa
		if (Auth::check()){
			// Si tenemos sesión activa mostrará la página de inicio
			return Redirect::to('/inicio');
		}
		// Si no hay sesión activa mostramos el formulario
		return View::make('auth.password');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function enviar( Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

		if (tab_usuario::where('da_email', '=', $request->email)->exists()) {

            DB::beginTransaction();
            try {
 
             $usuario = tab_usuario::where('da_email', $request->email)->first();
             $email = $usuario->da_email;
             $name = $usuario->nb_usuario;

             $cuenta = tab_usuario::find($usuario->id);
             $cuenta->codigo_confirmacion = $codigo_confirmacion = str_random(30);
             $cuenta->save();

             $tab_notificacion = new tab_notificacion;
             $tab_notificacion->id_tab_usuario = $usuario->id;
             $tab_notificacion->de_notificacion = 'Recuperar contraseña de usuario';
             $tab_notificacion->ip_cliente = $request->ip();
             $tab_notificacion->de_icono = 'fa-envelope text-info';
             $tab_notificacion->save();
 
             //DB::commit();

            try{
			    Mail::send(
                    'emails.password', array('codigo_confirmacion' =>$codigo_confirmacion, 'usuario' => $name ), 
				    function($message) use ($email, $name){
                        $message->sender('noreply@test.com');
                        //$message->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
				        $message->to($email, $name)->subject('Gobel - RECUPERACION DE CONTRASEÑA');
				    }
				);
			}catch(\Exception $e){
                /*return Redirect::back()->withErrors([
                    'da_mensaje' => 'Hubo un error al enviar el correo Electronico. Intente de Nuevo.',
                ]);*/
                return Redirect::back()->withErrors([
                    'da_mensaje' => $e->getMessage()
                ])->withInput( $request->all());
            }
            
            DB::commit();

            Session::flash('msg', 'Por favor, consulta tu email para link de recuperacion de contraseña.');
            return Redirect::to('/');
            
        }catch (\Illuminate\Database\QueryException $e)
        {
          DB::rollback();
            /*return Redirect::back()->withErrors([
                'da_mensaje' => 'Error en la transaccion. Intente de Nuevo',
            ]);*/
            return Redirect::back()->withErrors([
                'da_mensaje' => $e->getMessage()
            ])->withInput( $request->all());
        }

        }else{
            return Redirect::back()->withErrors([
                'da_mensaje' => 'El email ingresado no coincide con nuestros registros. Intente de Nuevo',
            ])->withInput();
        }

    }
	
}
