@extends($activeTemplate .'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4">
                        <h4 class="mb-4 text-center">@lang('Please Verify Your Email to Get Access')</h4>

                        <form class="w-100" method="POST" action="{{route('user.verify.email')}}">
                            @csrf
                            <h6 class="mt-2 mb-2 text-center">@lang('Your Email'):  <strong class="text--base">{{auth()->user()->email}}</strong></h6>

                            <div class="form-group">
                                <label>@lang('Verification Code')</label>
                                <input type="text" name="email_verified_code" id="code" class="form-control" required>
                            </div>
                            <div>
                                <button type="submit" class="cmn-btn"> @lang('Verify')</button>
                            </div>
                            <p class="mt-2"><small>@lang('Please check including your Junk/Spam Folder. If not found, you can') <a href="{{route('user.send.verify.code')}}?type=email" class="text--base"> @lang('Resend code')</a></small></p>
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
