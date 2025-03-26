<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="en">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- <title>Minimal Dashboard - Examples of just how powerful ArchitectUI really is!</title> -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('Layout.stylesheet')
        <script>
            var base_url="{{url::to('/')}}";
        </script>
        @yield('page-stylesheet')
    </head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar">
        <!--================== 
            Header
        ====================-->
        @include('Layout.header')
        <div class="app-main">
            <!--================== 
                Sidebar
            ====================-->
            @include('Layout.sidebar')
            <div class="app-main__outer">
                @yield('content')
                <!--================== 
                    Footer
                ====================-->
                @include('Layout.footer')
            </div>
        </div>
    </div>
    <div class="loader loader-content">
        <div class="line-scale-pulse-out">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="CommonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel2"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="formContent">
                </div>
            </div><!-- modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->
    <!-- Order Preview Modal -->
    <div class="modal fade" id="OrderPreviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel2"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body order_details" id="formContent"></div>
            </div><!-- modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->
    <!-- Modal -->
    <div class="modal fade" id="LocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
        <div class="modal-dialog location-dailog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="location-modal-title" id="myModalLabel2"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="LocationformContent">
                </div>
            </div><!-- modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->
    <!--================== 
        Script
    ====================-->
    @include('Layout.script')
    <!--================== 
        Extra Script
    ====================-->
    @stack('scripts')
    @yield('page-script')
</body>
</html>