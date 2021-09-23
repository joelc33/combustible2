@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Page JS Code -->

    <script>
        jQuery(function(){ Dashmix.helpers([ 'flatpickr']); });
    </script>
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('configuracion/ejercicio/guardar').'/'.$data->id }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="{{ URL::to('configuracion/ejercicio/lista') }}">
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

                        <div class="form-group form-row">
                            <div class="col-8">
                                <label for="ejercicio">Ejercicio</label>
                                <input type="text" class="form-control {!! $errors->has('ejercicio') ? 'is-invalid' : '' !!}" id="ejercicio" name="ejercicio" placeholder="Ejercicio..." value="{{ empty(old('ejercicio'))? $data->id : old('ejercicio') }}" {{ $errors->has('ejercicio') ? 'aria-describedby="ejercicio-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('ejercicio') )
                                    <div id="ejercicio-error" class="invalid-feedback animated fadeIn">{{ $errors->first('ejercicio') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group form-row">
                            <div class="col-4">
                                <label for="fecha_desde">Fecha Desde</label>
                                <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha_desde') ? 'is-invalid' : '' !!}" id="fecha_desde" name="fecha_desde" placeholder="d-m-Y" data-date-format="d-m-Y" value="{{ empty(old('fecha_desde'))? $data->fe_inicio : old('fecha_desde') }}" {{ $errors->has('fecha_desde') ? 'aria-describedby="fecha_desde-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('fecha_desde') )
                                    <div id="fecha_desde-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fecha_desde') }}</div>
                                @endif
                            </div>

                            <div class="col-4">
                                <label for="fecha_hasta">Fecha Hasta</label>
                                <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha_hasta') ? 'is-invalid' : '' !!}" id="fecha_hasta" name="fecha_hasta" placeholder="d-m-Y" data-date-format="d-m-Y" value="{{ empty(old('fecha_hasta'))? $data->fe_fin : old('fecha_hasta') }}" {{ $errors->has('fecha_hasta') ? 'aria-describedby="fecha_hasta-error" aria-invalid="true"' : '' }}>
                                @if( $errors->has('fecha_hasta') )
                                    <div id="fecha_hasta-error" class="invalid-feedback animated fadeIn">{{ $errors->first('fecha_hasta') }}</div>
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