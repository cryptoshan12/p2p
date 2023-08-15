@extends($activeTemplate.'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content',true)
    @endphp

    <section class="hero bg_img"  data-background="{{ getImage('assets/images/frontend/banner/'.@$bannerContent->data_values->image,'1920x1270') }}">
        <div class="container position-relative">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-5 text-xl-left text-center">
                    <h2 class="hero__title text-white mb-3">{{__(@$bannerContent->data_values->heading)}}</h2>
                    <p class="hero__details text-white">{{__(@$bannerContent->data_values->sub_heading)}}</p>

                    <div class="btn-group mt-4 justify-content-xl-start justify-content-center">
                        <a href="{{__(@$bannerContent->data_values->button_one_url)}}" class="cmn-btn">{{__(@$bannerContent->data_values->button_one_text)}}</a>
                        <a href="{{__(@$bannerContent->data_values->button_two_url)}}" class="cmn-btn">{{__(@$bannerContent->data_values->button_two_text)}}</a>
                    </div>
                </div>
                <div class="col-xl-6 mt-5">
                    <div class="bitcoin-form-wrapper">
                        <div class="form-image"><img src="{{ getImage('assets/images/frontend/banner/'.@$bannerContent->data_values->form_bg,'700x465') }}" alt="image"></div>
                        <h5 class="title text-white">@lang(@$bannerContent->data_values->form_header)</h5>
                        <form class="bitcoin-form" action="{{route('advertisement.search')}}" method="GET">
                            <div class="row align-items-center">
                                <div class="col-md-6 form-group">
                                    <select class="select" name="type" required>
                                        <option value="">@lang('Select Buy or Sell')</option>
                                        <option value="buy">@lang('Buy')</option>
                                        <option value="sell">@lang('Sell')</option>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <select class="select" name="crypto_code" required>
                                        <option value="">@lang('Select Crypto Currency')</option>
                                        @foreach ($cryptos as $item)
                                            <option value="{{$item->code}}">{{__($item->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <select class="select" id="fiat-gateway" name="fiat_gateway_slug" required>
                                        <option value="">@lang('Select Payment Method')</option>
                                        @foreach ($fiatGateways as $item)
                                            <option value="{{$item->slug}}" data-id="{{$item->id}}">{{__($item->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <select class="select" id="fiat-currency" name="fiat_code" required>

                                    </select>
                                </div>

                                <div class="col-lg-12 form-group">
                                    <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Preferred Amount (optional)')">
                                </div>

                                <div class="col-lg-12">
                                    <button type="submit" class="cmn-btn w-100 mt-3">@lang(@$bannerContent->data_values->form_button_text)</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- bitcoin-form-wrapper end -->
                </div>
            </div>
        </div>
    </section>

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection

@push('script')
    <script>
        (function($){
            "use strict";

            $('#fiat-gateway').on('change',function(){
                var methodId = $(this).find('option:selected').data('id');
                var html = ``;

                if (methodId) {

                    $.ajax({
                        type: "get",
                        url: "{{route('advertisement.supported.currency')}}",
                        data: {id:methodId},
                        dataType: "json",

                        success: function (response) {
                            if(response.success){

                                if(response.supportedCurrencies.length > 0) {
                                    $.each(response.supportedCurrencies, function (index, value) {
                                        html += `<option value="${value.code}">${value.code}</option>`;
                                    });
                                }

                                $('#fiat-currency').html(html);

                            }else{
                                notify('error', response.error);
                            }
                        }
                    });
                }else{
                    html = `<option value="">@lang('Select One')</option>`;
                    $('#fiat-currency').html(html);
                }

            }).change();

        })(jQuery)
    </script>
@endpush


