@extends($activeTemplate .'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')


    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4">
                        <h4 class="mb-4 text-center">@lang('2FA Verification')</h4>

                        <form class="w-100" method="POST" action="{{route('user.go2fa.verify')}}">
                            @csrf
                            <h6 class="mt-2 mb-2 text-center">@lang('Current Time'):  <strong class="text--base">{{\Carbon\Carbon::now()}}</strong></h6>

                            <div class="form-group">
                                <label>@lang('Verification Code') <span class="text-center">*</span></label>
                                <input type="text" name="code" id="code" class="form-control" required>
                            </div>
                            <div>
                                <button type="submit" class="cmn-btn"> @lang('Verify')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
<script>
    (function($){
        "use strict";
        $('#code').on('input change', function () {
          var xx = document.getElementById('code').value;

              $(this).val(function (index, value) {
                 value = value.substr(0,7);
                  return value.replace(/\W/gi, '').replace(/(.{3})/g, '$1 ');
              });

      });
    })(jQuery)
</script>
@endpush
