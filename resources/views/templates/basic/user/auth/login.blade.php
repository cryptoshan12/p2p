@extends($activeTemplate.'layouts.auth')

@section('content')
    @php
        $loginContent = getContent('login.content',true)
    @endphp

    <section class="account-section">
        <div class="left">
          <div class="line-bg">
            <img src="{{asset($activeTemplateTrue.'images/line-bg.png')}}" alt="image">
          </div>
          <div class="account-form-area">
            <div class="text-center">
              <a href="{{url('/')}}" class="account-logo"><img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" alt="image"></a>
            </div>
            <form class="mt-5" method="POST" action="{{ route('user.login')}}" onsubmit="return submitUserForm();">
                @csrf
              <div class="form-group">
                <label>@lang('Username or Email')</label>
                <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
              </div>
              <div class="form-group">
                <label>@lang('Password')</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <div class="form-group google-captcha">
                @php echo loadReCaptcha() @endphp
              </div>

              @include($activeTemplate.'partials.custom_captcha')

              <div>
                <button type="submit" class="cmn-btn w-100">@lang('Login Now')</button>
              </div>
              <div class="row align-items-center mt-3">
                <div class="col-lg-6">
                  <p>@lang('Haven\'t an account')? <a href="{{route('user.register')}}" class="base--color">@lang('Signup now')</a></p>
                </div>
                <div class="col-lg-6 text-lg-right">
                  <a href="{{route('user.password.request')}}" class="mt-3 base--color">@lang('Forgot password')?</a>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="right bg_img" data-background="{{ getImage('assets/images/frontend/login/'.@$loginContent->data_values->image,'1150x950') }}">
          <div class="content text-center">
            <h2 class="text-white mb-4">{{__(@$loginContent->data_values->heading)}}</h2>
            <p class="text-white">{{__(@$loginContent->data_values->sub_heading)}}</p>
          </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
    </script>
@endpush
