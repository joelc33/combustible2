@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->

    <!-- Page JS Code -->

@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('autenticar/usuario/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('autenticar/usuario/lista') }}">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
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

                        <h2 class="content-heading">Datos de Cuenta</h2>
                        <div class="form-group">
                            <label for="usuario">Usuario</label>
                            <input type="text" class="form-control {!! $errors->has('usuario') ? 'is-invalid' : '' !!}" id="usuario" name="usuario" placeholder="Usuario..." value="{{ old('usuario') }}" {{ $errors->has('usuario') ? 'aria-describedby="usuario-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('usuario') )
                                <div id="usuario-error" class="invalid-feedback animated fadeIn">{{ $errors->first('usuario') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select class="custom-select {!! $errors->has('rol') ? 'is-invalid' : '' !!}" name="rol" id="rol" {{ $errors->has('rol') ? 'aria-describedby="rol-error" aria-invalid="true"' : '' }}>
                                @foreach($tab_rol as $rol)
                                    <option value="{{ $rol->id }}" {{ $rol->id == old('rol') ? 'selected' : '' }}>{{ $rol->de_rol }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('rol') )
                                <div id="rol-error" class="invalid-feedback animated fadeIn">{{ $errors->first('rol') }}</div>
                            @endif
                        </div>
                        <h2 class="content-heading">Datos de Usuario</h2>
                        <div class="form-group">
                            <label for="nombre">Nombre / Apellido</label>
                            <input type="text" class="form-control {!! $errors->has('nombre') ? 'is-invalid' : '' !!}" id="nombre" name="nombre" placeholder="Nombre / Apellido..." value="{{ old('nombre') }}" {{ $errors->has('nombre') ? 'aria-describedby="nombre-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('nombre') )
                                <div id="nombre-error" class="invalid-feedback animated fadeIn">{{ $errors->first('nombre') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="text" class="form-control {!! $errors->has('correo') ? 'is-invalid' : '' !!}" id="correo" name="correo" placeholder="Correo Electrónico..." value="{{ old('correo') }}" {{ $errors->has('correo') ? 'aria-describedby="correo-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('correo') )
                                <div id="correo-error" class="invalid-feedback animated fadeIn">{{ $errors->first('correo') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="empresa">Organismo / Empresa</label>
                            <select class="custom-select {!! $errors->has('empresa') ? 'is-invalid' : '' !!}" name="empresa" id="empresa" {{ $errors->has('empresa') ? 'aria-describedby="empresa-error" aria-invalid="true"' : '' }}>
                                @foreach($tab_empresa as $empresa)
                                    <option value="{{ $empresa->id }}" {{ $empresa->id == old('empresa') ? 'selected' : '' }}>{{ $empresa->nb_empresa }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('empresa') )
                                <div id="empresa-error" class="invalid-feedback animated fadeIn">{{ $errors->first('empresa') }}</div>
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