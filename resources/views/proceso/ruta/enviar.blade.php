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
    <form action="{{ URL::to('proceso/solicitud/procesar'). '/'. $data->id }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="solicitud" value="{{ $data->id_tab_solicitud }}">
        <input type="hidden" name="ruta" value="{{ $data->id }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('proceso/solicitud/pendiente') }}">
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

                        <h2 class="content-heading pt-0">Formulario: Cambiar Estatus</h2>

                        <div class="form-group">
                            <label for="de_solicitud">Solicitud</label>
                            <input type="text" readonly class="form-control {!! $errors->has('de_solicitud') ? 'is-invalid' : '' !!}" id="de_solicitud" name="de_solicitud" placeholder="Solicitud..." value="{{ empty(old('de_solicitud'))? $data->de_solicitud : old('de_solicitud') }}" {{ $errors->has('de_solicitud') ? 'aria-describedby="de_solicitud-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('de_solicitud') )
                                <div id="de_solicitud-error" class="invalid-feedback animated fadeIn">{{ $errors->first('de_solicitud') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="proceso">Tarea Actual</label>
                            <input type="text" readonly class="form-control {!! $errors->has('proceso') ? 'is-invalid' : '' !!}" id="proceso" name="proceso" placeholder="Proceso..." value="{{ empty(old('proceso'))? $data->de_proceso : old('proceso') }}" {{ $errors->has('proceso') ? 'aria-describedby="proceso-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('proceso') )
                                <div id="proceso-error" class="invalid-feedback animated fadeIn">{{ $errors->first('proceso') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="observacion">Observacion</label>
                            <textarea class="form-control {!! $errors->has('observacion') ? 'is-invalid' : '' !!}" id="observacion" name="observacion" rows="3" placeholder="Observacion.." {{ $errors->has('observacion') ? 'aria-describedby="observacion-error" aria-invalid="true"' : '' }}>{{ old('observacion') }}</textarea>
                            <div class="form-text text-muted font-size-sm font-italic">Breve Observación del Tramite.</div>
                            @if( $errors->has('observacion') )
                                <div id="observacion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('observacion') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="estatus">Estatus</label>
                            <select class="custom-select {!! $errors->has('estatus') ? 'is-invalid' : '' !!}" name="estatus" id="estatus" {{ $errors->has('estatus') ? 'aria-describedby="estatus-error" aria-invalid="true"' : '' }}>
                                @foreach($tab_estatus as $estatus)
                                    <option value="{{ $estatus->id }}" {{ $estatus->id == old('estatus') ? 'selected' : '' }}>{{ $estatus->de_estatus }}</option>
                                @endforeach
                            </select>
                            @if( $errors->has('estatus') )
                                <div id="estatus-error" class="invalid-feedback animated fadeIn">{{ $errors->first('estatus') }}</div>
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