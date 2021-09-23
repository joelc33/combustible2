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
    <form action="{{ URL::to('configuracion/solicitud/guardar') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('configuracion/solicitud/lista') }}">
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
                            <label for="identificador">N° Identificador</label>
                            <input type="text" class="form-control {!! $errors->has('identificador') ? 'is-invalid' : '' !!}" id="identificador" name="identificador" placeholder="N° Identificador..." value="{{ old('identificador') }}" {{ $errors->has('identificador') ? 'aria-describedby="identificador-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('identificador') )
                                <div id="identificador-error" class="invalid-feedback animated fadeIn">{{ $errors->first('identificador') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripcion</label>
                            <input type="text" class="form-control {!! $errors->has('descripcion') ? 'is-invalid' : '' !!}" id="descripcion" name="descripcion" placeholder="Descripcion..." value="{{ old('descripcion') }}" {{ $errors->has('descripcion') ? 'aria-describedby="descripcion-error" aria-invalid="true"' : '' }}>
                            @if( $errors->has('descripcion') )
                                <div id="descripcion-error" class="invalid-feedback animated fadeIn">{{ $errors->first('descripcion') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Opción de solicitud</label>
                            <div class="custom-control custom-checkbox custom-control-primary mb-1">
                                <input type="checkbox" class="custom-control-input" id="visible" name="visible" @if (old('visible')) checked @endif >
                                <label class="custom-control-label" for="visible">¿Visible?</label>
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