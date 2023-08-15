@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    @php
        $content = getContent('trade_info.content',true);
        if($content){
            $content = $content->data_values;
        }
    @endphp

    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-12 mb-30">
                <div class="btn-group justify-content-end">
                    <a href="{{route('user.advertisement.index')}}" class="cmn-btn2 btn-md">@lang('My Advertisements')</a>
                </div>
            </div>
            <div class="col-12">
              <form class="create-trade-form" action="{{route('user.advertisement.update',$advertisement->id)}}" method="POST">
                @csrf
                <div class="create-trade-form__block">
                  <h4 class="title">@lang('Trade')</h4>
                  <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                          <label>@lang('I want to') ... <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->i_want_to)"></i></label>
                          <select class="select" name="type" required>
                            <option value="1">@lang('Buy')</option>
                            <option value="2">@lang('Sell')</option>
                          </select>
                          <code class="text--base tradeChargeText"></code>
                        </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>@lang('Crypto Currency') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->crypto_currency)"></i></label>
                        <select class="select" name="crypto_id" required>

                          @foreach ($cryptos as $item)
                            <option value="{{$item->id}}" data-crypto="{{$item->rate}}" data-currency="{{$item->code}}">{{__($item->name)}}</option>
                          @endforeach

                        </select>
                      </div>
                    </div>
                  </div>
                </div><!-- create-trade-form__block end -->
                <div class="create-trade-form__block">
                  <h4 class="title">@lang('Payment Information')</h4>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>@lang('Fiat Payment Method') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->fiat_payment_method)"></i></label>

                        <select class="select" id="fiat-gateway" name="fiat_gateway_id" required>

                          @foreach ($fiatGateways as $item)
                            <option value="{{$item->id}}" data-id="{{$item->id}}">{{__($item->name)}}</option>
                          @endforeach

                        </select>

                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>@lang('Currency') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->currency)"></i></label>

                        <select class="select" id="fiat-currency" name="fiat_id" required>

                        </select>

                      </div>
                    </div>
                    <div class="col-md-6">
                      <label>@lang('Margin') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->margin)"></i></label>
                      <div class="input-group mb-3">
                        <input type="number" step="0.01" class="form-control" name="margin" value="{{$advertisement->margin}}" placeholder="@lang('Margin rate')"aria-describedby="basic-addon1" required>
                        <div class="input-group-append">
                          <span class="input-group-text" id="basic-addon1">%</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Payment Window') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->payment_window)"></i></label>

                            <select class="select" name="window" required>
                                @foreach ($paymentWindows as $item)
                                    <option value="{{$item->minute}}">{{$item->minute}} @lang('Minutes')</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>@lang('Minimum Limit') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->minimum_limit)"></i></label>

                        <div class="input-group mb-3">
                            <input type="number" step="any" name="min" value="{{getAmount($advertisement->min)}}" placeholder="@lang('Minimum amount')" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text currency-text">{{__($advertisement->fiat->code)}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>@lang('Maximum Limit') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->maximum_limit)"></i></label>

                        <div class="input-group mb-3">
                            <input type="number" step="any" name="max" value="{{getAmount($advertisement->max)}}" placeholder="@lang('Max amount')" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text currency-text">{{__($advertisement->fiat->code)}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label> @lang('Price Equation') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->price_equation)"></i></label>
                        <div class="input-group mb-3">
                            <p id="priceEquationt" class="text-success font-weight-bolder">{{rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin)}} {{__($advertisement->fiat->code)}}/{{__($advertisement->crypto->code)}}</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label>@lang('Payment Details') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->payment_details)"></i></label>
                        <textarea placeholder="@lang('Payment details')" class="form-control" name="details" required>{{$advertisement->details}}</textarea>
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label>@lang('Terms of Trade') <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" title="@lang(@$content->terms_of_trades)"></i></label>
                        <textarea placeholder="@lang('Terms of Trade')" class="form-control" name="terms" required>{{$advertisement->terms}}</textarea>
                      </div>
                    </div>
                  </div>
                </div><!-- create-trade-form__block end -->

                <button type="submit" class="cmn-btn w-100">@lang('Update')</button>
              </form><!-- create-trade-wrapper end -->
            </div>
          </div>
        </div>
    </section>

@endsection

