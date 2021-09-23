@extends('layouts.auth')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('/assets/js/plugins/vide/jquery.vide.min.js') }}"></script>
    <script src="{{ asset('/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Page JS Code -->
    <script src="{{ asset('/assets/js/pages/op_auth_lock.min.js') }}"></script>
@endsection

@section('content')

<!-- Page Content -->
<div class="bg-video" data-vide-bg="{{ asset('/assets/media/videos/city_night') }}" data-vide-options="posterType: jpg">
    <div class="row no-gutters bg-xplay-op">

        <div class="hero-static col-md-6 d-flex align-items-center bg-white">
            <div class="p-3 w-100">
                <div class="text-center">
                    <a class="link-fx text-danger font-w700 font-size-h1" href="{{ url('/') }}">
                        <span class="text-dark">GOBEL</span><span class="text-primary"> Administrativo</span>
                    </a>
                    <p class="text-uppercase font-w700 font-size-sm text-muted">Recuperar Contraseña</p>
                </div>
                <div class="py-2 text-center">
                    <div class="block-content">
                        <i class="fa fa-user-tie fa-2x text-muted"></i>
                        <p class="mt-3 mb-0 font-w600 font-size-lg">
                            {{ $usuario->nb_usuario }}
                        </p>
                    </div>
                </div>
                <div class="row no-gutters justify-content-center">
                    <div class="col-sm-8 col-xl-6">

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

                        @if( $errors->has('da_mensaje') )
                            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                                <div class="flex-fill mr-3">
                                    <p class="mb-0">{{ $errors->first('da_mensaje') }}</p>
                                </div>
                            </div>
                        @endif

                        <form class="js-validation-lock" action="{{ url('/recuperar/cambiar') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="token" value="{{ $llave }}">
                            <div class="py-3">
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg form-control-alt {!! $errors->has('contraseña') ? 'is-invalid' : '' !!}" {!! $errors->has('contraseña') ? 'aria-describedby="contraseña-error" aria-invalid="true"' : '' !!} id="contraseña" name="contraseña" placeholder="Contraseña.." value="{{ old('contraseña') }}">
                                    @if( $errors->has('contraseña') )
                                        <div id="contraseña-error" class="invalid-feedback animated fadeIn">{{ $errors->first('contraseña') }}</div>
                                    @endif
                                    </div>
                                    <div class="form-group">
                                    <input type="password" class="form-control form-control-lg form-control-alt {!! $errors->has('contraseña_confirmation') ? 'is-invalid' : '' !!}" {!! $errors->has('contraseña_confirmation') ? 'aria-describedby="contraseña_confirmation-error" aria-invalid="true"' : '' !!} id="contraseña_confirmation" name="contraseña_confirmation" placeholder="Confirmar Contraseña.." value="{{ old('contraseña_confirmation') }}">
                                    @if( $errors->has('contraseña_confirmation') )
                                        <div id="contraseña_confirmation-error" class="invalid-feedback animated fadeIn">{{ $errors->first('contraseña_confirmation') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-block btn-hero-lg btn-hero-danger">
                                    <i class="fa fa-fw fa-lock-open mr-1"></i> Cambiar
                                </button>
                                <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                    <a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/recuperar') }}">
                                        <i class="fa fa-exclamation-triangle text-muted mr-1"></i> ¿Olvidó su Contraseña?
                                    </a>
                                    <a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/') }}">
                                        <i class="fa fa-sign-out-alt text-muted mr-1"></i> Iniciar Sesión
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-static col-md-6 d-none d-md-flex align-items-md-center justify-content-md-center text-md-center">
            <div class="p-3">
                <p class="display-4 font-w700 text-white mb-0">
                    Visualiza tu idea..
                </p>
                <p class="font-size-h1 font-w600 text-white-75 mb-0">
                    ..y construyelo!
                </p>
            </div>
        </div>

    </div>
</div>

<!-- END Page Content -->
@endsection
