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
    <form action="{{ URL::to('proceso/solicitud/guardar') }}"  method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_persona" value="{{ $tab_persona->id_persona }}">
                <input type="hidden" name="id_referir" value="{{ (empty($tab_persona->id_referir))?'':$tab_persona->id_referir }}">
                <div class="block">
                    <div class="block-header block-header-default">
                        <a class="btn btn-light" href="{{ URL::to('proceso/ruta/lista').'/'.$id }}">
                            <i class="fa fa-arrow-left mr-1"></i> Volver
                        </a>
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
                                        <label for="cedula">Cedula:</label>{{ $tab_persona->cedula }}
                                    </div>                         
                                    
                                    <div class="col-md-7">
                                        <label for="nombre">Nombre y Apellido: </label>{{ $tab_persona->nombres.' '. $tab_persona->apellidos}}
                                    </div>

                                    <div class="col-md-3">
                                        <label for="edad">Edad: </label>{{ $tab_persona->edad}} años
                                    </div>

                                     <div class="col-md-8">
                                        <label for="edad">Sexo: </label>{{ $tab_persona->de_sexo}}
                                    </div>

                                    <div class="col-md-10">
                                        <label for="edad">Instituto:  </label>{{ $tab_persona->de_instituto}}
                                    </div>

                                    <div class="col-md-10">
                                        <label for="edad">Especialidad:  </label>{{ $tab_persona->de_especialidad}}
                                    </div>

                                    <div class="col-md-10">
                                        <label for="edad">Observación:  </label>{{ $tab_persona->de_observacion}}
                                    </div>
                                </div>

                                <br>

                                 <h2 class="content-heading pt-0">Datos de la Solicitud</h2>

                                    <div class="form-group form-row">                           
                                        <label for="cedula">Solicitud</label>
                                    </div>
                                    <div class="form-group form-row">
                                        <select class="custom-select {!! $errors->has('solicitud') ? 'is-invalid' : '' !!}" name="solicitud" id="solicitud" {{ $errors->has('solicitud') ? 'aria-describedby="solicitud-error" aria-invalid="true"' : '' }}>
                                            <option value="" >Seleccione...</option>
                                            @foreach($tab_tipo_solicitud as $tipo_solicitud)
                                                <option value="{{ $tipo_solicitud->id }}" {{ ($tipo_solicitud->id == $tab_persona->id_tab_tipo_solicitud) ? 'selected' : '' }}>{{ $tipo_solicitud->de_solicitud}}</option>
                                            @endforeach
                                        </select>
                                        @if( $errors->has('solicitud') )
                                            <div id="solicitud-error" class="invalid-feedback animated fadeIn">{{ $errors->first('solicitud') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group form-row">
                                                    <label for="observacion">Descripción</label>
                                                    <textarea rows="5" important;" class="form-control {!! $errors->has('observacion') ? 'is-invalid' : '' !!}" id="observacion" name="observacion" {{ $errors->has('observacion') ? 'aria-describedby="observacion-error" aria-invalid="true"' : '' }}>{{ (empty($tab_informe->observacion))?old('observacion'):$tab_informe->observacion }}</textarea>
                                                    @if( $errors->has('observacion') )
                                                        <div id="observacion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('observacion') }}</div>
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