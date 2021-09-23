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
    <form action="{{ URL::to('consulta/registrarInforme') }}"  method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_persona" value="{{ $tab_persona->id }}">
                <input type="hidden" name="id_ruta" value="{{ $id_ruta }}">
                <input type="hidden" name="id_informe" value="{{ (empty($tab_informe->id))?'':$tab_informe->id }}">
                <div class="block">
                    <div class="block-header block-header-default">
                        <a class="btn btn-light" href="{{ URL::to('proceso/ruta/lista').'/'.$id_ruta }}">
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

                                     <div class="col-md-3">
                                        <label for="edad">Sexo: </label>{{ $tab_persona->de_sexo}}
                                    </div>
                                </div>

                                <br>

                                 <h2 class="content-heading pt-0">Datos del Informe</h2>

                                 <div class="row">
                                            
                                            <div class="col-md-10">
                                                <div class="form-group form-row">
                                                    <label for="de_protocolo_tecnico">Protocolo Técnico</label>
                                                    <textarea rows="5" important;" class="form-control {!! $errors->has('de_protocolo_tecnico') ? 'is-invalid' : '' !!}" id="de_protocolo_tecnico" name="de_protocolo_tecnico" {{ $errors->has('de_protocolo_tecnico') ? 'aria-describedby="de_protocolo_tecnico-error" aria-invalid="true"' : '' }}> {{ (empty($tab_informe->de_protocolo_tecnico))?old('de_protocolo_tecnico'):$tab_informe->de_protocolo_tecnico}}</textarea>
                                                    @if( $errors->has('de_protocolo_tecnico') )
                                                        <div id="de_protocolo_tecnico-error" class="invalid-feedback animated fadeIn">{{ $errors->first('de_protocolo_tecnico') }}</div>
                                                    @endif
                                                </div>
                                            </div>                                             

                                            <div class="col-md-10">
                                                <div class="form-group form-row">
                                                    <label for="de_informe">Detalle del Informe</label>
                                                    <textarea rows="10" important;" class="form-control {!! $errors->has('de_informe') ? 'is-invalid' : '' !!}" id="de_informe" name="de_informe"  {{ $errors->has('de_informe') ? 'aria-describedby="de_informe-error" aria-invalid="true"' : '' }}> {{ (empty($tab_informe->de_informe))?old('de_informe'):$tab_informe->de_informe }} </textarea>
                                                    @if( $errors->has('de_informe') )
                                                        <div id="de_informe-error" class="invalid-feedback animated fadeIn">{{ $errors->first('de_informe') }}</div>
                                                    @endif
                                                </div>
                                            </div> 
                                            <div class="col-md-10">
                                                <div class="form-group form-row">
                                                    <label for="de_protocolo_tecnico">Conclusiones</label>
                                                    <textarea rows="5" important;" class="form-control {!! $errors->has('de_conclusion') ? 'is-invalid' : '' !!}" id="de_conclusion" name="de_conclusion" {{ $errors->has('de_conclusion') ? 'aria-describedby="de_conclusion-error" aria-invalid="true"' : '' }}>{{ (empty($tab_informe->de_conclusion))?old('de_conclusion'):$tab_informe->de_conclusion }}</textarea>
                                                    @if( $errors->has('de_conclusion') )
                                                        <div id="de_conclusion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('de_conclusion') }}</div>
                                                    @endif
                                                </div>
                                            </div>                                                
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