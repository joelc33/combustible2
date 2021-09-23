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
    <script>

        jQuery(function(){ Dashmix.helpers([ 'flatpickr', 'select2']); });

        $(function( $ ){
            $('#cedula').on('blur', function(e) {

                $("#resultado").html(
                '<div class="d-flex align-items-center">'+
                    '<strong>Buscando Persona...</strong>'+
                    '<div class="spinner-grow ml-auto" role="status" aria-hidden="true"></div>'+
                '</div>');

                $.ajax({
                    url: '{{ url('telemedicina/persona/buscar') }}',
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        cedula:$("#cedula").val()
                    },
                    success: function(data, status, xhr){

                        if(data.data){                            
                            $("#resultado").html('El Paciente ya se encuentra registrado');
                            $("#persona").val(data.data.id);
                            $("#nombres").val(data.data.nombres);
                            $("#apellido").val(data.data.apellidos);
                            $("#sexo").val(data.data.id_sexo);
                            $("#telefono").val(data.data.telefono);
                            $("#direccion").val(data.data.direccion);
                            $("#correos").val(data.data.correo);
                            $("#correos").val(data.data.correo);
                            $("#municipio").val(data.data.id_municipio);
                            $("#fe_nacimiento").val(data.data.fe_nacimiento);
                            $("#nacionalidad").val(data.data.id_nacionalidad);
                        }else{
                            $("#resultado").html(data.msg);
                            $("#nombres").val('');
                            $("#apellido").val('');
                            $("#sexo").val('');
                            $("#telefono").val('');
                            $("#direccion").val('');
                            $("#correos").val('');
                            $("#persona").val('');
                            $("#municipio").val('');
                            $("#fe_nacimiento").val('');
                            $("#nacionalidad").val('');

                        }
                    }
                });

            });
            

        });
    </script>
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('telemedicina/persona/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('proceso/consulta/listapaciente') }}">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Paciente</li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
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
                        <input type="hidden" id="persona" name="persona" value="{{ old('persona') }}">
                        <div class="form-group form-row"> 
                          <div id="resultado"></div>
                        </div>
                        <div class="form-group form-row">                           
                            <div class="col-4">
                                <label for="nacionalidad">Nacionalidad</label>
                                <select class="custom-select {!! $errors->has('nacionalidad') ? 'is-invalid' : '' !!}" name="nacionalidad" id="nacionalidad" {{ $errors->has('nacionalidad') ? 'aria-describedby="nacionalidad-error" aria-invalid="true"' : '' }}>
                                    <option value="0" >Seleccione...</option>
                                    @foreach($tab_nacionalidad as $nacionalidad)
                                        <option value="{{ $nacionalidad->id }}" {{ $nacionalidad->id == old('nacionalidad') ? 'selected' : '' }}>{{ $nacionalidad->de_nacionalidad }}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('nacionalidad') )
                                    <div id="nacionalidad-error" class="invalid-feedback animated fadeIn">{{ $errors->first('nacionalidad') }}</div>
                                @endif
                            </div>   


                            <div class="col-4">
                                <label for="cedula">Cédula</label>
                                <div class="input-group">
                                    <input type="text" class="form-control {!! $errors->has('cedula') ? 'is-invalid' : '' !!}" id="cedula" name="cedula" placeholder="Cedula..." value="{{ old('cedula') }}" {{ $errors->has('cedula') ? 'aria-describedby="cedula-error" aria-invalid="true"' : '' }}>
