<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> {{ $general->sitename(__($pageTitle)) }}</title>
    @include('partials.seo')

   <!-- bootstrap 4  -->
   <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/vendor/bootstrap.min.css')}}">
    <!-- dashdoard main css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/main.css')}}">
    <!-- custom css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/custom.css')}}">
    <!-- site color -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/color.php?color1='.$general->base_color)}}">
    @stack('style-lib')

    @stack('style')
</head>
<body>

@stack('fbComment')


    @include($activeTemplate.'partials.preloader')

    <div class="page-wrapper">
            @yield('content')
    </div>


    @php
        $cookie = App\Models\Frontend::where('data_keys','cookie.data')->first();
    @endphp

    @if(@$cookie->data_values->status && !session('cookie_accepted'))
        <div class="cookie-remove">
            <div class="cookie__wrapper">
                <div class="container">
                    <div class="flex-wrap align-items-center justify-content-between">
                        <h4 class="title">@lang('Cookie Policy')</h4>
                        <p class="txt my-2">
                            @php echo @$cookie->data_values->description @endphp
                        </p>
                        <div class="button-wrapper">
                            <button class="cmn-btn policy cookie">@lang('Accept')</button>
                            <a class="cmn-btn" href="{{ @$cookie->data_values->link }}" target="_blank" class=" mt-2">@lang('Read Policy')</a>
                            <a href="javascript:void(0)" class="btn--close cookie-close"><i class="las la-times"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


 <!-- jQuery library -->
 <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
<!-- bootstrap js -->
<script src="{{asset($activeTemplateTrue.'js/vendor/bootstrap.bundle.min.js')}}"></script>
<!-- slick slider js -->
<script src="{{asset($activeTemplateTrue.'js/vendor/slick.min.js')}}"></script>
<!-- scroll animation -->
<script src="{{asset($activeTemplateTrue.'js/vendor/wow.min.js')}}"></script>
<!-- dashboard custom js -->
<script src="{{asset($activeTemplateTrue.'js/main.js')}}"></script>

@stack('script-lib')

@stack('script')

@include('partials.plugins')

@include('partials.notify')


<script>
    (function ($) {
        "use strict";
        $(".langSel").on("change", function() {
            window.location.href = "{{route('home')}}/change/"+$(this).val() ;
        });

        $('.cookie').on('click',function () {

            var url = "{{ route('cookie.accept') }}";

            $.get(url,function(response){

                if(response.success){
                    notify('success',response.success);
                    $('.cookie-remove').html('');
                }
            });
        });

        $('.cookie-close').on('click',function () {
            $('.cookie-remove').html('');
        });

    })(jQuery);
</script>

</body>
</html>
