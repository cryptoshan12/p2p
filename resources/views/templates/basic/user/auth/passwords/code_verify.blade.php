@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4">
                        <h4 class="mb-4">@lang('Verification Process')</h4>
                        <form class="w-100" method="POST" action="{{ route('user.password.verify.code') }}">
                            @csrf

                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="form-group">
                                <label>@lang('Verification Code')</label>
                                <input type="text" class="form-control"  name="code" id="code" required>
                            </div>
                            <div>
                                <button type="submit" class="cmn-btn"> @lang('Send Password Code')</button>
                            </div>
                            <p class="mt-2"><small>@lang('Please check including your Junk/Spam Folder. If not found, you can') <a href="{{ route('user.password.request') }}" class="text--base"> @lang('Resend code')</a></small></p>
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
