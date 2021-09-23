@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script>

        jQuery(function(){ Dashmix.helpers([ 'flatpickr', 'select2']); });

    </script>    
    <!-- Page JS Code -->
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('combustible/despacho/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="id_despacho" id="id_despacho">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="javascript:history.back()">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Nuevo Despacho</li>
                    <li class="breadcrumb-item active" aria-current="page">Registro</li>
                </ol>
            </nav>
        </div>                
                {{--<div class="block-options">
                    <div class="custom-control custom-switch custom-control-success">
                        <input type="checkbox" class="custom-control-input" id="dm-post-edit-active" name="dm-post-edit-active" checked>
                        <label class="custom-control-label" for="dm-post-edit-active">Set post as active</label>
                    </div>
                </div>--}}
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
                       
                        <div class="form-group form-row"> 
                          <div id="resultado"></div>
                        </div>
                    

                        <div class="form-group">
                             <h2 class="content-heading pt-0">Datos del Despacho</h2>
                        </div>  
                        
                        <div class="form-group form-row"> 

                             
                            <div class="col-3">
                                    <label for="fecha">Fecha</label>
                                    <input type="text" class="js-flatpickr form-control bg-white {!! $errors->has('fecha') ? 'is-invalid' : '' !!}" id="fecha" name="fecha" placeholder="d-m-Y" data-date-format="d-m-Y" value="{{ old('fecha') }}" {{ $errors->has('fecha') ? 'aria-describedby="fecha-error" aria-invalid="true"' : '' }}>
                                    @if( $errors->has('fecha') )
                                        <div id="fecha-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fecha') }}</div>
                                    @endif
                            </div>
                         

                            <div class="col-8">
                                <label for="estacion_servicio">Estación de Servicio</label>
                                <select class="custom-select {!! $errors->has('estacion_servicio') ? 'is-invalid' : '' !!}" name="estacion_servicio" id="estacion_servicio" {{ $errors->has('estacion_servicio') ? 'aria-describedby="estacion_servicio-error" aria-invalid="true"' : '' }}>
                                    <option value="0" >Seleccione...</option>
                                    @foreach($tab_estacion_servicio as $estacion_servicio)
                                        <option value="{{ $estacion_servicio->id }}" {{ $estacion_servicio->id == old('estacion_servicio') ? 'selected' : '' }}>{{ $estacion_servicio->de_estacion_servicio }}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('estacion_servicio') )
                                    <div id="estacion_servicio-error" class="invalid-feedback animated fadeIn">{{ $errors->first('estacion_servicio') }}</div>
                                @endif
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