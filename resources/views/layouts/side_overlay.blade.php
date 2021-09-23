<!-- Side Overlay-->
<aside id="side-overlay">
    <!-- Side Header -->
    <div class="bg-image" style="background-image: url('{{ asset('assets/media/various/bg_side_overlay_header.jpg') }}');">
        <div class="bg-primary-op">
            <div class="content-header">
                <!-- User Avatar -->
                <a class="img-link mr-1" href="javascript:void(0)">
                    <img class="img-avatar img-avatar48" src="{{ asset('assets/media/avatars/avatar0.jpg') }}" alt="">
                </a>
                <!-- END User Avatar -->

                <!-- User Info -->
                <div class="ml-2">
                    <a class="text-white font-w600" href="javascript:void(0)">{{ Auth::user()->nb_usuario }}</a>
                    <div class="text-white-75 font-size-sm">{{ Auth::user()->da_login }}</div>
                </div>
                <!-- END User Info -->

                <!-- Close Side Overlay -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <a class="ml-auto text-white" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_close">
                    <i class="fa fa-times-circle"></i>
                </a>
                <!-- END Close Side Overlay -->
            </div>
        </div>
    </div>
    <!-- END Side Header -->

    <!-- Side Content -->
    <div class="content-side">
        <!-- Side Overlay Tabs -->
        <div class="block block-transparent pull-x pull-t">
            <ul class="nav nav-tabs nav-tabs-block nav-justified" data-toggle="tabs" role="tablist">
                {{--
                <li class="nav-item">
                    <a class="nav-link active" href="#so-settings">
                        <i class="fa fa-fw fa-cog"></i>
                    </a>
                </li>
                --}}
                {{--
                <li class="nav-item">
                    <a class="nav-link" href="#so-people">
                        <i class="far fa-fw fa-user-circle"></i>
                    </a>
                </li>
                --}}
                <li class="nav-item">
                    <a class="nav-link active" href="#so-profile">
                        <i class="far fa-fw fa-edit"></i>
                    </a>
                </li>
            </ul>
            <div class="block-content tab-content overflow-hidden">
{{--
                @include('layouts.side_overlay_settings')
--}}
{{--
                @include('layouts.side_overlay_people')
--}}
                @include('layouts.side_overlay_profile')

            </div>
        </div>
        <!-- END Side Overlay Tabs -->
    </div>
    <!-- END Side Content -->
</aside>
<!-- END Side Overlay -->