@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Page JS Code -->

    <script>
        jQuery(function(){ Dashmix.helpers([ 'flatpickr', 'select2']); });
    </script>
    
<script type="text/javascript">
$(function () {
             

    });
</script>    
    
@endsection

@section('content')


<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('telemedicina/registrarConsulta') }}"  method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="solicitud" value="{{ $solicitud }}">
        <input type="hidden" name="ruta" value="{{ $ruta }}">  
        <input type="hidden" name="id_persona" value="{{ $tab_proceso->id_persona }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('proceso/ruta/lista').'/'.$solicitud }}">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">{{$tab_tipo_solicitud->de_solicitud}}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tab_solicitud->nu_solicitud }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tab_proceso->de_proceso }}</li>                    
                </ol>
            </nav>
        </div> 
            </div>
            <div class="block-content">
                <div class="row justify-content-center push">
                    <div class="col-md-10">

                        {{--
                        @if (count($errors) > 0)
                            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                                <div class="flex-fill mr-3">
                                    <p class="mb-0">Hay problemas con su validacion!</p>
                                </div>
                                <div class="flex-00-auto">
                                    <i class="fa fa-fw fa-times-circle"></i>
                                </div>
                            </div>
                        @endif
                        --}}
                        @if( $errors->has('da_alert_form') )
                        <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                            <div class="flex-fill mr-3">
                                <p class="mb-0">{{ $errors->first('da_alert_form') }}</p>
                            </div>
                        </div>
                        @endif
                        
                     <h2 class="content-heading pt-0">Datos del Paciente</h2>

                     <div class="row">
                        <div class="col-md-3">
                            <label for="cedula">Cédula:</label> {{ $tab_persona->cedula }}
                        </div>                         

                        <div class="col-md-7">
                            <label for="nombre">Nombre y Apellido: </label> {{ $tab_persona->nombres.' '. $tab_persona->apellidos}}
                        </div>

                        <div class="col-md-3">
                            <label for="edad">Edad: </label> {{ $tab_persona->edad}} años
                        </div>

                         <div class="col-md-3">
                            <label for="edad">Sexo: </label> {{ $tab_persona->de_sexo}}
                        </div>
                    </div>

                    <br>                        
                        <h2 class="content-heading pt-0">Datos de la Consulta</h2>
                        <div class="form-group form-row">
                            <div class="col-8">
                                <label for="fecha">Fecha</label>
                                <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha') ? 'is-invalid' : '' !!}" id="fecha" name="fecha" placeholder="d-m-Y HH:MM" data-date-format="Y-m-d H:i" data-enable-time="true" value="{{ empty(old('fecha'))? date('Y-m-d H:i') : old('fecha') }}" {{ $errors->has('fecha') ? 'aria-describedby="fecha-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('fecha') )
                                    <div id="fecha-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fecha') }}</div>
                                @endif
                            </div>

                        </div>                        

                <label for="correo">Antecedentes Patologicos de Paciente</label>   
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="diabetes" id="diabetes" @if ( old('diabetes')) checked @endif>
                                <label for="daily" title="Diariamente">
                                    Diabetes
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="obesidad" id="obesidad" @if ( old('obesidad')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Obesidad
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="cancer" id="cancer" @if ( old('cancer')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Cáncer
                                </label>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="hipertencion" id="hipertencion" @if ( old('hipertencion')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Hipertensión
                                </label>
                            </div>
                        </div>
                    </div>    
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="hepatitis" id="hepatitis" @if ( old('hepatitis')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Hepatitis
                                </label>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="asmatico" id="asmatico" @if ( old('asmatico')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Asma
                                </label>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="tiroide" id="tiroide" @if ( old('tiroide')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Tiroide
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="cardiopata" id="cardiopata" @if ( old('cardiopata')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Cardiopatía
                                </label>
                            </div>
                        </div>
                    </div>                       
                    <div class="col-md-6">
                        <div class="form-group form-row">
                            <input type="text" class="form-control {!! $errors->has('otros') ? 'is-invalid' : '' !!}" id="otros" name="otros" placeholder="Otros..." value="{{ old('otros') }}" {{ $errors->has('otros') ? 'aria-describedby="otros-error" aria-invalid="true"' : '' }}>
                        </div>
                    </div>                     
                    
                </div>                        

                <label for="correo">Antecedentes Patologicos de Familiares</label>   
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="diabetesf" id="diabetesf" @if ( old('diabetesf')) checked @endif>
                                <label for="daily" title="Diariamente">
                                    Diabetes
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="obesidadf" id="obesidadf" @if ( old('obesidadf')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Obesidad
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="cancerf" id="cancerf" @if ( old('cancerf')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Cáncer
                                </label>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="hipertencionf" id="hipertencionf" @if ( old('hipertencionf')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Hipertensión
                                </label>
                            </div>
                        </div>
                    </div>    
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="hepatitisf" id="hepatitisf" @if ( old('hepatitisf')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Hepatitis
                                </label>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="asmaticof" id="asmaticof" @if ( old('asmaticof')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Asma
                                </label>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="tiroidef" id="tiroidef" @if ( old('tiroidef')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Tiroide
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="cardiopataf" id="cardiopataf" @if ( old('cardiopataf')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Cardiopatía
                                </label>
                            </div>
                        </div>
                    </div>                      
                    <div class="col-md-6">
                        <div class="form-group form-row">
                            <input type="text" class="form-control {!! $errors->has('otrosf') ? 'is-invalid' : '' !!}" id="otrosf" name="otrosf" placeholder="Otros..." value="{{ old('otrosf') }}" {{ $errors->has('otrosf') ? 'aria-describedby="otrosf-error" aria-invalid="true"' : '' }}>
                        </div>
                    </div>                   
                </div>
                 <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-block">Es alergico algun medicamento</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline1" name="in_alergico" value="1">
                                <label class="form-check-label" for="example-radios-inline1">Si</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline2" name="in_alergico"  value="2">
                                <label class="form-check-label" for="example-radios-inline2">no</label>
                            </div>
                            </div>
                         </div>
                        <div class="col-md-6">
                        <div class="form-group form-row">
                            <input type="text" class="form-control {!! $errors->has('alergico') ? 'is-invalid' : '' !!}" id="alergico" name="alergico" placeholder="..." value="{{ old('alergico') }}" {{ $errors->has('alergico') ? 'aria-describedby="alergico-error" aria-invalid="true"' : '' }}>
                        </div>
                        </div>
                                         
                </div>      
                
                <label for="correo">Hábitos Tóxicos</label>   
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="tabaco" id="tabaco" @if ( old('tabaco')) checked @endif>
                                <label for="daily" title="Diariamente">
                                    Tabaco
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="alcohol" id="obesidad" @if ( old('alcohol')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Alcohol
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="droga" id="droga" @if ( old('droga')) checked @endif>
                                <label for="monthly" title="Mensual">
                                    Droga
                                </label>
                            </div>
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="form-group form-row">
                            <input type="text" class="form-control {!! $errors->has('otrosh') ? 'is-invalid' : '' !!}" id="otrosh" name="otrosh" placeholder="Otros..." value="{{ old('otrosh') }}" {{ $errors->has('otrosh') ? 'aria-describedby="otrosh-error" aria-invalid="true"' : '' }}>
                        </div>
                    </div>                     
                    
                </div>  
                
                <label for="vacunacion">Vacunación</label>
                                 <div class="row">
                     <div class="col-md-2">
                        <div class="form-group">
                            <label class="d-block">Covid-19</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline1" name="covid" value="1">
                                <label class="form-check-label" for="example-radios-inline1">Si</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline2" name="covid"  value="2">
                                <label class="form-check-label" for="example-radios-inline2">no</label>
                            </div>
                            </div>
                         </div>
                        <div class="col-md-3">
                        <div class="form-group form-row">
                        <label for="fecha">Fecha Covid</label>
                        <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha_covid') ? 'is-invalid' : '' !!}" id="fecha_covid" name="fecha_covid" placeholder="d-m-Y HH:MM" data-date-format="Y-m-d H:i" data-enable-time="true" value="{{ old('fecha_covid') }}" {{ $errors->has('fecha_covid') ? 'aria-describedby="fecha_covid-error" aria-invalid="true"' : '' }}>
                        </div>
                        </div>
                     <div class="col-md-2"></div>
                         
                         <div class="col-md-2">
                        <div class="form-group">
                            <label class="d-block">Vacuna</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline1" name="vacuna" value="1">
                                <label class="form-check-label" for="example-radios-inline1">Si</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="example-radios-inline2" name="vacuna"  value="2">
                                <label class="form-check-label" for="example-radios-inline2">no</label>
                            </div>
                            </div>
                         </div>
                        <div class="col-md-3">
                        <div class="form-group form-row">
                        <label for="fecha">Fecha Vacuna</label>
                        <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha_vacuna') ? 'is-invalid' : '' !!}" id="fecha_vacuna" name="fecha_vacuna" placeholder="d-m-Y HH:MM" data-date-format="Y-m-d H:i" data-enable-time="true" value="{{ old('fecha_vacuna') }}" {{ $errors->has('fecha_vacuna') ? 'aria-describedby="fecha_vacuna-error" aria-invalid="true"' : '' }}>
                        </div>
                        </div>                                         
                </div>
                

                
                        <div class="form-group form-row">
                            <label for="informe">Motivo de la Consulta</label>
                            <textarea class="form-control {!! $errors->has('informe') ? 'is-invalid' : '' !!}" id="informe" name="informe" rows="3" placeholder="Motivo.." {{ $errors->has('informe') ? 'aria-describedby="informe-error" aria-invalid="true"' : '' }}>{{ old('informe') }}</textarea>
                            @if( $errors->has('informe') )
                                <div id="informe-error" class="invalid-feedback animated fadeIn">{{ $errors->first('informe') }}</div>
                            @endif
                        </div>
                
                        <div class="form-group form-row">
                            <label for="diagnostico">Posible Diagnostico</label>
                            <textarea class="form-control {!! $errors->has('diagnostico') ? 'is-invalid' : '' !!}" id="diagnostico" name="diagnostico" rows="3" placeholder="Posible diagnostico.." {{ $errors->has('diagnostico') ? 'aria-describedby="diagnostico-error" aria-invalid="true"' : '' }}>{{ old('diagnostico') }}</textarea>
                            @if( $errors->has('diagnostico') )
                                <div id="diagnostico-error" class="invalid-feedback animated fadeIn">{{ $errors->first('diagnostico') }}</div>
                            @endif
                        </div>                 

                        <div class="form-group form-row">
                            <label for="tratamiento">Tratamiento</label>
                            <textarea class="form-control {!! $errors->has('tratamiento') ? 'is-invalid' : '' !!}" id="tratamiento" name="tratamiento" rows="3" placeholder="..." {{ $errors->has('tratamiento') ? 'aria-describedby="tratamiento-error" aria-invalid="true"' : '' }}>{{ old('tratamiento') }}</textarea>
                            @if( $errors->has('tratamiento') )
                                <div id="tratamiento-error" class="invalid-feedback animated fadeIn">{{ $errors->first('tratamiento') }}</div>
                            @endif
                        </div> 
                
                        <div class="form-group form-row">
                            <label for="posologia">Posología</label>
                            <textarea class="form-control {!! $errors->has('posologia') ? 'is-invalid' : '' !!}" id="posologia" name="posologia" rows="3" placeholder="..." {{ $errors->has('posologia') ? 'aria-describedby="posologia-error" aria-invalid="true"' : '' }}>{{ old('posologia') }}</textarea>
                            @if( $errors->has('posologia') )
                                <div id="posologia-error" class="invalid-feedback animated fadeIn">{{ $errors->first('posologia') }}</div>
                            @endif
                        </div>                
                
                        <div class="form-group form-row">
                            <label for="medico">Medico</label>
                            <input type="text" class="form-control {!! $errors->has('medico') ? 'is-invalid' : '' !!}" id="medico" name="medico" placeholder="Medico..." value="{{ old('medico') }}" {{ $errors->has('medico') ? 'aria-describedby="medico-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('medico') )
                                <div id="medico-error" class="invalid-feedback animated fadeIn">{{ $errors->first('medico') }}</div>
                            @endif
                        </div>                
                
                        <div class="form-group form-row">
                            <label for="especialidad">Especialidad</label>
                            <input type="text" class="form-control {!! $errors->has('especialidad') ? 'is-invalid' : '' !!}" id="especialidad" name="especialidad" placeholder="Especialidad..." value="{{ old('especialidad') }}" {{ $errors->has('especialidad') ? 'aria-describedby="especialidad-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('especialidad') )
                                <div id="especialidad-error" class="invalid-feedback animated fadeIn">{{ $errors->first('especialidad') }}</div>
                            @endif
                        </div>                
                
                    </div>
                </div>
            </div>
        </div>
        <div class="block-content bg-body-light">
            <div class="row justify-content-center push">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-fw fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </form>
    </div>
    <!-- END New Post -->
</div>
<!-- END Page Content -->

@endsection