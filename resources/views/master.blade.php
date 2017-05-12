<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Set Csrf token on all pages -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Load Bootstrap-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.2/sweetalert2.css" rel="stylesheet" type="text/css">
        <title>Inventory Manger</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css')}}"/>
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <script src="https://use.fontawesome.com/682442a8be.js"></script>

        <!-- Set Csrf token to be used by javascript and axios-->
        <script>
window.Laravel = <?php
echo json_encode([
    'csrfToken' => csrf_token(),
]);
?>
        </script>
        <!-- Styles -->
        <style>
            .location-tab{
                height:104px;
                padding-left: 150px;
            }
            
            .location-tab > img{
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: auto;
                max-width: 130px;
            }
            
            .text-primary{
                color: #29ABE2 !important;
            }
            
            .panel-heading{
                background-color: #29ABE2 !important;
                color: white !important;
            }
            
            .panel{
                border-color: #29ABE2 !important;
            }
            
            .btn-primary{
                background-color: #29ABE2 !important;
                color: white !important;
                border-color: #29ABE2 !important;
                border-radius: 3px;
                margin: 10px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div id="wrapper">
                @yield('content')
            </div>
        </div>
        <!-- Load Jquery and bootstrap js-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.2/sweetalert2.min.js"></script>
        <script src="{{ asset('/js/app.js')}}"></script>
        @yield('scripts')
    </body>
</html>
