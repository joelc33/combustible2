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
    <form action="{{ URL::to('telemedicina/persona/guardar').'/'.$data->id }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('telemedicina/persona/lista') }}">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Paciente</li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
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

                            <div class="col-4">
                                <label for="cedula">Cedula</label>
                                <div class="input-group">
                                    <input type="text" class="form-control {!! $errors->has('cedula') ? 'is-invalid' : '' !!}" id="cedula" name="cedula" placeholder="Cedula..." value="{{ empty(old('cedula'))? $data->cedula : old('cedula') }}" {{ $errors->has('cedula') ? 'aria-describedby="cedula-error" aria-invalid="true"' : '' }}>
                                    @if( $errors->has('cedula') )
                                        <div id="cedula-error" class="invalid-feedback animated fadeIn">{{ $errors->first('cedula') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>                        
                       
                        <div class="form-group">
                            <label for="nombre">Nombres</label>
                            <input type="text" class="form-control {!! $errors->has('nombre') ? 'is-invalid' : '' !!}" id="nombres" name="nombres" placeholder="Nombres..." value="{{ empty(old('nombres'))? $data->nombres : old('nombres') }}" {{ $errors->has('nombres') ? 'aria-describedby="nombres-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('nombres') )
                                <div id="nombres-error" class="invalid-feedback animated fadeIn">{{ $errors->first('nombre') }}</div>
                            @endif
                        </div>                        

                        <div class="form-group">
                            <label for="apellido">Apellidos</label>
                            <input type="text" class="form-control {!! $errors->has('apellido') ? 'is-invalid' : '' !!}" id="apellido" name="apellido" placeholder="Apellidos..." value="{{ empty(old('apellido'))? $data->apellidos : old('apellido') }}" {{ $errors->has('apellido') ? 'aria-describedby="apellido-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('apellido') )
                                <div id="apellido-error" class="invalid-feedback animated fadeIn">{{ $errors->first('apellido') }}</div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select class="custom-select {!! $errors->has('sexo') ? 'is-invalid' : '' !!}" name="sexo" id="sexo" {{ $errors->has('sexo') ? 'aria-describedby="sexo-error" aria-invalid="true"' : '' }}>
                                <option value="0" >Seleccione...</option>
                                @foreach($tab_sexo as $sexo)
                                    <option value="{{ $sexo->id }}" {{ ($sexo->id == $data->id_sexo) ? 'selected' : '' }}>{{ $sexo->de_sexo }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('sexo') )
                                <div id="sexo-error" class="invalid-feedback animated fadeIn">{{ $errors->first('sexo') }}</div>
                            @endif
                        </div>     
                        
                        <div class="form-group form-row">
                            <div class="col-4">
                                <label for="fe_nacimiento">Fecha de Nacimiento</label>
                                <input type="text" class="js-flatpickr form-control bg-white {!! $errors->has('fe_nacimiento') ? 'is-invalid' : '' !!}" id="fe_nacimiento" name="fe_nacimiento" placeholder="d-m-Y" data-date-format="Y-m-d" value="{{ empty(old('fe_nacimiento'))? $data->fe_nacimiento : old('fe_nacimiento') }}" {{ $errors->has('fe_nacimiento') ? 'aria-describedby="fe_nacimiento-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('fe_nacimiento') )
                                    <div id="fe_nacimiento-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fe_nacimiento') }}</div>
                                @endif
                            </div>
                        </div>                        
                        
                        <div class="form-group">
                            <label for="telefono">Telefono</label>
                            <input type="text" class="form-control {!! $errors->has('telefono') ? 'is-invalid' : '' !!}" id="telefono" name="telefono" placeholder="Telefono..." value="{{ empty(old('telefono'))? $data->telefono : old('telefono') }}" {{ $errors->has('telefono') ? 'aria-describedby="telefono-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('telefono') )
                                <div id="telefono-error" class="invalid-feedback animated fadeIn">{{ $errors->first('telefono') }}</div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="municipio">Municipio</label>
                            <select class="custom-select {!! $errors->has('municipio') ? 'is-invalid' : '' !!}" name="municipio" id="municipio" {{ $errors->has('municipio') ? 'aria-describedby="municipio-error" aria-invalid="true"' : '' }}>
                                <option value="0" >Seleccione...</option>
                                @foreach($tab_municipio as $municipio)
                                    <option value="{{ $municipio->id }}" {{ $municipio->id == $data->id_municipio ? 'selected' : '' }}>{{ $municipio->de_municipio }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('municipio') )
                                <div id="municipio-error" class="invalid-feedback animated fadeIn">{{ $errors->first('municipio') }}</div>
                            @endif
                        </div>                        
                        
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control {!! $errors->has('direccion') ? 'is-invalid' : '' !!}" id="direccion" name="direccion" placeholder="Direccion..." value="{{ empty(old('direccion'))? $data->direccion : old('direccion') }}" {{ $errors->has('direccion') ? 'aria-describedby="direccion-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('direccion') )
                                <div id="direccion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('direccion') }}</div>
                            @endif
                        </div>  
                        
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="text" class="form-control {!! $errors->has('correos') ? 'is-invalid' : '' !!}" id="correos" name="correos" placeholder="Correo..." value="{{ empty(old('correos'))? $data->correo : old('correos') }}" {{ $errors->has('correos') ? 'aria-describedby="correos-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('correos') )
                                <div id="correos-error" class="invalid-feedback animated fadeIn">{{ $errors->first('correos') }}</div>
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