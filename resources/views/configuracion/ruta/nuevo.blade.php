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
    <form action="{{ URL::to('configuracion/ruta/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="solicitud" value="{{ $data['id_tab_solicitud'] }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('configuracion/ruta/lista').'/'.$data['id_tab_solicitud'] }}">
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

                        <div class="form-group">
                            <label for="proceso">Proceso</label>
                            <select class="custom-select {!! $errors->has('proceso') ? 'is-invalid' : '' !!}" name="proceso" id="proceso" {{ $errors->has('proceso') ? 'aria-describedby="proceso-error" aria-invalid="true"' : '' }}>
                                @foreach($tab_proceso as $proceso)
                                    <option value="{{ $proceso->id }}" {{ $proceso->id == old('proceso') ? 'selected' : '' }}>{{ $proceso->de_proceso }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('proceso') )
                                <div id="proceso-error" class="invalid-feedback animated fadeIn">{{ $errors->first('proceso') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="orden">N° Orden</label>
                            <input type="text" class="form-control {!! $errors->has('orden') ? 'is-invalid' : '' !!}" id="orden" name="orden" placeholder="N° Orden..." value="{{ old('orden') }}" {{ $errors->has('orden') ? 'aria-describedby="orden-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('orden') )
                                <div id="orden-error" class="invalid-feedback animated fadeIn">{{ $errors->first('orden') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Cargar Datos</label>
                            <div class="custom-control custom-checkbox custom-control-primary mb-1">
                                <input type="checkbox" class="custom-control-input" id="in_datos" name="in_datos" @if (old('in_datos')) checked @endif >
                                <label class="custom-control-label" for="in_datos">¿Requerido?</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="entorno">Entorno</label>
                            <select class="custom-select {!! $errors->has('entorno') ? 'is-invalid' : '' !!}" name="entorno" id="entorno" {{ $errors->has('entorno') ? 'aria-describedby="entorno-error" aria-invalid="true"' : '' }}>
                                @foreach($tab_entorno as $entorno)
                                    <option value="{{ $entorno->id }}" {{ $entorno->id == old('entorno') ? 'selected' : '' }}>{{ $entorno->de_entorno }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('entorno') )
                                <div id="entorno-error" class="invalid-feedback animated fadeIn">{{ $errors->first('entorno') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="controlador">Controlador</label>
                            <input type="text" class="form-control {!! $errors->has('controlador') ? 'is-invalid' : '' !!}" id="controlador" name="controlador" placeholder="Controlador..." value="{{ old('controlador') }}" {{ $errors->has('controlador') ? 'aria-describedby="controlador-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('controlador') )
                                <div id="controlador-error" class="invalid-feedback animated fadeIn">{{ $errors->first('controlador') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="accion">Accion</label>
                            <input type="text" class="form-control {!! $errors->has('accion') ? 'is-invalid' : '' !!}" id="accion" name="accion" placeholder="Accion..." value="{{ old('accion') }}" {{ $errors->has('accion') ? 'aria-describedby="accion-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('accion') )
                                <div id="accion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('accion') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="reporte">Reporte</label>
                            <input type="text" class="form-control {!! $errors->has('reporte') ? 'is-invalid' : '' !!}" id="reporte" name="reporte" placeholder="Reporte..." value="{{ old('reporte') }}" {{ $errors->has('reporte') ? 'aria-describedby="reporte-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('reporte') )
                                <div id="reporte-error" class="invalid-feedback animated fadeIn">{{ $errors->first('reporte') }}</div>
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