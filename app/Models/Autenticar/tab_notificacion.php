<?php

namespace App\Models\Autenticar;
//*******agregar esta linea******//
use Carbon\Carbon;
use App;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_notificacion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_notificacion';

    protected function getTimeAgo($date)
    {
        Carbon::setLocale(App::getLocale());
        return Carbon::parse($date)->diffForHumans();
    }
}
