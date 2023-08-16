@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row">
            <div class="col-lg-12 mb-4">
              <h2>@if($advertisement->type == 1) @lang('Sell') @elseif($advertisement->type == 2) @lang('Buy') @else @endif {{__($advertisement->crypto->name)}} @lang('using') {{__($advertisement->fiatGateway->name)}} @lang('with') {{__($advertisement->fiat->name)}} ({{__($advertisement->fiat->code)}})</h2>

              <p class="mt-2">{{__($general->sitename)}} @lang('user named') {{__($advertisement->user->fullname)}} @lang('wishes to') @if($advertisement->type == 1) @lang('buy') @elseif($advertisement->type == 2) @lang('sell') @else @endif {{__($advertisement->crypto->name)}} @if($advertisement->type == 1) @lang('from') @elseif($advertisement->type == 2) @lang('to') @else @endif @lang('you').</p>
            </div>
            <div class="col-lg-7">
              <ul class="trade-request-details-list">
                <li>
                  <span class="caption">@lang('Rate')</span>
                  <span class="value">{{rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin)}} {{__($advertisement->fiat->code)}}/ {{__($advertisement->crypto->code)}}</span>
                </li>
                <li>
                  <span class="caption">@lang('Payment Method')</span>
                  <span class="value">{{__($advertisement->fiatGateway->name)}}</span>
                </li>
                <li>
                  <span class="caption">@lang('User')</span>
                  <span class="value">{{__($advertisement->user->fullname)}}</span>
                </li>
                <li>
                  <span class="caption">@lang('Trade Limits')</span>
                  <span class="value">{{showAmount($advertisement->min)}} - {{showAmount($maxLimit)}} {{__($advertisement->fiat->code)}}</span>
                </li>
                <li>
                  <span class="caption">@lang('Payment Window')</span>
                  <span class="value">{{__($advertisement->window)}} (@lang('minutes'))</span>
                </li>
              </ul><!-- trade-request-details-list end -->

              <form class="trade-request-form mt-5" action="{{route('user.trade.request.store',$advertisement->id)}}" method="POST">
                @csrf
                <h6 class="mb-3">@lang('How much you wish') @if($advertisement->type == 1) @lang('sell') @elseif($advertisement->type == 2) @lang('buy') @else @endif?</h6>
                <div class="row">
                  <div class="col-xl-6 form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg--base text-white" id="payment1">{{__($advertisement->fiat->code)}}</span>
                      </div>
                      <input type="number" step="any" id="amount" name="amount" class="form-control" placeholder="0.00" aria-describedby="payment1" required autocomplete="off">
                    </div>
                    <small class="text-danger message"></small>
                  </div>
                  <div class="col-xl-6 form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg--base text-white" id="payment2">{{__($advertisement->crypto->code)}}</span>
                      </div>
                      <input type="number" step="any" id="final-amount" class="form-control" placeholder="0.00" aria-describedby="payment2" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-md-12 form-group">
                    <p class="text-danger text-sm mb-1"><i class="fas fa-info"></i> @lang('Remember to write about your comfort payment methods in the message').</p>
                    <textarea class="form-control mt-3" name="details" placeholder="@lang('Write your contact message and other information to the trade here')..." required></textarea>
                  </div>
                  <div class="col-md-12">
                    <button type="submit" class="cmn-btn w-100">@lang('Send Trade Request')</button>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-lg-5 pl-xl-5 mt-lg-0 mt-4">
              <div class="terms-sidebar">
                <div class="terms-sidebar__widget">
                  <h6 class="title text-white"><i class="las la-file-invoice text--base"></i> @lang('Terms of trade with') {{__($advertisement->user->fullname)}}</h6>
                  <p>{{__($advertisement->terms)}}</p>
                </div><!-- terms-sidebar__widget end -->
                <div class="terms-sidebar__widget">
                  <h6 class="title text-white"><i class="las la-file-invoice-dollar text--base"></i> @lang('Payment details of') {{__($advertisement->user->fullname)}}</h6>
                  <p>{{__($advertisement->details)}}</p>
                </div><!-- terms-sidebar__widget end -->
              </div><!-- terms-sidebar end -->
            </div>
          </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($){
            "use strict";

            $('#amount').on('input', function(){
                var min = '{{$advertisement->min}}';
                var max = '{{$maxLimit}}';
                var amount = $('#amount').val();
                var rate = '{{rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin)}}';

                $('.message').text('');

                if (parseFloat(amount) < parseFloat(min)) {
                    $('.message').text(`@lang('Minimum Limit is : ${parseFloat(min).toFixed(2)}')`);
                    $('#final-amount').val(0);
                }else if (parseFloat(amount) > parseFloat(max)) {
                    $('.message').text(`@lang('Maximum Limit is : ${parseFloat(max).toFixed(2)}')`);
                    $('#final-amount').val(0);
                }else {
                    var finalAmount = (1 / parseFloat(rate)) * parseFloat(amount);
                    $('#final-amount').val(parseFloat(finalAmount).toFixed(8));
                }

            });

            $('#final-amount').on('input', function(){
                var min = '{{$advertisement->min}}';
                var max = '{{$maxLimit}}';
                var amount = $('#final-amount').val();
                var rate = '{{rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin)}}';

                $('.message').text('');

                var finalAmount = parseFloat(rate) * parseFloat(amount);

                if (parseFloat(finalAmount) < parseFloat(min)) {
                    $('.message').text(`@lang('Minimum Limit is : ${parseFloat(min).toFixed(2)}')`);
                    $('#amount').val(0);
                }else if (parseFloat(finalAmount) > parseFloat(max)) {
                    $('.message').text(`@lang('Maximum Limit is : ${parseFloat(max).toFixed(2)}')`);
                    $('#amount').val(0);
                }else {
                    $('#amount').val(parseFloat(finalAmount).toFixed(2));
                }

            });
        })(jQuery)
    </script>
@endpush
