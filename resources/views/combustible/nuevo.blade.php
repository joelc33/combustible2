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

        $(function( $ ){
            $('#placa').on('blur', function(e) {

                $("#resultado").html(
                '<div class="d-flex align-items-center">'+
                    '<strong>Buscando Vehiculo...</strong>'+
                    '<div class="spinner-grow ml-auto" role="status" aria-hidden="true"></div>'+
                '</div>');

                $.ajax({
                    url: '{{ url('combustible/vehiculo/buscar') }}',
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        placa:$("#placa").val()
                    },
                    success: function(data, status, xhr){

                        if(data.data){                            
                            $("#resultado").html('El Vehiculo ya se encuentra registrado');
                            $("#id_propietario").val(data.data.id_propietario);
                            $("#id_vehiculo").val(data.data.id_vehiculo);
                            $("#nombres").val(data.data.nombres);
                            $("#apellido").val(data.data.apellidos);
                            $("#cedula").val(data.data.cedula);
                            $("#gerencia").val(data.data.id_gerencia);
                            $("#nacionalidad").val(data.data.id_nacionalidad);

                           
                            $("#modelo").val(data.data.de_modelo);
                            $("#marca").val(data.data.de_marca);
                            $("#color").val(data.data.de_color);
                        }else{
                            $("#resultado").html(data.msg);
                            $("#id_propietario").val('');
                            $("#id_vehiculo").val('');
                            $("#nombres").val('');
                            $("#apellido").val('');
                            $("#cedula").val('');
                            $("#gerencia").val('');
                            $("#nacionalidad").val('');

                           
                            $("#modelo").val('');
                            $("#marca").val('');
                            $("#color").val('');

                        }
                    }
                });

            });


            $('#cedula').on('blur', function(e) {

                $("#resultado").html(
                '<div class="d-flex align-items-center">'+
                    '<strong>Buscando Persona...</strong>'+
                    '<div class="spinner-grow ml-auto" role="status" aria-hidden="true"></div>'+
                '</div>');

                $.ajax({
                    url: '{{ url('combustible/vehiculo/buscarPersona') }}',
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        cedula:$("#cedula").val()
                    },
                    success: function(data, status, xhr){

                        if(data.data){                            
                            $("#resultado").html('El Vehiculo ya se encuentra registrado');
                            $("#id_propietario").val(data.data.id_propietario);                           
                            $("#nombres").val(data.data.nombres);
                            $("#apellido").val(data.data.apellidos);
                           
                            $("#gerencia").val(data.data.id_gerencia);
                            $("#nacionalidad").val(data.data.id_nacionalidad);

                         
                        }else{
                            $("#resultado").html(data.msg);
                            $("#id_propietario").val('');
                            $("#nombres").val('');
                            $("#apellido").val('');
                            $("#gerencia").val('');
                            $("#nacionalidad").val('');
                        }
                    }
                });

            });
            

        });
    </script>    

    <!-- Page JS Code -->
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('combustible/vehiculo/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="id_vehiculo" id="id_vehiculo">
        <input type="hidden" name="id_propietario" id="id_propietario">
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
                             <h2 class="content-heading pt-0">Datos del Vehiculo</h2>
                        </div>  


                       

                        <div class="form-group form-row">  
                              <div class="col-4">
                                <label for="placa">Placa</label>
                                <input type="text" class="form-control {!! $errors->has('placa') ? 'is-invalid' : '' !!}" id="placa" name="placa" placeholder="placa..." value="{{ old('placa') }}" {{ $errors->has('placa') ? 'aria-describedby="placa-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('placa') )
                                    <div id="placa-error" class="invalid-feedback animated fadeIn">{{ $errors->first('placa') }}</div>
                                @endif
                            </div>                                           
                        
                       
                              <div class="col-6">
                                <label for="marca">Marca</label>
                                <input type="text" class="form-control {!! $errors->has('marca') ? 'is-invalid' : '' !!}" id="marca" name="marca" placeholder="marca..." value="{{ old('marca') }}" {{ $errors->has('marca') ? 'aria-describedby="marca-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('marca') )
                                    <div id="marca-error" class="invalid-feedback animated fadeIn">{{ $errors->first('marca') }}</div>
                                @endif
                            </div>                                           
                        </div> 
                    

                        <div class="form-group form-row">   
                             <div class="col-6">
                                <label for="modelo">modelo</label>
                                <input type="text" class="form-control {!! $errors->has('modelo') ? 'is-invalid' : '' !!}" id="modelo" name="modelo" placeholder="modelo..." value="{{ old('modelo') }}" {{ $errors->has('modelo') ? 'aria-describedby="modelo-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('modelo') )
                                    <div id="modelo-error" class="invalid-feedback animated fadeIn">{{ $errors->first('modelo') }}</div>
                                @endif
                            </div>                                           
                        
                              <div class="col-6">
                                <label for="color">Color</label>
                                <input type="text" class="form-control {!! $errors->has('color') ? 'is-invalid' : '' !!}" id="color" name="color" placeholder="color..." value="{{ old('color') }}" {{ $errors->has('color') ? 'aria-describedby="color-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('color') )
                                    <div id="color-error" class="invalid-feedback animated fadeIn">{{ $errors->first('color') }}</div>
                                @endif
                            </div>                                           
                        </div> 

                        <div class="form-group">
                             <h2 class="content-heading pt-0">Datos del Propietario</h2>
                        </div>  
                         <div class="form-group form-row">  

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

                         <label for="gerencia">Gerencia</label>
                                <select class="custom-select {!! $errors->has('gerencia') ? 'is-invalid' : '' !!}" name="gerencia" id="gerencia" {{ $errors->has('gerencia') ? 'aria-describedby="gerencia-error" aria-invalid="true"' : '' }}>
                                    <option value="0" >Seleccione...</option>
                                    @foreach($tab_gerencia as $gerencia)
                                        <option value="{{ $gerencia->id }}" {{ $gerencia->id == old('gerencia') ? 'selected' : '' }}>{{ $gerencia->de_gerencia }}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('gerencia') )
                                    <div id="gerencia-error" class="invalid-feedback animated fadeIn">{{ $errors->first('gerencia') }}</div>
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