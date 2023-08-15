@extends($activeTemplate.'layouts.frontend')
@section('content')
    @php
        $contactContent = getContent('contact.content',true);
        $contactElements = getContent('contact.element');
    @endphp

    @include($activeTemplate.'partials.breadcrumb')

    <div class="container  pt-120 pb-120">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="contact-form-wrapper">
                    <form class="contact-form" action="" method="POST">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-lg-6 form-group">
                                <input name="name" type="text" placeholder="@lang('Your Name')" class="form-control" value="@if(auth()->user()){{auth()->user()->fullname}}@else{{old('name')}}@endif" @if(auth()->user()) readonly @endif required>
                            </div>
                            <div class="col-lg-6 form-group">
                                <input name="email" type="text" placeholder="@lang('Enter E-Mail Address')" class="form-control" value="@if(auth()->user()){{auth()->user()->email}}@else{{old('email')}}@endif" @if(auth()->user()) readonly @endif required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <input name="subject" type="text" placeholder="@lang('Write your subject')" class="form-control" value="{{old('subject')}}" required>
                            </div>
                            <div class="col-lg-12 form-group">
                                <textarea name="message" wrap="off" placeholder="@lang('Write your message')" class="form-control">{{old('message')}}</textarea>
                            </div>

                            <div class="col-lg-12">
                                <button type="submit" class="cmn-btn w-100">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </div><!-- contact-form-wrapper end -->
            </div>
            <div class="col-lg-6 pl-lg-5 order-lg-2 order-1 mb-lg-0 mb-4">
                @foreach($contactElements as $item)
                    <div class="contact-item @if(!$loop->first) mt-4 @endif">
                        <div class="icon">
                            @php echo @$item->data_values->icon @endphp
                        </div>
                        <div class="content">
                            <h4 class="title">{{__(@$item->data_values->heading)}}</h4>
                            <p>{{__(@$item->data_values->details)}}</p>
                        </div>
                    </div><!-- contact-item end -->
                @endforeach
            </div>
        </div>
    </div>
    <div class="map-area">
        <iframe src = "https://maps.google.com/maps?q={{@$contactContent->data_values->latitude}},{{@$contactContent->data_values->longitude}}&hl=es;z=14&amp;output=embed"></iframe>
    </div>


    @if($sections != null)
        @foreach(json_decode($sections) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif

@endsection
