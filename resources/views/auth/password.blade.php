@extends('layouts.auth')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Page JS Code -->
    <script src="{{ asset('/assets/js/pages/op_auth_reminder.min.js') }}"></script>
@endsection

@section('content')

<!-- Page Content -->
<div class="bg-image" style="background-image: url('{{ asset('/assets/media/photos/photo19@2x.jpg') }}');">
    <div class="row no-gutters bg-gd-sun-op">
        <div class="hero-static col-md-6 d-flex align-items-center bg-white">
            <div class="p-3 w-100">
                <div class="text-center">
                    <a class="link-fx text-warning font-w700 font-size-h1" href="{{ url('/') }}">
                        <span class="text-dark">GOBEL</span><span class="text-primary"> Administrativo</span>
                    </a>
                    <p class="text-uppercase font-w700 font-size-sm text-muted">Recuperar contraseña</p>
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

                        <form class="js-validation-reminder" action="{{ url('/recuperar/enviar') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group py-3">
                                <input type="text" class="form-control form-control-lg form-control-alt {!! $errors->has('email') ? 'is-invalid' : '' !!}" {!! $errors->has('email') ? 'aria-describedby="email-error" aria-invalid="true"' : '' !!} id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                                @if( $errors->has('email') )
                                    <div id="email-error" class="invalid-feedback animated fadeIn">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-block btn-hero-lg btn-hero-warning">
                                    <i class="fa fa-fw fa-reply mr-1"></i> Recuperar
                                </button>
                                <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                    <a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/') }}">
                                        <i class="fa fa-sign-in-alt text-muted mr-1"></i> Iniciar Sesión
                                    </a>
                                    {{--<a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/registrar') }}">
                                        <i class="fa fa-plus text-muted mr-1"></i> Registrarse
                                    </a>--}}
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
                    No te preocupes por el fracaso..
                </p>
                <p class="font-size-h1 font-w600 text-white-75 mb-0">
                    ..pero aprende de eso!
                </p>
            </div>
        </div>
    </div>
</div>

<!-- END Page Content -->
@endsection