<!--                                    <div class="input-group-append">
                                        <span class="input-group-text btn-hapus">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>-->
                                    @if( $errors->has('cedula') )
                                        <div id="cedula-error" class="invalid-feedback animated fadeIn">{{ $errors->first('cedula') }}</div>
                                    @endif
                                </div>
                            </div>
                           
                       

                       
                         </div>                       
                       
                        <div class="form-group">
                            <label for="nombres">Nombres</label>
                            <input type="text" class="form-control {!! $errors->has('nombres') ? 'is-invalid' : '' !!}" id="nombres" name="nombres" placeholder="Nombres..." value="{{ old('nombres') }}" {{ $errors->has('nombres') ? 'aria-describedby="nombres-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('nombres') )
                                <div id="nombres-error" class="invalid-feedback animated fadeIn">{{ $errors->first('nombres') }}</div>
                            @endif
                        </div>                        

                        <div class="form-group">
                            <label for="apellido">Apellidos</label>
                            <input type="text" class="form-control {!! $errors->has('apellido') ? 'is-invalid' : '' !!}" id="apellido" name="apellido" placeholder="Apellidos..." value="{{ old('apellido') }}" {{ $errors->has('apellido') ? 'aria-describedby="apellido-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('apellido') )
                                <div id="apellido-error" class="invalid-feedback animated fadeIn">{{ $errors->first('apellido') }}</div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select class="custom-select {!! $errors->has('sexo') ? 'is-invalid' : '' !!}" name="sexo" id="sexo" {{ $errors->has('sexo') ? 'aria-describedby="sexo-error" aria-invalid="true"' : '' }}>
                                <option value="0" >Seleccione...</option>
                                @foreach($tab_sexo as $sexo)
                                    <option value="{{ $sexo->id }}" {{ $sexo->id == old('sexo') ? 'selected' : '' }}>{{ $sexo->de_sexo }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('sexo') )
                                <div id="sexo-error" class="invalid-feedback animated fadeIn">{{ $errors->first('sexo') }}</div>
                            @endif
                        </div>  
                        
                        <div class="form-group form-row">
                            <div class="col-4">
                                <label for="fe_nacimiento">Fecha de Nacimiento</label>
                                <input type="text" class="js-flatpickr form-control bg-white {!! $errors->has('fe_nacimiento') ? 'is-invalid' : '' !!}" id="fe_nacimiento" name="fe_nacimiento" placeholder="d-m-Y" data-date-format="d-m-Y" value="{{ old('fe_nacimiento') }}" {{ $errors->has('fe_nacimiento') ? 'aria-describedby="fe_nacimiento-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('fe_nacimiento') )
                                    <div id="fe_nacimiento-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fe_nacimiento') }}</div>
                                @endif
                            </div>
                        </div>                         
                        
                        <div class="form-group">
                            <label for="telefono">Telefono</label>
                            <input type="text" class="form-control {!! $errors->has('telefono') ? 'is-invalid' : '' !!}" id="telefono" name="telefono" placeholder="Telefono..." value="{{ old('telefono') }}" {{ $errors->has('telefono') ? 'aria-describedby="telefono-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('telefono') )
                                <div id="telefono-error" class="invalid-feedback animated fadeIn">{{ $errors->first('telefono') }}</div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="municipio">Municipio</label>
                            <select class="custom-select {!! $errors->has('municipio') ? 'is-invalid' : '' !!}" name="municipio" id="municipio" {{ $errors->has('municipio') ? 'aria-describedby="municipio-error" aria-invalid="true"' : '' }}>
                                <option value="0" >Seleccione...</option>
                                @foreach($tab_municipio as $municipio)
                                    <option value="{{ $municipio->id }}" {{ $municipio->id == old('municipio') ? 'selected' : '' }}>{{ $municipio->de_municipio }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('municipio') )
                                <div id="municipio-error" class="invalid-feedback animated fadeIn">{{ $errors->first('municipio') }}</div>
                            @endif
                        </div>                        
                        
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control {!! $errors->has('direccion') ? 'is-invalid' : '' !!}" id="direccion" name="direccion" placeholder="Direccion..." value="{{ old('direccion') }}" {{ $errors->has('direccion') ? 'aria-describedby="direccion-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('direccion') )
                                <div id="direccion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('direccion') }}</div>
                            @endif
                        </div>  
                        
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="text" class="form-control {!! $errors->has('correo') ? 'is-invalid' : '' !!}" id="correos" name="correos" placeholder="Correo..." value="{{ old('correos') }}" {{ $errors->has('correo') ? 'aria-describedby="correo-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('correo') )
                                <div id="correo-error" class="invalid-feedback animated fadeIn">{{ $errors->first('correo') }}</div>
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