@push('script')
    <script>
        (function($){
            "use strict";
            $('select[name="type"]').val('{{$advertisement->type}}');
            $('select[name="crypto_id"]').val('{{$advertisement->crypto_id}}');
            $('select[name="fiat_gateway_id"]').val('{{$advertisement->fiat_gateway_id}}');
            $('select[name="window"]').val('{{$advertisement->window}}');

            let ajaxCounter = null;

            $('#fiat-gateway').on('change',function(){
                var methodId = $(this).find('option:selected').val();

                $.ajax({
                    type: "get",
                    url: "{{route('advertisement.supported.currency')}}",
                    data: {id:methodId},
                    dataType: "json",

                    success: function (response) {
                        if(response.success){
                            var html = `<option data-fiat="" value="">@lang('Select One')</option>`;

                            if(response.supportedCurrencies.length > 0) {
                                $.each(response.supportedCurrencies, function (index, value) {
                                    if (value.id == `{{$advertisement->fiat_id}}`) {
                                        ajaxCounter = 1;
                                    }

                                    html += `<option value="${value.id}" data-fiat="${parseFloat(value.rate)}" data-currency="${value.code}" ${value.id == `{{$advertisement->fiat_id}}` ? 'selected': '' }>${value.code}</option>`;
                                });
                                console.log(ajaxCounter);

                                $('#fiat-currency').html(html);

                                if (ajaxCounter == null) {
                                    $(document).find('.currency-text').text('');
                                }else{
                                    fiatCurrency = $('select[name="fiat_id"]').find('option:selected').data('currency');
                                    $(document).find('.currency-text').text(`@lang('${fiatCurrency}')`);
                                    ajaxCounter = null;
                                }

                            }

                        }else{
                            notify('error', response.error);
                        }
                    }
                });
            }).change();
            
            $('select[name="type"]').on('change', function() {
                if (this.value == 1) {
                    $('.tradeChargeText').empty();
                } else {
                    if (parseFloat('{{ $general->trade_charge }}') > 0) {
                        $('.tradeChargeText').text(`@lang('For selling') {{ getAmount($general->trade_charge) }}% @lang('will be charged for each completed trade.')`);
                    }
                }
            }).change();

            var type = $('select[name="type"]').find('option:selected').val();
            var cryptoRate = $('select[name="crypto_id"]').find('option:selected').data('crypto');
            var margin = $('input[name="margin"]').val();
            var cryptoCurrency = $('select[name="crypto_id"]').find('option:selected').data('currency');
            var fiatRate = null;
            var fiatCurrency = null;

            $('select[name="type"]').on('change',function(){
               type = $(this).find('option:selected').val();
               priceEquation();
            });

            $('select[name="crypto_id"]').on('change',function(){
               cryptoRate = $(this).find('option:selected').data('crypto');
               cryptoCurrency = $(this).find('option:selected').data('currency');
               fiatRate = $('select[name="fiat_id"]').find('option:selected').data('fiat');
               fiatCurrency = $('select[name="fiat_id"]').find('option:selected').data('currency');
               priceEquation();
            });

            $('select[name="fiat_id"]').on('change',function(){
               fiatRate = $(this).find('option:selected').data('fiat');
               fiatCurrency = $(this).find('option:selected').data('currency');
               $(document).find('.currency-text').text(`@lang('${fiatCurrency}')`);
               priceEquation();
            });

            $('input[name="margin"]').on('input',function(){
               margin = $(this).val();
               fiatRate =  $('select[name="fiat_id"]').find('option:selected').data('fiat');
               fiatCurrency =  $('select[name="fiat_id"]').find('option:selected').data('currency');
               priceEquation();
            });

            function priceEquation() {
                if(!fiatRate) {
                    $('#priceEquationt').text('0.00');
                }else{
                    var amount = parseFloat(cryptoRate) * parseFloat(fiatRate);
                    var rate;
                    if (!margin) {
                        $('#priceEquationt').text(parseFloat(amount).toFixed(2) + ' ' + fiatCurrency + '/' + cryptoCurrency);
                    }else{
                        if (parseInt(type) == 1) {
                            rate = parseFloat(amount) - ((amount * parseFloat(margin)) / 100);
                        }else if(parseInt(type) == 2) {
                            rate = parseFloat(amount) + ((amount * parseFloat(margin)) / 100);
                        }
                        $('#priceEquationt').text(parseFloat(rate).toFixed(2) + ' ' + fiatCurrency + '/' + cryptoCurrency);
                    }
                }
            }
        })(jQuery)
    </script>
@endpush
