@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('/assets/js/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!-- Page JS Code -->
    <script src="{{ asset('/assets/js/pages/be_pages_dashboard.min.js') }}"></script>
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full">
    <!-- Overview -->
  <!--  <div class="row row-deck">
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded text-center d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1">
                    <div class="item rounded-lg bg-body-dark mx-auto my-3">
                        <i class="fa fa-info-circle text-muted"></i>
                    </div>
                    <div class="text-black font-size-h1 font-w700">{{ $total_pendiente }}</div>
                    <div class="text-muted mb-3">
                        Procesos Pendientes
                    </div>
                    <div class="d-inline-block px-3 py-1 rounded-lg font-size-sm font-w600 text-success bg-success-lighter">
                    {{ number_format($total_pendiente*100/$total) }}%
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500" href="{{ URL::to('proceso/solicitud/lista') }}">
                        Visualizar
                        <i class="fa fa-arrow-right ml-1 opacity-25"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded text-center d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1">
                    <div class="item rounded-lg bg-body-dark mx-auto my-3">
                        <i class="fa fa-hourglass-half text-muted"></i>
                    </div>
                    <div class="text-black font-size-h1 font-w700">{{ $total_en_proceso }}</div>
                    <div class="text-muted mb-3">Procesos en Cola</div>
                    <div class="d-inline-block px-3 py-1 rounded-lg font-size-sm font-w600 text-success bg-success-lighter">
                        {{ number_format($total_en_proceso*100/$total) }}%
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500" href="{{ URL::to('proceso/solicitud/pendiente') }}">
                        Visualizar
                        <i class="fa fa-arrow-right ml-1 opacity-25"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded text-center d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1">
                    <div class="item rounded-lg bg-body-dark mx-auto my-3">
                        <i class="fa fa-search-minus text-muted"></i>
                    </div>
                    <div class="text-black font-size-h1 font-w700">{{ $total_anulado }}</div>
                    <div class="text-muted mb-3">Procesos Anulados</div>
                    <div class="d-inline-block px-3 py-1 rounded-lg font-size-sm font-w600 text-danger bg-danger-lighter">
                        {{ number_format($total_anulado*100/$total) }}%
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500" href="{{ URL::to('proceso/solicitud/anulado') }}">
                        Visualizar
                        <i class="fa fa-arrow-right ml-1 opacity-25"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded text-center d-flex flex-column">
                <div class="block-content block-content-full">
                    <div class="item rounded-lg bg-body-dark mx-auto my-3">
                        <i class="fa fa-check-double text-muted"></i>
                    </div>
                    <div class="text-black font-size-h1 font-w700">{{ $total_completo }}</div>
                    <div class="text-muted mb-3">Procesos Completados</div>
                    <div class="d-inline-block px-3 py-1 rounded-lg font-size-sm font-w600 text-success bg-success-lighter">
                        {{ number_format($total_completo*100/$total) }}%
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500" href="{{ URL::to('proceso/solicitud/completo') }}">
                        Visualizar
                        <i class="fa fa-arrow-right ml-1 opacity-25"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>-->
    <!-- END Overview -->
</div>

@endsection