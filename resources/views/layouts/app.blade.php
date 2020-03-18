<html>
    <head>
        <title> @yield('title')</title>
    </head>
    <body>
    @section('sidebar')
        This is the master sidebar.
    @show
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('widgets.default')
            </div>
        </div>
    </div>
    </body>
</html>
<?php die;
