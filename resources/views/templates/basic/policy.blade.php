@extends($activeTemplate.'layouts.frontend')

@section('content')
    @include($activeTemplate.'partials.breadcrumb')
    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-12 mb-4 ">
                <h3 class="mb-4">{{__($policyDetails->data_values->title)}}</h4>
                @php
                    echo __($policyDetails->data_values->details);
                @endphp
            </div>
          </div>
        </div>
    </section>
@endsection
